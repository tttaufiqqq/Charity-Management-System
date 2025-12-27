#!/bin/bash

echo "========================================"
echo "Charity-Izz Distributed Setup - izzhilmy"
echo "========================================"
echo ""

echo "Step 1: Copying .env template..."
cp env-templates/.env.izzhilmy .env
echo ""

echo "Step 2: Generating Laravel app key..."
php artisan key:generate
echo ""

echo "Step 3: Installing Composer dependencies..."
composer install
echo ""

echo "Step 4: Installing NPM dependencies..."
npm install
echo ""

echo "Step 5: Starting Docker containers (PostgreSQL User DB)..."
docker-compose -f docker-compose-izzhilmy.yml up -d
echo ""

echo "Waiting for database to be ready (30 seconds)..."
sleep 30
echo ""

echo "Step 6: Running migrations on izz database..."
php artisan migrate --database=izz --force
echo ""

echo "Step 7: Seeding database..."
php artisan db:seed --database=izz --force
echo ""

echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "Your database is now running and exposed on port 5432"
echo "Other team members can connect to your IP address on this port."
echo ""
echo "Next steps:"
echo "1. Find your IP address: ifconfig or ip addr"
echo "2. Share your IP with the team"
echo "3. Update .env with team members' IP addresses"
echo "4. Test connections: php test-connections.php"
echo "5. Start Laravel: php artisan serve"
echo ""
