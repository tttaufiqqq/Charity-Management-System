# Distributed Database Architecture Implementation Plan
**Project:** Charity-Izz
**Goal:** Transform monolithic Laravel app into heterogeneous distributed microservices architecture

## Architecture Overview

Transform the current monolithic application into **5 independent microservices** with heterogeneous databases:

### Service Distribution

| Service | Owner | Database | Port | Tables |
|---------|-------|----------|------|--------|
| User Management | Izz | PostgreSQL (5432) | 8001 | user, roles, permissions, model_has_roles |
| Volunteer Management | Sashvini | MariaDB (3307) | 8002 | volunteer, volunteer_skill, skill, event (SOURCE), event_participation |
| Event Management | Izzati | PostgreSQL (5433) | 8003 | organization, campaign (SOURCE), event_role |
| Donation Management | Hannah | MySQL (3306) | 8004 | donor, donation, donation_allocation |
| Recipient Management | Adam | MySQL (3308) | 8005 | public_profile, recipient |

**Key Decisions:**
- **Event table**: Owned by Volunteer Service (Sashvini's MariaDB)
- **Campaign table**: Owned by Event Management Service (Izzati's PostgreSQL)
- Event_role (Izzati) and event_participation (Sashvini) split across services - handled via APIs

## Critical Cross-Service Dependencies

### Foreign Key Relationships Spanning Services

1. **User → All Services**: volunteer.User_ID, donor.User_ID, public.User_ID, organization.Organizer_ID
2. **Campaign → Donation/Recipient**: donation.Campaign_ID, donation_allocation.Campaign_ID
3. **Event → Event Management**: event_role.Event_ID, event_participation.Event_ID (cross-DB)
4. **Recipient → Donation**: donation_allocation.Recipient_ID

**Strategy**: Remove database-level foreign keys, enforce referential integrity in application code via API validation

## Implementation Roadmap

### Phase 1: Database Infrastructure (Week 1)

**1.1 Docker Compose Setup**
Create `docker-compose-microservices.yml` with 5 databases:
- PostgreSQL (User): Port 5432
- MariaDB (Volunteer): Port 3307
- PostgreSQL (Event): Port 5433
- MySQL (Donation): Port 3306
- MySQL (Recipient): Port 3308
- Redis (Queue): Port 6379

**1.2 Laravel Database Connections**
Configure `config/database.php` with 5 connections:
```php
'pgsql_user' => [...], 'mariadb_volunteer' => [...],
'pgsql_event' => [...], 'mysql_donation' => [...],
'mysql_recipient' => [...]
```

**1.3 Run Migrations**
Execute migrations on all 5 databases to replicate schema

**1.4 Data Migration**
Partition existing data to appropriate databases:
- Copy users/roles → User DB
- Copy volunteers/skills → Volunteer DB
- Copy campaigns/events/organizations → Event DB (split event to Volunteer DB)
- Copy donors/donations → Donation DB
- Copy recipients/public → Recipient DB

**1.5 Remove Foreign Key Constraints**
Drop cross-service foreign keys in migrations (keep columns as regular integers)

### Phase 2: Authentication Service (Week 2)

**2.1 Install Laravel Sanctum** in User Service
```bash
cd user-service && composer require laravel/sanctum
```

**2.2 Create Authentication API**
Endpoints in User Service:
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login` (returns JWT token)
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/password/reset`

**2.3 Token Validation Middleware**
Create middleware in all other services to validate tokens with User Service:
```php
class ValidateServiceToken {
    public function handle($request, $next) {
        $token = $request->bearerToken();
        $user = UserApiService::validateToken($token);
        if (!$user) abort(401);
        $request->merge(['authenticated_user' => $user]);
        return $next($request);
    }
}
```

**2.4 User Management APIs**
- `GET /api/v1/users`
- `GET /api/v1/users/{id}`
- `PUT /api/v1/users/{id}`
- `DELETE /api/v1/users/{id}`
- Role management endpoints

### Phase 3: Service API Implementation (Weeks 3-6)

**3.1 Volunteer Service APIs (Sashvini)**
Implement RESTful endpoints:
- Volunteer profile CRUD
- Skills management
- Event CRUD (owns event table)
- Event registration/cancellation (`event_participation`)
- Volunteer schedule

Create service clients:
- `UserApiService` (validate User_ID references)
- `EventRoleApiService` (validate Role_ID from Event Management)

**3.2 Event Management APIs (Izzati)**
Implement RESTful endpoints:
- Organization CRUD
- Campaign CRUD (source of truth)
- Event role CRUD
- Campaign/event approval workflows
- Campaign collected amount updates

Create service clients:
- `UserApiService` (validate Organizer_ID)
- `VolunteerApiService` (get event participants, update hours)
- `EventApiService` (get events from Volunteer service)

**3.3 Donation Management APIs (Hannah)**
Implement RESTful endpoints:
- Donor profile CRUD
- Donation CRUD with ToyyibPay integration
- Payment callback/return handlers
- Receipt PDF generation
- Donation allocation CRUD

Create service clients:
- `UserApiService` (validate User_ID)
- `CampaignApiService` (browse campaigns, validate Campaign_ID)
- `RecipientApiService` (validate Recipient_ID for allocations)

**3.4 Recipient Management APIs (Adam)**
Implement RESTful endpoints:
- Public profile CRUD
- Recipient application CRUD
- Recipient approval workflow
- Allocation history viewing

Create service clients:
- `UserApiService` (validate User_ID)
- `CampaignApiService` (get campaign details)
- `AllocationApiService` (get allocations from Donation service)

### Phase 4: Service Communication (Week 7)

**4.1 HTTP Service Client Pattern**
Create base service client class in each service:
```php
class BaseApiService {
    protected function call($method, $url, $data = []) {
        $response = Http::timeout(5)
            ->withToken(request()->bearerToken())
            ->$method($url, $data);
        if (!$response->successful()) {
            throw new ServiceException("Service call failed");
        }
        return $response->json();
    }
}
```

**4.2 Circuit Breaker Implementation**
Add circuit breaker pattern to prevent cascading failures:
```php
class CircuitBreaker {
    public function call(callable $callback) {
        if (Cache::get("circuit:open:{$service}")) {
            throw new ServiceUnavailableException();
        }
        try {
            return $callback();
        } catch (\Exception $e) {
            $this->recordFailure();
            throw $e;
        }
    }
}
```

**4.3 Configure Service URLs**
Add to each service's `.env`:
```
USER_SERVICE_URL=http://localhost:8001
VOLUNTEER_SERVICE_URL=http://localhost:8002
EVENT_SERVICE_URL=http://localhost:8003
DONATION_SERVICE_URL=http://localhost:8004
RECIPIENT_SERVICE_URL=http://localhost:8005
```

### Phase 5: Data Consistency (Week 8)

**5.1 Saga Pattern for Donation Flow**
Implement choreography-based saga:

1. **Donation Service**: Process payment → Create donation (Status=Completed) → Update Donor.Total_Donated
2. **Donation Service**: Emit `DonationCompleted` event to queue
3. **Event Management Service**: Listen to event → Update Campaign.Collected_Amount
4. **Event Management Service**: If update fails → Emit `CampaignUpdateFailed` event
5. **Donation Service**: Listen to failure → Mark donation for manual review (compensating transaction)

**5.2 Event-Driven Updates**
Set up Laravel event broadcasting with Redis:
- Install `predis/predis`
- Configure `BROADCAST_DRIVER=redis`
- Create event classes: `DonationCompleted`, `CampaignApproved`, `EventCompleted`
- Create listeners in respective services

**5.3 Reconciliation Jobs**
Create scheduled jobs to ensure eventual consistency:
```php
// Donation Service - scheduled daily
class ReconcileCampaignTotalsJob {
    public function handle() {
        $totals = Donation::where('Payment_Status', 'Completed')
            ->groupBy('Campaign_ID')
            ->selectRaw('Campaign_ID, SUM(Amount) as total')
            ->get();
        foreach ($totals as $item) {
            CampaignApiService::syncCollectedAmount($item->Campaign_ID, $item->total);
        }
    }
}
```

**5.4 Application-Level Referential Integrity**
Add validation before creating records with foreign keys:
```php
// Donation Service - before creating donation
$campaign = app(CampaignApiService::class)->find($campaignId);
if (!$campaign) {
    throw new ValidationException('Campaign not found');
}
```

### Phase 6: API Gateway (Week 9)

**6.1 Create Gateway Application**
New Laravel app as API Gateway (Port 8000)

**6.2 Request Routing**
Implement proxy routes in `routes/api.php`:
```php
Route::prefix('v1')->group(function () {
    Route::any('auth/{path?}', [GatewayController::class, 'proxyToUser']);
    Route::any('volunteers/{path?}', [GatewayController::class, 'proxyToVolunteer']);
    Route::any('campaigns/{path?}', [GatewayController::class, 'proxyToEvent']);
    Route::any('donations/{path?}', [GatewayController::class, 'proxyToDonation']);
    Route::any('recipients/{path?}', [GatewayController::class, 'proxyToRecipient']);
});
```

**6.3 Request Aggregation**
For complex pages requiring multiple services:
```php
public function getCampaignDetails($id) {
    $campaign = CampaignApiService::find($id);
    $donations = DonationApiService::getByCampaign($id);
    $organization = OrganizationApiService::find($campaign['Organization_ID']);
    return response()->json([
        'campaign' => $campaign,
        'organization' => $organization,
        'donations' => $donations,
    ]);
}
```

**6.4 Add Rate Limiting**
Configure throttling in gateway

**6.5 CORS Configuration**
Set up CORS headers in gateway for frontend access

### Phase 7: Frontend Migration (Weeks 10-11)

**7.1 Update Blade Templates**
Convert Blade views to use Alpine.js + API calls:
```html
<div x-data="campaignBrowser()">
    <div x-show="loading">Loading...</div>
    <template x-for="campaign in campaigns">
        <div>
            <h3 x-text="campaign.Title"></h3>
            <button @click="donate(campaign.Campaign_ID)">Donate</button>
        </div>
    </template>
</div>

<script>
function campaignBrowser() {
    return {
        campaigns: [],
        loading: true,
        async init() {
            const response = await fetch('/api/v1/campaigns', {
                headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
            });
            this.campaigns = await response.json();
            this.loading = false;
        }
    }
}
</script>
```

**7.2 Replace Livewire Components**
Convert Livewire analytics components to Alpine.js with API calls

**7.3 Client-Side Token Management**
- Store JWT in localStorage on login
- Add token to all API requests
- Handle token expiration and refresh
- Redirect to login on 401 responses

**7.4 Update Form Submissions**
Convert all forms to submit via API:
```javascript
async function submitDonation(formData) {
    const response = await fetch('/api/v1/donations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${getToken()}`
        },
        body: JSON.stringify(formData)
    });
    if (response.ok) {
        // Handle success
    }
}
```

### Phase 8: Testing (Week 12)

**8.1 Unit Tests per Service**
Write Pest tests for each service's endpoints:
```php
it('creates a donation successfully', function () {
    Http::fake([
        'http://localhost:8003/api/v1/campaigns/*' => Http::response([...], 200)
    ]);
    $response = $this->postJson('/api/v1/donations', [...]);
    $response->assertStatus(201);
});
```

**8.2 Integration Tests**
Test actual inter-service communication (with all services running)

**8.3 End-to-End Tests**
Create Postman/Newman test collections for critical user flows:
- User registration → Create campaign → Make donation → Allocate funds
- Volunteer registration → Register for event → Update hours

**8.4 Load Testing**
Use Apache JMeter or k6 to test API performance under load

**8.5 Security Testing**
- Test authentication flows
- Test authorization across services
- Validate token security
- Test SQL injection, XSS prevention

### Phase 9: Deployment (Weeks 13-14)

**9.1 Docker Containerization**
Create Dockerfile for each service:
```dockerfile
FROM php:8.2-fpm
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD php artisan serve --host=0.0.0.0 --port=8001
```

**9.2 Production Docker Compose**
Update docker-compose.yml with all services and databases

**9.3 Health Check Endpoints**
Add to each service:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
});
```

**9.4 Logging & Monitoring**
- Install Laravel Telescope in each service
- Configure centralized logging
- Set up alerts for service failures

**9.5 Database Backups**
Configure automated backups for all 5 databases

## Critical Implementation Details

### Handling Pivot Tables Across Services

**Event Participation (Volunteer DB references Event Management DB)**
```php
// Volunteer Service - before creating event_participation
$event = app(EventApiService::class)->find($eventId);
if (!$event) throw new ValidationException('Event not found');

$role = app(EventApiService::class)->getRole($roleId);
if (!$role) throw new ValidationException('Role not found');

EventParticipation::create([...]);

// Notify Event Management to increment role filled count
app(EventApiService::class)->incrementRoleFilled($roleId);
```

**Donation Allocation (Donation DB references Event Mgmt + Recipient DBs)**
```php
// Donation Service - before creating allocation
$campaign = app(CampaignApiService::class)->find($campaignId);
if (!$campaign) throw new ValidationException('Campaign not found');

$recipient = app(RecipientApiService::class)->find($recipientId);
if (!$recipient || $recipient['Status'] !== 'Approved') {
    throw new ValidationException('Invalid recipient');
}

// Check available funds
$allocated = DonationAllocation::where('Campaign_ID', $campaignId)->sum('Amount_Allocated');
$available = $campaign['Collected_Amount'] - $allocated;
if ($amount > $available) throw new ValidationException('Insufficient funds');

DonationAllocation::create([...]);
```

### Distributed Transaction Pattern (Donation Flow)

**Step 1: Donation Service**
```php
DB::transaction(function () use ($request) {
    // Validate campaign exists (API call)
    $campaign = app(CampaignApiService::class)->find($request->campaign_id);

    // Create donation
    $donation = Donation::create([
        'Payment_Status' => 'Pending',
        ...
    ]);

    // Process payment with ToyyibPay
    $payment = app(ToyyibPayService::class)->createBill($donation);

    if ($payment['success']) {
        $donation->update(['Payment_Status' => 'Completed']);
        $donor->increment('Total_Donated', $donation->Amount);

        // Emit event for async campaign update
        event(new DonationCompleted($donation));
    }
});
```

**Step 2: Event Management Service (Listener)**
```php
class UpdateCampaignCollectedAmount {
    public function handle(DonationCompleted $event) {
        try {
            $campaign = Campaign::find($event->donation->Campaign_ID);
            $campaign->increment('Collected_Amount', $event->donation->Amount);
        } catch (\Exception $e) {
            Log::error("Failed to update campaign: " . $e->getMessage());
            event(new CampaignUpdateFailed($event->donation));
        }
    }
}
```

**Step 3: Donation Service (Compensating Transaction)**
```php
class HandleCampaignUpdateFailure {
    public function handle(CampaignUpdateFailed $event) {
        // Mark donation for manual review
        $donation = Donation::find($event->donation->Donation_ID);
        $donation->update(['needs_manual_review' => true]);

        // Send alert to admin
        Mail::to('admin@charity.com')->send(new DonationSyncFailure($donation));
    }
}
```

### Caching Strategy

**Cache frequently accessed reference data:**
```php
class CampaignApiService {
    public function find($id) {
        return Cache::remember("campaign:{$id}", 300, function () use ($id) {
            $response = Http::get("{$this->baseUrl}/{$id}");
            return $response->json();
        });
    }
}
```

**Invalidate cache on updates:**
```php
// Event Management Service
class Campaign extends Model {
    protected static function booted() {
        static::updated(function ($campaign) {
            Cache::forget("campaign:{$campaign->Campaign_ID}");
            event(new CampaignUpdated($campaign));
        });
    }
}
```

## Critical Files to Modify

### Database Configuration
- `config/database.php` - Add 5 database connections

### Migrations
- All migrations referencing cross-service foreign keys - drop FK constraints
- `database/migrations/2025_11_25_173320_create_donation.php` - Remove Campaign_ID FK
- `database/migrations/2025_11_25_173415_create_donation_allocation.php` - Remove Campaign_ID and Recipient_ID FKs

### Models
- `app/Models/Campaign.php` - Add event broadcasting, API resource transformation
- `app/Models/Donation.php` - Implement Saga pattern, emit events
- `app/Models/EventParticipation.php` - Add cross-service validation

### Controllers to Split
- `app/Http/Controllers/EventManagementController.php` → Event Management APIs
- `app/Http/Controllers/DonationManagementController.php` → Donation APIs
- `app/Http/Controllers/VolunteerController.php` → Volunteer APIs
- `app/Http/Controllers/RecipientManagementController.php` → Recipient APIs

### Routes
- `routes/web.php` - Convert to `routes/api.php` in each service
- Create new API Gateway routes

## Environment Configuration

Each service needs `.env` with:
```env
APP_NAME="Service Name"
APP_PORT=800X
DB_CONNECTION=specific_connection
DB_HOST=127.0.0.1
DB_PORT=XXXX
DB_DATABASE=charity_izz_servicename

# Service URLs
USER_SERVICE_URL=http://localhost:8001
VOLUNTEER_SERVICE_URL=http://localhost:8002
EVENT_SERVICE_URL=http://localhost:8003
DONATION_SERVICE_URL=http://localhost:8004
RECIPIENT_SERVICE_URL=http://localhost:8005

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
QUEUE_CONNECTION=redis
BROADCAST_DRIVER=redis
```

## Success Criteria

- [ ] All 5 services running independently on separate ports
- [ ] All 5 databases operational with partitioned data
- [ ] JWT authentication working across all services
- [ ] Cross-service API calls successful with proper error handling
- [ ] Distributed transaction (donation flow) working end-to-end
- [ ] Event participation with cross-service role validation working
- [ ] Fund allocation with cross-service validation working
- [ ] Admin approval workflows functional
- [ ] Frontend making API calls and displaying data correctly
- [ ] All tests passing (unit, integration, e2e)
- [ ] No data inconsistencies between services
- [ ] Services handle failures gracefully (circuit breaker working)
- [ ] Performance acceptable (response times < 500ms for most endpoints)

## Risk Mitigation

1. **Service Unavailability**: Circuit breaker pattern, health checks, retry logic
2. **Data Inconsistency**: Saga pattern, reconciliation jobs, event-driven sync
3. **Performance**: Caching, batch endpoints, async operations
4. **Security**: Token validation, HTTPS, rate limiting, request logging
5. **Deployment**: Docker containerization, health checks, monitoring, rollback plan
