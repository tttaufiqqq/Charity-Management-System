# Ubuntu Payment Gateway Setup Guide

## Overview

This guide explains how to fix ToyyibPay payment gateway issues on Ubuntu/Linux systems. The payment gateway works on Windows but may fail on Ubuntu due to SSL certificate verification differences.

## The Problem

The payment gateway uses SSL certificate verification that behaves differently on Ubuntu:

1. **SSL Certificate Path** - Custom `cacert.pem` file may not be accessible
2. **File Permissions** - Ubuntu web server (www-data/nginx/apache) may lack read permissions
3. **System CA Certificates** - Ubuntu uses different CA certificate locations than Windows
4. **OpenSSL Configuration** - Different OpenSSL versions and configurations

## The Solution

The codebase has been updated to automatically detect the environment and use the appropriate SSL verification method.

### Files Modified

1. `app/Services/ToyyibPayService.php` - Smart SSL certificate handling
2. `config/services.php` - Added `verify_ssl` configuration option

---

## Setup Instructions for Ubuntu

### Step 1: Install Required PHP Extensions

```bash
# Update package list
sudo apt-get update

# Install required PHP extensions
sudo apt-get install -y php-curl php-mbstring php-xml openssl ca-certificates

# For specific PHP version (e.g., PHP 8.2)
sudo apt-get install -y php8.2-curl php8.2-mbstring php8.2-xml

# Verify installations
php -m | grep -E 'curl|mbstring|openssl'
```

### Step 2: Update CA Certificates

```bash
# Update system CA certificates
sudo apt-get install -y ca-certificates
sudo update-ca-certificates

# If you need to add custom certificates
# sudo cp /path/to/custom-cert.crt /usr/local/share/ca-certificates/
# sudo update-ca-certificates
```

### Step 3: Configure Environment Variables

Edit your `.env` file and add/update these settings:

#### Option A: Use System CA Certificates (Recommended for Production)

```env
TOYYIBPAY_SECRET_KEY=your_secret_key_here
TOYYIBPAY_CATEGORY_CODE=your_category_code_here
TOYYIBPAY_SANDBOX=true
TOYYIBPAY_VERIFY_SSL=true
```

#### Option B: Disable SSL Verification (Development/Testing Only)

```env
TOYYIBPAY_SECRET_KEY=your_secret_key_here
TOYYIBPAY_CATEGORY_CODE=your_category_code_here
TOYYIBPAY_SANDBOX=true
TOYYIBPAY_VERIFY_SSL=false
```

**⚠️ WARNING**: Never use `TOYYIBPAY_VERIFY_SSL=false` in production! This disables SSL security checks.

### Step 4: Set File Permissions (If Using Custom cacert.pem)

If you want to use the custom `storage/cacert.pem` file:

```bash
# Navigate to project directory
cd /path/to/Charity-Izz

# Copy cacert.pem to storage (if not already there)
# You can download it from: https://curl.se/ca/cacert.pem
# wget -O storage/cacert.pem https://curl.se/ca/cacert.pem

# Set correct permissions
chmod 644 storage/cacert.pem

# Set correct ownership (replace www-data with your web server user)
# For Nginx: www-data
# For Apache: www-data or apache
sudo chown www-data:www-data storage/cacert.pem

# Verify permissions
ls -la storage/cacert.pem
```

### Step 5: Clear Laravel Caches

```bash
# Clear all Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild config cache
php artisan config:cache
```

### Step 6: Restart Web Server

```bash
# For Nginx
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm  # Adjust PHP version as needed

# For Apache
sudo systemctl restart apache2

# For Laravel development server (if using)
# Just stop and restart: php artisan serve
```

---

## Testing the Payment Gateway

### 1. Check PHP Configuration

```bash
# Create a test PHP file to check cURL settings
php -r "echo 'cURL: ' . (extension_loaded('curl') ? 'Enabled' : 'Disabled') . PHP_EOL;"
php -r "echo 'OpenSSL: ' . (extension_loaded('openssl') ? 'Enabled' : 'Disabled') . PHP_EOL;"

# Check PHP cURL version
php -r "echo curl_version()['version'] . PHP_EOL;"
```

### 2. Test Payment Flow

1. Navigate to your application: `http://your-domain/campaigns`
2. Select a campaign and click "Donate Now"
3. Fill in the donation form
4. Submit and verify redirect to ToyyibPay
5. Complete test payment
6. Verify return to your application

### 3. Check Logs

If payment fails, check the Laravel logs:

```bash
# View real-time logs
tail -f storage/logs/laravel.log

# Search for ToyyibPay errors
grep -i "toyyibpay\|payment" storage/logs/laravel.log

# Check web server error logs
# Nginx:
sudo tail -f /var/log/nginx/error.log

# Apache:
sudo tail -f /var/log/apache2/error.log
```

---

## Common Issues and Solutions

### Issue 1: "SSL certificate problem: unable to get local issuer certificate"

**Solution**: Use system CA certificates or disable SSL verification for testing

```bash
# Add to .env
TOYYIBPAY_VERIFY_SSL=true  # Use system CA certs

# OR for testing only
TOYYIBPAY_VERIFY_SSL=false  # Disable SSL verification
```

### Issue 2: "Permission denied" when accessing cacert.pem

**Solution**: Fix file permissions

```bash
sudo chown www-data:www-data storage/cacert.pem
chmod 644 storage/cacert.pem
```

### Issue 3: cURL extension not loaded

**Solution**: Install and enable cURL

```bash
sudo apt-get install php8.2-curl
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Issue 4: "Connection timed out" or "Could not resolve host"

**Solution**: Check firewall and DNS

```bash
# Test connectivity to ToyyibPay
curl -I https://dev.toyyibpay.com

# Check DNS resolution
nslookup dev.toyyibpay.com

# Allow outgoing HTTPS connections (if firewall is blocking)
sudo ufw allow out 443/tcp
```

### Issue 5: "Failed to create payment bill"

**Solution**: Verify ToyyibPay credentials

```bash
# Check .env values
grep TOYYIBPAY .env

# Ensure secret key and category code are correct
# Test with ToyyibPay sandbox credentials first
```

---

## Debugging Commands

### Check PHP Configuration

```bash
# View PHP configuration
php -i | grep -E 'curl|openssl|ssl'

# Check loaded extensions
php -m

# Check specific extension
php -r "var_dump(extension_loaded('curl'));"
```

### Test SSL Certificate

```bash
# Test SSL connection to ToyyibPay
openssl s_client -connect dev.toyyibpay.com:443 -servername dev.toyyibpay.com

# Check if CA bundle is accessible
curl -v https://dev.toyyibpay.com 2>&1 | grep -i 'ssl\|certificate'
```

### Check File System

```bash
# Verify storage directory permissions
ls -la storage/

# Check if cacert.pem exists and is readable
test -r storage/cacert.pem && echo "Readable" || echo "Not readable"

# Check file size
ls -lh storage/cacert.pem
```

### Monitor Real-Time Logs

```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log | grep -i payment

# Watch PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Watch web server logs
sudo tail -f /var/log/nginx/access.log
```

---

## Production Deployment Checklist

Before deploying to production Ubuntu server:

- [ ] Install all required PHP extensions (curl, mbstring, openssl)
- [ ] Update system CA certificates (`sudo update-ca-certificates`)
- [ ] Set `TOYYIBPAY_VERIFY_SSL=true` in production `.env`
- [ ] Use production ToyyibPay credentials (not sandbox)
- [ ] Set proper file permissions on storage directory
- [ ] Configure HTTPS on your web server
- [ ] Test payment flow thoroughly
- [ ] Set up monitoring and alerts for payment failures
- [ ] Configure proper error logging
- [ ] Implement payment webhook verification

---

## Additional Resources

### ToyyibPay Documentation
- Sandbox: https://dev.toyyibpay.com/
- Production: https://toyyibpay.com/
- API Docs: https://toyyibpay.com/apireference/

### Laravel HTTP Client
- Docs: https://laravel.com/docs/11.x/http-client
- SSL Verification: https://laravel.com/docs/11.x/http-client#ssl-certificates

### CA Certificates
- Download Latest: https://curl.se/ca/cacert.pem
- Ubuntu CA Certs: `/etc/ssl/certs/ca-certificates.crt`

---

## Support

If you continue to experience issues:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Enable debug mode temporarily**: Set `APP_DEBUG=true` in `.env` (development only!)
3. **Test with cURL directly**:
   ```bash
   curl -X POST https://dev.toyyibpay.com/index.php/api/createBill \
     -d "userSecretKey=YOUR_KEY" \
     -d "categoryCode=YOUR_CODE" \
     -d "billName=Test" \
     -d "billAmount=1000"
   ```

4. **Verify PHP version compatibility**: Ensure PHP 8.2+ is installed
5. **Check ToyyibPay status**: Ensure their API is operational

---

## Environment Comparison

| Setting | Windows | Ubuntu (Development) | Ubuntu (Production) |
|---------|---------|---------------------|-------------------|
| SSL Verification | Custom cacert.pem | System CA certs or disabled | System CA certs (required) |
| Web Server User | N/A | www-data | www-data |
| File Permissions | Automatic | Must set manually | Must set manually |
| CA Cert Location | `storage/cacert.pem` | `/etc/ssl/certs/` | `/etc/ssl/certs/` |
| Recommended Setting | `verify` = cacert.pem | `verify` = true/false | `verify` = true |

---

## Quick Reference

### Recommended .env Settings

```env
# Production (Ubuntu)
TOYYIBPAY_SANDBOX=false
TOYYIBPAY_VERIFY_SSL=true
TOYYIBPAY_SECRET_KEY=your_production_key
TOYYIBPAY_CATEGORY_CODE=your_production_code

# Development (Ubuntu)
TOYYIBPAY_SANDBOX=true
TOYYIBPAY_VERIFY_SSL=true  # or false for quick testing
TOYYIBPAY_SECRET_KEY=your_sandbox_key
TOYYIBPAY_CATEGORY_CODE=your_sandbox_code
```

### Quick Setup Commands

```bash
# One-liner setup for Ubuntu
sudo apt-get update && \
sudo apt-get install -y php8.2-curl php8.2-mbstring ca-certificates && \
sudo update-ca-certificates && \
cd /path/to/Charity-Izz && \
php artisan config:clear && \
php artisan cache:clear && \
sudo systemctl restart nginx && \
sudo systemctl restart php8.2-fpm
```

---

*Last Updated: December 2024*
*Laravel Version: 12.x*
*PHP Version: 8.2+*
