# Cache Management Automation

This document explains the automated cache management system implemented to prevent database connection issues in the Charity-Izz distributed database architecture.

---

## Problem Summary

The application uses a heterogeneous distributed database architecture with 5 databases:
- **Izzhilmy** (PostgreSQL) - Users, roles, permissions, sessions, cache
- **Hannah** (MySQL) - Donations, donors, donation allocations
- **Sashvini** (MariaDB) - Volunteers, skills, event participation
- **Izzati** (PostgreSQL) - Organizations, campaigns, events
- **Adam** (MySQL) - Public profiles, recipients

**Issue**: When `.env` database configuration is modified, Laravel caches the old configuration, causing "table does not exist" errors because queries are sent to the wrong database.

**Root Cause**: PHP server process caches configuration and model connections until restarted.

---

## Automated Solutions Implemented

### 1. Comprehensive Cache Clearing Command

**Command**: `php artisan cache:clear-all`

**Location**: `app/Console/Commands/ClearAllCaches.php`

**What it clears**:
- Configuration cache (`config:clear`)
- Route cache (`route:clear`)
- Compiled Blade views (`view:clear`)
- Application cache (`cache:clear`)
- Event cache (`event:clear`)

**Usage**:
```bash
# Standard output
php artisan cache:clear-all

# Silent mode (no output)
php artisan cache:clear-all --silent
```

---

### 2. Composer Scripts

**Added Scripts**:

```bash
# Clear all Laravel caches
composer cache-clear

# Start development servers (auto-clears cache first)
composer dev

# Fresh migration with seed (clears cache first)
composer fresh
```

**Location**: `composer.json` scripts section

**How it works**:
- `composer dev` - Automatically runs `cache-clear` before starting servers
- `composer fresh` - Automatically runs `cache-clear` before fresh migration
- `composer cache-clear` - Manual cache clearing

---

### 3. Model Connection Verification

All models have explicit `protected $connection` properties:

| Model | Database | Connection |
|-------|----------|-----------|
| User, Role, Permission | Izzhilmy | `izzhilmy` |
| Donor, Donation, DonationAllocation | Hannah | `hannah` |
| Volunteer, Skill, VolunteerSkill, EventParticipation | Sashvini | `sashvini` |
| Organization, Campaign, Event, EventRole | Izzati | `izzati` |
| PublicProfile, Recipient | Adam | `adam` |

**Verified**: All 17 models have explicit connections configured.

---

### 4. Environment Configuration

**Default Connection**: `izzhilmy` (central infrastructure database)

**.env Configuration**:
```bash
# Default connection (used by framework infrastructure)
DB_CONNECTION=izzhilmy
DB_HOST=10.135.118.146
DB_PORT=5432
DB_DATABASE=workshop
DB_USERNAME=postgres
DB_PASSWORD=password

# Distributed databases (DB1-DB5) remain unchanged
# Models use explicit connections to these databases
```

**Important**: The default `DB_CONNECTION` is only used for:
- Laravel framework infrastructure (migrations table tracking)
- Models without explicit `$connection` property (none in this app)
- Fallback when connection not specified

All application models explicitly specify their connections via `protected $connection = 'database_name'`.

---

## When to Clear Cache

### **Automatic Clearing** (handled for you):
✅ When running `composer dev`
✅ When running `composer fresh`

### **Manual Clearing Required**:
⚠️ After modifying `.env` database configuration
⚠️ After updating `config/database.php`
⚠️ After adding/modifying model `$connection` properties
⚠️ When experiencing "table does not exist" errors
⚠️ After pulling changes that modify configurations

---

## Testing the System

### Test 1: Verify Model Connections

```bash
php artisan tinker
```

```php
// Test each model's connection
$user = new \App\Models\User();
echo "User: " . $user->getConnectionName() . "\n"; // Should output: izzhilmy

$campaign = new \App\Models\Campaign();
echo "Campaign: " . $campaign->getConnectionName() . "\n"; // Should output: izzati

$donor = new \App\Models\Donor();
echo "Donor: " . $donor->getConnectionName() . "\n"; // Should output: hannah

$volunteer = new \App\Models\Volunteer();
echo "Volunteer: " . $volunteer->getConnectionName() . "\n"; // Should output: sashvini

$publicProfile = new \App\Models\PublicProfile();
echo "PublicProfile: " . $publicProfile->getConnectionName() . "\n"; // Should output: adam
```

### Test 2: Login with Different Roles

```bash
# Start development server (auto-clears cache)
composer dev
```

Then test:
1. ✅ Login as **donor** → Should work without errors
2. ✅ Login as **volunteer** → Should work without errors
3. ✅ Login as **organizer** → Should work without errors
4. ✅ Login as **public** → Should work without errors
5. ✅ Login as **admin** → Should work without errors

### Test 3: Cache Clearing Command

```bash
# Test comprehensive cache clearing
composer cache-clear

# Should output:
# 🧹 Clearing all Laravel caches...
#   ✓ Cleared Configuration cache
#   ✓ Cleared Route cache
#   ✓ Cleared Compiled views
#   ✓ Cleared Application cache
#   ✓ Cleared Event cache
# ✅ All caches cleared successfully!
# ⚠️  Remember to restart your development server for changes to take effect.
```

### Test 4: Development Server Auto-Cache-Clear

```bash
# Stop current server (Ctrl+C)

# Modify .env (change any value)

# Start server - should auto-clear cache
composer dev

# Should see cache clearing output before servers start
```

---

## Troubleshooting

### Issue: "Table does not exist" errors after login

**Solution**:
```bash
# 1. Clear all caches
composer cache-clear

# 2. Restart development server
# Press Ctrl+C to stop current server
composer dev
```

### Issue: Different roles work only with different DB_CONNECTION values

**Solution**: This was the original problem. It's now fixed because:
1. All models have explicit `$connection` properties
2. Default connection set to `izzhilmy` (infrastructure database)
3. Auto-cache-clearing prevents stale configuration

### Issue: Cache clearing doesn't fix the problem

**Solution**: Restart the PHP server process:
```bash
# Press Ctrl+C to stop composer dev

# Start fresh
composer dev
```

The PHP server caches configuration in memory. Clearing Laravel's cache is not enough - you MUST restart the server.

---

## Developer Workflow

### Daily Development:
```bash
# Start working
composer dev  # Auto-clears cache

# Make changes, test, commit
```

### After Pulling Changes:
```bash
# If .env or config changed
composer cache-clear
composer dev  # Restart server
```

### After Modifying Database Configuration:
```bash
# Always run these two commands
composer cache-clear
composer dev  # Restart server
```

---

## Technical Details

### Cache Storage Locations

All caches are stored in the **Izzhilmy database** (centralized infrastructure):
- **Config cache**: `bootstrap/cache/config.php` (file-based)
- **Route cache**: `bootstrap/cache/routes-v7.php` (file-based)
- **View cache**: `storage/framework/views/*` (file-based)
- **Application cache**: `izzhilmy.cache` table (database)
- **Event cache**: `bootstrap/cache/events.php` (file-based)

### Why Automatic Cache Clearing Works

1. **`composer dev` hook**: Runs `cache-clear` script before starting servers
2. **Fresh server process**: New PHP process loads fresh configuration from `.env`
3. **Explicit model connections**: Models don't rely on default connection
4. **Spatie config**: Permission models configured with `database_connection = 'izzhilmy'`

---

## Files Modified

1. `app/Console/Commands/ClearAllCaches.php` - New command
2. `composer.json` - Added scripts: `cache-clear`, updated `dev` and `fresh`
3. `CLAUDE.md` - Added cache management documentation
4. `.env` - Set `DB_CONNECTION=izzhilmy` as default

---

## Prevention Checklist

Before committing changes:
- [ ] All models have `protected $connection` property
- [ ] `.env` has `DB_CONNECTION=izzhilmy`
- [ ] New migrations specify connection: `Schema::connection('izzati')->table(...)`
- [ ] Tested with all user roles
- [ ] Documented any new cache-sensitive configurations

---

## Summary

**Problem**: Cache causing wrong database connections
**Solution**: Automated cache clearing + explicit model connections
**Result**: All user roles work without manual `.env` modifications

**Key Commands**:
- `composer cache-clear` - Clear caches manually
- `composer dev` - Start server (auto-clears cache)
- `composer fresh` - Fresh migration (auto-clears cache)
- `php artisan cache:clear-all` - Direct cache clearing

**Prevention**: Run `composer dev` to start servers - it auto-clears cache before starting!

---

**Generated**: 2026-01-07
**Platform**: Charity-Izz Distributed Database Architecture
