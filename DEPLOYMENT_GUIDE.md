# 🚀 DEPLOYMENT GUIDE - Production Ready

## 📋 Mục Lục

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Step-by-Step Deployment](#step-by-step-deployment)
4. [Post-Deployment Verification](#post-deployment-verification)
5. [Monitoring & Maintenance](#monitoring--maintenance)
6. [Troubleshooting](#troubleshooting)

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### Code Ready
- [x] All features complete (100%)
- [x] No syntax errors
- [x] All tests passed locally
- [x] Documentation complete
- [x] Security features implemented

### Database Ready
- [ ] Create production database
- [ ] Backup current development database
- [ ] Test migration scripts
- [ ] Prepare seed data (if needed)

### Environment Ready
- [ ] Production server provisioned
- [ ] Domain name configured
- [ ] SSL certificate obtained
- [ ] Email service configured (for notifications)

---

## 🖥 SERVER REQUIREMENTS

### Minimum Requirements
- **OS:** Ubuntu 20.04 LTS or Windows Server 2019+
- **PHP:** 8.2 or higher
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **Database:** MySQL 8.0+ or MariaDB 10.5+
- **RAM:** 2GB minimum (4GB recommended)
- **Storage:** 20GB minimum (SSD recommended)
- **SSL:** Let's Encrypt or commercial SSL certificate

### PHP Extensions Required
```bash
# Check installed extensions
php -m

# Required extensions:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD (for image processing, if needed)
```

### Software Stack
```
- Composer 2.6+
- Node.js 18+ (for Vite, if using)
- Git
- Supervisor (for queue workers, optional)
```

---

## 📦 STEP-BY-STEP DEPLOYMENT

### Step 1: Server Setup (Ubuntu Example)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP 8.2 and extensions
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
  php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath -y

# Install MySQL
sudo apt install mysql-server -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (optional, for Vite)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### Step 2: Clone Repository

```bash
# Navigate to web directory
cd /var/www/

# Clone repository
git clone https://github.com/your-repo/exam-system.git
cd exam-system

# Set permissions
sudo chown -R www-data:www-data /var/www/exam-system
sudo chmod -R 755 /var/www/exam-system
sudo chmod -R 775 /var/www/exam-system/storage
sudo chmod -R 775 /var/www/exam-system/bootstrap/cache
```

### Step 3: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies (if using Vite)
npm install
npm run build
```

### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment
nano .env
```

**Production `.env` Configuration:**
```env
APP_NAME="Luyện thi THPT Tin học"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=exam_system_prod
DB_USERNAME=exam_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Database Setup

```bash
# Create database
sudo mysql -u root -p

mysql> CREATE DATABASE exam_system_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql> CREATE USER 'exam_user'@'localhost' IDENTIFIED BY 'your_secure_password';
mysql> GRANT ALL PRIVILEGES ON exam_system_prod.* TO 'exam_user'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> EXIT;

# Run migrations
php artisan migrate --force

# Seed initial data (admin user, sample questions)
php artisan db:seed --force
```

### Step 7: Optimize Laravel

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 8: Configure Web Server

#### Apache Configuration

```bash
# Create virtual host
sudo nano /etc/apache2/sites-available/exam-system.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/exam-system/public

    <Directory /var/www/exam-system/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/exam-system-error.log
    CustomLog ${APACHE_LOG_DIR}/exam-system-access.log combined

    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/exam-system/public

    <Directory /var/www/exam-system/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/your-cert.crt
    SSLCertificateKeyFile /etc/ssl/private/your-key.key
    SSLCertificateChainFile /etc/ssl/certs/your-chain.crt

    ErrorLog ${APACHE_LOG_DIR}/exam-system-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/exam-system-ssl-access.log combined
</VirtualHost>
```

```bash
# Enable modules
sudo a2enmod rewrite
sudo a2enmod ssl

# Enable site
sudo a2ensite exam-system.conf

# Disable default site
sudo a2dissite 000-default.conf

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

#### Nginx Configuration (Alternative)

```bash
sudo nano /etc/nginx/sites-available/exam-system
```

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/exam-system/public;

    index index.php index.html;

    ssl_certificate /etc/ssl/certs/your-cert.crt;
    ssl_certificate_key /etc/ssl/private/your-key.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/exam-system /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### Step 9: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx -y   # For Nginx

# Obtain certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
# OR
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (runs twice daily)
sudo systemctl status certbot.timer
```

### Step 10: Setup Backup Directory

```bash
# Create backup directory
mkdir -p /var/www/exam-system/storage/app/backups

# Set permissions
sudo chown www-data:www-data /var/www/exam-system/storage/app/backups
sudo chmod 775 /var/www/exam-system/storage/app/backups
```

### Step 11: Configure Cron Jobs (Optional)

```bash
# Edit crontab
sudo crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/exam-system && php artisan schedule:run >> /dev/null 2>&1

# Add daily backup (3 AM)
0 3 * * * cd /var/www/exam-system && php artisan backup:run >> /var/log/backup.log 2>&1
```

---

## ✅ POST-DEPLOYMENT VERIFICATION

### 1. Test Website Access
```bash
# Check if site is accessible
curl -I https://yourdomain.com

# Should return 200 OK
```

### 2. Test Login
- Navigate to `https://yourdomain.com`
- Try logging in with admin account
- Verify dashboard loads correctly

### 3. Test Student Workflow
1. Register new student account
2. Choose an exam
3. Take exam
4. Submit
5. View results
6. Check statistics

### 4. Test Teacher Features
1. Login as teacher
2. Add a question
3. Create random exam
4. View question list

### 5. Test Admin Features
1. Login as admin
2. View dashboard (check charts)
3. Create user
4. Create backup
5. View backup history

### 6. Check Logs
```bash
# Laravel logs
tail -f /var/www/exam-system/storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/exam-system-error.log

# Nginx logs
tail -f /var/log/nginx/error.log
```

### 7. Performance Test
```bash
# Install Apache Bench
sudo apt install apache2-utils -y

# Test 100 requests, 10 concurrent
ab -n 100 -c 10 https://yourdomain.com/

# Should handle without errors
```

---

## 📊 MONITORING & MAINTENANCE

### Daily Tasks
- ✅ Check application logs for errors
- ✅ Verify backup completion
- ✅ Monitor disk space usage
- ✅ Check database size

### Weekly Tasks
- ✅ Review user activity
- ✅ Check for failed jobs (if using queues)
- ✅ Test backup restore procedure
- ✅ Update security patches

### Monthly Tasks
- ✅ Update dependencies (`composer update`)
- ✅ Review performance metrics
- ✅ Audit user accounts
- ✅ Clean old backup files

### Monitoring Tools (Recommended)

#### 1. Laravel Telescope (Development)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### 2. Server Monitoring
```bash
# Install htop
sudo apt install htop -y

# Monitor resources
htop

# Check disk usage
df -h

# Check memory
free -h
```

#### 3. Application Monitoring
- **New Relic** (APM)
- **Sentry** (Error tracking)
- **Pingdom** (Uptime monitoring)
- **Google Analytics** (User analytics)

---

## 🐛 TROUBLESHOOTING

### Issue 1: 500 Internal Server Error

**Symptoms:** White page or 500 error

**Solutions:**
```bash
# Check Laravel log
tail -f storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerate cache
php artisan config:cache
php artisan route:cache
```

### Issue 2: Database Connection Error

**Symptoms:** "SQLSTATE[HY000] [1045] Access denied"

**Solutions:**
```bash
# Check .env file
nano .env

# Verify credentials
mysql -u exam_user -p exam_system_prod

# Reset user password
mysql -u root -p
mysql> ALTER USER 'exam_user'@'localhost' IDENTIFIED BY 'new_password';
mysql> FLUSH PRIVILEGES;
```

### Issue 3: File Upload Not Working

**Symptoms:** Backup/restore fails

**Solutions:**
```bash
# Check upload limits
nano /etc/php/8.2/fpm/php.ini

# Update these values:
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Issue 4: Charts Not Rendering

**Symptoms:** Empty chart containers

**Solutions:**
- Check browser console for errors
- Verify Chart.js CDN loads: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js`
- Clear browser cache
- Check for JavaScript errors in console

### Issue 5: Session Issues

**Symptoms:** User logged out frequently

**Solutions:**
```bash
# Check session configuration
nano .env

# Set session driver
SESSION_DRIVER=database

# Create sessions table
php artisan session:table
php artisan migrate

# Or use file driver
SESSION_DRIVER=file

# Set proper permissions
sudo chmod -R 775 storage/framework/sessions
```

---

## 🔐 SECURITY BEST PRACTICES

### 1. Regular Updates
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Composer dependencies
composer update

# Update Node packages (if using)
npm update
```

### 2. Firewall Configuration
```bash
# Install UFW
sudo apt install ufw -y

# Allow SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable
```

### 3. Fail2Ban (Brute Force Protection)
```bash
# Install
sudo apt install fail2ban -y

# Configure
sudo nano /etc/fail2ban/jail.local

# Add:
[apache-auth]
enabled = true
port = http,https
filter = apache-auth
logpath = /var/log/apache2/*error.log
maxretry = 3
bantime = 3600
```

### 4. Database Security
```bash
# Run security script
sudo mysql_secure_installation

# Disable remote root login
# Remove anonymous users
# Remove test database
```

### 5. Laravel Security
```env
# .env settings
APP_DEBUG=false
APP_ENV=production

# Enable HTTPS only
SESSION_SECURE_COOKIE=true
```

---

## 📞 SUPPORT

### Getting Help
- **Documentation:** `/docs` folder
- **Laravel Docs:** https://laravel.com/docs
- **Community:** Laravel Forums, Stack Overflow
- **Issues:** GitHub Issues page

### Emergency Contacts
- **Sysadmin:** sysadmin@yourdomain.com
- **Developer:** dev@yourdomain.com
- **Support:** support@yourdomain.com

---

## ✅ DEPLOYMENT CHECKLIST

Before going live, verify:

- [ ] Production environment configured
- [ ] Database created and migrated
- [ ] SSL certificate installed
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Error logging enabled
- [ ] Performance tested
- [ ] Security hardened
- [ ] Documentation reviewed
- [ ] Team trained
- [ ] Rollback plan ready

---

<p align="center">
  <strong>🚀 READY FOR PRODUCTION!</strong><br>
  <em>Follow this guide step-by-step for successful deployment</em>
</p>

---

*Last Updated: December 7, 2025*
