@echo off
echo ========================================
echo Charity-Izz Distributed Setup - izati
echo ========================================
echo.

echo Step 1: Copying .env template...
copy env-templates\.env.izati .env
echo.

echo Step 2: Generating Laravel app key...
php artisan key:generate
echo.

echo Step 3: Installing Composer dependencies...
call composer install
echo.

echo Step 4: Installing NPM dependencies...
call npm install
echo.

echo Step 5: Starting Docker containers (PostgreSQL Event DB)...
docker-compose -f docker-compose-izati.yml up -d
echo.

echo Waiting for database to be ready (30 seconds)...
timeout /t 30 /nobreak >nul
echo.

echo Step 6: Running migrations on izati database...
php artisan migrate --database=izati --force
echo.

echo Step 7: Seeding database...
php artisan db:seed --database=izati --force
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Your database is now running and exposed on port 5433
echo Other team members can connect to your IP address on this port.
echo.
echo Next steps:
echo 1. Find your IP address: ipconfig
echo 2. Share your IP with the team
echo 3. Update .env with team members' IP addresses
echo 4. Test connections: php test-connections.php
echo 5. Start Laravel: php artisan serve
echo.
pause
