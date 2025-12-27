# Distributed Database Architecture with API Communication

## Overview

This document outlines a **distributed database architecture** where each module has a **complete copy of all tables** but only manages data for their assigned tables. Modules communicate via **REST APIs** to access data from other modules.

## Architecture Pattern

**Schema Replication + Data Partitioning + API Communication**

- ✅ Each database has the complete schema (all tables)
- ✅ Each module only writes to their assigned tables
- ✅ Each module exposes APIs for their tables
- ✅ Cross-module data access via HTTP/REST APIs
- ✅ True microservices architecture

## Module Distribution

| Team Member | Database | Port | Module | Tables They Manage (Write Access) | Tables They Read Only |
|-------------|----------|------|--------|-----------------------------------|----------------------|
| **Izzhilmy** | PostgreSQL | 5432 | User Management | user, role, admin, model_has_roles, model_has_permissions, role_has_permissions | All other tables |
| **Sashvini** | MariaDB | 3307 | Volunteer Management | volunteer, volunteer_skill, skill, event_participation | All other tables |
| **Izzati** | PostgreSQL | 5433 | Event Management | organization, event, campaign, event_role | All other tables |
| **Hannah** | MySQL | 3306 | Donation Management | donor, donation, donation_allocation | All other tables |
| **Adam** | MySQL | 3308 | Recipient Management | public_profile, recipient | All other tables |

## Application Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Frontend (Blade/Livewire)                    │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      API Gateway / Route Layer                       │
│              (Routes requests to appropriate module API)             │
└─────────────────────────────────────────────────────────────────────┘
                                    │
        ┌───────────┬───────────┬───────────┬───────────┬──────────┐
        │           │           │           │           │          │
        ▼           ▼           ▼           ▼           ▼          ▼
   ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐
   │  User  │  │Volunt. │  │ Event  │  │Donation│  │Recipi. │
   │  API   │  │  API   │  │  API   │  │  API   │  │  API   │
   │ Module │  │ Module │  │ Module │  │ Module │  │ Module │
   └────────┘  └────────┘  └────────┘  └────────┘  └────────┘
        │           │           │           │           │
        ▼           ▼           ▼           ▼           ▼
   ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐
   │Postgres│  │MariaDB │  │Postgres│  │ MySQL  │  │ MySQL  │
   │  5432  │  │  3307  │  │  5433  │  │  3306  │  │  3308  │
   │(Complete│  │(Complete│  │(Complete│  │(Complete│  │(Complete│
   │ Schema) │  │ Schema) │  │ Schema) │  │ Schema) │  │ Schema) │
   └────────┘  └────────┘  └────────┘  └────────┘  └────────┘
```

## Data Ownership Rules

### Write Rules (Who Can INSERT/UPDATE/DELETE)

| Table | Owner | API Endpoint | Port |
|-------|-------|-------------|------|
| user, role, admin, model_has_roles, model_has_permissions, role_has_permissions | Izzhilmy | `/api/users/*`, `/api/roles/*`, `/api/admins/*` | 8001 |
| volunteer, volunteer_skill, skill, event_participation | Sashvini | `/api/volunteers/*`, `/api/skills/*`, `/api/event-participation/*` | 8002 |
| organization, event, campaign, event_role | Izzati | `/api/organizations/*`, `/api/events/*`, `/api/campaigns/*` | 8003 |
| donor, donation, donation_allocation | Hannah | `/api/donors/*`, `/api/donations/*`, `/api/allocations/*` | 8004 |
| public_profile, recipient | Adam | `/api/public-profiles/*`, `/api/recipients/*` | 8005 |

### Read Rules

- Each module can read their own tables directly from their database
- Each module can read other tables via API calls to the owning module
- **OR** each module can read cached/replicated data from their own database (read-only)

## Implementation Steps

### Step 1: Database Setup

#### Option A: Docker Compose (Recommended)

Create `docker-compose-distributed.yml`:

```yaml
version: '3.8'

services:
  # Izzhilmy - User Management (PostgreSQL)
  user-db:
    image: postgres:16
    container_name: charity_user_db
    environment:
      POSTGRES_DB: charity_izz
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5432:5432"
    volumes:
      - user_data:/var/lib/postgresql/data

  # Sashvini - Volunteer Management (MariaDB)
  volunteer-db:
    image: mariadb:11
    container_name: charity_volunteer_db
    environment:
      MYSQL_DATABASE: charity_izz
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3307:3306"
    volumes:
      - volunteer_data:/var/lib/mysql

  # Izzati - Event Management (PostgreSQL)
  event-db:
    image: postgres:16
    container_name: charity_event_db
    environment:
      POSTGRES_DB: charity_izz
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: password
    ports:
      - "5433:5432"
    volumes:
      - event_data:/var/lib/postgresql/data

  # Hannah - Donation Management (MySQL)
  donation-db:
    image: mysql:8
    container_name: charity_donation_db
    environment:
      MYSQL_DATABASE: charity_izz
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - donation_data:/var/lib/mysql

  # Adam - Recipient Management (MySQL)
  recipient-db:
    image: mysql:8
    container_name: charity_recipient_db
    environment:
      MYSQL_DATABASE: charity_izz
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3308:3306"
    volumes:
      - recipient_data:/var/lib/mysql

volumes:
  user_data:
  volunteer_data:
  event_data:
  donation_data:
  recipient_data:
```

Start all databases:
```bash
docker-compose -f docker-compose-distributed.yml up -d
```

### Step 2: Create 5 Laravel Applications (One Per Module)

Each team member runs their own Laravel instance:

```
charity-izz/
├── user-management/          (Izzhilmy - Laravel app)
│   ├── app/
│   ├── routes/api.php        (Exposes user APIs)
│   ├── .env                  (DB_PORT=5432, APP_PORT=8001)
│   └── ...
├── volunteer-management/     (Sashvini - Laravel app)
│   ├── app/
│   ├── routes/api.php        (Exposes volunteer APIs)
│   ├── .env                  (DB_PORT=3307, APP_PORT=8002)
│   └── ...
├── event-management/         (Izzati - Laravel app)
│   ├── app/
│   ├── routes/api.php        (Exposes event APIs)
│   ├── .env                  (DB_PORT=5433, APP_PORT=8003)
│   └── ...
├── donation-management/      (Hannah - Laravel app)
│   ├── app/
│   ├── routes/api.php        (Exposes donation APIs)
│   ├── .env                  (DB_PORT=3306, APP_PORT=8004)
│   └── ...
└── recipient-management/     (Adam - Laravel app)
    ├── app/
    ├── routes/api.php        (Exposes recipient APIs)
    ├── .env                  (DB_PORT=3308, APP_PORT=8005)
    └── ...
```

**Create each Laravel app:**
```bash
# User Management
composer create-project laravel/laravel user-management
cd user-management
# Configure .env with DB_PORT=5432, APP_PORT=8001

# Repeat for other 4 modules...
```

### Step 3: Run All Migrations on All Databases

Since each database needs the complete schema, run ALL migrations on each database:

```bash
# In user-management (PostgreSQL 5432)
cd user-management
php artisan migrate

# In volunteer-management (MariaDB 3307)
cd volunteer-management
php artisan migrate

# In event-management (PostgreSQL 5433)
cd event-management
php artisan migrate

# In donation-management (MySQL 3306)
cd donation-management
php artisan migrate

# In recipient-management (MySQL 3308)
cd recipient-management
php artisan migrate
```

**Result**: All 5 databases now have identical schemas.

### Step 4: Configure Each Application

#### User Management (.env)
```env
APP_NAME="User Management API"
APP_URL=http://localhost:8001
APP_PORT=8001

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=charity_izz
DB_USERNAME=postgres
DB_PASSWORD=password

# Other module API endpoints
VOLUNTEER_API_URL=http://localhost:8002/api
EVENT_API_URL=http://localhost:8003/api
DONATION_API_URL=http://localhost:8004/api
RECIPIENT_API_URL=http://localhost:8005/api
```

#### Volunteer Management (.env)
```env
APP_NAME="Volunteer Management API"
APP_URL=http://localhost:8002
APP_PORT=8002

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=charity_izz
DB_USERNAME=root
DB_PASSWORD=password

# Other module API endpoints
USER_API_URL=http://localhost:8001/api
EVENT_API_URL=http://localhost:8003/api
DONATION_API_URL=http://localhost:8004/api
RECIPIENT_API_URL=http://localhost:8005/api
```

**Repeat for other 3 modules** (Event: 8003, Donation: 8004, Recipient: 8005)

### Step 5: Create API Resources for Each Module

#### Example: User Management API (Izzhilmy)

**routes/api.php:**
```php
<?php

use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\RoleApiController;
use Illuminate\Support\Facades\Route;

// User APIs
Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::get('/{id}', [UserApiController::class, 'show']);
    Route::post('/', [UserApiController::class, 'store']);
    Route::put('/{id}', [UserApiController::class, 'update']);
    Route::delete('/{id}', [UserApiController::class, 'destroy']);
});

// Role APIs
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleApiController::class, 'index']);
    Route::get('/{id}', [RoleApiController::class, 'show']);
    Route::post('/', [RoleApiController::class, 'store']);
    Route::put('/{id}', [RoleApiController::class, 'update']);
    Route::delete('/{id}', [RoleApiController::class, 'destroy']);
});

// Authentication
Route::post('/login', [UserApiController::class, 'login']);
Route::post('/register', [UserApiController::class, 'register']);
```

**app/Http/Controllers/Api/UserApiController.php:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }
}
```

#### Example: Event Management API (Izzati)

**routes/api.php:**
```php
<?php

use App\Http\Controllers\Api\CampaignApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\OrganizationApiController;
use Illuminate\Support\Facades\Route;

// Campaign APIs
Route::prefix('campaigns')->group(function () {
    Route::get('/', [CampaignApiController::class, 'index']);
    Route::get('/{id}', [CampaignApiController::class, 'show']);
    Route::post('/', [CampaignApiController::class, 'store']);
    Route::put('/{id}', [CampaignApiController::class, 'update']);
    Route::delete('/{id}', [CampaignApiController::class, 'destroy']);
    Route::get('/{id}/donations', [CampaignApiController::class, 'getDonations']);
});

// Event APIs
Route::prefix('events')->group(function () {
    Route::get('/', [EventApiController::class, 'index']);
    Route::get('/{id}', [EventApiController::class, 'show']);
    Route::post('/', [EventApiController::class, 'store']);
    Route::put('/{id}', [EventApiController::class, 'update']);
    Route::delete('/{id}', [EventApiController::class, 'destroy']);
    Route::get('/{id}/participants', [EventApiController::class, 'getParticipants']);
});

// Organization APIs
Route::prefix('organizations')->group(function () {
    Route::get('/', [OrganizationApiController::class, 'index']);
    Route::get('/{id}', [OrganizationApiController::class, 'show']);
    Route::post('/', [OrganizationApiController::class, 'store']);
    Route::put('/{id}', [OrganizationApiController::class, 'update']);
    Route::delete('/{id}', [OrganizationApiController::class, 'destroy']);
});
```

**app/Http/Controllers/Api/CampaignApiController.php:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CampaignApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->has('organization_id')) {
            $query->where('Organization_ID', $request->organization_id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        return response()->json($campaign);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Title' => 'required|string',
            'Description' => 'required|string',
            'Goal_Amount' => 'required|numeric|min:0',
            'Organization_ID' => 'required|integer',
            // Add other fields
        ]);

        $campaign = Campaign::create($validated);

        return response()->json($campaign, 201);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        $campaign->update($request->all());

        return response()->json($campaign);
    }

    public function destroy($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted'], 200);
    }

    // Get donations for this campaign (calls Donation API)
    public function getDonations($id)
    {
        $donationApiUrl = env('DONATION_API_URL');

        $response = Http::get("{$donationApiUrl}/donations", [
            'campaign_id' => $id
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Failed to fetch donations'], 500);
    }
}
```

#### Example: Donation Management API (Hannah)

**routes/api.php:**
```php
<?php

use App\Http\Controllers\Api\DonationApiController;
use App\Http\Controllers\Api\DonorApiController;
use Illuminate\Support\Facades\Route;

// Donation APIs
Route::prefix('donations')->group(function () {
    Route::get('/', [DonationApiController::class, 'index']);
    Route::get('/{id}', [DonationApiController::class, 'show']);
    Route::post('/', [DonationApiController::class, 'store']);
    Route::put('/{id}', [DonationApiController::class, 'update']);
    Route::delete('/{id}', [DonationApiController::class, 'destroy']);
});

// Donor APIs
Route::prefix('donors')->group(function () {
    Route::get('/', [DonorApiController::class, 'index']);
    Route::get('/{id}', [DonorApiController::class, 'show']);
    Route::get('/{id}/donations', [DonorApiController::class, 'getDonations']);
});
```

**app/Http/Controllers/Api/DonationApiController.php:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DonationApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::query();

        // Filter by campaign
        if ($request->has('campaign_id')) {
            $query->where('Campaign_ID', $request->campaign_id);
        }

        // Filter by donor
        if ($request->has('donor_id')) {
            $query->where('Donor_ID', $request->donor_id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json(['error' => 'Donation not found'], 404);
        }

        return response()->json($donation);
    }

    public function store(Request $request)
    {
        // Validate campaign exists (call Event API)
        $campaignApiUrl = env('EVENT_API_URL');
        $campaignResponse = Http::get("{$campaignApiUrl}/campaigns/{$request->Campaign_ID}");

        if (!$campaignResponse->successful()) {
            return response()->json(['error' => 'Invalid campaign'], 400);
        }

        $validated = $request->validate([
            'Donor_ID' => 'required|integer',
            'Campaign_ID' => 'required|integer',
            'Amount' => 'required|numeric|min:0',
            'Payment_Method' => 'required|string',
        ]);

        // Generate receipt number
        $validated['Receipt_Number'] = 'RCP-' . time() . '-' . rand(1000, 9999);
        $validated['Donation_Date'] = now();

        $donation = Donation::create($validated);

        // Update campaign collected amount (call Event API)
        Http::put("{$campaignApiUrl}/campaigns/{$donation->Campaign_ID}/increment-amount", [
            'amount' => $donation->Amount
        ]);

        return response()->json($donation, 201);
    }
}
```

### Step 6: Create API Client Services

To make API calls easier, create service classes:

**app/Services/CampaignApiService.php** (in Donation module):
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CampaignApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('EVENT_API_URL') . '/campaigns';
    }

    public function getAll($filters = [])
    {
        $response = Http::get($this->baseUrl, $filters);
        return $response->successful() ? $response->json() : [];
    }

    public function getById($id)
    {
        $response = Http::get("{$this->baseUrl}/{$id}");
        return $response->successful() ? $response->json() : null;
    }

    public function create($data)
    {
        $response = Http::post($this->baseUrl, $data);
        return $response->successful() ? $response->json() : null;
    }

    public function update($id, $data)
    {
        $response = Http::put("{$this->baseUrl}/{$id}", $data);
        return $response->successful() ? $response->json() : null;
    }

    public function delete($id)
    {
        $response = Http::delete("{$this->baseUrl}/{$id}");
        return $response->successful();
    }
}
```

**Usage in Controller:**
```php
use App\Services\CampaignApiService;

class DonationController extends Controller
{
    protected $campaignApi;

    public function __construct(CampaignApiService $campaignApi)
    {
        $this->campaignApi = $campaignApi;
    }

    public function create()
    {
        // Fetch campaigns from Event Management API
        $campaigns = $this->campaignApi->getAll(['status' => 'Active']);

        return view('donations.create', compact('campaigns'));
    }
}
```

### Step 7: Run All Laravel Applications

Each team member runs their application on a different port:

```bash
# Terminal 1 - User Management
cd user-management
php artisan serve --port=8001

# Terminal 2 - Volunteer Management
cd volunteer-management
php artisan serve --port=8002

# Terminal 3 - Event Management
cd event-management
php artisan serve --port=8003

# Terminal 4 - Donation Management
cd donation-management
php artisan serve --port=8004

# Terminal 5 - Recipient Management
cd recipient-management
php artisan serve --port=8005
```

### Step 8: Create API Gateway (Optional but Recommended)

Create a main Laravel application that acts as a gateway and routes requests:

**Main Application routes/api.php:**
```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// Proxy to User Management
Route::prefix('users')->group(function () {
    Route::any('{path?}', function ($path = '') {
        return Http::send(
            request()->method(),
            'http://localhost:8001/api/users/' . $path,
            ['query' => request()->query(), 'json' => request()->all()]
        )->json();
    })->where('path', '.*');
});

// Proxy to Volunteer Management
Route::prefix('volunteers')->group(function () {
    Route::any('{path?}', function ($path = '') {
        return Http::send(
            request()->method(),
            'http://localhost:8002/api/volunteers/' . $path,
            ['query' => request()->query(), 'json' => request()->all()]
        )->json();
    })->where('path', '.*');
});

// Proxy to Event Management
Route::prefix('events')->group(function () {
    Route::any('{path?}', function ($path = '') {
        return Http::send(
            request()->method(),
            'http://localhost:8003/api/events/' . $path,
            ['query' => request()->query(), 'json' => request()->all()]
        )->json();
    })->where('path', '.*');
});

// Proxy to Donation Management
Route::prefix('donations')->group(function () {
    Route::any('{path?}', function ($path = '') {
        return Http::send(
            request()->method(),
            'http://localhost:8004/api/donations/' . $path,
            ['query' => request()->query(), 'json' => request()->all()]
        )->json();
    })->where('path', '.*');
});

// Proxy to Recipient Management
Route::prefix('recipients')->group(function () {
    Route::any('{path?}', function ($path = '') {
        return Http::send(
            request()->method(),
            'http://localhost:8005/api/recipients/' . $path,
            ['query' => request()->query(), 'json' => request()->all()]
        )->json();
    })->where('path', '.*');
});
```

Now all APIs are accessible through one endpoint:
- `http://localhost:8000/api/users/*` → Routes to User module
- `http://localhost:8000/api/campaigns/*` → Routes to Event module
- `http://localhost:8000/api/donations/*` → Routes to Donation module

## Data Synchronization Strategy

### Approach 1: API Calls Only (No Local Replication)

Each module only reads from their own tables OR calls APIs for other data:

```php
// In Donation module - showing donations with campaign details
public function index()
{
    $donations = Donation::all();
    $campaignIds = $donations->pluck('Campaign_ID')->unique();

    // Call Event API to get campaign details
    $campaignApi = new CampaignApiService();
    $campaigns = collect();

    foreach ($campaignIds as $campaignId) {
        $campaign = $campaignApi->getById($campaignId);
        if ($campaign) {
            $campaigns->put($campaignId, $campaign);
        }
    }

    return view('donations.index', compact('donations', 'campaigns'));
}
```

**Pros:**
- Always fresh data
- No synchronization needed
- Single source of truth

**Cons:**
- Slower (HTTP overhead)
- Depends on other services being available
- More network requests

### Approach 2: Event-Driven Replication

When data changes in the owner module, broadcast events to update other databases:

```php
// In Event module - when Campaign is created/updated
use Illuminate\Support\Facades\Http;

class Campaign extends Model
{
    protected static function booted()
    {
        static::created(function ($campaign) {
            self::syncToOtherDatabases($campaign, 'created');
        });

        static::updated(function ($campaign) {
            self::syncToOtherDatabases($campaign, 'updated');
        });

        static::deleted(function ($campaign) {
            self::syncToOtherDatabases($campaign, 'deleted');
        });
    }

    protected static function syncToOtherDatabases($campaign, $action)
    {
        $modules = [
            env('USER_API_URL'),
            env('VOLUNTEER_API_URL'),
            env('DONATION_API_URL'),
            env('RECIPIENT_API_URL'),
        ];

        foreach ($modules as $moduleUrl) {
            Http::post("{$moduleUrl}/sync/campaign", [
                'action' => $action,
                'data' => $campaign->toArray(),
            ]);
        }
    }
}
```

**In other modules (e.g., Donation module) - Receive sync:**
```php
// routes/api.php
Route::post('/sync/campaign', [SyncController::class, 'syncCampaign']);

// SyncController.php
public function syncCampaign(Request $request)
{
    $action = $request->action;
    $data = $request->data;

    switch ($action) {
        case 'created':
        case 'updated':
            Campaign::updateOrCreate(
                ['Campaign_ID' => $data['Campaign_ID']],
                $data
            );
            break;
        case 'deleted':
            Campaign::where('Campaign_ID', $data['Campaign_ID'])->delete();
            break;
    }

    return response()->json(['status' => 'synced']);
}
```

**Pros:**
- Fast local reads
- No dependency on other services for reads
- Eventual consistency

**Cons:**
- Data may be slightly out of sync
- Requires sync endpoints in all modules
- More complex

### Approach 3: Scheduled Sync Jobs

Run periodic jobs to sync data:

```php
// In Donation module - sync campaigns every 5 minutes
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->job(new SyncCampaignsJob)->everyFiveMinutes();
}

// app/Jobs/SyncCampaignsJob.php
class SyncCampaignsJob implements ShouldQueue
{
    public function handle()
    {
        $campaignApi = new CampaignApiService();
        $campaigns = $campaignApi->getAll();

        foreach ($campaigns as $campaignData) {
            Campaign::updateOrCreate(
                ['Campaign_ID' => $campaignData['Campaign_ID']],
                $campaignData
            );
        }
    }
}
```

**Pros:**
- Simple to implement
- Predictable sync intervals
- Fast local reads

**Cons:**
- Data can be stale
- Unnecessary syncs if data hasn't changed

## Complete API Endpoint List

### User Management (Port 8001)
```
GET    /api/users
GET    /api/users/{id}
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}
POST   /api/login
POST   /api/register

GET    /api/roles
GET    /api/roles/{id}
POST   /api/roles
```

### Volunteer Management (Port 8002)
```
GET    /api/volunteers
GET    /api/volunteers/{id}
POST   /api/volunteers
PUT    /api/volunteers/{id}
DELETE /api/volunteers/{id}

GET    /api/skills
GET    /api/skills/{id}
POST   /api/skills

GET    /api/event-participation
GET    /api/event-participation/{id}
POST   /api/event-participation
PUT    /api/event-participation/{id}
DELETE /api/event-participation/{id}
```

### Event Management (Port 8003)
```
GET    /api/campaigns
GET    /api/campaigns/{id}
POST   /api/campaigns
PUT    /api/campaigns/{id}
DELETE /api/campaigns/{id}
GET    /api/campaigns/{id}/donations

GET    /api/events
GET    /api/events/{id}
POST   /api/events
PUT    /api/events/{id}
DELETE /api/events/{id}
GET    /api/events/{id}/participants

GET    /api/organizations
GET    /api/organizations/{id}
POST   /api/organizations
PUT    /api/organizations/{id}
DELETE /api/organizations/{id}
```

### Donation Management (Port 8004)
```
GET    /api/donations
GET    /api/donations/{id}
POST   /api/donations
PUT    /api/donations/{id}
DELETE /api/donations/{id}

GET    /api/donors
GET    /api/donors/{id}
GET    /api/donors/{id}/donations

GET    /api/allocations
POST   /api/allocations
```

### Recipient Management (Port 8005)
```
GET    /api/recipients
GET    /api/recipients/{id}
POST   /api/recipients
PUT    /api/recipients/{id}
DELETE /api/recipients/{id}

GET    /api/public-profiles
GET    /api/public-profiles/{id}
POST   /api/public-profiles
```

## Testing Strategy

### Test Each Module Independently

```bash
# In each module directory
php artisan test
```

### Test API Integration

Create integration tests that test cross-module communication:

```php
// In Donation module
it('creates a donation for a valid campaign', function () {
    // Mock the Campaign API response
    Http::fake([
        'http://localhost:8003/api/campaigns/*' => Http::response([
            'Campaign_ID' => 1,
            'Title' => 'Test Campaign',
            'Status' => 'Active',
        ], 200)
    ]);

    $response = $this->postJson('/api/donations', [
        'Donor_ID' => 1,
        'Campaign_ID' => 1,
        'Amount' => 100.00,
        'Payment_Method' => 'Credit Card',
    ]);

    $response->assertStatus(201);
    expect($response->json())->toHaveKey('Receipt_Number');
});
```

### End-to-End Testing with Postman

Create Postman collection with all API endpoints:

1. Create User (POST `http://localhost:8001/api/users`)
2. Create Organization (POST `http://localhost:8003/api/organizations`)
3. Create Campaign (POST `http://localhost:8003/api/campaigns`)
4. Create Donor (POST `http://localhost:8004/api/donors`)
5. Create Donation (POST `http://localhost:8004/api/donations`)
6. Get Campaign Donations (GET `http://localhost:8003/api/campaigns/1/donations`)

## Advantages of This Approach

1. ✅ **True Module Independence**: Each module is a standalone application
2. ✅ **Complete Schema Access**: Each database has all tables for reference
3. ✅ **Flexible Deployment**: Can deploy modules separately later
4. ✅ **Realistic Microservices**: Mirrors real-world distributed architecture
5. ✅ **Team Autonomy**: Each person fully controls their database
6. ✅ **No Cross-Database Queries**: All communication via HTTP/REST
7. ✅ **Heterogeneous Databases**: Each module uses their preferred database type
8. ✅ **Scalable**: Can add more modules or scale individual modules

## Disadvantages & Challenges

1. ⚠️ **More Complex Setup**: 5 separate Laravel applications
2. ⚠️ **HTTP Overhead**: API calls slower than direct database queries
3. ⚠️ **Data Consistency**: Requires careful synchronization strategy
4. ⚠️ **Service Dependencies**: Modules depend on each other being online
5. ⚠️ **Increased Testing Complexity**: Must test both unit and integration
6. ⚠️ **No Foreign Key Constraints**: Must validate relationships in code
7. ⚠️ **More Resource Intensive**: 5 applications running simultaneously

## Simplified Alternative: Single App with Module Separation

If managing 5 apps is too complex, use **one Laravel app** with module-based controllers:

```
charity-izz/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/
│   │   │   ├── UserModule/       (Izzhilmy's APIs)
│   │   │   ├── VolunteerModule/  (Sashvini's APIs)
│   │   │   ├── EventModule/      (Izzati's APIs)
│   │   │   ├── DonationModule/   (Hannah's APIs)
│   │   │   └── RecipientModule/  (Adam's APIs)
│   ├── Models/
│   │   ├── User.php              (connection: pgsql_user)
│   │   ├── Volunteer.php         (connection: mariadb_volunteer)
│   │   ├── Campaign.php          (connection: pgsql_event)
│   │   ├── Donation.php          (connection: mysql_donation)
│   │   └── Recipient.php         (connection: mysql_recipient)
```

Still have 5 databases, but one Laravel application with modular API routes.

## Recommended Approach

**For Learning/Academic Project:**
- Use **5 separate Laravel applications** (full microservices)
- Implement **API-only communication** (Approach 1)
- Create **API Gateway** for unified access
- Use **Docker Compose** for easy database setup

**For Production/Real Use:**
- Start with **single Laravel app** + **5 databases**
- Use **event-driven replication** (Approach 2)
- Implement **caching** for frequently accessed data
- Add **API versioning** and **authentication** (Laravel Sanctum/Passport)

## Next Steps

1. Set up all 5 databases using Docker Compose
2. Create 5 Laravel applications (or clone current app 5 times)
3. Configure database connections for each
4. Run migrations on all databases
5. Create API controllers for each module
6. Implement API service classes
7. Test cross-module communication
8. Implement chosen synchronization strategy
9. Create API documentation (Swagger/OpenAPI)
10. Deploy and test end-to-end workflows

Good luck with your distributed database implementation!
