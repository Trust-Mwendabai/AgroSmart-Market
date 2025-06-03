# AgroSmart Market

A digital marketplace connecting farmers and buyers in Zambia, built with PHP and MySQL. AgroSmart Market provides a seamless platform for farmers to sell their fresh produce directly to consumers and businesses.

## 🌟 Latest Features

- **Enhanced Product Listings**
  - Beautiful product cards with hover effects
  - Category-based filtering and search
  - Sort by price, newest, and popularity
  - Stock level indicators
  - Organic product badges

- **Improved User Experience**
  - Responsive design for all devices
  - Intuitive navigation
  - Quick view product details
  - Shopping cart functionality
  - Wishlist feature

- **Farmer Profiles**
  - Detailed farmer/store pages
  - Product catalog management
  - Order tracking
  - Sales analytics

- **Admin Dashboard**
  - User management
  - Product moderation
  - Sales reporting
  - Category management
  - System configuration

## 🚀 Key Features

- **User Authentication**
  - Multi-role system (Farmers, Buyers, Admin)
  - Secure password hashing
  - Email verification
  - Password recovery

- **Product Management**
  - Rich product listings with images
  - Category and tag system
  - Inventory tracking
  - Product reviews and ratings

- **Order System**
  - Shopping cart functionality
  - Checkout process
  - Order history
  - Email notifications

- **Messaging**
  - Real-time chat between buyers and sellers
  - Order-related messaging
  - Notifications system

## 🛠️ Requirements

### Server Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- Web server (Apache/Nginx)
- SSL certificate (for production)
- PHP Extensions:
  - PDO PHP Extension
  - OpenSSL PHP Extension
  - Mbstring PHP Extension
  - Tokenizer PHP Extension
  - JSON PHP Extension
  - cURL PHP Extension
  - Fileinfo PHP Extension
  - GD Library (for image processing)

### Development Tools
- Composer (for PHP dependencies)
- Git (for version control)
- Node.js & NPM (for frontend assets)

## 🚀 Getting Started

### Local Development Setup

1. **Clone the repository**
   ```bash
   # Clone the repository
   git clone https://github.com/yourusername/agrosmart-market.git
   cd agrosmart-market
   
   # Install PHP dependencies
   composer install
   
   # Install frontend dependencies
   npm install
   npm run dev
   
   # Copy environment file
   cp .env.example .env
   php artisan key:generate
   ```

2. **Set up your web server**
   - For XAMPP:
     - Copy the project to `htdocs` folder
     - Start Apache and MySQL services through XAMPP Control Panel
     - Enable `mod_rewrite` in Apache
   - For Laravel Valet (recommended for Mac):
     ```bash
     valet link agrosmart-market
     cd agrosmart-market
     valet secure
     ```
   - For other servers:
     - Configure your web server to point to the `public` directory
     - Ensure PHP and MySQL are running
     - Set up proper URL rewriting rules

3. **Configure the database**
   - Create a new MySQL database:
     ```sql
     CREATE DATABASE agrosmart_market;
     CREATE USER 'agrosmart_user'@'localhost' IDENTIFIED BY 'your_secure_password';
     GRANT ALL PRIVILEGES ON agrosmart_market.* TO 'agrosmart_user'@'localhost';
     FLUSH PRIVILEGES;
     ```
   - Update your `.env` file with database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=agrosmart_market
     DB_USERNAME=agrosmart_user
     DB_PASSWORD=your_secure_password
     ```
   - Run database migrations and seeders:
     ```bash
     php artisan migrate --seed
     ```

4. **Set up file permissions**
   - Set proper permissions:
     ```bash
     # Storage and cache directories
     chmod -R 775 storage/
     chmod -R 775 bootstrap/cache/
     
     # Create storage links
     php artisan storage:link
     
     # Ensure upload directories are writable
     chmod -R 775 public/uploads/
     chmod -R 775 storage/app/public
     ```

5. **Run the application**
   - Start the development server:
     ```bash
     php artisan serve
     ```
   - Or use Laravel Valet:
     ```bash
     valet open
     ```
   - Access the application at `http://localhost:8000` or your configured domain
   - Admin dashboard: `http://localhost:8000/admin`
     - Default admin credentials:
       - Email: admin@agrosmart.com
       - Password: admin123

## 🔧 Configuration

### Environment Variables

Edit the `.env` file to configure your application:

```env
APP_NAME="AgroSmart Market"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agrosmart_market
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Cache Configuration

After changing configuration, run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Production Deployment

### Server Requirements
- PHP 8.0+ with required extensions
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Nginx recommended)
- Redis (for caching and queues)
- SSL certificate (Let's Encrypt recommended)

### Recommended PHP Settings
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 512M
max_input_vars = 3000
session.cookie_httponly = 1
session.cookie_secure = 1
```

### Deployment Steps

1. **Server Preparation**
   ```bash
   # Update system packages
   sudo apt update && sudo apt upgrade -y
   
   # Install required software
   sudo apt install -y nginx mysql-server php8.1-fpm php8.1-{mysql,mbstring,xml,curl,zip,gd}
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   
   # Install Node.js & NPM
   curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
   sudo apt install -y nodejs
   ```

2. **Application Setup**
   ```bash
   # Clone repository
   git clone https://github.com/yourusername/agrosmart-market.git /var/www/agrosmart-market
   cd /var/www/agrosmart-market
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install && npm run production
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/agrosmart-market
   sudo chmod -R 775 storage bootstrap/cache
   
   # Generate application key
   cp .env.example .env
   php artisan key:generate
   
   # Configure environment
   nano .env
   
   # Run migrations and seeders
   php artisan migrate --force
   php artisan db:seed --force
   
   # Create storage link
   php artisan storage:link
   
   # Cache configuration
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Nginx Configuration**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com www.yourdomain.com;
       return 301 https://$host$request_uri;
   }
   
   server {
       listen 443 ssl http2;
       server_name yourdomain.com www.yourdomain.com;
       
       ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
       
       root /var/www/agrosmart-market/public;
       index index.php index.html index.htm;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
       }
       
       location ~ /\.ht {
           deny all;
       }
       
       # Security headers
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
       add_header X-XSS-Protection "1; mode=block";
       add_header Referrer-Policy "strict-origin-when-cross-origin";
       
       # Disable directory listing
       autoindex off;
   }
   ```

4. **SSL Certificate**
   ```bash
   # Install Certbot
   sudo apt install -y certbot python3-certbot-nginx
   
   # Obtain SSL certificate
   sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
   
   # Set up auto-renewal
   (crontab -l 2>/dev/null; echo "0 0,12 * * * root python3 -c 'import random; import time; time.sleep(random.random() * 3600)' && certbot renew -q") | sudo crontab -
   ```

## 🛠️ Maintenance

### Updating the Application

```bash
# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run production

# Run migrations
php artisan migrate --force

# Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Database

```bash
# Create database backup
mysqldump -u [username] -p[password] agrosmart_market > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u [username] -p[password] agrosmart_market < backup_file.sql
```

## 🔒 Security

### Best Practices

1. **File Permissions**
   ```bash
   # Set proper permissions
   find /var/www/agrosmart-market -type d -exec chmod 755 {} \;
   find /var/www/agrosmart-market -type f -exec chmod 644 {} \;
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

2. **Environment Protection**
   - Keep `.env` file outside web root
   - Set `APP_DEBUG=false` in production
   - Use strong application key
   - Enable HTTPS

3. **Regular Updates**
   - Keep PHP and server software updated
   - Update dependencies regularly
   - Monitor for security advisories

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built with ❤️ for Zambian farmers and buyers
- Thanks to all contributors who have helped improve this project
- Special thanks to our beta testers and early adopters
   - For Apache, ensure `.htaccess` is enabled
   - Set document root to the project directory
   - Enable URL rewriting
   - Configure SSL redirect

5. **Security measures**
   - Update default admin credentials
   - Set secure file permissions
   - Enable error logging
   - Configure backup system

6. **Run installation**
   - Visit `https://your-domain.com/install.php`
   - Complete the installation process
   - Remove or protect `install.php` after setup

## Troubleshooting

1. **Database connection issues**
   - Verify database credentials
   - Check if MySQL service is running
   - Ensure database exists

2. **File upload problems**
   - Check directory permissions
   - Verify PHP upload settings
   - Check available disk space

3. **404 errors**
   - Verify .htaccess is present
   - Check URL rewriting is enabled
   - Confirm file permissions

4. **Performance issues**
   - Enable PHP opcache
   - Configure MySQL query cache
   - Optimize image uploads

## Support

For support, please:
- Open an issue on GitHub
- Contact support@agrosmart.com
- Check the documentation at docs.agrosmart.com

## License

This project is licensed under the MIT License - see the LICENSE file for details.
