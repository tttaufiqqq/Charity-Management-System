# Cross-Machine Distributed Database Setup Guide

## Overview

This guide explains how to set up the Charity-Izz distributed database architecture across multiple team members' machines, where each person:
- Runs Docker containers on their own machine
- Owns and hosts specific database tables
- Can perform CRUD operations on other team members' tables
- Sees real-time data changes from other team members

## Architecture: Hybrid Distributed Approach

Each team member:
1. **Hosts their own database** (exposed to network)
2. **Runs their own Laravel application** (connects to all 5 databases across the network)
3. **Owns specific tables** (source of truth for their module)

### Data Ownership

| Team Member | IP Address (Example) | Database Hosted | Port | Tables Owned |
|-------------|---------------------|-----------------|------|--------------|
| **izzhilmy** | 192.168.1.101 | PostgreSQL | 5432 | user, role, admin, permissions |
| **sashvini** | 192.168.1.102 | MariaDB | 3307 | volunteer, volunteer_skill, skill, event_participation |
| **izati** | 192.168.1.103 | PostgreSQL | 5433 | organization, campaign, event, event_role |
| **hannah** | 192.168.1.104 | MySQL | 3306 | donor, donation, donation_allocation |
| **adam** | 192.168.1.105 | MySQL | 3308 | public_profile, recipient |

## Prerequisites

1. **All team members on the same network** (same WiFi, VPN, or LAN)
2. **Static or reserved IP addresses** (or update .env when IP changes)
3. **Firewall rules allowing database ports**
4. **Docker & Docker Compose** installed
5. **PHP 8.2+, Composer, Node.js** installed

## Setup Instructions

### Phase 1: Network Configuration

#### Step 1: Find Your IP Address

**Windows:**
```bash
ipconfig
# Look for "IPv4 Address" under your active network adapter
```

**Mac/Linux:**
```bash
ifconfig
# Look for "inet" under your active network adapter
# OR
ip addr show
```

**Example output:**
```
IPv4 Address: 192.168.1.101
```

Each team member should note their IP address and share it with the team.

#### Step 2: Configure Firewall

Each team member must allow incoming connections on their database port.

**Windows Firewall:**
```powershell
# Run as Administrator
# For izzhilmy (PostgreSQL - Port 5432)
New-NetFirewallRule -DisplayName "PostgreSQL User DB" -Direction Inbound -LocalPort 5432 -Protocol TCP -Action Allow

# For sashvini (MariaDB - Port 3307)
New-NetFirewallRule -DisplayName "MariaDB Volunteer DB" -Direction Inbound -LocalPort 3307 -Protocol TCP -Action Allow

# For izati (PostgreSQL - Port 5433)
New-NetFirewallRule -DisplayName "PostgreSQL Event DB" -Direction Inbound -LocalPort 5433 -Protocol TCP -Action Allow

# For hannah (MySQL - Port 3306)
New-NetFirewallRule -DisplayName "MySQL Donation DB" -Direction Inbound -LocalPort 3306 -Protocol TCP -Action Allow

# For adam (MySQL - Port 3308)
New-NetFirewallRule -DisplayName "MySQL Recipient DB" -Direction Inbound -LocalPort 3308 -Protocol TCP -Action Allow
```

**Mac:**
```bash
# Open System Preferences → Security & Privacy → Firewall → Firewall Options
# Click "+" and add Docker
# Or disable firewall for local network (not recommended for public networks)
```

**Linux (UFW):**
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

### Phase 2: Docker Configuration

Each team member runs **ONLY their assigned database container** + shared services.

#### izzhilmy's Docker Compose

**File:** `docker-compose-izzhilmy.yml`
```yaml
version: '3.8'

services:
  # izzhilmy ONLY runs PostgreSQL User DB
  postgres-user:
    image: postgres:16-alpine
    container_name: charity-user-db
    environment:
      POSTGRES_DB: charity_izz_user
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5432:5432"  # Exposed to network
    volumes:
      - postgres-user-data:/var/lib/postgresql/data
    networks:
      - charity-network
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U postgres"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Shared Redis (optional - can run on one machine)
  redis:
    image: redis:7-alpine
    container_name: charity-redis
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    networks:
      - charity-network

volumes:
  postgres-user-data:
  redis-data:

networks:
  charity-network:
    driver: bridge
```

**Start:**
```bash
docker-compose -f docker-compose-izzhilmy.yml up -d
```

#### sashvini's Docker Compose

**File:** `docker-compose-sashvini.yml`
```yaml
version: '3.8'

services:
  # sashvini ONLY runs MariaDB Volunteer DB
  mariadb-volunteer:
    image: mariadb:11-jammy
    container_name: charity-volunteer-db
    environment:
      MYSQL_DATABASE: charity_izz_volunteer
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3307:3306"  # Exposed to network
    volumes:
      - mariadb-volunteer-data:/var/lib/mysql
    networks:
      - charity-network
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  mariadb-volunteer-data:

networks:
  charity-network:
    driver: bridge
```

**Start:**
```bash
docker-compose -f docker-compose-sashvini.yml up -d
```

#### izati's Docker Compose

**File:** `docker-compose-izati.yml`
```yaml
version: '3.8'

services:
  # izati ONLY runs PostgreSQL Event DB
  postgres-event:
    image: postgres:16-alpine
    container_name: charity-event-db
    environment:
      POSTGRES_DB: charity_izz_event
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5433:5432"  # Exposed to network (host port 5433)
    volumes:
      - postgres-event-data:/var/lib/postgresql/data
    networks:
      - charity-network
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U postgres"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  postgres-event-data:

networks:
  charity-network:
    driver: bridge
```

**Start:**
```bash
docker-compose -f docker-compose-izati.yml up -d
```

#### hannah's Docker Compose

**File:** `docker-compose-hannah.yml`
```yaml
version: '3.8'

services:
  # hannah ONLY runs MySQL Donation DB
  mysql-donation:
    image: mysql:8-oracle
    container_name: charity-donation-db
    environment:
      MYSQL_DATABASE: charity_izz_donation
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3306:3306"  # Exposed to network
    volumes:
      - mysql-donation-data:/var/lib/mysql
    networks:
      - charity-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  mysql-donation-data:

networks:
  charity-network:
    driver: bridge
```

**Start:**
```bash
docker-compose -f docker-compose-hannah.yml up -d
```

#### adam's Docker Compose

**File:** `docker-compose-adam.yml`
```yaml
version: '3.8'

services:
  # adam ONLY runs MySQL Recipient DB
  mysql-recipient:
    image: mysql:8-oracle
    container_name: charity-recipient-db
    environment:
      MYSQL_DATABASE: charity_izz_recipient
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3308:3306"  # Exposed to network (host port 3308)
    volumes:
      - mysql-recipient-data:/var/lib/mysql
    networks:
      - charity-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  mysql-recipient-data:

networks:
  charity-network:
    driver: bridge
```

**Start:**
```bash
docker-compose -f docker-compose-adam.yml up -d
```

### Phase 3: Laravel Configuration

**IMPORTANT:** All team members run the SAME Laravel codebase and connect to ALL 5 databases across the network.

#### Update .env for Each Team Member

Each person creates their own `.env` file with IP addresses of all team members.

**Example: izzhilmy's .env**
```env
APP_NAME="Charity-Izz Distributed"
APP_URL=http://localhost:8000

# Default connection (User DB - izzhilmy hosts this)
DB_CONNECTION=izz

# izzhilmy's Database (User Management) - LOCALHOST
DB_IZZ_HOST=127.0.0.1
DB_IZZ_PORT=5432
DB_IZZ_DATABASE=charity_izz_user
DB_IZZ_USERNAME=postgres
DB_IZZ_PASSWORD=password

# sashvini's Database (Volunteer Management) - REMOTE
DB_SASHVINI_HOST=192.168.1.102
DB_SASHVINI_PORT=3307
DB_SASHVINI_DATABASE=charity_izz_volunteer
DB_SASHVINI_USERNAME=root
DB_SASHVINI_PASSWORD=password

# izati's Database (Event Management) - REMOTE
DB_IZATI_HOST=192.168.1.103
DB_IZATI_PORT=5433
DB_IZATI_DATABASE=charity_izz_event
DB_IZATI_USERNAME=postgres
DB_IZATI_PASSWORD=password

# hannah's Database (Donation Management) - REMOTE
DB_HANNAH_HOST=192.168.1.104
DB_HANNAH_PORT=3306
DB_HANNAH_DATABASE=charity_izz_donation
DB_HANNAH_USERNAME=root
DB_HANNAH_PASSWORD=password

# adam's Database (Recipient Management) - REMOTE
DB_ADAM_HOST=192.168.1.105
DB_ADAM_PORT=3308
DB_ADAM_DATABASE=charity_izz_recipient
DB_ADAM_USERNAME=root
DB_ADAM_PASSWORD=password
```

**Example: hannah's .env**
```env
APP_NAME="Charity-Izz Distributed"
APP_URL=http://localhost:8000

# Default connection
DB_CONNECTION=hannah

# izzhilmy's Database (User Management) - REMOTE
DB_IZZ_HOST=192.168.1.101
DB_IZZ_PORT=5432
DB_IZZ_DATABASE=charity_izz_user
DB_IZZ_USERNAME=postgres
DB_IZZ_PASSWORD=password

# sashvini's Database (Volunteer Management) - REMOTE
DB_SASHVINI_HOST=192.168.1.102
DB_SASHVINI_PORT=3307
DB_SASHVINI_DATABASE=charity_izz_volunteer
DB_SASHVINI_USERNAME=root
DB_SASHVINI_PASSWORD=password

# izati's Database (Event Management) - REMOTE
DB_IZATI_HOST=192.168.1.103
DB_IZATI_PORT=5433
DB_IZATI_DATABASE=charity_izz_event
DB_IZATI_USERNAME=postgres
DB_IZATI_PASSWORD=password

# hannah's Database (Donation Management) - LOCALHOST
DB_HANNAH_HOST=127.0.0.1
DB_HANNAH_PORT=3306
DB_HANNAH_DATABASE=charity_izz_donation
DB_HANNAH_USERNAME=root
DB_HANNAH_PASSWORD=password

# adam's Database (Recipient Management) - REMOTE
DB_ADAM_HOST=192.168.1.105
DB_ADAM_PORT=3308
DB_ADAM_DATABASE=charity_izz_recipient
DB_ADAM_USERNAME=root
DB_ADAM_PASSWORD=password
```

**Repeat this pattern for all 5 team members** - each person sets their own DB to localhost and others to remote IPs.

### Phase 4: Database Initialization

Each team member initializes **ONLY their own database**.

#### izzhilmy - Initialize User Database

```bash
# Start PostgreSQL container
docker-compose -f docker-compose-izzhilmy.yml up -d

# Wait for health check
docker ps

# Run migrations for User DB only
php artisan migrate --database=izz

# Seed user data
php artisan db:seed --database=izz --class=UserSeeder
php artisan db:seed --database=izz --class=RoleSeeder
```

#### sashvini - Initialize Volunteer Database

```bash
# Start MariaDB container
docker-compose -f docker-compose-sashvini.yml up -d

# Run migrations for Volunteer DB only
php artisan migrate --database=sashvini

# Seed volunteer data
php artisan db:seed --database=sashvini --class=VolunteerSeeder
php artisan db:seed --database=sashvini --class=SkillSeeder
```

#### izati - Initialize Event Database

```bash
# Start PostgreSQL container
docker-compose -f docker-compose-izati.yml up -d

# Run migrations for Event DB only
php artisan migrate --database=izati

# Seed event data
php artisan db:seed --database=izati --class=CampaignSeeder
php artisan db:seed --database=izati --class=EventSeeder
php artisan db:seed --database=izati --class=OrganizationSeeder
```

#### hannah - Initialize Donation Database

```bash
# Start MySQL container
docker-compose -f docker-compose-hannah.yml up -d

# Run migrations for Donation DB only
php artisan migrate --database=hannah

# Seed donation data
php artisan db:seed --database=hannah --class=DonationSeeder
```

#### adam - Initialize Recipient Database

```bash
# Start MySQL container
docker-compose -f docker-compose-adam.yml up -d

# Run migrations for Recipient DB only
php artisan migrate --database=adam

# Seed recipient data
php artisan db:seed --database=adam --class=RecipientSeeder
```

### Phase 5: Testing Cross-Machine Connectivity

Each team member should test connections to ALL databases.

**Test script:** `test-connections.php`
```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$connections = ['izz', 'sashvini', 'izati', 'hannah', 'adam'];

echo "Testing database connections...\n\n";

foreach ($connections as $connection) {
    try {
        DB::connection($connection)->getPdo();
        echo "✅ {$connection}: Connected\n";
    } catch (Exception $e) {
        echo "❌ {$connection}: Failed - {$e->getMessage()}\n";
    }
}
```

**Run:**
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
```

### Phase 6: Testing CRUD Operations

#### Test Case: izzhilmy creates donation on hannah's database

**On izzhilmy's machine:**
```bash
php artisan tinker
```

```php
// Create a donation in hannah's database
use App\Models\Donation;

$donation = new Donation();
$donation->setConnection('hannah'); // hannah's database
$donation->Donor_ID = 1;
$donation->Campaign_ID = 1;
$donation->Amount = 100.00;
$donation->Payment_Method = 'Online Banking';
$donation->Receipt_Number = 'TEST-' . time();
$donation->Donation_Date = now();
$donation->save();

echo "Donation created with ID: {$donation->Donation_ID}\n";
```

**On hannah's machine:**
```bash
php artisan tinker
```

```php
// Verify donation appears in hannah's database
use App\Models\Donation;

$donations = Donation::all();
echo "Total donations: " . $donations->count() . "\n";
$donations->each(function($d) {
    echo "ID: {$d->Donation_ID}, Amount: {$d->Amount}\n";
});
```

**hannah should see the donation created by izzhilmy!**

## How Data Flow Works

### Example: Creating a Donation (izzhilmy → hannah)

1. **izzhilmy** opens their Laravel app
2. They navigate to create donation page
3. Form submits to `DonationController@store`
4. Controller creates `Donation` model
5. Donation model has `protected $connection = 'hannah'`
6. Laravel connects to **hannah's MySQL** (IP: 192.168.1.104:3306)
7. Data is inserted into **hannah's database**
8. **hannah** refreshes their app and sees the new donation

### Example: Cross-Module Query

**izzhilmy wants to view all donations with campaign details:**

```php
// In izzhilmy's Laravel app

// Get donations from hannah's database
$donations = \DB::connection('hannah')
    ->table('donation')
    ->get();

// Get campaign IDs
$campaignIds = $donations->pluck('Campaign_ID')->unique();

// Get campaigns from izati's database
$campaigns = \DB::connection('izati')
    ->table('campaign')
    ->whereIn('Campaign_ID', $campaignIds)
    ->get()
    ->keyBy('Campaign_ID');

// Combine data
foreach ($donations as $donation) {
    $donation->campaign = $campaigns[$donation->Campaign_ID] ?? null;
}

return view('donations.index', compact('donations'));
```

## Important Considerations

### 1. Network Stability

- **Problem:** If a team member's machine is offline, their database is unavailable
- **Solution:**
  - Implement try-catch blocks for database connections
  - Add circuit breaker pattern (already in BaseApiService)
  - Cache frequently accessed data locally

### 2. IP Address Changes

- **Problem:** DHCP may assign different IPs after restart
- **Solutions:**
  - Configure router to reserve IP addresses for team members
  - Use hostnames instead of IPs (configure hosts file)
  - Use dynamic DNS service
  - Create a shared config file that everyone updates

### 3. Security

**Current setup uses default passwords for demo purposes.**

For production or serious development:
```yaml
# Use strong passwords
POSTGRES_PASSWORD: "Y0urStr0ngP@ssw0rd"
MYSQL_ROOT_PASSWORD: "An0th3rStr0ngP@ss"
```

### 4. Performance

- **Remote queries are slower** than localhost
- **Use caching** aggressively:
  ```php
  $campaigns = Cache::remember('campaigns_active', 300, function() {
      return \DB::connection('izati')->table('campaign')
          ->where('Status', 'Active')
          ->get();
  });
  ```

### 5. Data Consistency

- **Problem:** Multiple people can modify same record simultaneously
- **Solutions:**
  - Implement optimistic locking (version column)
  - Use database transactions
  - Implement queue-based writes

## Troubleshooting

### Connection Refused

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Check:**
1. Is the target machine's Docker container running?
   ```bash
   docker ps
   ```
2. Is firewall blocking the port?
3. Can you ping the target machine?
   ```bash
   ping 192.168.1.104
   ```
4. Is the IP address correct in `.env`?

### Connection Timeout

**Error:** `SQLSTATE[HY000] [2002] Connection timed out`

**Check:**
1. Are both machines on the same network?
2. Is VPN interfering?
3. Try telnet to test port:
   ```bash
   telnet 192.168.1.104 3306
   ```

### Access Denied

**Error:** `SQLSTATE[HY000] [1045] Access denied for user 'root'@'ip-address'`

**Fix:** MySQL/MariaDB need to allow remote connections.

**On hannah's machine (MySQL host):**
```bash
# Connect to container
docker exec -it charity-donation-db bash

# Connect to MySQL
mysql -u root -p

# Grant remote access
CREATE USER 'root'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON charity_izz_donation.* TO 'root'@'%';
FLUSH PRIVILEGES;
EXIT;

# Restart container
docker restart charity-donation-db
```

### Slow Queries

**Solution:** Add indexes to foreign key columns

```bash
php artisan make:migration add_indexes_to_donation_table --database=hannah
```

```php
public function up()
{
    Schema::connection('hannah')->table('donation', function (Blueprint $table) {
        $table->index('Campaign_ID');
        $table->index('Donor_ID');
    });
}
```

## Monitoring

### Create Health Check Dashboard

**File:** `routes/web.php`
```php
Route::get('/health-dashboard', function() {
    $connections = [
        'izz' => 'izzhilmy (User DB)',
        'sashvini' => 'sashvini (Volunteer DB)',
        'izati' => 'izati (Event DB)',
        'hannah' => 'hannah (Donation DB)',
        'adam' => 'adam (Recipient DB)',
    ];

    $status = [];
    foreach ($connections as $conn => $label) {
        try {
            $start = microtime(true);
            DB::connection($conn)->getPdo();
            $latency = round((microtime(true) - $start) * 1000, 2);
            $status[$label] = [
                'status' => 'Connected',
                'latency' => $latency . 'ms',
                'color' => 'green'
            ];
        } catch (Exception $e) {
            $status[$label] = [
                'status' => 'Disconnected',
                'error' => $e->getMessage(),
                'color' => 'red'
            ];
        }
    }

    return view('health-dashboard', compact('status'));
});
```

Access at: `http://localhost:8000/health-dashboard`

## Best Practices

1. **Always specify connection explicitly:**
   ```php
   Donation::on('hannah')->where('Amount', '>', 100)->get();
   ```

2. **Use repository pattern for cross-database queries:**
   ```php
   class DonationRepository {
       public function getDonationsWithCampaigns() {
           // Handle cross-database logic here
       }
   }
   ```

3. **Implement retry logic:**
   ```php
   $retries = 3;
   while ($retries > 0) {
       try {
           return DB::connection('hannah')->table('donation')->get();
       } catch (Exception $e) {
           $retries--;
           if ($retries === 0) throw $e;
           sleep(1);
       }
   }
   ```

4. **Use Laravel events for synchronization:**
   ```php
   // When donation created, update campaign collected amount
   event(new DonationCreated($donation));
   ```

## Summary

This setup allows:
- ✅ **izzhilmy can create donations** → Data goes to hannah's MySQL
- ✅ **hannah sees donations** → Reading from her local MySQL
- ✅ **True distributed system** → 5 databases across 5 machines
- ✅ **Each person owns their domain** → Full control over their tables
- ✅ **Cross-machine CRUD** → Anyone can access anyone's data
- ✅ **Heterogeneous databases** → PostgreSQL, MySQL, MariaDB

Each Laravel app connects to all 5 databases across the network, creating a truly distributed system where data ownership is distributed but access is universal.
