# 🗺️ Migration Distribution Map

**Charity-Izz Heterogeneous Database Migrations**

## Database Assignment Strategy

### **Connection 1: Izzhilmy (PostgreSQL)**
System tables + User authentication

| Migration File | Table | Has Cross-DB FK? |
|----------------|-------|------------------|
| `0001_01_01_000000_create_users_table.php` | users, password_reset_tokens, sessions | ❌ No |
| `0001_01_01_000001_create_cache_table.php` | cache, cache_locks | ❌ No |
| `0001_01_01_000002_create_jobs_table.php` | jobs, job_batches, failed_jobs | ❌ No |
| `2025_11_25_165753_create_permission_tables.php` | roles, permissions, etc. | ❌ No |

**Total: 4 migrations**

---

### **Connection 2: Sashvini (MariaDB)**
Volunteer management system

| Migration File | Table | Has Cross-DB FK? |
|----------------|-------|------------------|
| `2025_11_25_173120_create_volunteer.php` | volunteer | ⚠️ YES → User_ID (izzhilmy) |
| `2025_11_25_173201_create_skill.php` | skill | ❌ No |
| `2025_11_25_173230_create_volunteer_skill.php` | volunteer_skill | ❌ No (same DB) |
| `2025_11_25_173619_create_event_participation.php` | event_participation | ⚠️ YES → Event_ID (izzati) |
| `2025_12_18_234841_add_role_to_event_participation_table.php` | (alter) | ⚠️ YES → Role_ID (izzati) |

**Total: 5 migrations** | **Cross-DB FKs to remove: 3**

---

### **Connection 3: Izzati (PostgreSQL)**
Campaign & event operations

| Migration File | Table | Has Cross-DB FK? |
|----------------|-------|------------------|
| `2025_11_25_173101_create_organization.php` | organization | ⚠️ YES → Organizer_ID (izzhilmy) |
| `2025_11_25_173254_create_campaign.php` | campaign | ❌ No (Organization same DB) |
| `2025_11_25_173447_create_event.php` | event | ❌ No (Organization same DB) |
| `2025_12_18_234839_create_event_role_table.php` | event_role | ❌ No (Event same DB) |
| `2026_01_02_214059_create_campaign_recipient_suggestions_table.php` | campaign_recipient_suggestions | ⚠️ YES → Recipient_ID (adam), Suggested_By (izzhilmy) |

**Total: 5 migrations** | **Cross-DB FKs to remove: 3**

---

### **Connection 4: Hannah (MySQL)**
Financial transactions

| Migration File | Table | Has Cross-DB FK? |
|----------------|-------|------------------|
| `2025_11_25_173015_create_donor.php` | donor | ⚠️ YES → User_ID (izzhilmy) |
| `2025_11_25_173320_create_donation.php` | donation | ⚠️ YES → Campaign_ID (izzati) |
| `2025_11_25_173415_create_donation_allocation.php` | donation_allocation | ⚠️ YES → Campaign_ID (izzati), Recipient_ID (adam) |

**Total: 3 migrations** | **Cross-DB FKs to remove: 4**

---

### **Connection 5: Adam (MySQL)**
Public profiles & recipients

| Migration File | Table | Has Cross-DB FK? |
|----------------|-------|------------------|
| `2025_11_25_173035_create_public.php` | public | ⚠️ YES → User_ID (izzhilmy) |
| `2025_11_25_173345_create_recipient.php` | recipient | ❌ No (Public_ID same DB) |

**Total: 2 migrations** | **Cross-DB FKs to remove: 1**

---

## Summary

| Database | Migrations | Cross-DB FKs to Remove |
|----------|-----------|----------------------|
| Izzhilmy (PostgreSQL) | 4 | 0 |
| Sashvini (MariaDB) | 5 | 3 |
| Izzati (PostgreSQL) | 5 | 3 |
| Hannah (MySQL) | 3 | 4 |
| Adam (MySQL) | 2 | 1 |
| **TOTAL** | **19** | **11** |

---

## Migration Execution Order

When databases are online, run migrations in this order to respect dependencies:

1. **Izzhilmy** - Users, roles, permissions (no dependencies)
2. **Adam** - Public profiles (depends on izzhilmy users)
3. **Sashvini** - Volunteers, skills (depends on izzhilmy users)
4. **Izzati** - Organizations, campaigns, events (depends on izzhilmy users)
5. **Hannah** - Donors, donations (depends on izzhilmy users & izzati campaigns)

**Command:**
```bash
php artisan db:fresh-all --seed
```

This command handles the order automatically.

---

## Cross-Database Foreign Keys to Remove

### Sashvini (MariaDB)
```php
// volunteer table
$table->foreign('User_ID')->references('id')->on('users'); // ❌ REMOVE

// event_participation table
$table->foreign('Event_ID')->references('Event_ID')->on('event'); // ❌ REMOVE
$table->foreign('Role_ID')->references('Role_ID')->on('event_role'); // ❌ REMOVE
```

### Izzati (PostgreSQL)
```php
// organization table
$table->foreign('Organizer_ID')->references('id')->on('users'); // ❌ REMOVE

// campaign_recipient_suggestions table
$table->foreign('Recipient_ID')->references('Recipient_ID')->on('recipient'); // ❌ REMOVE
$table->foreign('Suggested_By')->references('id')->on('users'); // ❌ REMOVE
```

### Hannah (MySQL)
```php
// donor table
$table->foreign('User_ID')->references('id')->on('users'); // ❌ REMOVE

// donation table
$table->foreign('Campaign_ID')->references('Campaign_ID')->on('campaign'); // ❌ REMOVE

// donation_allocation table
$table->foreign('Campaign_ID')->references('Campaign_ID')->on('campaign'); // ❌ REMOVE
$table->foreign('Recipient_ID')->references('Recipient_ID')->on('recipient'); // ❌ REMOVE
```

### Adam (MySQL)
```php
// public table
$table->foreign('User_ID')->references('id')->on('users'); // ❌ REMOVE
```

---

**Generated for**: Heterogeneous Distributed Database Implementation
**Date**: 2026-01-07
