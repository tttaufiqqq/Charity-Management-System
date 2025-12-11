# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Charity-Izz** is a comprehensive charity management platform built with Laravel 12 that connects organizations, donors, volunteers, and recipients in the philanthropic ecosystem. The platform manages the complete lifecycle of charitable giving: campaign creation, donation collection, volunteer event management, and fund allocation to recipients.

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates, Livewire 3.7, Alpine.js 3.x, TailwindCSS 3.x
- **Database**: PostgreSQL (primary)
- **Build**: Vite 7.x
- **Testing**: Pest PHP 4.x
- **Auth**: Laravel Breeze (Blade)
- **Authorization**: Spatie Laravel-Permission (role-based)
- **PDF Generation**: Barryvdh/Laravel-DomPDF

## Common Commands

### Setup
```bash
# Initial setup (composer script)
composer setup

# Manual setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
```

### Development
```bash
# Run development servers (recommended - runs all 3 concurrently)
composer dev
# This runs: php artisan serve, php artisan queue:listen, npm run dev

# Or run individually:
php artisan serve
php artisan queue:listen --tries=1
npm run dev

# Frontend only
npm run dev      # Development with hot reload
npm run build    # Production build
```

### Testing
```bash
# Run all tests
composer test
# OR
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with filter
php artisan test --filter=testName
```

### Code Quality
```bash
# Format code (must run before finalizing changes)
vendor/bin/pint --dirty

# View logs
php artisan pail
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh migration with seeding (development)
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

## Application Architecture

### User Roles & Access

The application uses Spatie Laravel-Permission for role-based access control:

- **admin**: Full system access, approves campaigns/events/recipients, analytics dashboard
- **organizer**: Creates and manages campaigns and events, allocates funds to recipients
- **volunteer**: Participates in events, manages skills and availability
- **donor**: Makes donations to campaigns, views donation history and receipts
- **public**: Browses campaigns/events, applies as recipient

Default admin credentials: `admin@gmail.com` / `password`

### Core Models & Relationships

**User Model** - Central authentication model with role-based profiles:
- hasOne: Donor, PublicProfile, Organization, Volunteer

**Organization Model** (`organization` table):
- belongsTo: User (via Organizer_ID)
- hasMany: Campaign, Event

**Campaign Model** (`campaign` table):
- belongsTo: Organization
- hasMany: Donation, DonationAllocation
- belongsToMany: Recipient (through donation_allocation)
- Statuses: Active, Completed, Pending (requires admin approval)
- Scopes: `active()`, `completed()`

**Event Model** (`event` table):
- belongsTo: Organization (via Organizer_ID)
- belongsToMany: Volunteer (through event_participation with Total_Hours)
- Scopes: `upcoming()`, `completed()`, `ongoing()`

**Donation Model** (`donation` table):
- belongsTo: Donor, Campaign
- Unique receipt number generation

**Volunteer Model** (`volunteer` table):
- belongsTo: User
- belongsToMany: Skill (through volunteer_skill with Skill_Level)
- belongsToMany: Event (through event_participation)

**Recipient Model** (`recipient` table):
- belongsTo: PublicProfile
- belongsToMany: Campaign (through donation_allocation)
- Statuses: Pending, Approved (requires admin approval)
- Scopes: `pending()`, `approved()`

### Primary Keys Convention

**IMPORTANT**: This application uses custom primary key naming (not Laravel's default `id`):
- User: `id` (default)
- Organization: `Organization_ID`
- Campaign: `Campaign_ID`
- Event: `Event_ID`
- Donation: `Donation_ID`
- Volunteer: `Volunteer_ID`
- Donor: `Donor_ID`
- Recipient: `Recipient_ID`
- Skill: `Skill_ID`
- PublicProfile: `Public_ID`

When creating new models or migrations in this domain, follow the existing `{ModelName}_ID` convention.

### Main Modules

**Volunteer Management** (`VolunteerController`):
- Routes: `/volunteer/*`
- Dashboard, profile management, event registration/cancellation, skills CRUD

**Event Management** (`EventManagementController`):
- Routes: `/events/*`, `/campaigns/all`
- Campaign and event CRUD for organizers
- Volunteer management for events (track hours, bulk updates)
- Admin approval workflows

**Donation Management** (`DonationManagementController`):
- Routes: `/campaigns/*`, `/my-donations`, `/donation/*`
- Browse campaigns with search/filter/sort
- Donation process with multiple payment methods
- PDF receipt generation (individual and bulk download)

**Recipient Management** (`RecipientManagementController`):
- Routes: `/recipients/*`, `/campaigns/{id}/allocate`
- Fund allocation from campaigns to recipients
- Admin approval of recipient applications

**Reporting & Analytics** (Livewire components):
- Routes: `/admin/analytics/*`
- Components: `AdminDashboard`, `CampaignAnalytics`, `DonorAnalytics`, `EventAnalytics`
- Time-series charts for donations, campaigns, events, user growth
- Date range filtering (30/60/90 days)

### Route Structure

All authenticated routes use `auth` middleware. Role-specific routes use `role:` middleware.

```
/volunteer/* - role:volunteer
/events/* - role:organizer (management)
/campaigns/* - role:organizer (management)
/my-donations - role:donor
/recipients/* - role:organizer (allocation) OR role:admin (approval)
/admin/* - role:admin
/public/* - Public browsing (no auth required for some)
/profile/* - Authenticated users
```

### Database Naming Conventions

Tables use snake_case. This application uses a **non-standard naming approach**:
- Organization table: `organization` (not `organizations`)
- Campaign table: `campaign` (not `campaigns`)
- Most tables are singular

Follow existing table naming when creating new migrations.

### Payment Methods

The application supports multiple payment methods (stored as strings in donations):
- Online Banking
- Credit/Debit Card
- E-Wallet
- Other

Payment processing is currently mocked - no actual gateway integration exists.

## Development Guidelines

### Creating New Features

1. **Use Artisan commands** to generate files:
   ```bash
   php artisan make:model ModelName -mfsc  # model, migration, factory, seeder, controller
   php artisan make:controller ControllerName
   php artisan make:livewire ComponentName
   php artisan make:test TestName  # feature test
   php artisan make:test TestName --unit  # unit test
   ```

2. **Follow existing patterns**:
   - Check sibling files for structure, naming, and approach
   - Use custom primary key naming (`{ModelName}_ID`)
   - Follow singular table naming where established
   - Use role middleware for authorization

3. **Database operations**:
   - Always use Eloquent over `DB::` facade
   - Eager load relationships to prevent N+1 queries
   - Use query scopes for reusable filters
   - Use decimal(10,2) for monetary values

4. **Frontend**:
   - Use Livewire for reactive components (see analytics dashboards)
   - Use Alpine.js for simple interactivity
   - Follow TailwindCSS conventions (use `gap` for spacing, support dark mode if enabled)
   - Check for existing Blade components before creating new ones

### Testing Requirements

Every change must include tests. Use Pest PHP syntax:

```php
it('creates a donation', function () {
    $campaign = Campaign::factory()->create();
    $donor = Donor::factory()->create();

    $donation = Donation::create([
        'Donor_ID' => $donor->Donor_ID,
        'Campaign_ID' => $campaign->Campaign_ID,
        'Amount' => 100.00,
        // ...
    ]);

    expect($donation)->not->toBeNull();
});
```

Run tests before finalizing:
```bash
php artisan test --filter=relevantTestName
```

### Code Formatting

Always run before committing:
```bash
vendor/bin/pint --dirty
```

### Validation

Always create Form Request classes for validation (not inline in controllers):
```bash
php artisan make:request StoreCampaignRequest
```

Check sibling Form Requests to see if the app uses array or string-based validation rules.

## Common Patterns

### Approval Workflows

Campaigns, events, and recipients require admin approval:
1. Created with `Status = 'Pending'`
2. Admin reviews at `/admin/*` routes
3. Admin approves/rejects
4. Status changes to `Active` or `Approved`

### Fund Allocation Flow

1. Campaign collects donations → `Collected_Amount` increases
2. Organizer views eligible recipients for campaign
3. Organizer allocates funds via `DonationAllocation` model
4. Allocation records track: `Recipient_ID`, `Campaign_ID`, `Amount_Allocated`, `Allocated_At`

### Event Participation

1. Volunteer browses upcoming events
2. Registers via `EventParticipation` record (Status: 'Registered')
3. Attends event
4. Organizer records hours (auto-calculated from event dates or manual)
5. Hours stored in `event_participation.Total_Hours`

### Receipt Generation

Donations automatically generate unique receipt numbers. PDFs are generated on-demand using DomPDF:
```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('donation-management.receipt', $data);
return $pdf->download("receipt-{$receiptNo}.pdf");
```

## Important Notes

1. **No API layer**: This is a Blade-based application with no RESTful API controllers
2. **Queue infrastructure exists** but no custom Job classes yet (jobs table created)
3. **Payment processing is mocked** - no real gateway integration
4. **No file uploads** for campaign/event images
5. **No email notifications** configured (mail driver exists but unused)
6. **Laravel 12 structure**: No `app/Http/Middleware/` directory, middleware registered in `bootstrap/app.php`
7. **Service Providers**: Only `AppServiceProvider` exists (minimal)
8. **Livewire for analytics only**: Most views are traditional Blade templates

## Useful Queries

### Get active campaigns with donation totals:
```php
Campaign::query()
    ->where('Status', 'Active')
    ->with('organization')
    ->withSum('donations', 'Amount')
    ->get();
```

### Get volunteer's upcoming events:
```php
$volunteer->events()
    ->upcoming()
    ->with('organization')
    ->get();
```

### Get recipient allocations:
```php
$recipient->campaigns()
    ->withPivot('Amount_Allocated', 'Allocated_At')
    ->get();
```

## Database Seeding

The application provides comprehensive seeders for development:

```bash
php artisan db:seed
```

This creates:
1. Roles and admin user (admin@gmail.com / password)
2. Skills catalog
3. Test users with various roles
4. Sample events, campaigns, donations
5. Sample fund allocations

## Frontend Assets

When frontend changes aren't reflected:
1. Ensure Vite dev server is running: `npm run dev`
2. Or rebuild assets: `npm run build`
3. Or use the comprehensive dev script: `composer dev`

## Configuration

- **Database**: Configure in `.env` (PostgreSQL recommended)
- **Queue**: Uses database driver (ensure queue worker is running)
- **Mail**: Configured but not actively used
- **Cache**: Database/file driver (no Redis by default)
- **Timezone**: UTC (configurable in `config/app.php`)
