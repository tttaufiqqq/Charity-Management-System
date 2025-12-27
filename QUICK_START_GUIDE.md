# Quick Start Guide - Cross-Machine Distributed Setup

This is a step-by-step guide to get your distributed Charity-Izz system running across multiple team members' machines.

## Prerequisites Checklist

- [ ] All team members on the same network (WiFi/LAN)
- [ ] Docker installed on each machine
- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js & npm installed
- [ ] Git repository cloned

## Step-by-Step Setup Process

### Phase 1: Team Coordination (Do this FIRST as a group)

#### 1.1 Find and Share IP Addresses

**Each team member:**

**Windows:**
```bash
ipconfig
```
Look for "IPv4 Address" (e.g., 192.168.1.101)

**Mac/Linux:**
```bash
ifconfig
# or
ip addr show
```

**Create a shared document with everyone's IPs:**
```
izzhilmy:  192.168.1.101
sashvini:  192.168.1.102
izati:     192.168.1.103
hannah:    192.168.1.104
adam:      192.168.1.105
```

#### 1.2 Configure Firewalls (IMPORTANT!)

**Windows (Run as Administrator):**
```powershell
# For izzhilmy only:
New-NetFirewallRule -DisplayName "PostgreSQL User DB" -Direction Inbound -LocalPort 5432 -Protocol TCP -Action Allow

# For sashvini only:
New-NetFirewallRule -DisplayName "MariaDB Volunteer DB" -Direction Inbound -LocalPort 3307 -Protocol TCP -Action Allow

# For izati only:
New-NetFirewallRule -DisplayName "PostgreSQL Event DB" -Direction Inbound -LocalPort 5433 -Protocol TCP -Action Allow

# For hannah only:
New-NetFirewallRule -DisplayName "MySQL Donation DB" -Direction Inbound -LocalPort 3306 -Protocol TCP -Action Allow

# For adam only:
New-NetFirewallRule -DisplayName "MySQL Recipient DB" -Direction Inbound -LocalPort 3308 -Protocol TCP -Action Allow
```

**Mac:**
System Preferences → Security & Privacy → Firewall → Allow Docker

**Linux:**
```bash
# For izzhilmy
sudo ufw allow 5432/tcp

# For sashvini
sudo ufw allow 3307/tcp

# For izati
sudo ufw allow 5433/tcp

# For hannah
sudo ufw allow 3306/tcp

# For adam
sudo ufw allow 3308/tcp
```

### Phase 2: Individual Setup (Each person does this on their own machine)

#### 2.1 Run Your Setup Script

**Windows:**

**izzhilmy:**
```bash
setup-izzhilmy.bat
```

**sashvini:**
```bash
setup-sashvini.bat
```

**izati:**
```bash
setup-izati.bat
```

**hannah:**
```bash
setup-hannah.bat
```

**adam:**
```bash
setup-adam.bat
```

**Mac/Linux:**

Make script executable first:
```bash
chmod +x setup-izzhilmy.sh  # (or your name)
./setup-izzhilmy.sh
```

#### 2.2 Update .env with Team IPs

**After running your setup script, edit your `.env` file:**

Replace the example IPs (192.168.1.101, etc.) with the **actual IPs** from your shared document.

**Example for izzhilmy:**
```env
# Keep your own DB as localhost
DB_IZZ_HOST=127.0.0.1

# Update others with actual IPs
DB_SASHVINI_HOST=192.168.1.102  # ← Change this
DB_IZATI_HOST=192.168.1.103      # ← Change this
DB_HANNAH_HOST=192.168.1.104     # ← Change this
DB_ADAM_HOST=192.168.1.105       # ← Change this
```

**Save the file!**

### Phase 3: Testing (Everyone does this)

#### 3.1 Test Database Connections

Run the test script:
```bash
php test-connections.php
```

**Expected output:**
```
✅ izz: Connected
✅ sashvini: Connected
✅ izati: Connected
✅ hannah: Connected
✅ adam: Connected

🎉 All database connections successful!
```

**If some connections fail:**
1. Double-check IP addresses in `.env`
2. Verify the other person's Docker container is running
3. Check firewall settings
4. Try pinging the remote machine: `ping 192.168.1.104`

#### 3.2 Start Laravel Application

```bash
php artisan serve
```

Open browser: `http://localhost:8000`

### Phase 4: Testing Cross-Machine Data (Demo)

#### Test Case: izzhilmy creates data in hannah's database

**On izzhilmy's machine:**

```bash
php artisan tinker
```

```php
// Create a donation in hannah's database
$donation = new App\Models\Donation();
$donation->setConnection('hannah');  // Connect to hannah's DB
$donation->Donor_ID = 1;
$donation->Campaign_ID = 1;
$donation->Amount = 250.00;
$donation->Payment_Method = 'Online Banking';
$donation->Receipt_Number = 'TEST-' . time();
$donation->Donation_Date = now();
$donation->save();

echo "Created donation ID: {$donation->Donation_ID} in hannah's database!\n";
```

**On hannah's machine:**

```bash
php artisan tinker
```

```php
// Check if the donation appears
$donations = App\Models\Donation::all();
echo "Total donations: " . $donations->count() . "\n";

// Show the latest donation
$latest = App\Models\Donation::latest('Donation_ID')->first();
echo "Latest donation: Amount = {$latest->Amount}, Receipt = {$latest->Receipt_Number}\n";
```

**✅ hannah should see the donation created by izzhilmy!**

## Troubleshooting

### Problem: Connection Refused

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Solutions:**
1. Check if Docker container is running on target machine:
   ```bash
   docker ps
   ```
2. Verify firewall allows the port
3. Try accessing from browser: `http://192.168.1.104:3306` (should show connection error, not timeout)

### Problem: Connection Timeout

**Error:** `SQLSTATE[HY000] [2002] Connection timed out`

**Solutions:**
1. Ping the target machine: `ping 192.168.1.104`
2. Check if both machines are on the same network
3. Disable VPN if active
4. Check router/network configuration

### Problem: Access Denied

**Error:** `SQLSTATE[HY000] [1045] Access denied for user 'root'@'...'`

**This means connection works but authentication fails!**

**For MySQL/MariaDB users (hannah, sashvini, adam):**

```bash
# Enter your Docker container
docker exec -it charity-donation-db-hannah bash  # (or your container name)

# Connect to MySQL
mysql -u root -p
# Password: password

# Grant remote access
CREATE USER 'root'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;

# Restart container
exit
docker restart charity-donation-db-hannah
```

### Problem: IP Address Changed

**After reconnecting to WiFi, IP addresses may change.**

**Solutions:**
1. **Quick fix:** Find new IPs and update everyone's `.env` files
2. **Permanent fix:** Configure router to reserve IP addresses (DHCP reservation)

### Problem: Slow Queries

**Remote database queries are slower than localhost.**

**Solutions:**
1. Use caching for frequently accessed data
2. Batch queries instead of N+1
3. Ensure both machines have good network connection
4. Add database indexes on foreign key columns

## Daily Workflow

### Starting Work

**Each team member (every time you start working):**

1. Start your Docker container:
   ```bash
   # Windows
   docker-compose -f docker-compose-yourname.yml up -d

   # Verify it's running
   docker ps
   ```

2. Start Laravel:
   ```bash
   php artisan serve
   ```

3. Optionally start queue worker:
   ```bash
   php artisan queue:listen
   ```

4. Optionally start Vite (for frontend development):
   ```bash
   npm run dev
   ```

### During Development

- **You own specific tables** - only you should seed/modify structure
- **Others can read/write your data** - but shouldn't change schema
- **Communicate before migrations** - coordinate schema changes

### Ending Work

1. Stop Laravel (Ctrl+C)
2. Optionally stop Docker (data persists):
   ```bash
   docker-compose -f docker-compose-yourname.yml down
   ```
   OR keep it running for team access

## Data Ownership Reference

| Who | Owns | Database | Port |
|-----|------|----------|------|
| **izzhilmy** | user, role, admin, permissions | PostgreSQL | 5432 |
| **sashvini** | volunteer, volunteer_skill, skill, event_participation | MariaDB | 3307 |
| **izati** | organization, campaign, event, event_role | PostgreSQL | 5433 |
| **hannah** | donor, donation, donation_allocation | MySQL | 3306 |
| **adam** | public_profile, recipient | MySQL | 3308 |

## Important Notes

1. **Keep Docker running** - Your teammates need access to your database
2. **Static IPs recommended** - Configure router to assign same IPs
3. **Same network required** - All must be on same WiFi/LAN
4. **Backup data** - Docker volumes persist, but backup important data
5. **Coordinate migrations** - Don't run migrations without team agreement
6. **Default passwords** - Change for production use

## Getting Help

**Check logs:**
```bash
# Laravel logs
php artisan pail

# Docker logs
docker logs charity-user-db-izzhilmy  # (your container name)
```

**Test specific connection:**
```bash
php artisan tinker
```
```php
DB::connection('hannah')->getPdo();  // Replace 'hannah' with connection name
```

**View connection health:**
```bash
php test-connections.php
```

## Success Criteria

You're fully set up when:
- ✅ Your Docker container is running
- ✅ `php test-connections.php` shows all 5 connections working
- ✅ You can query other team members' tables
- ✅ Other team members can query your tables
- ✅ Laravel app loads successfully
- ✅ You can perform CRUD operations across all databases

Good luck with your distributed database project! 🚀
