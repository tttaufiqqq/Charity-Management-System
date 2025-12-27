#!/bin/bash

echo "======================================"
echo "Charity-Izz Distributed DB Setup"
echo "======================================"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo ""
echo -e "${YELLOW}Step 1: Starting Docker containers...${NC}"
docker-compose -f docker-compose-microservices.yml up -d

echo ""
echo -e "${YELLOW}Step 2: Waiting for databases to be ready...${NC}"
sleep 10

echo ""
echo -e "${YELLOW}Step 3: Running migrations on all databases...${NC}"

echo -e "${GREEN}  → Migrating User Service (izz - PostgreSQL)${NC}"
php artisan migrate --database=izz --force

echo -e "${GREEN}  → Migrating Volunteer Service (sashvini - MariaDB)${NC}"
php artisan migrate --database=sashvini --force

echo -e "${GREEN}  → Migrating Event Management (izati - PostgreSQL)${NC}"
php artisan migrate --database=izati --force

echo -e "${GREEN}  → Migrating Donation Service (hannah - MySQL)${NC}"
php artisan migrate --database=hannah --force

echo -e "${GREEN}  → Migrating Recipient Service (adam - MySQL)${NC}"
php artisan migrate --database=adam --force

echo ""
echo -e "${YELLOW}Step 4: Seeding data...${NC}"
php artisan db:seed --force

echo ""
echo -e "${YELLOW}Step 5: Testing database connections...${NC}"
php artisan tinker --execute="
try {
    echo 'User Service (izz): ' . (DB::connection('izz')->getPdo() ? '✓ Connected' : '✗ Failed') . PHP_EOL;
    echo 'Volunteer Service (sashvini): ' . (DB::connection('sashvini')->getPdo() ? '✓ Connected' : '✗ Failed') . PHP_EOL;
    echo 'Event Management (izati): ' . (DB::connection('izati')->getPdo() ? '✓ Connected' : '✗ Failed') . PHP_EOL;
    echo 'Donation Service (hannah): ' . (DB::connection('hannah')->getPdo() ? '✓ Connected' : '✗ Failed') . PHP_EOL;
    echo 'Recipient Service (adam): ' . (DB::connection('adam')->getPdo() ? '✓ Connected' : '✗ Failed') . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo -e "${GREEN}======================================"
echo -e "Setup Complete!"
echo -e "======================================${NC}"
echo ""
echo "Next steps:"
echo "  1. Start queue worker: php artisan queue:listen redis"
echo "  2. Start application: php artisan serve"
echo "  3. Test API: curl http://localhost:8000/api/v1/health"
echo ""
echo "View logs: docker-compose -f docker-compose-microservices.yml logs -f"
echo ""
