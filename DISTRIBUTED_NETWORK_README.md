# Charity-Izz - Cross-Machine Distributed Database Architecture

## Overview

This project implements a **true distributed database architecture** where each team member:
- Runs their own database server on their machine
- Hosts specific tables (data ownership)
- Connects to all other team members' databases over the network
- Can perform CRUD operations on any table, regardless of location

## What Makes This Special?

✅ **Heterogeneous Databases**: PostgreSQL, MySQL, and MariaDB working together
✅ **Network Distribution**: Databases physically distributed across 5 machines
✅ **Cross-Machine Access**: izzhilmy can create data in hannah's database
✅ **Real Distributed System**: Simulates real-world microservices architecture
✅ **Single Laravel App**: One codebase connects to all distributed databases

## Architecture

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│  izzhilmy    │      │   sashvini   │      │    izati     │
│  (Host 1)    │      │   (Host 2)   │      │   (Host 3)   │
├──────────────┤      ├──────────────┤      ├──────────────┤
│ PostgreSQL   │      │   MariaDB    │      │ PostgreSQL   │
│   :5432      │      │    :3307     │      │   :5433      │
│              │      │              │      │              │
│ user tables  │      │ volunteer    │      │ organization │
│ role tables  │      │ skill tables │      │ campaign     │
└──────────────┘      └──────────────┘      │ event tables │
                                             └──────────────┘

┌──────────────┐      ┌──────────────┐
│   hannah     │      │     adam     │
│  (Host 4)    │      │   (Host 5)   │
├──────────────┤      ├──────────────┤
│    MySQL     │      │    MySQL     │
│    :3306     │      │    :3308     │
│              │      │              │
│ donation     │      │ recipient    │
│ donor tables │      │ public_prof  │
└──────────────┘      └──────────────┘

           ALL connected via Network
```

## Data Ownership

| Team Member | Database Type | Port | Tables Owned |
|-------------|---------------|------|--------------|
| **izzhilmy** | PostgreSQL | 5432 | user, role, admin, model_has_roles, model_has_permissions, role_has_permissions, personal_access_tokens, password_reset_tokens, sessions |
| **sashvini** | MariaDB | 3307 | volunteer, volunteer_skill, skill, event_participation |
| **izati** | PostgreSQL | 5433 | organization, event, campaign, event_role |
| **hannah** | MySQL | 3306 | donor, donation, donation_allocation |
| **adam** | MySQL | 3308 | public_profile, recipient |

**Shared Laravel Tables**: cache, cache_locks, jobs, job_batches, failed_jobs (stored in izzhilmy's DB by default)

## Files Created for This Setup

### Documentation
- `NETWORK_DISTRIBUTED_SETUP.md` - Comprehensive technical guide
- `QUICK_START_GUIDE.md` - Step-by-step setup instructions
- `DISTRIBUTED_NETWORK_README.md` - This file (overview)

### Docker Compose Files (One per team member)
- `docker-compose-izzhilmy.yml` - PostgreSQL User DB
- `docker-compose-sashvini.yml` - MariaDB Volunteer DB
- `docker-compose-izati.yml` - PostgreSQL Event DB
- `docker-compose-hannah.yml` - MySQL Donation DB
- `docker-compose-adam.yml` - MySQL Recipient DB

### Environment Templates
- `env-templates/.env.izzhilmy`
- `env-templates/.env.sashvini`
- `env-templates/.env.izati`
- `env-templates/.env.hannah`
- `env-templates/.env.adam`

### Setup Scripts

**Windows:**
- `setup-izzhilmy.bat`
- `setup-sashvini.bat`
- `setup-izati.bat`
- `setup-hannah.bat`
- `setup-adam.bat`

**Linux/Mac:**
- `setup-izzhilmy.sh`

### Testing
- `test-connections.php` - Test connectivity to all databases

## Quick Start (TL;DR)

### 1. Coordinate with Team
- Everyone shares their IP address
- Everyone configures firewall to allow their database port

### 2. Run Your Setup Script
```bash
# Windows
setup-yourname.bat

# Linux/Mac
chmod +x setup-yourname.sh
./setup-yourname.sh
```

### 3. Update .env
Edit `.env` and replace example IPs with actual team member IPs

### 4. Test Connections
```bash
php test-connections.php
```

Should show all 5 connections successful!

### 5. Start Laravel
```bash
php artisan serve
```

## How It Works

### Example: izzhilmy creates a donation

**What happens:**
1. izzhilmy's Laravel app loads `Donation` model
2. Model specifies: `protected $connection = 'hannah'`
3. Laravel reads `.env`: `DB_HANNAH_HOST=192.168.1.104`
4. Connects to hannah's MySQL server over network
5. Inserts data into hannah's database
6. hannah refreshes her app → sees the new donation!

### Code Example

```php
// On izzhilmy's machine - create donation in hannah's database
$donation = new Donation();
$donation->Donor_ID = 1;
$donation->Campaign_ID = 1;
$donation->Amount = 100.00;
$donation->Payment_Method = 'Credit Card';
$donation->save();  // Automatically goes to hannah's MySQL!

// On hannah's machine - view all donations
$donations = Donation::all();  // Reads from her local MySQL
```

## Prerequisites

### Software Requirements
- Docker & Docker Compose
- PHP 8.2+
- Composer
- Node.js & npm
- Git

### Network Requirements
- All team members on same network (WiFi/LAN)
- Static or reserved IP addresses (recommended)
- Firewall configured to allow database ports
- Good network connectivity (low latency)

## Common Use Cases

### Viewing Data Across Databases

```php
// Get donations with campaign details (crosses databases)
$donations = Donation::on('hannah')->get();  // hannah's MySQL

$campaignIds = $donations->pluck('Campaign_ID')->unique();

$campaigns = Campaign::on('izati')  // izati's PostgreSQL
    ->whereIn('Campaign_ID', $campaignIds)
    ->get()
    ->keyBy('Campaign_ID');

// Combine
foreach ($donations as $donation) {
    $donation->campaign = $campaigns[$donation->Campaign_ID] ?? null;
}
```

### Explicit Connection Switching

```php
// Query sashvini's volunteer database
$volunteers = DB::connection('sashvini')
    ->table('volunteer')
    ->where('Availability_Status', 'Available')
    ->get();

// Query izati's event database
$events = DB::connection('izati')
    ->table('event')
    ->where('Status', 'Upcoming')
    ->get();
```

### Creating Related Data

```php
// Create organization in izati's database
$org = new Organization();
$org->setConnection('izati');
$org->Organization_Name = 'Hope Foundation';
$org->save();

// Create user in izzhilmy's database and link
$user = new User();
$user->setConnection('izz');
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->save();

// Update organization with user ID
$org->Organizer_ID = $user->id;
$org->save();
```

## Best Practices

### 1. Always Specify Connection
```php
// Good
Donation::on('hannah')->get();

// Risky (uses default connection)
Donation::all();
```

### 2. Use Caching for Remote Data
```php
$campaigns = Cache::remember('active_campaigns', 300, function() {
    return Campaign::on('izati')->where('Status', 'Active')->get();
});
```

### 3. Handle Connection Failures
```php
try {
    $donations = Donation::on('hannah')->get();
} catch (Exception $e) {
    // hannah's database is unreachable
    Log::error('Cannot connect to donation database: ' . $e->getMessage());
    return response()->json(['error' => 'Donation service unavailable'], 503);
}
```

### 4. Coordinate Schema Changes
Before running migrations that affect shared tables:
1. Notify the team
2. Coordinate timing
3. Test on one machine first
4. Have backups ready

## Troubleshooting

### All Connections Failing
- Check if you're on the same network
- Verify Docker containers are running: `docker ps`
- Try pinging team members: `ping 192.168.1.104`

### Specific Connection Failing
- Check if that person's Docker is running
- Verify IP address in `.env` is correct
- Check firewall on their machine
- Try `telnet 192.168.1.104 3306` to test port

### Slow Queries
- Normal for remote databases (network latency)
- Use caching
- Minimize cross-database joins
- Consider batch operations

### Access Denied Errors
- MySQL/MariaDB need remote access enabled
- See QUICK_START_GUIDE.md for fix
- Ensure passwords match in `.env`

## Security Considerations

**Current Setup (Development Only):**
- Default passwords (`password`)
- No encryption
- No authentication between services
- Databases exposed to entire network

**For Production:**
- Use strong passwords
- Implement SSL/TLS for database connections
- Add VPN or network isolation
- Implement API authentication
- Use environment-specific configs
- Enable database user access restrictions

## Performance Tips

1. **Cache aggressively** - Remote queries are slower
2. **Batch operations** - Reduce round trips
3. **Index foreign keys** - Faster lookups
4. **Use connection pooling** - Laravel handles this
5. **Monitor latency** - Use `test-connections.php`

## Team Workflow

### Daily Routine

**Morning (Each person):**
```bash
# Start your database
docker-compose -f docker-compose-yourname.yml up -d

# Verify it's running
docker ps

# Start Laravel
php artisan serve
```

**During Work:**
- You can read/write any table
- Only modify schema of tables you own
- Coordinate before major changes
- Keep Docker running for team access

**End of Day:**
```bash
# Stop Laravel (Ctrl+C)

# Optionally stop Docker (data persists)
docker-compose -f docker-compose-yourname.yml down

# OR leave running for continuous team access
```

### Git Workflow

**What to commit:**
- Model changes
- Migration files (for your tables)
- Controller changes
- Shared documentation updates

**What NOT to commit:**
- `.env` file (IP addresses are machine-specific)
- Docker volumes
- Database dumps (coordinate separately)

**Sharing migrations:**
1. Create migration: `php artisan make:migration ...`
2. Commit to Git
3. Team pulls changes
4. Each person runs migration on their database:
   ```bash
   php artisan migrate --database=yourconnection
   ```

## Advantages of This Architecture

✅ **True Distribution** - Each team member fully controls their domain
✅ **Resilience** - One database down doesn't crash entire system
✅ **Technology Freedom** - Use best database for each module
✅ **Scalability** - Easy to add more team members/services
✅ **Learning** - Experience real distributed system challenges
✅ **Team Autonomy** - Parallel development without conflicts

## Challenges & Solutions

| Challenge | Solution |
|-----------|----------|
| Network dependency | Implement caching & fallbacks |
| No foreign keys | Application-level validation |
| Slower queries | Cache frequently accessed data |
| IP changes | DHCP reservation or hostname mapping |
| Coordination | Good communication & documentation |

## Resources

- **Setup Guide**: `QUICK_START_GUIDE.md`
- **Technical Details**: `NETWORK_DISTRIBUTED_SETUP.md`
- **Original Plan**: `DISTRIBUTED_DATABASE_PLAN.md`
- **API Architecture**: `DISTRIBUTED_API_ARCHITECTURE_PLAN.md`
- **Test Script**: `test-connections.php`

## Support

**Test connectivity:**
```bash
php test-connections.php
```

**Check Laravel logs:**
```bash
php artisan pail
```

**Check Docker logs:**
```bash
docker logs container-name
```

**Database connection test:**
```bash
php artisan tinker
DB::connection('hannah')->getPdo();
```

## Summary

You now have a **true distributed database system** where:
- 5 databases run on 5 different machines
- All machines are connected via network
- One Laravel application connects to all databases
- Team members can perform cross-machine CRUD operations
- Data is distributed but universally accessible

This simulates real-world microservices architecture and teaches distributed system concepts while building your charity management platform.

**Ready to start?** → See `QUICK_START_GUIDE.md`

Good luck! 🚀
