# Charity-Izz - Quick Setup Guide

Complete setup guide for group members to get the project running locally.

## Prerequisites

Before starting, ensure you have:
- **PHP 8.2 or higher** (Check: `php -v`)
- **Composer** (Check: `composer -V`)
- **Node.js 18+ and NPM** (Check: `node -v` and `npm -v`)
- **PostgreSQL 14+** (or MySQL 8+ as alternative)
- **Git** (Check: `git --version`)

## Step 1: Clone the Repository

```bash
git clone <repository-url>
cd Charity-Izz
```

## Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

## Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Step 4: Database Setup

### Option A: PostgreSQL (Recommended)

1. Create a new database:
```sql
-- Login to PostgreSQL
psql -U postgres

-- Create database
CREATE DATABASE charity_izz;

-- Create user (optional)
CREATE USER charity_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE charity_izz TO charity_user;
```

2. Update `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=charity_izz
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Option B: MySQL (Alternative)

1. Create database:
```sql
CREATE DATABASE charity_izz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=charity_izz
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Step 5: Run Migrations and Seeders

```bash
# Run migrations to create tables
php artisan migrate

# Seed the database with realistic sample data
php artisan db:seed
```

**Note**: If you need to reset and start fresh:
```bash
php artisan migrate:fresh --seed
```

## Step 6: Build Frontend Assets

```bash
# For development (with hot reload)
npm run dev

# For production
npm run build
```

## Step 7: Start Development Server

### Option A: Using Composer Script (Recommended)
This runs all three servers concurrently:
```bash
composer dev
```

This command starts:
- Laravel development server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Vite dev server (`npm run dev`)

### Option B: Manual (Run in separate terminals)

Terminal 1 - Laravel Server:
```bash
php artisan serve
```

Terminal 2 - Queue Worker:
```bash
php artisan queue:listen --tries=1
```

Terminal 3 - Vite Dev Server:
```bash
npm run dev
```

## Step 8: Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

## Default Login Credentials

After seeding, you can log in with these accounts:

### Admin Account
- **Email**: `admin@gmail.com`
- **Password**: `password`
- **Access**: Full system access, approve campaigns/events/recipients

### Donor Accounts (8 total)
- **Email**: `ahmad.donor@gmail.com`
- **Password**: `password`
- (More donors: siti.donor@gmail.com, kumar.donor@gmail.com, etc.)

### Volunteer Accounts (6 total)
- **Email**: `izzati.volunteer@gmail.com`
- **Password**: `password`

### Organizer Accounts (4 organizations)
- **Email**: `admin@ykr.org.my`
- **Password**: `password`

### Public Accounts (4 total)
- **Email**: `hassan.public@gmail.com`
- **Password**: `password`

**All accounts use the same password: `password`**

## What Sample Data is Included?

The seeders create realistic Malaysian charity data:

✅ **4 Charitable Organizations** (Malaysian NGOs with proper registration numbers)
✅ **48 Campaigns** (7 active + 5 completed per organization)
✅ **~60 Volunteer Events** (Upcoming, Ongoing, Completed, Pending)
✅ **11 Recipients** (with detailed needs descriptions)
✅ **200+ Donations** (with realistic amounts and receipt numbers)
✅ **8 Donors** (Malaysian names with donation histories)
✅ **6 Volunteers** (with skills and event participation)
✅ **Fund Allocations** (linking donations to recipients)

## Common Issues & Solutions

### Issue: "SQLSTATE[42000]: Syntax error or access violation"
**Solution**: Make sure your database exists and credentials in `.env` are correct.

### Issue: "Class 'Spatie\Permission\Models\Role' not found"
**Solution**: Run `composer install` again and clear cache:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue: "Vite manifest not found"
**Solution**: Make sure you're running `npm run dev` or have run `npm run build`.

### Issue: Migration fails with "SQLSTATE[42P01]: Undefined table"
**Solution**: Check migration order. Run `php artisan migrate:fresh` to reset.

### Issue: "The recipient table has wrong column type"
**Solution**: We fixed the `Approved_At` column from decimal to timestamp. Run fresh migration:
```bash
php artisan migrate:fresh --seed
```

## Development Workflow

### Making Changes

1. **Database Changes**: Create new migration
   ```bash
   php artisan make:migration description_of_change
   ```

2. **Code Formatting**: Run Laravel Pint before committing
   ```bash
   vendor/bin/pint --dirty
   ```

3. **Testing**: Run tests
   ```bash
   php artisan test
   ```

### Viewing Logs

```bash
# Real-time log viewing
php artisan pail

# Or check log files
tail -f storage/logs/laravel.log
```

## Project Structure Overview

```
charity-izz/
├── app/
│   ├── Http/Controllers/        # Controllers for each module
│   │   ├── VolunteerController.php
│   │   ├── EventManagementController.php
│   │   ├── DonationManagementController.php
│   │   └── RecipientManagementController.php
│   ├── Models/                  # Eloquent models
│   └── Livewire/               # Livewire components (analytics)
├── database/
│   ├── migrations/             # Database schema (with comments & indexes)
│   └── seeders/                # Realistic sample data
├── resources/
│   ├── views/                  # Blade templates
│   └── js/                     # Frontend JavaScript
├── routes/
│   └── web.php                 # Application routes
└── tests/                      # Pest PHP tests
```

## Key Features to Explore

1. **Campaign Management** (`/campaigns`)
   - Browse active campaigns
   - Make donations
   - Download receipts (PDF)

2. **Volunteer Events** (`/volunteer`)
   - View upcoming events
   - Register for events
   - Track volunteer hours

3. **Admin Dashboard** (`/admin/analytics`)
   - Campaign analytics
   - Donor analytics
   - Event analytics

4. **Fund Allocation** (`/campaigns/{id}/allocate`)
   - Allocate campaign funds to recipients
   - Track distributions

## Need Help?

- Check `CLAUDE.md` for detailed project architecture
- Review existing code patterns before implementing new features
- Ask group members or instructor

## Quick Reference Commands

```bash
# Setup (one-time)
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Development (daily)
composer dev                    # Starts all servers

# Reset database
php artisan migrate:fresh --seed

# Code quality
vendor/bin/pint --dirty        # Format code
php artisan test               # Run tests

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

**Last Updated**: December 2025
**Laravel Version**: 12.x
**PHP Version**: 8.2+
