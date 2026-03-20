# Squid Proxy Quota Enforcement Architecture
## Real-time Bandwidth Accounting + Mid-Download Blocking + Web Dashboard

Author: Md Golam Kibria
Environment: Squid 5.9 + MySQL 8 + Node.js 18 + PHP 8.1 + Python 3 + Laravel 10 + tshark
Last updated: 2026-03-20

---

# 🎯 Goal

✅ Accurate bandwidth accounting (per user)
✅ Persistent usage tracking in database
✅ Real-time quota enforcement
✅ Immediate download termination when quota exceeded
✅ Persistent iptables block on quota-exceeded users
✅ Web dashboard for users to view and unblock their proxy accounts
✅ Production-safe & restart-safe design

---

# 🧠 High Level Design

The system is split into **three responsibilities**:

| Layer | Responsibility |
|-------|----------------|
| Accounting | Exact usage after each request completes |
| Enforcement | Live monitoring + instant block + iptables rule |
| Management | Laravel dashboard for viewing usage + unblocking |

---

# 🏗 Architecture Diagram

```
Client
   ↓
Squid Proxy (port 3128)
   ├── Auth helpers: ipdbauth.js (Node.js), basic_db_auth (Perl), quota_check (PHP)
   ↓
────────────────────────────────────────
│  bandwidth.log (user + bytes/req)    │
│  tshark live TCP packets             │
────────────────────────────────────────
        ↓                  ↓
  ingestor.py        quota_enforcer_tshark.py
        ↓                  ↓
      MySQL  ←─────────────┘
        ↓
  squid_users.used_bytes  (source of truth)
  squid_users.is_blocked  (block flag)
        ↓
  Laravel Dashboard (user UI + unblock)
```

---

# 🔹 Components

---

## 1️⃣ Squid Configuration

### Auth helpers (`/etc/squid/squid.conf`)

Three external helpers work together to authenticate and authorize each request:

| Helper | Language | Role |
|--------|----------|------|
| `ipdbauth.js` | Node.js 18 | IP allowlist check — verifies client IP is registered |
| `basic_db_auth` | Perl | Username/password auth against `squid_users` table (MD5) |
| `mysql_check_quota.php` | PHP 8.1 | Quota check — returns ERR if user's quota is exhausted |

> **Important:** `ipdbauth.js` uses the `??` (nullish coalescing) operator which requires **Node.js 14+**. Node.js 12 will crash this helper and prevent Squid from starting.

### Log format

```
logformat bandwidth %ts.%03tu %>a %un %rm %ru %>Hs %<st
access_log /var/log/squid/bandwidth.log bandwidth
access_log /var/log/squid/access.log squid
```

Produces lines in `bandwidth.log`:

```
timestamp  client_ip  username  method  url  status  bytes
```

> **Note:** `%<st` = bytes received from origin server. Cache hits log 0 bytes. To count bytes delivered to client regardless of cache, use `%>st` instead.

### Access rules

```
http_access deny !ipauth          # must be registered IP
http_access deny !db-auth         # must authenticate
http_access allow ipauth db-auth quota_ok  # must have remaining quota
http_access deny all
```

---

## 2️⃣ MySQL Schema

### `squid_users`

| Column | Type | Purpose |
|--------|------|---------|
| `user` | VARCHAR | Username |
| `quota_bytes` | BIGINT | Total allowed bytes (synced from `bandwidth_limit_gb`) |
| `used_bytes` | BIGINT | Cumulative bytes consumed |
| `bandwidth_limit_gb` | DECIMAL | Limit in GB (mutator auto-sets `quota_bytes`) |
| `is_blocked` | TINYINT | 1 = blocked by enforcer or admin |
| `last_seen_at` | DATETIME | Last activity timestamp |

### `proxy_requests`

Audit log of every proxied request:

```sql
ts, client_ip, username, method, url, status, bytes, created_at
```

Indexed on `(client_ip, id)` for fast IP→user lookup by the enforcer.

---

# 🔹 Accounting Service

## `ingestor.py` (`/root/bandwidth-log-process/ingestor.py`)

### Flow

```
tail -F /var/log/squid/bandwidth.log
   ↓
parse line → skip if no username or bytes=0
   ↓
INSERT INTO proxy_requests (ts, client_ip, username, method, url, status, bytes)
   ↓
UPDATE squid_users SET used_bytes = used_bytes + {bytes} WHERE user = {username}
```

### Key properties

- Restart-safe: uses `tail -F`, picks up where it left off
- Auto-reconnects MySQL on disconnect
- Logs to `/var/log/squid/bandwidth-ingestor.log`
- Runs as: `root` (service: `bandwidth-ingestor.service`)

---

# 🔹 Enforcement Service

## `quota_enforcer_tshark.py` (`/usr/local/bin/quota_enforcer_tshark.py`)

### Why tshark?

Provides real-time per-IP byte counts **during** an active download — before Squid logs the completed request. This enables mid-download enforcement that the ingestor alone cannot provide.

### Flow

```
tshark -i any -f "tcp src port 3128 src host {PROXY_IP}"
   ↓
accumulate live_total[client_ip] += tcp.len  (rolling, resets after 10s idle)
   ↓
every second:
  for each active IP:
    1. resolve username from proxy_requests (last completed request for this IP)
    2. fetch quota_bytes, used_bytes, is_blocked from squid_users
    3. if is_blocked == 1 → SKIP (already blocked, don't stack iptables rules)
    4. remaining = quota_bytes - used_bytes
    5. if remaining <= 0  → kill_ip + block_user
    6. if rolling_bytes > remaining → kill_ip + block_user
```

### Block action (`kill_ip`)

```bash
iptables -I INPUT -s {ip} -p tcp --dport 3128 -j REJECT --reject-with tcp-reset
conntrack -D -s {ip}
```

- `iptables -I` — blocks all future connections from that IP
- `conntrack -D` — immediately kills the active download session
- `block_user` — sets `is_blocked = 1` in `squid_users`

> **Critical:** The enforcer checks `is_blocked` before firing `kill_ip`. If a user is already blocked, it skips adding another iptables rule. Without this, rules stack up and become impossible to clear with a single `iptables -D`.

### Logs

`/var/log/squid/quota_enforcer.log`

---

# 🔹 Unblock Flow

## How unblocking works

When a user exceeds their quota, the enforcer:
1. Sets `is_blocked = 1` in DB
2. Adds `iptables REJECT` rule for client IP
3. Kills active connection via `conntrack -D`

To unblock, the Laravel dashboard calls `ModifyAction` which:
1. Updates `bandwidth_limit_gb` (and `quota_bytes` via model mutator) to the new higher value
2. Sets `is_blocked = 0` in DB
3. **Loops** `iptables -D` until all stacked REJECT rules are removed (not just one)

### `ModifyAction` unblock condition

```php
// Only unblocks if new quota exceeds current usage
if ($oldIsBlocked && $newQuotaBytes > $oldUsedBytes) {
    // set is_blocked = 0
    // loop iptables -D until all rules gone
}
```

### sudo permission for `www-data`

PHP-FPM runs as `www-data` which cannot run `iptables` by default. A sudoers rule grants the minimum needed:

```
# /etc/sudoers.d/www-data-iptables
www-data ALL=(root) NOPASSWD: /sbin/iptables
```

---

# 🔹 Laravel Web Dashboard

## User Dashboard (`/user/dashboard`)

Displays:
- Total proxy users, active/disabled counts
- Bandwidth usage (total GB used vs limit)
- **Blocked Proxy Users** section (if any are blocked)
- 7-day bandwidth chart (AJAX, from `proxy_requests` table)

## Blocked Users Panel

Shows each blocked proxy account with:
- Username
- Reason: `Quota Exceeded` or `Manually Blocked`
- Used GB / Current Limit
- **Unblock** button → opens modal

## Unblock Modal

- Fetches **fresh** `used_bytes` via AJAX when opened (avoids stale page data)
- Requires new limit > current `used_bytes`
- Calls `POST /user/blocked-users/{id}/unblock` with CSRF token
- On success: reloads page

## Relevant routes

```
GET  /user/dashboard                         → index()
GET  /user/bandwidth-data                    → getBandwidthData()
GET  /user/blocked-users/{id}/status         → blockedUserStatus()   ← fresh used_bytes for modal
POST /user/blocked-users/{id}/unblock        → unblockUser()
```

---

# 🔹 Services

| Service | Script | User | Restarts |
|---------|--------|------|---------|
| `bandwidth-ingestor.service` | `/root/bandwidth-log-process/ingestor.py` | root | always |
| `quota-enforcer.service` | `/usr/local/bin/quota_enforcer_tshark.py` | root | always |
| `squid.service` | squid | proxy | — |
| `php8.1-fpm.service` | php-fpm | www-data | — |
| `nginx.service` | nginx | www-data | — |

### Common commands

```bash
systemctl status quota-enforcer
systemctl restart quota-enforcer

systemctl status bandwidth-ingestor
systemctl restart bandwidth-ingestor

# Check for stale iptables rules
iptables -L INPUT -n | grep 3128

# Manually flush all proxy block rules
iptables -L INPUT -n --line-numbers | grep 3128
iptables -D INPUT {rule_number}
```

---

# 🔹 Log Files

| Log | Written by | Purpose |
|-----|-----------|---------|
| `/var/log/squid/bandwidth.log` | Squid | Per-request bytes — consumed by ingestor |
| `/var/log/squid/access.log` | Squid | Full Squid access log (debug) |
| `/var/log/squid/quota_enforcer.log` | quota_enforcer | Block/kill events |
| `/var/log/squid/bandwidth-ingestor.log` | ingestor | Insert/update confirmations |
| `/var/log/squid/quota_helper.log` | mysql_check_quota.php | PHP helper per-request results |
| `/var/log/squid/ipdbauth.log` | ipdbauth.js | Node.js IP auth results |
| `/storage/logs/laravel.log` | Laravel | App errors |

> **Ownership gotcha:** Squid log files must be owned by `proxy:proxy`. Laravel logs must be owned by `www-data:www-data`. Running `php artisan` or `touch` as root corrupts ownership. Always use `sudo -u www-data php artisan`.

---

# 🔹 Runtime Flow Examples

### Normal download

```
user001 downloads 5MB, quota=2GB, used=100MB
→ remaining = 1.9GB
→ rolling = 5MB
→ 5MB < 1.9GB → OK
→ ingestor logs 5MB → used_bytes += 5MB
```

### Quota exceeded mid-download

```
user001 downloads 100MB, quota=100MB, used=85MB
→ remaining = 15MB
→ rolling accumulates past 15MB
→ enforcer: kill_ip (iptables -I + conntrack -D)
→ enforcer: block_user (is_blocked=1)
→ enforcer: subsequent loops see is_blocked=1 → SKIP (no more stacking)
```

### Unblock via dashboard

```
Admin/user opens dashboard → sees user001 blocked (used=101MB, limit=100MB)
→ clicks Unblock → modal fetches fresh used_bytes = 101MB
→ user enters new limit = 200MB
→ POST /blocked-users/{id}/unblock
→ ModifyAction: quota_bytes = 200MB, is_blocked = 0
→ loops iptables -D until all REJECT rules removed
→ enforcer next loop: is_blocked=0, remaining=99MB → OK
→ user001 can connect again
```

---

# 🔹 Known Limitations

- `%<st` in bandwidth.log counts bytes **from origin**, not from Squid cache. Cache-hit traffic is not counted toward quota. Switch to `%>st` to count all bytes sent to client.
- The quota_check helper (`ttl=10`) caches the quota result for 10 seconds per user. A user who just hit their quota may get one more request through before being denied at the Squid ACL level.
- tshark resolves user from `proxy_requests` using the **last completed request** for a given IP. If an IP has never completed a request, real-time enforcement is skipped for that session.

---

# 🔹 Optional Improvements

- Switch `%<st` → `%>st` in bandwidth.log format (count cached traffic)
- nftables instead of iptables
- Daily quota reset cron
- Per-user speed shaping (`tc qdisc`)
- Prometheus metrics + Grafana dashboards
- ClickHouse for analytics at scale
- Burst allowance (grace bytes before hard block)

---

# ✅ Final Result

✔ Accurate post-request accounting via ingestor
✔ Real-time mid-download enforcement via tshark
✔ Persistent iptables block on quota-exceeded users
✔ No rule stacking — enforcer skips already-blocked users
✔ Full unblock via Laravel dashboard — clears all stacked rules
✔ Fresh usage data in unblock modal — prevents stale-data validation failures
✔ Database-driven, service-based, production-safe design

> A mini ISP-grade quota gateway built on Squid + Linux + Python + Laravel
