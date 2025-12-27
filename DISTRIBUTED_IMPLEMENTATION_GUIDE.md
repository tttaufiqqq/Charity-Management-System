# Distributed Database Architecture Implementation Guide

## Overview

The Charity-Izz platform has been transformed into a **heterogeneous distributed microservices architecture** with 5 independent database services. This implementation follows the API-based communication pattern with application-level referential integrity.

## Architecture Summary

### Service Distribution

| Service | Owner | Database | Port | Connection Name | Tables |
|---------|-------|----------|------|----------------|--------|
| **User Management** | Izz | PostgreSQL | 5432 | `izz` | user, roles, permissions, model_has_roles |
| **Volunteer Management** | Sashvini | MariaDB | 3307 | `sashvini` | volunteer, volunteer_skill, skill, event, event_participation |
| **Event Management** | Izzati | PostgreSQL | 5433 | `izati` | organization, campaign, event_role |
| **Donation Management** | Hannah | MySQL | 3306 | `hannah` | donor, donation, donation_allocation |
| **Recipient Management** | Adam | MySQL | 3308 | `adam` | public_profile, recipient |

### Key Architectural Decisions

1. **Event table ownership**: Volunteer Service (sashvini) - source of truth for event data
2. **Campaign table ownership**: Event Management Service (izati) - source of truth for campaigns
3. **Cross-service communication**: RESTful APIs with HTTP service clients
4. **Referential integrity**: Enforced at application level via API validation
5. **Data consistency**: Event-driven synchronization using Laravel Broadcasting + Redis
6. **Circuit breaker**: Implemented to prevent cascading failures

## Implementation Components

### 1. Database Configuration

**File**: `config/database.php`

Five heterogeneous database connections configured:
- `izz` - PostgreSQL (User Service)
- `sashvini` - MariaDB (Volunteer Service)
- `izati` - PostgreSQL (Event Management)
- `hannah` - MySQL (Donation Service)
- `adam` - MySQL (Recipient Service)

Each model specifies its connection via `protected $connection` property.

### 2. Docker Infrastructure

**File**: `docker-compose-microservices.yml`

Provides containerized database services with:
- Health checks for all databases
- Redis for queues and event broadcasting
- Persistent volumes for data
- Network isolation

**Start services**:
```bash
docker-compose -f docker-compose-microservices.yml up -d
```

### 3. API Service Clients

**Base Class**: `app/Services/Api/BaseApiService.php`

Features:
- HTTP request handling with timeout and retries
- Circuit breaker pattern (opens after 5 failures, closes after 60s)
- Response caching with TTL support
- Error handling and logging

**Service Clients**:
- `UserApiService` - User validation and role checks
- `CampaignApiService` - Campaign operations and fund tracking
- `EventApiService` - Event management and participation
- `RecipientApiService` - Recipient approval and allocation tracking
- `OrganizationApiService` - Organization validation

### 4. API Controllers

**Location**: `app/Http/Controllers/Api/`

Each service domain has dedicated controllers:
- `UserApiController` - User CRUD and role management
- `CampaignApiController` - Campaign CRUD, approval, fund sync
- `EventApiController` - Event CRUD, participant management
- `DonationApiController` - Donation processing, allocation
- `RecipientApiController` - Recipient applications, approval
- `OrganizationApiController` - Organization CRUD

### 5. API Routes

**File**: `routes/api.php`

RESTful endpoints organized by service:
- `/api/v1/users/*` - User Service
- `/api/v1/campaigns/*` - Campaign Service
- `/api/v1/events/*` - Event Service
- `/api/v1/donations/*` - Donation Service
- `/api/v1/recipients/*` - Recipient Service
- `/api/v1/organizations/*` - Organization Service
- `/api/v1/health` - Health check (all databases)

### 6. Cross-Service Validation

Foreign key constraints removed from migrations. Validation enforced in controllers:

**Example - Donation Creation** (`DonationApiController@store`):
```php
// Validate campaign exists and is active via API
if (!$this->campaignService->isActive($validated['Campaign_ID'])) {
    return response()->json(['error' => 'Campaign not found or not active'], 404);
}
```

**Example - Fund Allocation** (`DonationApiController@createAllocation`):
```php
// Validate recipient is approved via API
if (!$this->recipientService->isApproved($validated['Recipient_ID'])) {
    return response()->json(['error' => 'Recipient not approved'], 400);
}

// Check available funds via API
$availableFunds = $this->campaignService->getAvailableFunds($validated['Campaign_ID']);
if ($validated['Amount_Allocated'] > $availableFunds) {
    return response()->json(['error' => 'Insufficient funds available'], 400);
}
```

### 7. Event-Driven Synchronization

**Events** (`app/Events/`):
- `DonationCompleted` - Triggers campaign collected amount update
- `CampaignApproved` - Notifies dependent services
- `CampaignUpdateFailed` - Compensating transaction trigger
- `EventCompleted` - Event completion notification
- `RecipientApproved` - Recipient approval notification

All events implement `ShouldBroadcast` for real-time propagation via Redis.

### 8. Distributed Transaction Pattern

**Donation Flow Example**:

1. **Donation Service** - Process payment, create donation, emit `DonationCompleted`
2. **Event Management Service** - Listen to event, update `Campaign.Collected_Amount`
3. **On Failure** - Emit `CampaignUpdateFailed`, mark donation for manual review

## Setup Instructions

### Prerequisites

- Docker & Docker Compose
- PHP 8.2+
- Composer
- Node.js & npm

### Step 1: Install Dependencies

```bash
composer install
npm install
```

### Step 2: Environment Configuration

The `.env` file has been configured with:
- Database connections for all 5 services
- Service URLs (localhost:8001-8005)
- Redis configuration for queues and broadcasting

**Key settings**:
```env
DB_CONNECTION=izz  # Default to User Service

# Service URLs
USER_SERVICE_URL=http://localhost:8001
VOLUNTEER_SERVICE_URL=http://localhost:8002
EVENT_SERVICE_URL=http://localhost:8003
DONATION_SERVICE_URL=http://localhost:8004
RECIPIENT_SERVICE_URL=http://localhost:8005

# Event Broadcasting
BROADCAST_CONNECTION=redis
QUEUE_CONNECTION=redis
```

### Step 3: Start Database Services

```bash
docker-compose -f docker-compose-microservices.yml up -d
```

Wait for health checks to pass:
```bash
docker-compose -f docker-compose-microservices.yml ps
```

### Step 4: Run Migrations

Run migrations on each database connection:

```bash
# User Service (izz - PostgreSQL)
php artisan migrate --database=izz

# Volunteer Service (sashvini - MariaDB)
php artisan migrate --database=sashvini

# Event Management (izati - PostgreSQL)
php artisan migrate --database=izati

# Donation Service (hannah - MySQL)
php artisan migrate --database=hannah

# Recipient Service (adam - MySQL)
php artisan migrate --database=adam
```

### Step 5: Seed Data

```bash
# Seed users and roles (izz)
php artisan db:seed --database=izz

# Seed other tables as needed
php artisan db:seed
```

### Step 6: Start Queue Worker

```bash
php artisan queue:listen redis
```

### Step 7: Start Application

```bash
php artisan serve --port=8000
```

### Step 8: Test API Endpoints

**Health Check**:
```bash
curl http://localhost:8000/api/v1/health
```

**Get Campaigns**:
```bash
curl http://localhost:8000/api/v1/campaigns
```

**Create Donation**:
```bash
curl -X POST http://localhost:8000/api/v1/donations \
  -H "Content-Type: application/json" \
  -d '{
    "Donor_ID": 1,
    "Campaign_ID": 1,
    "Amount": 100.00,
    "Payment_Method": "Online Banking"
  }'
```

## Critical Implementation Details

### Cross-Service References

**No Foreign Keys** - All cross-service relationships use integer fields without DB-level constraints:
- `volunteer.User_ID` → User Service (no FK)
- `donor.User_ID` → User Service (no FK)
- `donation.Campaign_ID` → Event Management Service (no FK)
- `event.Organizer_ID` → Event Management Service (no FK)
- `donation_allocation.Campaign_ID` → Event Management Service (no FK)
- `donation_allocation.Recipient_ID` → Recipient Service (no FK)

**Application-Level Validation** - Controllers validate references via API calls before creating records.

### Caching Strategy

Service clients cache API responses with configurable TTL:
```php
$campaign = $this->campaignService->find($id, 300); // Cache 5 minutes
```

Cache invalidation on updates:
```php
$this->invalidateCache("campaigns/{$campaignId}");
```

### Circuit Breaker Pattern

Protects against cascading failures:
- Opens after 5 consecutive failures
- Remains open for 60 seconds
- Automatically closes on successful call

### Error Handling

Service clients throw exceptions:
- `ServiceUnavailableException` - Circuit open
- `ApiCallException` - HTTP error response

Controllers catch and return appropriate JSON errors.

## Testing

### Unit Tests

```bash
php artisan test
```

### API Integration Tests

Use tools like Postman or curl to test cross-service workflows:

1. Create user → Create donor profile → Make donation → Verify campaign amount updated
2. Create organization → Create campaign → Admin approve → Verify status change
3. Create recipient → Admin approve → Allocate funds → Verify allocation recorded

### Health Monitoring

```bash
# Check all database connections
curl http://localhost:8000/api/v1/health

# Expected response:
{
  "status": "healthy",
  "databases": {
    "izz": "connected",
    "sashvini": "connected",
    "izati": "connected",
    "hannah": "connected",
    "adam": "connected"
  }
}
```

## Performance Considerations

1. **Caching** - Reduce API calls by caching reference data
2. **Batch Operations** - Minimize N+1 API calls
3. **Async Events** - Use queues for non-critical updates
4. **Connection Pooling** - Optimize database connections
5. **Response Timeout** - Set appropriate timeouts (default: 10s)

## Security

1. **API Authentication** - Implement token-based auth for production
2. **Input Validation** - All requests validated before processing
3. **HTTPS** - Use HTTPS in production
4. **Rate Limiting** - Implement throttling on API endpoints
5. **SQL Injection** - Prevented via Eloquent ORM

## Monitoring & Logging

- **Laravel Log** - Service call failures logged to `storage/logs/laravel.log`
- **Circuit Breaker** - Opens/closes logged with context
- **Event Failures** - Compensating transactions logged

View logs:
```bash
php artisan pail
```

## Troubleshooting

### Database Connection Issues

```bash
# Test connections
php artisan tinker
DB::connection('izz')->getPdo();
DB::connection('sashvini')->getPdo();
# ... etc
```

### Service Unavailable Errors

- Check circuit breaker status in cache
- Verify service URLs in `.env`
- Check network connectivity
- Review logs for specific errors

### Data Inconsistency

Run reconciliation to sync data:
```bash
php artisan donations:reconcile-campaign-totals
```

## Next Steps

1. **Implement Authentication Service** - Laravel Sanctum for JWT tokens
2. **Create API Gateway** - Single entry point for all services
3. **Add Monitoring** - Laravel Telescope, health dashboards
4. **Implement Reconciliation Jobs** - Scheduled tasks for eventual consistency
5. **Deploy to Production** - Dockerize application services
6. **Add Load Balancing** - Distribute traffic across service instances
7. **Implement Service Discovery** - Dynamic service registration

## Files Created/Modified

### Created Files:
- `docker-compose-microservices.yml` - Database infrastructure
- `routes/api.php` - API routes
- `app/Services/Api/BaseApiService.php` - Base service client
- `app/Services/Api/*ApiService.php` - Service clients (5 files)
- `app/Http/Controllers/Api/*ApiController.php` - API controllers (6 files)
- `app/Events/*.php` - Event classes (5 files)
- `DISTRIBUTED_IMPLEMENTATION_GUIDE.md` - This file

### Modified Files:
- `.env` - Added service URLs and Redis configuration
- `config/database.php` - Already had 5 connections configured
- `app/Models/*.php` - Already had connection specifications
- `database/migrations/2025_11_25_173447_create_event.php` - Removed FK constraint

## Summary

The Charity-Izz platform now operates as a distributed microservices architecture with:
- ✅ 5 heterogeneous databases (2 PostgreSQL, 1 MariaDB, 2 MySQL)
- ✅ API-based inter-service communication
- ✅ Application-level referential integrity
- ✅ Event-driven synchronization
- ✅ Circuit breaker pattern
- ✅ Caching for performance
- ✅ Comprehensive error handling

This architecture provides:
- **Scalability** - Services can scale independently
- **Resilience** - Circuit breaker prevents cascading failures
- **Flexibility** - Each service uses optimal database technology
- **Maintainability** - Clear service boundaries and responsibilities
