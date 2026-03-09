# EUProxy — Production Setup Guide

Full setup guide for the Squid-based authenticated proxy system with bandwidth quota enforcement.

---

## Architecture Overview

```
Client (192.168.88.x)
        │
        ▼
┌──────────────────────────────┐
│   Squid Proxy (:3128)        │
│                              │
│  1. ipdbauth.js     ──────── │──► squid_allowed_ips (MySQL)
│     IP allowlist check       │
│                              │
│  2. basic_db_auth   ──────── │──► squid_users.password (MD5)
│     Username/password auth   │
│                              │
│  3. mysql_check_quota.php ── │──► squid_users.is_blocked / quota_bytes
│     Per-request quota gate   │
└──────────────────────────────┘
        │
        ▼
┌──────────────────────────────┐
│  quota_enforcer_tshark.py    │
│  (systemd service)           │
│  tshark packet sniffing      │──► kill connection (iptables + conntrack)
│  real-time rolling bytes     │──► squid_users.is_blocked = 1
└──────────────────────────────┘

┌──────────────────────────────┐
│  EUProxy Laravel App         │
│  - Manage users / IPs        │
│  - Update bandwidth limits   │──► auto-unblock + clean iptables
│  - View subscriptions        │
└──────────────────────────────┘
```

---

## 1. System Requirements

- Ubuntu 22.04 / Debian 12
- MySQL 8.0 or MariaDB 10.6+
- PHP 8.1+
- Node.js 18+
- Python 3.10+
- Squid 5+

---

## 2. Package Installation

```bash
# Squid
apt install -y squid

# MySQL
apt install -y mysql-server

# PHP + extensions
apt install -y php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl

# Perl + DBI for basic_db_auth
apt install -y libdbi-perl libdbd-mysql-perl

# Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Python 3 + pip
apt install -y python3 python3-pip

# Python packages
pip3 install pymysql

# tshark (packet capture for quota enforcer)
apt install -y tshark

# conntrack (for killing connections)
apt install -y conntrack

# iptables (usually pre-installed)
apt install -y iptables

# Composer (for Laravel)
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

---

## 3. MySQL Database Setup

```sql
CREATE DATABASE squid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'squid'@'127.0.0.1' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON squid.* TO 'squid'@'127.0.0.1';
FLUSH PRIVILEGES;
```

The Laravel migrations create all required tables. Run them after app setup:

```bash
cd /home/gk/euproxy
php artisan migrate
```

Key tables:
- `squid_users` — proxy credentials, quota, block status
- `squid_allowed_ips` — IP allowlist per user
- `proxy_requests` — bandwidth usage log (written by SyncBandwidthUsage cron)

---

## 4. Squid Configuration

### `/etc/squid/squid.conf`

```squid
http_port 0.0.0.0:3128

# --- Safe ports ACLs ---
acl SSL_ports port 443
acl Safe_ports port 80
acl Safe_ports port 443
acl CONNECT method CONNECT

# --- AUTH (DB) ---
auth_param basic program /usr/lib/squid/basic_db_auth --dsn "DBI:mysql:host=127.0.0.1;port=3306;database=squid;" --user squid --password YOUR_DB_PASSWORD --table squid_users --md5 --persist
auth_param basic children 5
auth_param basic realm Web-Proxy
auth_param basic credentialsttl 10 minutes
auth_param basic casesensitive off
acl db-auth proxy_auth REQUIRED

# --- IP allowlist helper (node) ---
external_acl_type ipdbauth ttl=60 children-startup=2 children-max=10 %SRC /usr/bin/node /opt/squid-helpers/ipdbauth/ipdbauth.js
acl ipauth external ipdbauth

# --- Quota check helper (php) ---
external_acl_type quota_check ttl=10 children-startup=2 children-max=10 %LOGIN /usr/bin/php /usr/lib/squid/external_module/mysql_check_quota.php
acl quota_ok external quota_check

# --- Logging ---
logformat bandwidth %ts.%03tu %>a %un %rm %ru %>Hs %<st
access_log /var/log/squid/bandwidth.log bandwidth
access_log /var/log/squid/access.log squid

# --- ACCESS RULES ---
http_access deny !Safe_ports
http_access deny CONNECT !SSL_ports
http_access deny !ipauth
http_access deny !db-auth
http_access allow ipauth db-auth quota_ok
http_access deny all
```

### `/etc/squid/conf.d/debian.conf`

```
# Logs are managed by logrotate on Debian
logfile_rotate 0
```

---

## 5. Helper Scripts

### 5.1 IP Allowlist Helper — Node.js

**Path:** `/opt/squid-helpers/ipdbauth/ipdbauth.js`

```bash
mkdir -p /opt/squid-helpers/ipdbauth
cd /opt/squid-helpers/ipdbauth
npm init -y
npm install mysql2
```

**File:** `/opt/squid-helpers/ipdbauth/ipdbauth.js`

```javascript
#!/usr/bin/env node

const mysql = require('mysql2/promise');
const readline = require('readline');
const fs = require('fs');
const path = require('path');

const LOG_FILE = '/var/log/squid/ipdbauth.log';

function log(msg) {
  const line = `[${new Date().toISOString()}] ${msg}\n`;
  fs.appendFile(LOG_FILE, line, () => {});
}

log('=== Helper started ===');

const pool = mysql.createPool({
  host: '127.0.0.1',
  user: 'squid',
  password: 'YOUR_DB_PASSWORD',
  database: 'squid',
  connectionLimit: 5
});

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
  terminal: false
});

async function checkIp(ip) {
  try {
    const [rows] = await pool.execute(
      'SELECT ip FROM squid_allowed_ips WHERE ip = ? LIMIT 1',
      [ip]
    );
    log(`CHECK---------here ${ip} → ${rows.length ? 'OK' : 'ERR'}`);
    return rows.length > 0;
  } catch (e) {
    log(`DB ERROR for ${ip} → ${e.message}`);
    return false;
  }
}

rl.on('line', async (line) => {
  try {
    log(`REQUEST-all data: ${line}`);
    line = line.trim();
    if (!line) return;

    const parts = line.split(/\s+/);

    let channel = null;
    let ip = null;

    if (/^\d+$/.test(parts[0]) && parts.length >= 2) {
      channel = parts[0];
      ip = parts[1];
    } else {
      ip = parts[0];
    }

    log(`PARSED channel=${channel ?? '-'} ip=${ip}`);

    const ok = await checkIp(ip);

    if (channel !== null)
      process.stdout.write(`${channel} ${ok ? 'OK' : 'ERR'}\n`);
    else
      process.stdout.write(`${ok ? 'OK' : 'ERR'}\n`);

  } catch (e) {
    log(`FATAL ERROR → ${e.stack}`);
    process.stdout.write('ERR\n');
  }
});

process.on('uncaughtException', err => {
  log(`UNCAUGHT → ${err.stack}`);
});

process.on('unhandledRejection', err => {
  log(`UNHANDLED PROMISE → ${err}`);
});
```

---

### 5.2 Basic DB Auth — Perl

**Path:** `/usr/lib/squid/basic_db_auth`

This is a modified version of Squid's built-in `basic_db_auth` with added logging.

```perl
#!/usr/bin/perl

use strict;
use Pod::Usage;
use Getopt::Long;
use DBI;
use Digest::MD5 qw(md5 md5_hex md5_base64);
use Digest::SHA qw(sha1 sha1_hex sha1_base64);

# ---- LOG FUNCTION ----
sub log_msg {
    my ($msg) = @_;
    my $logfile = "/var/log/squid/basic_db_auth.log";
    my $time = scalar localtime();
    if (open(my $fh, ">>", $logfile)) {
        print $fh "[$time] $msg\n";
        close($fh);
    }
}
# ----------------------

my $dsn = "DBI:mysql:database=squid";
my $db_user = undef;
my $db_passwd = undef;
my $db_table = "passwd";
my $db_usercol = "user";
my $db_passwdcol = "password";
my $db_cond = "enabled = 1";
my $plaintext = 0;
my $md5 = 0;
my $sha1 = 0;
my $persist = 0;
my $isjoomla = 0;
my $debug = 0;
my $hashsalt = undef;

GetOptions(
    'dsn=s'      => \$dsn,
    'user=s'     => \$db_user,
    'password=s' => \$db_passwd,
    'table=s'    => \$db_table,
    'usercol=s'  => \$db_usercol,
    'passwdcol=s'=> \$db_passwdcol,
    'cond=s'     => \$db_cond,
    'plaintext'  => \$plaintext,
    'md5'        => \$md5,
    'sha1'       => \$sha1,
    'persist'    => \$persist,
    'joomla'     => \$isjoomla,
    'debug'      => \$debug,
    'salt=s'     => \$hashsalt,
);

my ($_dbh, $_sth);
$db_cond = "block = 0" if $isjoomla;

sub close_db() {
    return if !defined($_dbh);
    undef $_sth;
    $_dbh->disconnect();
    undef $_dbh;
}

sub open_db() {
    return $_sth if defined $_sth;
    log_msg("Opening DB connection to $dsn");
    $_dbh = DBI->connect($dsn, $db_user, $db_passwd);
    if (!defined $_dbh) {
        warn ("Could not connect to $dsn\n");
        log_msg("DB connection FAILED");
        my @driver_names = DBI->available_drivers();
        my $msg = "DSN drivers apparently installed, available:";
        foreach my $dn (@driver_names) { $msg .= " $dn"; }
        log_msg($msg);
        return undef;
    }
    my $sql_query = "SELECT $db_passwdcol FROM $db_table WHERE $db_usercol = ?" . ($db_cond ne "" ? " AND $db_cond" : "");
    log_msg("Preparing SQL: $sql_query");
    $_sth = $_dbh->prepare($sql_query) || die;
    return $_sth;
}

sub check_password($$) {
    my ($password, $key) = @_;
    if ($isjoomla) {
        my $salt;
        my $key2;
        ($key2,$salt) = split (/:/, $key);
        return 1 if md5_hex($password.$salt).':'.$salt eq $key;
    } else {
        return 1 if defined $hashsalt && crypt($password, $hashsalt) eq $key;
        return 1 if crypt($password, $key) eq $key;
        return 1 if $md5 && md5_hex($password) eq $key;
        return 1 if $sha1 && sha1_hex($password) eq $key;
        return 1 if $plaintext && $password eq $key;
    }
    return 0;
}

sub query_db($) {
    my ($user) = @_;
    log_msg("Query DB for user: $user");
    my ($sth) = open_db() || return undef;
    if (!$sth->execute($user)) {
        log_msg("Query failed, retrying DB connection");
        close_db();
        open_db() || return undef;
        $sth->execute($user) || return undef;
    }
    return $sth;
}

my $status;
$|=1;

while (<>) {
    my ($user, $password) = split;
    $status = "ERR";
    $user =~ s/%(..)/pack("H*", $1)/ge;
    $password =~ s/%(..)/pack("H*", $1)/ge;
    log_msg("Login attempt for user: $user");
    $status = "ERR database error";
    my $sth = query_db($user) || next;
    $status = "ERR unknown login";
    my $row = $sth->fetchrow_arrayref() || next;
    $status = "ERR login failure";
    next if (!check_password($password, @$row[0]));
    log_msg("Login SUCCESS for user: $user");
    $status = "OK";
} continue {
    close_db() if (!$persist);
    if ($status ne "OK") {
        log_msg("Login FAILED for user");
    }
    print $status . "\n";
}
```

```bash
chmod +x /usr/lib/squid/basic_db_auth
```

> **Note:** Passwords must be stored as MD5 in the DB. The Laravel app handles this automatically via the `SquidUser` model mutator.

---

### 5.3 Quota Check Helper — PHP

**Path:** `/usr/lib/squid/external_module/mysql_check_quota.php`

```bash
mkdir -p /usr/lib/squid/external_module
```

```php
#!/usr/bin/php
<?php
ini_set('display_errors', '0');
error_reporting(0);

$LOG_FILE = '/var/log/squid/quota_helper.log';

function log_msg($msg) {
    global $LOG_FILE;
    file_put_contents($LOG_FILE, date('c') . ' ' . $msg . PHP_EOL, FILE_APPEND);
}

$dsn  = "mysql:host=127.0.0.1;port=3306;dbname=squid;charset=utf8mb4";
$dbu  = "squid";
$dbp  = "YOUR_DB_PASSWORD";

$pdo = null;
function get_pdo() {
    global $pdo, $dsn, $dbu, $dbp;
    if ($pdo) return $pdo;
    $pdo = new PDO($dsn, $dbu, $dbp, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2,
    ]);
    return $pdo;
}

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') { echo "OK\n"; flush(); continue; }

    $parts = preg_split('/\s+/', $line);
    $username = $parts[0] ?? '';

    log_msg("CHECK user={$username}");

    if ($username === '' || $username === '-') {
        log_msg("EMPTY username → OK");
        echo "OK\n"; flush();
        continue;
    }

    try {
        $pdo = get_pdo();

        $stmt = $pdo->prepare(
            "SELECT enabled, is_blocked, used_bytes, quota_bytes
             FROM squid_users
             WHERE user = ?
             LIMIT 1"
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) { log_msg("DENY user={$username} reason=not_found"); echo "ERR\n"; flush(); continue; }
        if (!(int)$user['enabled']) { log_msg("DENY user={$username} reason=disabled"); echo "ERR\n"; flush(); continue; }
        if ((int)$user['is_blocked']) { log_msg("DENY user={$username} reason=blocked"); echo "ERR\n"; flush(); continue; }

        if ((int)$user['quota_bytes'] > 0 && (int)$user['used_bytes'] >= (int)$user['quota_bytes']) {
            $pdo->prepare("UPDATE squid_users SET is_blocked = 1 WHERE user = ?")->execute([$username]);
            log_msg("DENY user={$username} reason=quota_exceeded");
            echo "ERR\n"; flush();
            continue;
        }

        log_msg("ALLOW user={$username}");
        echo "OK\n"; flush();

    } catch (Throwable $e) {
        log_msg("ERROR user={$username} msg=" . $e->getMessage());
        echo "OK\n"; flush(); // fail-open
        $pdo = null;
    }
}
```

```bash
chmod +x /usr/lib/squid/external_module/mysql_check_quota.php
```

---

### 5.4 Real-time Quota Enforcer — Python (tshark)

**Path:** `/usr/local/bin/quota_enforcer_tshark.py`

> Change `PROXY_IP` to your server's actual IP address.

```python
#!/usr/bin/env python3
import time
import logging
import subprocess
import pymysql
from collections import defaultdict

# ==============================
# CONFIG — adjust for your server
# ==============================
PROXY_IP = "YOUR_PROXY_IP"       # e.g. 192.168.88.13
SQUID_PORT = 3128

INTERVAL_SEC = 1
IDLE_RESET_SEC = 10
LOG_FILE = "/var/log/squid/quota_enforcer.log"
STATS_EVERY_LOOPS = 10

DB = {
    "host": "127.0.0.1",
    "user": "squid",
    "password": "YOUR_DB_PASSWORD",
    "database": "squid",
    "autocommit": True,
}

TSHARK_CMD = [
    "tshark", "-i", "any", "-l", "-n",
    "-f", f"tcp src port {SQUID_PORT} and src host {PROXY_IP}",
    "-T", "fields", "-E", "separator=\t",
    "-e", "frame.time_epoch", "-e", "ip.dst", "-e", "tcp.len"
]

# ==============================
# LOGGING
# ==============================
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.FileHandler(LOG_FILE), logging.StreamHandler()]
)
log = logging.getLogger("quota_enforcer_tshark")


def human(b):
    try:
        b = int(b)
    except Exception:
        return str(b)
    for unit in ["B", "KB", "MB", "GB", "TB"]:
        if b < 1024:
            return f"{b:.1f}{unit}"
        b /= 1024
    return f"{b:.1f}PB"


def kill_ip(ip, reason):
    log.warning(f"KILL ip={ip} reason={reason}")
    # Send TCP RST to kill existing connection + block new ones
    subprocess.call(["iptables", "-I", "INPUT", "-s", ip, "-p", "tcp",
                     "--dport", str(SQUID_PORT), "-j", "REJECT", "--reject-with", "tcp-reset"])
    subprocess.call(["conntrack", "-D", "-s", ip])


def block_user(cur, user, ip):
    cur.execute("UPDATE squid_users SET is_blocked=1 WHERE user=%s", (user,))
    log.warning(f"BLOCKED user={user} ip={ip} in DB")


def resolve_user_from_ip(cur, ip):
    cur.execute("""
        SELECT username, ts, url, status, bytes
        FROM proxy_requests
        WHERE client_ip = %s
          AND username IS NOT NULL
          AND username <> '-'
        ORDER BY id DESC
        LIMIT 1
    """, (ip,))
    return cur.fetchone()


def get_user_quota(cur, user):
    cur.execute("""
        SELECT quota_bytes, used_bytes, last_seen_at
        FROM squid_users
        WHERE user = %s
        LIMIT 1
    """, (user,))
    return cur.fetchone()


def main():
    log.info("==========================================")
    log.info("Quota Enforcer (tshark rolling) Started")
    log.info(f"ProxyIP={PROXY_IP} squid_port={SQUID_PORT} interval={INTERVAL_SEC}s idle_reset={IDLE_RESET_SEC}s")
    log.info("Rule: if (rolling_live_bytes > remaining_quota) => KILL immediately")
    log.info("==========================================")

    conn = pymysql.connect(**DB)
    cur = conn.cursor()

    log.info("Starting tshark: " + " ".join(TSHARK_CMD))
    proc = subprocess.Popen(TSHARK_CMD, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, bufsize=1)

    live_total = defaultdict(int)
    last_seen = {}

    loop = 0
    last_tick = time.time()

    while True:
        line = proc.stdout.readline()
        now = time.time()

        if line:
            parts = line.strip().split("\t")
            if len(parts) >= 3:
                epoch, dst_ip, tcp_len = parts[0], parts[1], parts[2]
                if dst_ip:
                    dst_ip = dst_ip.replace("::ffff:", "")
                    try:
                        n = int(tcp_len)
                    except Exception:
                        n = 0
                    if n > 0:
                        live_total[dst_ip] += n
                        last_seen[dst_ip] = now
            else:
                log.debug(f"TSHARK_BAD_LINE: {line.strip()}")

        # Reset rolling counters for idle IPs
        for ip in list(last_seen.keys()):
            if now - last_seen[ip] > IDLE_RESET_SEC:
                if live_total.get(ip, 0) > 0:
                    log.info(f"IDLE_RESET ip={ip} rolling_total_was={human(live_total[ip])}")
                live_total.pop(ip, None)
                last_seen.pop(ip, None)

        if now - last_tick >= INTERVAL_SEC:
            loop += 1
            last_tick = now

            active_ips = len(live_total)
            total_bytes = sum(live_total.values())
            log.info(f"--- Loop #{loop} active_ips={active_ips} rolling_total_all_ips={human(total_bytes)} ---")

            checked = mapped = blocked = 0

            for ip, rolling_bytes in list(live_total.items()):
                checked += 1

                r = resolve_user_from_ip(cur, ip)
                if not r:
                    log.info(f"SKIP ip={ip} rolling={human(rolling_bytes)} reason=no_user_mapping")
                    continue

                user, last_ts, last_url, last_status, last_b = r
                mapped += 1

                q = get_user_quota(cur, user)
                if not q:
                    log.warning(f"SKIP user={user} ip={ip} reason=no_row_in_squid_users")
                    continue

                quota, used, last_seen_at = q
                remaining = int(quota) - int(used)

                log.info(
                    f"CHECK ip={ip} user={user} rolling={human(rolling_bytes)} remaining={human(remaining)} "
                    f"(quota={human(quota)} used={human(used)} last_seen={last_seen_at})"
                )

                if remaining <= 0:
                    kill_ip(ip, "already_exceeded")
                    block_user(cur, user, ip)
                    blocked += 1
                    continue

                if int(rolling_bytes) > remaining:
                    log.warning(f"OVERFLOW ip={ip} user={user} rolling={human(rolling_bytes)} > remaining={human(remaining)}")
                    kill_ip(ip, "rolling_overflow")
                    block_user(cur, user, ip)
                    blocked += 1
                    live_total.pop(ip, None)
                    last_seen.pop(ip, None)
                else:
                    log.info(f"OK ip={ip} user={user}")

            if loop % STATS_EVERY_LOOPS == 0:
                log.info(f"SUMMARY loop={loop} checked={checked} mapped={mapped} blocked={blocked}")

        if proc.poll() is not None:
            err = proc.stderr.read() if proc.stderr else ""
            log.error(f"tshark exited! restarting... stderr={err}")
            proc = subprocess.Popen(TSHARK_CMD, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, bufsize=1)


if __name__ == "__main__":
    main()
```

```bash
chmod +x /usr/local/bin/quota_enforcer_tshark.py
```

---

## 6. Systemd Services

### quota-enforcer.service

**Path:** `/etc/systemd/system/quota-enforcer.service`

```ini
[Unit]
Description=Squid Quota Enforcer
After=network.target mysql.service mariadb.service squid.service

[Service]
Type=simple
ExecStart=/usr/bin/python3 /usr/local/bin/quota_enforcer_tshark.py
Restart=always
RestartSec=3
User=root

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable quota-enforcer
systemctl start quota-enforcer
```

---

## 7. Log Permissions

```bash
touch /var/log/squid/ipdbauth.log \
      /var/log/squid/basic_db_auth.log \
      /var/log/squid/quota_helper.log \
      /var/log/squid/quota_enforcer.log

chown proxy:proxy /var/log/squid/ipdbauth.log \
                  /var/log/squid/basic_db_auth.log \
                  /var/log/squid/quota_helper.log
chmod 664 /var/log/squid/*.log
```

---

## 8. Laravel App Setup

```bash
cd /home/gk/euproxy
composer install --no-dev
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_HOST=127.0.0.1
DB_DATABASE=squid
DB_USERNAME=squid
DB_PASSWORD=YOUR_DB_PASSWORD
```

```bash
php artisan migrate
php artisan db:seed
```

---

## 9. Log File Reference

| Log File | Written By | Purpose |
|----------|-----------|---------|
| `/var/log/squid/access.log` | Squid | Full access log |
| `/var/log/squid/bandwidth.log` | Squid | Bandwidth per request (timestamp, IP, user, method, URL, status, bytes) |
| `/var/log/squid/ipdbauth.log` | `ipdbauth.js` | IP allowlist check results |
| `/var/log/squid/basic_db_auth.log` | `basic_db_auth` | Auth attempts (success/fail) |
| `/var/log/squid/quota_helper.log` | `mysql_check_quota.php` | Per-request quota gate decisions |
| `/var/log/squid/quota_enforcer.log` | `quota_enforcer_tshark.py` | Real-time packet-level enforcement |

---

## 10. How Quota Enforcement Works

Two layers enforce quotas:

**Layer 1 — PHP quota helper (per new request)**
- Squid calls it for every new request
- Checks `is_blocked`, `enabled`, `used_bytes >= quota_bytes`
- TTL: 10 seconds (Squid caches the result)
- Sets `is_blocked=1` if quota exceeded

**Layer 2 — Python tshark enforcer (mid-download)**
- Sniffs live TCP traffic from the proxy
- Tracks rolling bytes per client IP every 1 second
- If rolling bytes > remaining quota → kills connection via `iptables + conntrack`
- Sets `is_blocked=1` in DB so Layer 1 also blocks future requests

---

## 11. Maintenance

### Manually unblock a user

```bash
mysql -u squid -p squid -e "UPDATE squid_users SET is_blocked=0, used_bytes=0 WHERE user='username';"

# Also remove iptables rule if enforcer blocked them
iptables -D INPUT -s CLIENT_IP -p tcp --dport 3128 -j REJECT --reject-with tcp-reset
```

### Check current iptables blocks

```bash
iptables -L INPUT -n --line-numbers | grep 3128
```

### Reload Squid config (no downtime)

```bash
squid -k reconfigure
```

### Service management

```bash
systemctl status squid quota-enforcer
systemctl restart quota-enforcer
journalctl -u quota-enforcer -f
```

### Reset a user's bandwidth for new billing cycle

```bash
mysql -u squid -p squid -e "
  UPDATE squid_users
  SET used_bytes=0, is_blocked=0, reset_at=NOW()
  WHERE user='username';
"
```

---

## 12. Debugging Guide

### 12.1 Squid won't start — `FATAL: Cannot open bandwidth.log`

**Symptom:**
```
FATAL: Cannot open '/var/log/squid/bandwidth.log' for writing.
The parent directory must be writeable by the user 'proxy'
```

**Cause:** Log files were created/written as `root` (e.g. after running artisan or manual commands as root). Squid runs as user `proxy` and cannot write to them.

**Fix:**
```bash
touch /var/log/squid/bandwidth.log /var/log/squid/access.log
chown proxy:proxy /var/log/squid/bandwidth.log /var/log/squid/access.log
chmod 664 /var/log/squid/bandwidth.log /var/log/squid/access.log
systemctl start squid
```

**Prevention:** Always check file ownership after any manual operations in `/var/log/squid/`.

---

### 12.2 PHP quota helper crashing — `external_acl_type exited`

**Symptom (journalctl -u squid):**
```
WARNING: external_acl_type #HlprXXXXX exited
Too few external_acl_type processes are running (need 1/10)
helperOpenServers: Starting 1/10 'php' processes
```
Repeating every ~60 seconds.

**Cause:** The PHP helper (`mysql_check_quota.php`) crashes on startup — usually a DB connection error or permission issue on the log file.

**Debug steps:**

1. **Run the helper manually** to see the real error:
```bash
echo "testuser" | php /usr/lib/squid/external_module/mysql_check_quota.php
```

2. **Check the helper's own log:**
```bash
tail -f /var/log/squid/quota_helper.log
```

3. **Check DB connectivity** from PHP:
```bash
php -r "new PDO('mysql:host=127.0.0.1;port=3306;dbname=squid;charset=utf8mb4','squid','YOUR_PASSWORD'); echo 'OK';"
```

4. **Check PHP version compatibility:** Script was developed on PHP 8.3 — server runs PHP 8.1. Test on the server explicitly:
```bash
php --version   # must be 8.1+
php /usr/lib/squid/external_module/mysql_check_quota.php <<< "alice_proxy1"
```

5. **Check quota_helper.log ownership:**
```bash
ls -la /var/log/squid/quota_helper.log
# Must be writable by proxy user
chown proxy:proxy /var/log/squid/quota_helper.log
```

---

### 12.3 Laravel `/user/bandwidth-data` returns 500

**Symptom:** Browser console shows:
```
GET https://yourdomain/user/bandwidth-data?range=7days&usernames[]=xxx 500 (Internal Server Error)
```

**Cause:** `laravel.log` is owned by `root` (created when running `php artisan` as root). The `www-data` user (php-fpm) cannot write to it. The `\Log::info()` call inside the controller throws an exception that escapes the try-catch.

**Debug steps:**

1. **Enable debug mode temporarily:**
```bash
# In .env
APP_DEBUG=true
php artisan config:clear
```
Then hit the endpoint — the JSON response will show the real error message.
Disable after diagnosis: `APP_DEBUG=false && php artisan config:clear`

2. **Check log file ownership:**
```bash
ls -la /var/www/euproxy/storage/logs/laravel.log
```

3. **Fix ownership:**
```bash
chown www-data:www-data /var/www/euproxy/storage/logs/laravel.log
chmod 664 /var/www/euproxy/storage/logs/laravel.log
```

4. **Fix all storage permissions at once:**
```bash
chown -R www-data:www-data /var/www/euproxy/storage
chmod -R 775 /var/www/euproxy/storage
```

**Prevention:** Always run artisan commands as `www-data`, not root:
```bash
sudo -u www-data php artisan migrate:fresh --seed
sudo -u www-data php artisan cache:clear
```

---

### 12.4 Seeder fails with duplicate entry error

**Symptom:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062
Duplicate entry 'rotating-residential' for key 'proxy_types.proxy_types_slug_unique'
```

**Cause:** `ProxyTypesAndPlansSeeder` uses `create()` instead of `firstOrCreate()` — not idempotent.

**Fix:** Use `firstOrCreate()` keyed on `slug` for proxy types, `name` for plans, and `feature_key` for features (already applied in the codebase).

**Fresh migrate (correct way):**
```bash
sudo -u www-data php artisan migrate:fresh --seed
```

---

### 12.5 General permission fix after running commands as root

Any time you run `php artisan`, `composer`, or write files as `root`, fix permissions:

```bash
chown -R www-data:www-data /var/www/euproxy/storage /var/www/euproxy/bootstrap/cache
chown proxy:proxy /var/log/squid/bandwidth.log /var/log/squid/access.log
```

---

### 12.6 No proxy logs written (access.log / bandwidth.log empty)

**Check squid is running:**
```bash
systemctl status squid
```

**If stopped/failed — check why:**
```bash
journalctl -u squid --since "1 hour ago" --no-pager | grep -E "FATAL|ERROR|exited|WARNING"
```

**Common causes:**
- Log file owned by root → fix with `chown proxy:proxy` (see 12.1)
- PHP quota helper crashing → fix (see 12.2)
- Node.js ipdbauth helper crashing → check `/var/log/squid/ipdbauth.log`

**Tail all squid logs at once:**
```bash
tail -f /var/log/squid/access.log \
         /var/log/squid/bandwidth.log \
         /var/log/squid/quota_helper.log \
         /var/log/squid/ipdbauth.log \
         /var/log/squid/basic_db_auth.log
```

---

### 12.7 Node.js ipdbauth helper crashes — `SyntaxError: Unexpected token '?'`

**Symptom (journalctl -u squid):**
```
WARNING: external_acl_type #Hlpr2 exited
Too few external_acl_type processes are running (need 1/10)
FATAL: The external_acl_type helpers are crashing too rapidly, need help!
```

**Debug — test the node helper manually:**
```bash
echo "1 127.0.0.1" | timeout 3 /usr/bin/node /opt/squid-helpers/ipdbauth/ipdbauth.js
```

**Error output:**
```
/opt/squid-helpers/ipdbauth/ipdbauth.js:63
    log(`PARSED channel=${channel ?? '-'} ip=${ip}`);
                                   ^
SyntaxError: Unexpected token '?'
```

**Cause:** `ipdbauth.js` uses the nullish coalescing operator (`??`) which requires **Node.js v14+**. The server was running Node.js **v12**.

**Check Node version:**
```bash
node --version   # v12.x = too old, need v14+
```

**Fix — upgrade Node.js to 18:**
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
node --version   # should show v18.x
systemctl restart squid
systemctl status squid
```

**Key rule:** `ipdbauth.js` requires **Node.js 14+** due to use of:
- `??` nullish coalescing operator (v14+)
- Template literals with expressions

Always verify Node version matches development environment before deploying.

---

### 12.8 Identifying which helper is crashing

Squid numbers helpers sequentially across all `external_acl_type` entries. With the current config:
- `ipdbauth` (node) → `#Hlpr1`, `#Hlpr2`
- `quota_check` (php) → `#Hlpr3`, `#Hlpr4`

**Test each helper manually to isolate the crash:**

```bash
# Test Node.js ipdbauth helper
echo "1 127.0.0.1" | timeout 3 /usr/bin/node /opt/squid-helpers/ipdbauth/ipdbauth.js

# Test PHP quota helper
echo "alice_proxy1" | timeout 3 /usr/bin/php /usr/lib/squid/external_module/mysql_check_quota.php

# Test Perl basic_db_auth helper
echo "alice_proxy1 password123" | timeout 3 /usr/lib/squid/basic_db_auth \
  --dsn "DBI:mysql:host=127.0.0.1;port=3306;database=squid;" \
  --user squid --password YOUR_DB_PASSWORD --table squid_users --md5 --persist
```

Expected output for each: `OK` (or `ERR` for bad credentials — but no crash/exception).

---

### 12.9 Log file empty — process writing to deleted inode

**Symptom:** Service is running and healthy in `journalctl`, but the log file on disk is 0 bytes.

**Diagnosis:**
```bash
# Find the PID
systemctl status quota-enforcer | grep "Main PID"

# Check open file descriptors for that PID
ls -la /proc/<PID>/fd/ | grep log
```

If you see `(deleted)` next to the log path:
```
l-wx------ 1 root root 64 ... 3 -> /var/log/squid/quota_enforcer.log (deleted)
```

The process opened the file at startup, then the file was deleted and recreated on disk (e.g. via `touch`, `rm`, or logrotate without `copytruncate`). The process keeps writing to the old inode — the new empty file on disk gets nothing.

**Quick fix:** Restart the service to reopen the new file:
```bash
systemctl restart quota-enforcer
```

**Permanent fix — logrotate with `copytruncate`:**

Instead of rotating by renaming (which changes the inode), `copytruncate` copies the file then truncates the original in-place. The process never loses its file handle.

Config at `/etc/logrotate.d/quota-enforcer`:
```
/var/log/squid/quota_enforcer.log
/var/log/squid/quota_helper.log
/var/log/squid/ipdbauth.log
/var/log/squid/basic_db_auth.log
/var/log/squid/bandwidth-ingestor.log {
    daily
    compress
    delaycompress
    rotate 7
    missingok
    copytruncate
    sharedscripts
}
```

> **Why `copytruncate` for these files?** Python (`quota_enforcer`), PHP (`quota_helper`), Node.js (`ipdbauth`), and Perl (`basic_db_auth`) processes do not support signal-based log reopening. Squid's own `access.log`/`bandwidth.log` are handled by the existing `/etc/logrotate.d/squid` config which uses `squid -k rotate` instead.

**Rule:** Never use `touch`, `rm`, or `> file` on a log file while the writing process is running. Always use `copytruncate` in logrotate, or restart the service after recreating a log file.

---

## 13. Password Notes

Passwords are stored as **MD5 hashes** in `squid_users.password`. The Laravel app hashes automatically via the model mutator. Squid's `basic_db_auth` is configured with `--md5` to match.

An `encrypted_password` column (AES via Laravel `Crypt`) stores a reversible copy for display in the admin UI.

**Never set passwords directly in MySQL with plain text** — always use the Laravel UI or hash manually:

```bash
# Get MD5 of a password for direct DB insert
echo -n "yourpassword" | md5sum
```
