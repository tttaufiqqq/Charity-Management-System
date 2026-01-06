# 🔧 Migration Update Guide

**Complete instructions for updating all 19 migrations for heterogeneous distributed database**

## ✅ Already Updated (3/19)

1. ✅ `0001_01_01_000000_create_users_table.php` - Izzhilmy (PostgreSQL)
2. ✅ `0001_01_01_000001_create_cache_table.php` - Izzhilmy (PostgreSQL)
3. ✅ `2025_11_25_173120_create_volunteer.php` - Sashvini (MariaDB) - **1 cross-DB FK removed**
4. ✅ `2025_11_25_173415_create_donation_allocation.php` - Hannah (MySQL) - **2 cross-DB FKs removed**

**Cross-DB FKs Removed So Far: 3/11**

---

## 📋 Remaining Updates Needed (16 migrations)

### **IZZHILMY (PostgreSQL) - 2 remaining**

#### 1. `0001_01_01_000002_create_jobs_table.php`
**Changes:**
- Add `protected $connection = 'izzhilmy';`
- Wrap all `Schema::create()` and `Schema::dropIfExists()` with `Schema::connection('izzhilmy')`
- **No cross-DB FKs to remove**

#### 2. `2025_11_25_165753_create_permission_tables.php` (Spatie roles)
**Changes:**
- Add `protected $connection = 'izzhilmy';`
- Wrap ALL Schema calls with `Schema::connection('izzhilmy')`
- **No cross-DB FKs to remove**

---

### **SASHVINI (MariaDB) - 3 remaining**

#### 3. `2025_11_25_173201_create_skill.php`
**Changes:**
- Add `protected $connection = 'sashvini';`
- Wrap with `Schema::connection('sashvini')`
- **No cross-DB FKs** (no foreign keys at all)

#### 4. `2025_11_25_173230_create_volunteer_skill.php`
**Changes:**
- Add `protected $connection = 'sashvini';`
- Wrap with `Schema::connection('sashvini')`
- **Keep FK constraints** (Volunteer_ID and Skill_ID are both in sashvini - same database!)

```php
// KEEP these FKs - they're same database:
$table->foreign('Skill_ID')->references('Skill_ID')->on('skill');
$table->foreign('Volunteer_ID')->references('Volunteer_ID')->on('volunteer');
```

#### 5. `2025_11_25_173619_create_event_participation.php`
**Changes:**
- Add `protected $connection = 'sashvini';`
- Wrap with `Schema::connection('sashvini')`
- **REMOVE cross-DB FK**: `Event_ID` → event table (izzati database)
- **KEEP same-DB FK**: `Volunteer_ID` → volunteer table (sashvini database)

```php
// Before:
$table->foreignId('Event_ID')->constrained('event', 'Event_ID')->onDelete('cascade');
$table->foreignId('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');

// After:
// ⚠️ Cross-database reference: Event_ID references event table in izzati database
$table->unsignedBigInteger('Event_ID')->index();
// ✅ Same database FK - KEEP:
$table->foreign('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');
```

#### 6. `2025_12_18_234841_add_role_to_event_participation_table.php`
**Changes:**
- Add `protected $connection = 'sashvini';`
- Wrap with `Schema::connection('sashvini')`
- **REMOVE cross-DB FK**: `Role_ID` → event_role table (izzati database)

```php
// Before:
$table->foreignId('Role_ID')->nullable()->constrained('event_role', 'Role_ID');

// After:
// ⚠️ Cross-database reference: Role_ID references event_role table in izzati database
$table->unsignedBigInteger('Role_ID')->nullable()->index();
```

---

### **IZZATI (PostgreSQL) - 5 remaining**

#### 7. `2025_11_25_173101_create_organization.php`
**Changes:**
- Add `protected $connection = 'izzati';`
- Wrap with `Schema::connection('izzati')`
- **REMOVE cross-DB FK**: `Organizer_ID` → users table (izzhilmy database)

```php
// Before:
$table->foreignId('Organizer_ID')->constrained('users')->onDelete('cascade');

// After:
// ⚠️ Cross-database reference: Organizer_ID references users table in izzhilmy database
$table->unsignedBigInteger('Organizer_ID')->index();
```

#### 8. `2025_11_25_173254_create_campaign.php`
**Changes:**
- Add `protected $connection = 'izzati';`
- Wrap with `Schema::connection('izzati')`
- **KEEP same-DB FK**: `Organization_ID` → organization table (izzati database)

```php
// KEEP this FK - same database:
$table->foreign('Organization_ID')->references('Organization_ID')->on('organization');
```

#### 9. `2025_11_25_173447_create_event.php`
**Changes:**
- Add `protected $connection = 'izzati';`
- Wrap with `Schema::connection('izzati')`
- **KEEP same-DB FK**: `Organizer_ID` → organization table (izzati database)

```php
// KEEP this FK - same database:
$table->foreign('Organizer_ID')->references('Organization_ID')->on('organization');
```

#### 10. `2025_12_18_234839_create_event_role_table.php`
**Changes:**
- Add `protected $connection = 'izzati';`
- Wrap with `Schema::connection('izzati')`
- **KEEP same-DB FK**: `Event_ID` → event table (izzati database)

```php
// KEEP this FK - same database:
$table->foreign('Event_ID')->references('Event_ID')->on('event');
```

#### 11. `2026_01_02_214059_create_campaign_recipient_suggestions_table.php`
**Changes:**
- Add `protected $connection = 'izzati';`
- Wrap with `Schema::connection('izzati')`
- **REMOVE 2 cross-DB FKs**:
  - `Recipient_ID` → recipient table (adam database)
  - `Suggested_By` → users table (izzhilmy database)
- **KEEP same-DB FK**: `Campaign_ID` → campaign table (izzati database)

```php
// Before:
$table->foreignId('Campaign_ID')->constrained('campaign', 'Campaign_ID');
$table->foreignId('Recipient_ID')->constrained('recipient', 'Recipient_ID');
$table->foreignId('Suggested_By')->constrained('users');

// After:
// ✅ Same database FK - KEEP:
$table->foreign('Campaign_ID')->constrained('campaign', 'Campaign_ID');
// ⚠️ Cross-database references - REMOVE FK constraints:
$table->unsignedBigInteger('Recipient_ID')->index();
$table->unsignedBigInteger('Suggested_By')->index();
```

---

### **HANNAH (MySQL) - 2 remaining**

#### 12. `2025_11_25_173015_create_donor.php`
**Changes:**
- Add `protected $connection = 'hannah';`
- Wrap with `Schema::connection('hannah')`
- **REMOVE cross-DB FK**: `User_ID` → users table (izzhilmy database)

```php
// Before:
$table->foreignId('User_ID')->constrained('users')->onDelete('cascade');

// After:
// ⚠️ Cross-database reference: User_ID references users table in izzhilmy database
$table->unsignedBigInteger('User_ID')->index();
```

#### 13. `2025_11_25_173320_create_donation.php`
**Changes:**
- Add `protected $connection = 'hannah';`
- Wrap with `Schema::connection('hannah')`
- **REMOVE cross-DB FK**: `Campaign_ID` → campaign table (izzati database)
- **KEEP same-DB FK**: `Donor_ID` → donor table (hannah database)

```php
// Before:
$table->foreign('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');
$table->foreign('Campaign_ID')->constrained('campaign', 'Campaign_ID')->onDelete('cascade');

// After:
// ✅ Same database FK - KEEP:
$table->foreign('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');
// ⚠️ Cross-database reference - REMOVE FK:
$table->unsignedBigInteger('Campaign_ID')->index();
```

---

### **ADAM (MySQL) - 2 remaining**

#### 14. `2025_11_25_173035_create_public.php`
**Changes:**
- Add `protected $connection = 'adam';`
- Wrap with `Schema::connection('adam')`
- **REMOVE cross-DB FK**: `User_ID` → users table (izzhilmy database)

```php
// Before:
$table->foreignId('User_ID')->constrained('users')->onDelete('cascade');

// After:
// ⚠️ Cross-database reference: User_ID references users table in izzhilmy database
$table->unsignedBigInteger('User_ID')->index();
```

#### 15. `2025_11_25_173345_create_recipient.php`
**Changes:**
- Add `protected $connection = 'adam';`
- Wrap with `Schema::connection('adam')`
- **KEEP same-DB FK**: `Public_ID` → public table (adam database)

```php
// KEEP this FK - same database:
$table->foreign('Public_ID')->references('Public_ID')->on('public');
```

---

## 📊 Summary

| Database | Migrations | Cross-DB FKs Remaining |
|----------|-----------|----------------------|
| Izzhilmy | 2 | 0 |
| Sashvini | 3 | 2 |
| Izzati | 5 | 3 |
| Hannah | 2 | 2 |
| Adam | 2 | 1 |
| **Completed** | **4** | **3 removed** |
| **TOTAL REMAINING** | **16** | **8** |

---

## 🚀 Quick Update Pattern

For each migration:

1. **Add connection property:**
```php
protected $connection = 'database_name';
```

2. **Wrap all Schema calls:**
```php
// Before:
Schema::create('table', function...);

// After:
Schema::connection('database_name')->create('table', function...);
```

3. **Remove cross-DB foreign keys:**
```php
// Before:
$table->foreignId('User_ID')->constrained('users')->onDelete('cascade');

// After:
// ⚠️ Cross-database reference: User_ID references users table in different_database
$table->unsignedBigInteger('User_ID')->index();
```

4. **Keep same-DB foreign keys:**
```php
// If both tables are in the SAME database connection, KEEP the FK:
$table->foreign('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');
```

---

**Date Created**: 2026-01-07
**Status**: 4/19 migrations updated, 8 cross-DB FKs remaining to remove
