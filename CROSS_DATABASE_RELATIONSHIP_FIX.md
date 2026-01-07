# Cross-Database Relationship Fix

## Problem

When logging in as **volunteer**, **donor**, **organizer**, or **public** roles, the application threw errors:

```
SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "event_participation" does not exist
(Connection: izzati, SQL: select count(*) as aggregate from "event" inner join "event_participation"...)
```

## Root Cause

Eloquent's `belongsToMany` relationship was trying to **JOIN across different databases**:

- `volunteer` table → sashvini database (MariaDB)
- `event_participation` table (pivot) → sashvini database (MariaDB)
- `event` table → izzati database (PostgreSQL)

When executing `$volunteer->events()->count()`, Eloquent attempted to perform a JOIN operation on the `izzati` connection, but the `event_participation` table doesn't exist in izzati - it's in sashvini.

**You cannot JOIN across different database servers.**

## Solution

Replace cross-database JOINs with **application-level queries**:

### ❌ BEFORE (Cross-Database JOIN - FAILS):
```php
// This tries to JOIN event (izzati) with event_participation (sashvini)
$totalEvents = $volunteer->events()->count();
$completedEvents = $volunteer->events()->where('event.Status', 'Completed')->count();
```

### ✅ AFTER (Application-Level Query - WORKS):
```php
// Step 1: Get event IDs from event_participation (sashvini database)
$eventIds = $volunteer->eventParticipations()->pluck('Event_ID');

// Step 2: Query events directly with those IDs (izzati database)
$totalEvents = \App\Models\Event::whereIn('Event_ID', $eventIds)->count();
$completedEvents = \App\Models\Event::whereIn('Event_ID', $eventIds)
    ->where('Status', 'Completed')
    ->count();
```

## Files Fixed

### 1. **ProfileController.php** (Lines 29-37)
- Fixed volunteer statistics queries
- Changed from `$volunteer->events()` to application-level queries

### 2. **VolunteerController.php** (Lines 302-324, 515-531)
- Fixed volunteer dashboard statistics
- Fixed volunteer profile statistics
- Changed from cross-database JOINs to separate queries

### 3. **welcome.blade.php** (Lines 391-411)
- Fixed volunteer dashboard preview
- Changed from `$volunteer->events()` to application-level queries

## Pattern to Follow

When you need to query across databases:

```php
// DON'T DO THIS (causes cross-database JOIN):
$volunteer->events()->where('Status', 'Active')->count();

// DO THIS INSTEAD (application-level query):
$eventIds = $volunteer->eventParticipations()->pluck('Event_ID');
$activeEvents = Event::whereIn('Event_ID', $eventIds)
    ->where('Status', 'Active')
    ->count();
```

## Why This Works

1. **First Query** (sashvini database):
   - Query: `SELECT Event_ID FROM event_participation WHERE Volunteer_ID = ?`
   - Returns: `[1, 5, 10, 15]` (example event IDs)

2. **Second Query** (izzati database):
   - Query: `SELECT * FROM event WHERE Event_ID IN (1, 5, 10, 15)`
   - Returns: Event records from izzati database

3. **No JOIN needed** - queries executed separately on their respective databases

## Relationships That Work Without JOINs

These relationships stay within one database and work fine:

✅ `$volunteer->skills()` - Both in sashvini
✅ `$volunteer->eventParticipations()` - Both in sashvini
✅ `$organization->campaigns()` - Both in izzati
✅ `$organization->events()` - Both in izzati
✅ `$donor->donations()` - Both in hannah
✅ `$campaign->donations()` - Campaign in izzati, but Donation queries don't JOIN

## Relationships That Need Special Handling

These cross databases and require application-level queries:

⚠️ `$volunteer->events()` - volunteer (sashvini) → event (izzati) via event_participation (sashvini)
⚠️ `$event->volunteers()` - event (izzati) → volunteer (sashvini) via event_participation (sashvini)

**Solution**: Use the pivot table relationship first, then query the target:

```php
// Get pivot data (stays in one database)
$participations = $volunteer->eventParticipations()->get();

// Extract IDs
$eventIds = $participations->pluck('Event_ID');

// Query target database
$events = Event::whereIn('Event_ID', $eventIds)->get();
```

## Testing

After the fix, all roles should work:

```bash
# Clear cache and restart server
composer cache-clear
composer dev

# Test login with:
- Volunteer role ✅
- Donor role ✅
- Organizer role ✅
- Public role ✅
- Admin role ✅
```

## Performance Considerations

**Application-level queries use 2 database calls instead of 1 JOIN**:

- **JOIN approach** (doesn't work cross-database): 1 query, but fails
- **Application-level** (works): 2 queries, succeeds

For small datasets (typical in this app), the performance difference is negligible. For large datasets, consider:
- Caching frequently accessed data
- Eager loading to reduce query counts
- Indexing foreign keys (Event_ID, Volunteer_ID, etc.)

## Key Takeaway

**In a distributed database architecture**:
- ✅ Relationships within same database: Use normal Eloquent relationships
- ⚠️ Relationships across databases: Use application-level queries (2-step approach)

**Always**:
1. Query the pivot/junction table in its database
2. Extract IDs
3. Query the target table in its database

This pattern prevents cross-database JOINs while maintaining data integrity.

---

**Fixed**: 2026-01-07
**Issue**: Cross-database JOINs causing "table does not exist" errors
**Solution**: Application-level queries with separate database calls
