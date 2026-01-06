# 🌐 Heterogeneous Distributed Database Architecture

**Charity-Izz Platform** - Full Distribution Implementation

## 📊 Database Distribution

### Architecture Overview
```
┌──────────────────────────────────────────────────────────────┐
│                   Charity-Izz Application                     │
│                    Laravel 12 + Livewire                      │
└──┬────────┬────────┬────────┬────────┬──────────────────────┘
   │        │        │        │        │
   ▼        ▼        ▼        ▼        ▼
┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐
│Izzhil│ │Sashvi│ │Izzati│ │Hannah│ │ Adam │
│  my  │ │  ni  │ │      │ │      │ │      │
│  PG  │ │MariaD│ │  PG  │ │MySQL │ │MySQL │
└──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘
   │        │        │        │        │
 Users   Volunt  Campaign  Donor   Public
 Roles     Skill   Event    Donat  Recip
 Admin            Org
```

## 🗄️ Database Connections

### 1. **Izzhilmy** (PostgreSQL) - Authentication & Authorization
- **Host**: 10.135.118.146:5432
- **Database**: `charity_auth`
- **Engine**: PostgreSQL
- **Purpose**: User management, authentication, role-based access control

**Tables:**
- `users` - User accounts
- `roles` - User roles (admin, organizer, volunteer, donor, public)
- `permissions` - Permission definitions
- `password_reset_tokens` - Password reset functionality
- `sessions` - User session management

---

### 2. **Sashvini** (MariaDB) - Volunteer Management
- **Host**: 10.135.118.65:3306
- **Database**: `charity_volunteers`
- **Engine**: MariaDB (using MySQL driver)
- **Purpose**: Volunteer profiles, skills, and event participation tracking

**Tables:**
- `volunteer` - Volunteer profiles and information
- `skill` - Available skills catalog
- `volunteer_skill` - Volunteer-to-skill mapping (many-to-many pivot)
- `event_participation` - Volunteer event registrations and hours tracking

---

### 3. **Izzati** (PostgreSQL) - Campaign & Event Operations
- **Host**: 10.135.118.61:5432
- **Database**: `charity_operations`
- **Engine**: PostgreSQL
- **Purpose**: Core charity operations, campaigns, events, organizations

**Tables:**
- `organization` - Charity organization profiles
- `campaign` - Fundraising campaigns
- `event` - Volunteer events
- `event_role` - Event roles for volunteers
- `campaign_recipient_suggestions` - AI/manual recipient suggestions

---

### 4. **Hannah** (MySQL) - Financial Transactions
- **Host**: 10.135.118.165:3306
- **Database**: `charity_finance`
- **Engine**: MySQL
- **Purpose**: Financial tracking, donations, fund allocations

**Tables:**
- `donor` - Donor profiles
- `donation` - Individual donations with receipt tracking
- `donation_allocation` - Fund allocation from campaigns to recipients

---

### 5. **Adam** (MySQL) - Public & Recipient Data
- **Host**: 10.135.118.171:3306
- **Database**: `charity_public`
- **Engine**: MySQL
- **Purpose**: Public profiles and recipient management

**Tables:**
- `public` - Public user profiles (non-authenticated users)
- `recipient` - Recipient applications and approvals

---

## 🔗 Cross-Database Relationships

### Critical Cross-Database Links

#### **User → Profile Relationships** (Izzhilmy → All)
```php
User (izzhilmy) → Donor (hannah)
User (izzhilmy) → Volunteer (sashvini)
User (izzhilmy) → Organization (izzati)
User (izzhilmy) → PublicProfile (adam)
```

#### **Campaign → Financial** (Izzati → Hannah)
```php
Campaign (izzati) → Donation (hannah)
Campaign (izzati) → DonationAllocation (hannah)
```

#### **Event → Volunteer** (Izzati → Sashvini)
```php
Event (izzati) → EventParticipation (sashvini)
Event (izzati) → Volunteer (sashvini) [via event_participation pivot]
```

#### **Allocation → Recipients** (Hannah → Adam)
```php
DonationAllocation (hannah) → Recipient (adam)
```

---

## 📋 Model Connection Mapping

### Izzhilmy Models (PostgreSQL)
| Model | Table | Cross-DB Relationships |
|-------|-------|----------------------|
| `User` | `users` | → Donor (hannah)<br>→ Volunteer (sashvini)<br>→ Organization (izzati)<br>→ PublicProfile (adam) |

### Sashvini Models (MariaDB)
| Model | Table | Cross-DB Relationships |
|-------|-------|----------------------|
| `Volunteer` | `volunteer` | ← User (izzhilmy)<br>→ Event (izzati) |
| `Skill` | `skill` | None |
| `EventParticipation` | `event_participation` | → Event (izzati) |

### Izzati Models (PostgreSQL)
| Model | Table | Cross-DB Relationships |
|-------|-------|----------------------|
| `Organization` | `organization` | ← User (izzhilmy) |
| `Campaign` | `campaign` | → Donation (hannah)<br>→ DonationAllocation (hannah)<br>→ Recipient (adam via hannah pivot) |
| `Event` | `event` | → EventParticipation (sashvini)<br>→ Volunteer (sashvini) |
| `EventRole` | `event_role` | → Volunteer (sashvini) |
| `CampaignRecipientSuggestion` | `campaign_recipient_suggestions` | → Recipient (adam)<br>← User (izzhilmy) |

### Hannah Models (MySQL)
| Model | Table | Cross-DB Relationships |
|-------|-------|----------------------|
| `Donor` | `donor` | ← User (izzhilmy) |
| `Donation` | `donation` | ← Campaign (izzati) |
| `DonationAllocation` | `donation_allocation` | ← Campaign (izzati)<br>→ Recipient (adam) |

### Adam Models (MySQL)
| Model | Table | Cross-DB Relationships |
|-------|-------|----------------------|
| `PublicProfile` | `public` | ← User (izzhilmy) |
| `Recipient` | `recipient` | ← DonationAllocation (hannah)<br>← Campaign (izzati via hannah pivot) |

---

## ⚠️ Important Constraints & Limitations

### **1. No Foreign Key Constraints Across Databases**
❌ Cannot enforce referential integrity between databases
✅ Must validate in application layer

### **2. No JOIN Queries Across Databases**
❌ `Campaign::with('donations')` uses multiple queries, not JOIN
✅ Laravel handles this transparently with `setConnection()`

### **3. No Distributed Transactions**
❌ Cannot rollback across multiple databases atomically
✅ Implement saga pattern or compensating transactions

### **4. Manual Data Consistency**
❌ No automatic cascade deletes across databases
✅ Use model events to sync deletions

---

## 🛠️ Management Commands

### **Fresh Migrate All Databases**
```bash
php artisan db:fresh-all
```
Drops and recreates all tables across all 5 databases.

### **Fresh Migrate with Seeding**
```bash
php artisan db:fresh-all --seed
```
Fresh migration + run seeders for test data.

### **Migrate Specific Database**
```bash
php artisan migrate --database=izzhilmy
php artisan migrate --database=sashvini
php artisan migrate --database=izzati
php artisan migrate --database=hannah
php artisan migrate --database=adam
```

---

## 📖 Usage Examples

### **Creating a Donation (Cross-Database)**
```php
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;

// Campaign is in Izzati (PostgreSQL)
$campaign = Campaign::on('izzati')->find($campaignId);

// Donor is in Hannah (MySQL)
$donor = Donor::on('hannah')->find($donorId);

// Donation goes to Hannah (MySQL)
$donation = Donation::on('hannah')->create([
    'Donor_ID' => $donor->Donor_ID,
    'Campaign_ID' => $campaign->Campaign_ID, // Cross-DB reference
    'Amount' => 100.00,
    // ...
]);

// Access cross-database relationship
$campaign->donations; // Uses setConnection() automatically
```

### **Volunteer Event Registration (Cross-Database)**
```php
use App\Models\Event;
use App\Models\Volunteer;
use App\Models\EventParticipation;

// Event is in Izzati (PostgreSQL)
$event = Event::on('izzati')->find($eventId);

// Volunteer is in Sashvini (MariaDB)
$volunteer = Volunteer::on('sashvini')->find($volunteerId);

// EventParticipation is in Sashvini (MariaDB)
EventParticipation::on('sashvini')->create([
    'Event_ID' => $event->Event_ID, // Cross-DB reference
    'Volunteer_ID' => $volunteer->Volunteer_ID,
    'Status' => 'Registered',
]);

// Access cross-database relationship
$event->volunteers; // Uses setConnection() automatically
```

---

## 🔐 Security Considerations

### **Application-Layer Validation**
Since foreign key constraints don't work across databases:

```php
// MUST validate Campaign exists before creating Donation
$campaign = Campaign::on('izzati')->find($request->Campaign_ID);
if (!$campaign) {
    throw new \Exception('Campaign not found');
}

// Then create donation in Hannah database
$donation = Donation::on('hannah')->create([...]);
```

### **Transaction Handling**
```php
// Transactions are per-database only
DB::connection('hannah')->transaction(function () {
    // All operations here are in Hannah database only
    Donation::create([...]);
    DonationAllocation::create([...]);
});

// Cannot span multiple databases:
// ❌ This doesn't work:
DB::connection('izzati')->transaction(function () {
    Campaign::create([...]); // Izzati
    Donation::create([...]); // Hannah - different DB, not in transaction!
});
```

---

## 📈 Performance Considerations

### **Eager Loading Across Databases**
```php
// This works but generates multiple queries
$campaigns = Campaign::on('izzati')
    ->with('donations') // Separate query to Hannah database
    ->with('organization') // Same database (Izzati)
    ->get();

// Query 1: SELECT * FROM campaign (Izzati)
// Query 2: SELECT * FROM organization WHERE ... (Izzati - same DB, fast)
// Query 3: SELECT * FROM donation WHERE ... (Hannah - different DB, slower)
```

### **Caching Strategy**
- Cache frequently accessed cross-database queries
- Use Redis for shared state across databases
- Consider read replicas for each database

---

## ✅ Testing Strategy

### **Test Database Connections**
```bash
php artisan tinker

# Test each connection
DB::connection('izzhilmy')->getPdo();
DB::connection('sashvini')->getPdo();
DB::connection('izzati')->getPdo();
DB::connection('hannah')->getPdo();
DB::connection('adam')->getPdo();
```

### **Test Cross-Database Relationships**
```php
$user = User::on('izzhilmy')->first();
$user->donor; // Should fetch from Hannah
$user->volunteer; // Should fetch from Sashvini
$user->organization; // Should fetch from Izzati
```

---

## 🎓 Learning Outcomes

This implementation demonstrates:
1. ✅ Heterogeneous database architecture (PostgreSQL + MySQL + MariaDB)
2. ✅ Cross-database relationship management
3. ✅ Distributed data modeling
4. ✅ Application-layer referential integrity
5. ✅ Multi-database transaction patterns
6. ✅ Performance optimization for distributed queries

---

**Generated for**: BITU3923 Database Workshop
**Institution**: UTeM
**Date**: 2026-01-07
