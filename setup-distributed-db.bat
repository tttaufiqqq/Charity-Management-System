@echo off
echo ======================================
echo Charity-Izz Distributed DB Setup
echo ======================================

echo.
echo Step 1: Starting Docker containers...
docker-compose -f docker-compose-microservices.yml up -d

echo.
echo Step 2: Waiting for databases to be ready...
timeout /t 10 /nobreak

echo.
echo Step 3: Running migrations on all databases...

echo   - Migrating User Service (izz - PostgreSQL)
php artisan migrate --database=izz --force

echo   - Migrating Volunteer Service (sashvini - MariaDB)
php artisan migrate --database=sashvini --force

echo   - Migrating Event Management (izati - PostgreSQL)
php artisan migrate --database=izati --force

echo   - Migrating Donation Service (hannah - MySQL)
php artisan migrate --database=hannah --force

echo   - Migrating Recipient Service (adam - MySQL)
php artisan migrate --database=adam --force

echo.
echo Step 4: Seeding data...
php artisan db:seed --force

echo.
echo ======================================
echo Setup Complete!
echo ======================================
echo.
echo Next steps:
echo   1. Start queue worker: php artisan queue:listen redis
echo   2. Start application: php artisan serve
echo   3. Test API: curl http://localhost:8000/api/v1/health
echo.
echo View logs: docker-compose -f docker-compose-microservices.yml logs -f
echo.
pause
