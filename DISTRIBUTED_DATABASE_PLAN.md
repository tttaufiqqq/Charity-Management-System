# Distributed Database Architecture Plan

## Overview

This document outlines the implementation of a **heterogeneous distributed database architecture** for Charity-Izz, where different modules use different database systems while running on a single machine (localhost).

## Database Distribution

| Team Member | Database Type | Port | Database Name | Module | Tables |
|-------------|---------------|------|---------------|--------|--------|
| Izzhilmy | PostgreSQL | 5432 | charity_izz_user | User Management | user, role, admin |
| Sashvini | MariaDB | 3307 | charity_izz_volunteer | Volunteer Management | volunteer, volunteer_skill, skill, event, event_participation |
| Izzati | PostgreSQL | 5433 | charity_izz_event | Event Management | organization, event, campaign, event_role |
| Hannah | MySQL | 3306 | charity_izz_donation | Donation Management | donor, donation, campaign, donation_allocation |
| Adam | MySQL | 3308 | charity_izz_recipient | Recipient Management | public_profile, recipient |

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                       │
├─────────────────────────────────────────────────────────────┤
│  Multiple Database Connections (config/database.php)         │
├──────────┬──────────┬──────────┬──────────┬────────────────┤
│   User   │ Volunteer│  Event   │ Donation │   Recipient    │
│   (PG)   │  (Maria) │   (PG)   │  (MySQL) │    (MySQL)     │
└──────────┴──────────┴──────────┴──────────┴────────────────┘
    5432       3307       5433       3306         3308
```

## Critical Issue: Overlapping Tables

**Problem**: Some tables appear in multiple modules:
- `event` table: Sashvini's module AND Izzati's module
- `campaign` table: Izzati's module AND Hannah's module

**Solution**: Establish **Single Source of Truth (SSOT)**
- `event` table → **Izzati's database** (Event Management is the primary owner)
- `campaign` table → **Izzati's database** (Event Management includes Campaign management)
- Other modules will access these via cross-database queries

### Revised Table Distribution

| Database | Tables |
|----------|--------|
| **charity_izz_user (PG - 5432)** | user, role, admin, model_has_roles, model_has_permissions, role_has_permissions, personal_access_tokens, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs |
| **charity_izz_volunteer (MariaDB - 3307)** | volunteer, volunteer_skill, skill, event_participation |
| **charity_izz_event (PG - 5433)** | organization, event, campaign, event_role |
| **charity_izz_donation (MySQL - 3306)** | donor, donation, donation_allocation |
| **charity_izz_recipient (MySQL - 3308)** | public_profile, recipient |

## Implementation Steps

### Step 1: Install Database Systems Locally

#### PostgreSQL (Ports 5432 and 5433)
```bash
# Install PostgreSQL (default port 5432)
# Download from: https://www.postgresql.org/download/windows/

# Create first database (default port 5432)
psql -U postgres
CREATE DATABASE charity_izz_user;

# Create second PostgreSQL instance on port 5433
# Option A: Use same PostgreSQL installation, different cluster
pg_ctl -D "C:\PostgreSQL\data2" -o "-p 5433" start
psql -U postgres -p 5433
CREATE DATABASE charity_izz_event;

# Option B: Use Docker
docker run --name postgres-5432 -e POSTGRES_PASSWORD=password -p 5432:5432 -d postgres
docker run --name postgres-5433 -e POSTGRES_PASSWORD=password -p 5433:5432 -d postgres
```

#### MySQL (Ports 3306 and 3308)
```bash
# Install MySQL (default port 3306)
# Download from: https://dev.mysql.com/downloads/installer/

# Create first database (default port 3306)
mysql -u root -p
CREATE DATABASE charity_izz_donation;

# Create second MySQL instance on port 3308
# Option: Use Docker
docker run --name mysql-3306 -e MYSQL_ROOT_PASSWORD=password -p 3306:3306 -d mysql:8
docker run --name mysql-3308 -e MYSQL_ROOT_PASSWORD=password -p 3308:3306 -d mysql:8
```

#### MariaDB (Port 3307)
```bash
# Install MariaDB
# Download from: https://mariadb.org/download/

# Configure to run on port 3307
# Edit my.ini: port = 3307

# Or use Docker
docker run --name mariadb-3307 -e MYSQL_ROOT_PASSWORD=password -p 3307:3306 -d mariadb:latest
```

### Step 2: Configure Laravel Database Connections

Edit `config/database.php`:

```php
'connections' => [

    // Izzhilmy - User Management (PostgreSQL - Port 5432)
    'pgsql_user' => [
        'driver' => 'pgsql',
        'host' => env('DB_USER_HOST', '127.0.0.1'),
        'port' => env('DB_USER_PORT', '5432'),
        'database' => env('DB_USER_DATABASE', 'charity_izz_user'),
        'username' => env('DB_USER_USERNAME', 'postgres'),
        'password' => env('DB_USER_PASSWORD', 'password'),
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
    ],

    // Sashvini - Volunteer Management (MariaDB - Port 3307)
    'mariadb_volunteer' => [
        'driver' => 'mysql',
        'host' => env('DB_VOLUNTEER_HOST', '127.0.0.1'),
        'port' => env('DB_VOLUNTEER_PORT', '3307'),
        'database' => env('DB_VOLUNTEER_DATABASE', 'charity_izz_volunteer'),
        'username' => env('DB_VOLUNTEER_USERNAME', 'root'),
        'password' => env('DB_VOLUNTEER_PASSWORD', 'password'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

    // Izzati - Event Management (PostgreSQL - Port 5433)
    'pgsql_event' => [
        'driver' => 'pgsql',
        'host' => env('DB_EVENT_HOST', '127.0.0.1'),
        'port' => env('DB_EVENT_PORT', '5433'),
        'database' => env('DB_EVENT_DATABASE', 'charity_izz_event'),
        'username' => env('DB_EVENT_USERNAME', 'postgres'),
        'password' => env('DB_EVENT_PASSWORD', 'password'),
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
    ],

    // Hannah - Donation Management (MySQL - Port 3306)
    'mysql_donation' => [
        'driver' => 'mysql',
        'host' => env('DB_DONATION_HOST', '127.0.0.1'),
        'port' => env('DB_DONATION_PORT', '3306'),
        'database' => env('DB_DONATION_DATABASE', 'charity_izz_donation'),
        'username' => env('DB_DONATION_USERNAME', 'root'),
        'password' => env('DB_DONATION_PASSWORD', 'password'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

    // Adam - Recipient Management (MySQL - Port 3308)
    'mysql_recipient' => [
        'driver' => 'mysql',
        'host' => env('DB_RECIPIENT_HOST', '127.0.0.1'),
        'port' => env('DB_RECIPIENT_PORT', '3308'),
        'database' => env('DB_RECIPIENT_DATABASE', 'charity_izz_recipient'),
        'username' => env('DB_RECIPIENT_USERNAME', 'root'),
        'password' => env('DB_RECIPIENT_PASSWORD', 'password'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

],
```

### Step 3: Update .env File

```env
# Default connection (keep for Laravel core tables if needed)
DB_CONNECTION=pgsql_user

# User Management Database (Izzhilmy - PostgreSQL)
DB_USER_HOST=127.0.0.1
DB_USER_PORT=5432
DB_USER_DATABASE=charity_izz_user
DB_USER_USERNAME=postgres
DB_USER_PASSWORD=password

# Volunteer Management Database (Sashvini - MariaDB)
DB_VOLUNTEER_HOST=127.0.0.1
DB_VOLUNTEER_PORT=3307
DB_VOLUNTEER_DATABASE=charity_izz_volunteer
DB_VOLUNTEER_USERNAME=root
DB_VOLUNTEER_PASSWORD=password

# Event Management Database (Izzati - PostgreSQL)
DB_EVENT_HOST=127.0.0.1
DB_EVENT_PORT=5433
DB_EVENT_DATABASE=charity_izz_event
DB_EVENT_USERNAME=postgres
DB_EVENT_PASSWORD=password

# Donation Management Database (Hannah - MySQL)
DB_DONATION_HOST=127.0.0.1
DB_DONATION_PORT=3306
DB_DONATION_DATABASE=charity_izz_donation
DB_DONATION_USERNAME=root
DB_DONATION_PASSWORD=password

# Recipient Management Database (Adam - MySQL)
DB_RECIPIENT_HOST=127.0.0.1
DB_RECIPIENT_PORT=3308
DB_RECIPIENT_DATABASE=charity_izz_recipient
DB_RECIPIENT_USERNAME=root
DB_RECIPIENT_PASSWORD=password
```

### Step 4: Update Model Connections

Each model must specify its database connection:

**User.php** (Izzhilmy):
```php
protected $connection = 'pgsql_user';
```

**Volunteer.php, Skill.php, VolunteerSkill.php, EventParticipation.php** (Sashvini):
```php
protected $connection = 'mariadb_volunteer';
```

**Organization.php, Event.php, Campaign.php, EventRole.php** (Izzati):
```php
protected $connection = 'pgsql_event';
```

**Donor.php, Donation.php, DonationAllocation.php** (Hannah):
```php
protected $connection = 'mysql_donation';
```

**PublicProfile.php, Recipient.php** (Adam):
```php
protected $connection = 'mysql_recipient';
```

### Step 5: Split Existing Migrations

Create migration files for each database connection:

```
database/migrations/
├── user_db/           (Izzhilmy - run with --database=pgsql_user)
├── volunteer_db/      (Sashvini - run with --database=mariadb_volunteer)
├── event_db/          (Izzati - run with --database=pgsql_event)
├── donation_db/       (Hannah - run with --database=mysql_donation)
└── recipient_db/      (Adam - run with --database=mysql_recipient)
```

Run migrations for each database:
```bash
php artisan migrate --path=database/migrations/user_db --database=pgsql_user
php artisan migrate --path=database/migrations/volunteer_db --database=mariadb_volunteer
php artisan migrate --path=database/migrations/event_db --database=pgsql_event
php artisan migrate --path=database/migrations/donation_db --database=mysql_donation
php artisan migrate --path=database/migrations/recipient_db --database=mysql_recipient
```

### Step 6: Handle Cross-Database Relationships

**Problem**: Laravel Eloquent relationships don't work across different database connections by default.

**Solutions**:

#### Option 1: Use Explicit Queries (Simplest)
```php
// In Volunteer model (mariadb_volunteer)
public function getEvents()
{
    $eventIds = DB::connection('mariadb_volunteer')
        ->table('event_participation')
        ->where('Volunteer_ID', $this->Volunteer_ID)
        ->pluck('Event_ID');

    return DB::connection('pgsql_event')
        ->table('event')
        ->whereIn('Event_ID', $eventIds)
        ->get();
}
```

#### Option 2: Use Repository Pattern
```php
// app/Repositories/VolunteerEventRepository.php
class VolunteerEventRepository
{
    public function getVolunteerEvents($volunteerId)
    {
        // Get event IDs from mariadb
        $eventIds = EventParticipation::where('Volunteer_ID', $volunteerId)
            ->pluck('Event_ID');

        // Get events from pgsql
        return Event::whereIn('Event_ID', $eventIds)->get();
    }
}
```

#### Option 3: Create Database Views (Advanced)
Create views in each database that replicate foreign key data.

### Step 7: Update Seeders

Create separate seeder files for each database:

```php
// database/seeders/UserDatabaseSeeder.php
class UserDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Set default connection
        DB::setDefaultConnection('pgsql_user');

        // Seed users, roles, admins
    }
}

// Run individually
php artisan db:seed --class=UserDatabaseSeeder
php artisan db:seed --class=VolunteerDatabaseSeeder
php artisan db:seed --class=EventDatabaseSeeder
php artisan db:seed --class=DonationDatabaseSeeder
php artisan db:seed --class=RecipientDatabaseSeeder
```

### Step 8: Testing Strategy

Create test database for each connection:
```php
// phpunit.xml or pest.php
<env name="DB_USER_DATABASE" value="charity_izz_user_test"/>
<env name="DB_VOLUNTEER_DATABASE" value="charity_izz_volunteer_test"/>
<env name="DB_EVENT_DATABASE" value="charity_izz_event_test"/>
<env name="DB_DONATION_DATABASE" value="charity_izz_donation_test"/>
<env name="DB_RECIPIENT_DATABASE" value="charity_izz_recipient_test"/>
```

## Data Synchronization Strategy

Since tables are distributed, you need strategies for maintaining data consistency:

### 1. Foreign Key References

**Problem**: Cannot use database-level foreign keys across databases.

**Solution**:
- Use application-level validation
- Create a service layer to ensure referential integrity
- Use database transactions where possible (within same database)

Example:
```php
// When creating a donation, verify campaign exists
class CreateDonationService
{
    public function create(array $data)
    {
        // Check campaign exists in event database
        $campaign = Campaign::find($data['Campaign_ID']);
        if (!$campaign) {
            throw new Exception('Campaign not found');
        }

        // Create donation in donation database
        return Donation::create($data);
    }
}
```

### 2. Shared Data Replication

For frequently accessed shared data (e.g., Campaign accessed by both Event and Donation modules):

**Option A: Read from Source, Cache Locally**
```php
// In Donation module, when accessing Campaign
$campaign = Cache::remember("campaign_{$id}", 3600, function() use ($id) {
    return DB::connection('pgsql_event')
        ->table('campaign')
        ->find($id);
});
```

**Option B: Event-Driven Sync**
When a Campaign is updated in `pgsql_event`, trigger an event that updates a cache or denormalized copy:
```php
// In Campaign model (pgsql_event)
protected static function booted()
{
    static::updated(function ($campaign) {
        event(new CampaignUpdated($campaign));
    });
}

// Listener updates cache
class UpdateCampaignCache
{
    public function handle(CampaignUpdated $event)
    {
        Cache::put("campaign_{$event->campaign->Campaign_ID}",
                   $event->campaign,
                   3600);
    }
}
```

### 3. Eventual Consistency

Accept that data may be slightly out of sync and use background jobs to reconcile:
```php
// Schedule regular sync job
// app/Console/Kernel.php
$schedule->job(new SyncCampaignDataJob)->hourly();
```

## Common Queries Pattern

### Example: Get Donations with Campaign Details

```php
// In DonationController (mysql_donation database)
public function index()
{
    // Get donations from donation database
    $donations = Donation::with('donor')->get();

    // Get campaign IDs
    $campaignIds = $donations->pluck('Campaign_ID')->unique();

    // Fetch campaigns from event database
    $campaigns = DB::connection('pgsql_event')
        ->table('campaign')
        ->whereIn('Campaign_ID', $campaignIds)
        ->get()
        ->keyBy('Campaign_ID');

    // Attach campaign data to donations
    $donations->each(function ($donation) use ($campaigns) {
        $donation->campaign = $campaigns[$donation->Campaign_ID] ?? null;
    });

    return view('donations.index', compact('donations'));
}
```

## Advantages of This Approach

1. **No Network Configuration**: All databases run on localhost
2. **True Heterogeneous Setup**: Each module uses its intended database type
3. **Module Independence**: Each team member works on their own database
4. **Real-World Simulation**: Mimics distributed architecture challenges
5. **Laravel Native**: Uses built-in multiple database connection feature

## Disadvantages & Challenges

1. **No Built-in Foreign Keys**: Must validate references in application code
2. **Complex Queries**: Cross-database joins are impossible; requires multiple queries
3. **Transaction Limitations**: Cannot use transactions across databases
4. **Performance**: Multiple database queries for related data
5. **Data Consistency**: Requires careful synchronization strategies
6. **Migration Complexity**: Must manage migrations for 5 separate databases
7. **Testing Complexity**: Must seed and test across all databases

## Alternative: Simpler Approach (If Acceptable)

If the goal is just to **simulate** distributed architecture for learning:

### Use Database Schemas Instead of Separate Instances

PostgreSQL and MySQL support schemas. Use one database with multiple schemas:

```php
// config/database.php
'pgsql' => [
    'driver' => 'pgsql',
    'schema' => 'user_module',  // Different schemas
],

'pgsql_event' => [
    'driver' => 'pgsql',
    'schema' => 'event_module',
],
```

**Pros**:
- Easier setup
- Foreign keys still work within same database
- Simpler transaction management

**Cons**:
- Not truly heterogeneous (all PostgreSQL or all MySQL)
- Doesn't teach multi-database challenges

## Recommended Timeline

1. **Week 1**: Setup all database instances, configure Laravel connections
2. **Week 2**: Split and run migrations, verify all tables created correctly
3. **Week 3**: Update all models with connection property, test CRUD operations
4. **Week 4**: Implement cross-database query patterns, update controllers
5. **Week 5**: Update seeders, test data flow across databases
6. **Week 6**: Integration testing, performance optimization

## Docker Compose Quick Start (Recommended)

Create `docker-compose.yml` for instant setup:

```yaml
version: '3.8'

services:
  postgres-user:
    image: postgres:16
    environment:
      POSTGRES_DB: charity_izz_user
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5432:5432"

  postgres-event:
    image: postgres:16
    environment:
      POSTGRES_DB: charity_izz_event
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5433:5432"

  mysql-donation:
    image: mysql:8
    environment:
      MYSQL_DATABASE: charity_izz_donation
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3306:3306"

  mysql-recipient:
    image: mysql:8
    environment:
      MYSQL_DATABASE: charity_izz_recipient
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3308:3306"

  mariadb-volunteer:
    image: mariadb:11
    environment:
      MYSQL_DATABASE: charity_izz_volunteer
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3307:3306"
```

Start all databases:
```bash
docker-compose up -d
```

## Conclusion

The **simplest approach** without networking is using Laravel's multiple database connections with all databases running on `localhost` but on different ports. This provides a true heterogeneous distributed setup while keeping everything local.

The main challenges are:
1. Cross-database relationships (solved with application-level queries)
2. Data consistency (solved with caching and sync jobs)
3. Migration management (solved with separate migration folders)

For educational purposes, this approach teaches real distributed database challenges without the complexity of network configuration.
