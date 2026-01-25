# Squid Proxy Quota Enforcement Architecture
## Real‑time Bandwidth Accounting + Mid‑Download Blocking

Author: Md Golam Kibria  
Environment: Squid + MySQL + Node.js auth + Python services + tshark  
Last updated: 2026

---

# 🎯 Goal

Provide:

✅ Accurate bandwidth accounting (per user)  
✅ Persistent usage tracking in database  
✅ Real‑time quota enforcement  
✅ Immediate download termination when quota exceeded  
✅ Production‑safe & restart‑safe design  

---

# 🧠 High Level Design

The system is split into **two responsibilities**:

| Layer | Responsibility |
|--------|----------------|
| Accounting | Exact usage after request completes |
| Enforcement | Live monitoring + instant block |

This avoids:

❌ double counting  
❌ inaccurate estimates  
❌ heavy packet processing  
❌ relying on Squid internals only  

---

# 🏗 Architecture Diagram

```
Client
   ↓
Squid Proxy (3128)
   ↓
------------------------------------------
| 1️⃣ bandwidth.log (user + bytes)       |
| 2️⃣ tshark live packets (tcp.len)     |
------------------------------------------
   ↓                ↓
Ingestor.py     QuotaEnforcer.py
   ↓                ↓
MySQL <-------------
   ↓
squid_users.used_bytes (source of truth)
```

---

# 🔹 Components

---

## 1️⃣ Squid Configuration

### Log format

Custom logformat:

```
logformat bandwidth %ts %>a %un %rm %ru %>Hs %<st
access_log /var/log/squid/bandwidth.log bandwidth
```

Produces:

```
timestamp ip username method url status bytes
```

Example:

```
1769319754.376 192.168.88.1 user001 GET http://... 200 3182460
```

### Purpose

✔ Final exact bytes per completed request  
✔ Reliable billing  
✔ No packet inspection needed  

---

## 2️⃣ Node.js DB Auth (ipdbauth)

### Role

Handles:

• Proxy authentication  
• Username ↔ IP mapping  
• Database driven access  

### Flow

```
Client → Squid → Node helper → MySQL
```

Ensures every request has:

```
username + client_ip
```

---

## 3️⃣ MySQL Schema

### squid_users

```sql
CREATE TABLE squid_users (
  user VARCHAR(100) PRIMARY KEY,
  quota_bytes BIGINT NOT NULL,
  used_bytes BIGINT NOT NULL DEFAULT 0,
  last_seen_at DATETIME
);
```

### proxy_requests

```sql
CREATE TABLE proxy_requests (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  ts DOUBLE,
  client_ip VARCHAR(45),
  username VARCHAR(100),
  method VARCHAR(10),
  url TEXT,
  status INT,
  bytes BIGINT
);

CREATE INDEX idx_proxy_requests_ip_id ON proxy_requests(client_ip, id);
```

---

# 🔹 Accounting Service

## ingestor.py

### Purpose

Reads:

```
/var/log/squid/bandwidth.log
```

### Flow

```
tail -F bandwidth.log
   ↓
insert proxy_requests
   ↓
update squid_users.used_bytes += bytes
```

### Why

✔ exact accounting  
✔ restart safe  
✔ auditable history  

---

# 🔹 Enforcement Service

## quota_enforcer.py (tshark based)

### Why tshark?

Kernel conntrack bytes unavailable on your kernel.  
Therefore use:

```
tcp.len
```

from tshark.

### Flow

```
tshark → aggregate per IP → resolve user → compare quota → conntrack -D
```

### Logic

For each second:

```
rolling_bytes[ip] += tcp.len
remaining = quota - used

if rolling_bytes > remaining:
    conntrack -D -s ip
```

### Result

✔ mid‑download kill  
✔ real‑time  
✔ no waiting for Squid log  

---

# 🔹 Enforcement Mechanism

## Kill active sessions

```
conntrack -D -s <client_ip>
```

Effect:

• immediate TCP reset  
• browser download stops instantly  

---

# 🔹 Services

## ingestor.service

```
ExecStart=/usr/bin/python3 /usr/local/bin/ingestor.py
Restart=always
```

## quota-enforcer.service

```
ExecStart=/usr/bin/python3 /usr/local/bin/quota_enforcer_tshark.py
Restart=always
User=root
```

Enable:

```
systemctl enable ingestor
systemctl enable quota-enforcer
```

---

# 🔹 Runtime Flow Example

## Normal download

```
user001 → 3MB/sec
remaining=24GB
=> allowed
```

## Quota exceeded

```
rolling=60MB
remaining=50MB
=> conntrack kill
=> download stops immediately
```

---

# 🔹 Why This Architecture Is Correct

### Separation of concerns

| Task | Tool |
|---------|-------------|
Accounting | Squid log |
Live usage | tshark |
Blocking | conntrack |
Storage | MySQL |

### Benefits

✔ accurate  
✔ lightweight  
✔ restart safe  
✔ scalable  
✔ production proven pattern (ISP style)  

---

# 🔹 Performance Notes

• tshark runs only for Squid port (low overhead)  
• MySQL queries indexed  
• Enforcement interval = 1s  
• Handles thousands of users easily  

---

# 🔹 Optional Improvements

You may later add:

• nftables blocking  
• temporary ban list  
• daily quota reset cron  
• Prometheus metrics  
• Grafana dashboards  
• ClickHouse analytics  
• burst/speed shaping  
• per‑user rate limiting  

---

# ✅ Final Result

You now have:

✔ Accurate post‑request accounting  
✔ Real‑time enforcement  
✔ Mid‑download cutoff  
✔ Database driven control  
✔ Fully automated services  

This is effectively:

> A mini ISP‑grade quota gateway built on Squid + Linux + Python

---

# End