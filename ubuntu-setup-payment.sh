#!/bin/bash

###############################################################################
# Ubuntu Payment Gateway Setup Script
# For Charity-Izz Laravel Application
###############################################################################

echo "=========================================="
echo "Ubuntu Payment Gateway Setup"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Detect PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo -e "${GREEN}Detected PHP Version: ${PHP_VERSION}${NC}"
echo ""

# Step 1: Update package list
echo -e "${YELLOW}Step 1: Updating package list...${NC}"
sudo apt-get update -qq

# Step 2: Install required PHP extensions
echo -e "${YELLOW}Step 2: Installing PHP extensions...${NC}"
sudo apt-get install -y php${PHP_VERSION}-curl php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml openssl ca-certificates

# Step 3: Update CA certificates
echo -e "${YELLOW}Step 3: Updating CA certificates...${NC}"
sudo update-ca-certificates

# Step 4: Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    echo "Please create .env file from .env.example first"
    exit 1
fi

# Step 5: Check ToyyibPay settings in .env
echo -e "${YELLOW}Step 4: Checking ToyyibPay configuration...${NC}"
if ! grep -q "TOYYIBPAY_VERIFY_SSL" .env; then
    echo "Adding TOYYIBPAY_VERIFY_SSL to .env..."
    echo "" >> .env
    echo "# Payment Gateway SSL Configuration" >> .env
    echo "TOYYIBPAY_VERIFY_SSL=true" >> .env
    echo -e "${GREEN}✓ Added TOYYIBPAY_VERIFY_SSL=true to .env${NC}"
else
    echo -e "${GREEN}✓ TOYYIBPAY_VERIFY_SSL already configured${NC}"
fi

# Step 6: Set file permissions for storage
echo -e "${YELLOW}Step 5: Setting file permissions...${NC}"
if [ -f storage/cacert.pem ]; then
    chmod 644 storage/cacert.pem
    echo -e "${GREEN}✓ Set permissions on cacert.pem${NC}"
else
    echo -e "${YELLOW}⚠ cacert.pem not found (this is OK, will use system CA certs)${NC}"
fi

# Ensure storage directory has correct permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Step 7: Set ownership (if running as root/sudo)
if [ "$EUID" -eq 0 ]; then
    echo -e "${YELLOW}Step 6: Setting ownership...${NC}"

    # Detect web server user
    if id "www-data" &>/dev/null; then
        WEB_USER="www-data"
    elif id "nginx" &>/dev/null; then
        WEB_USER="nginx"
    elif id "apache" &>/dev/null; then
        WEB_USER="apache"
    else
        WEB_USER=$(whoami)
        echo -e "${YELLOW}⚠ Could not detect web server user, using: ${WEB_USER}${NC}"
    fi

    chown -R ${WEB_USER}:${WEB_USER} storage bootstrap/cache
    echo -e "${GREEN}✓ Set ownership to ${WEB_USER}${NC}"
fi

# Step 8: Clear Laravel caches
echo -e "${YELLOW}Step 7: Clearing Laravel caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

# Step 9: Rebuild config cache
echo -e "${YELLOW}Step 8: Rebuilding config cache...${NC}"
php artisan config:cache
echo -e "${GREEN}✓ Config cached${NC}"

# Step 10: Test PHP extensions
echo ""
echo -e "${YELLOW}Step 9: Verifying PHP extensions...${NC}"
if php -m | grep -q curl; then
    echo -e "${GREEN}✓ cURL extension: Enabled${NC}"
else
    echo -e "${RED}✗ cURL extension: Missing${NC}"
fi

if php -m | grep -q openssl; then
    echo -e "${GREEN}✓ OpenSSL extension: Enabled${NC}"
else
    echo -e "${RED}✗ OpenSSL extension: Missing${NC}"
fi

if php -m | grep -q mbstring; then
    echo -e "${GREEN}✓ mbstring extension: Enabled${NC}"
else
    echo -e "${RED}✗ mbstring extension: Missing${NC}"
fi

# Step 11: Test SSL connection to ToyyibPay
echo ""
echo -e "${YELLOW}Step 10: Testing connection to ToyyibPay...${NC}"
if curl -s -o /dev/null -w "%{http_code}" https://dev.toyyibpay.com | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✓ Successfully connected to ToyyibPay sandbox${NC}"
else
    echo -e "${YELLOW}⚠ Could not connect to ToyyibPay (check firewall/network)${NC}"
fi

# Step 12: Restart web server (if running as root)
if [ "$EUID" -eq 0 ]; then
    echo ""
    echo -e "${YELLOW}Step 11: Restarting web server...${NC}"

    # Try to restart Nginx
    if systemctl is-active --quiet nginx; then
        systemctl restart nginx
        echo -e "${GREEN}✓ Nginx restarted${NC}"
    fi

    # Try to restart PHP-FPM
    if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
        systemctl restart php${PHP_VERSION}-fpm
        echo -e "${GREEN}✓ PHP-FPM restarted${NC}"
    fi

    # Try to restart Apache
    if systemctl is-active --quiet apache2; then
        systemctl restart apache2
        echo -e "${GREEN}✓ Apache restarted${NC}"
    fi
else
    echo ""
    echo -e "${YELLOW}Note: Run with sudo to restart web server automatically${NC}"
    echo "To restart manually, run:"
    echo "  sudo systemctl restart nginx"
    echo "  sudo systemctl restart php${PHP_VERSION}-fpm"
fi

# Summary
echo ""
echo "=========================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Verify your .env has correct ToyyibPay credentials"
echo "2. Test payment gateway by making a donation"
echo "3. Check logs if issues persist: tail -f storage/logs/laravel.log"
echo ""
echo "For detailed guide, see: UBUNTU_PAYMENT_GATEWAY_GUIDE.md"
echo ""

# Display current ToyyibPay configuration
echo "Current ToyyibPay Configuration:"
echo "--------------------------------"
grep "TOYYIBPAY" .env | sed 's/=.*_KEY=.*/=***HIDDEN***/g' | sed 's/=.*_SECRET=.*/=***HIDDEN***/g'
echo ""
