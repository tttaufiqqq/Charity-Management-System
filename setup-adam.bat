@echo off
echo ========================================
echo Charity-Izz Distributed Setup - adam
echo ========================================
echo.

echo Step 1: Copying .env template...
copy env-templates\.env.adam .env
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

echo Step 5: Starting Docker containers (MySQL Recipient DB)...
docker-compose -f docker-compose-adam.yml up -d
echo.

echo Waiting for database to be ready (30 seconds)...
timeout /t 30 /nobreak >nul
echo.

echo Step 6: Running migrations on adam database...
php artisan migrate --database=adam --force
echo.

echo Step 7: Seeding database...
php artisan db:seed --database=adam --force
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Your database is now running and exposed on port 3308
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
