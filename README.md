# AgroSmart Market

AgroSmart Market is a web application that connects farmers and buyers in Zambia, facilitating the direct sale of agricultural products and reducing the need for intermediaries.

## 🌱 Features

- **User Authentication**
  - Registration and login for farmers, buyers, and administrators
  - Role-based access control
  - Remember me functionality

- **Marketplace**
  - Product listings with categories and search functionality
  - Shopping cart system
  - Order management

- **Farmer Dashboard**
  - Product management (add, edit, delete)
  - Order tracking and fulfillment
  - Sales reports and analytics

- **Buyer Features**
  - Browse and purchase products
  - Order history and tracking
  - Direct messaging with farmers

- **Admin Dashboard**
  - User management
  - Order oversight
  - System analytics and reports

- **Messaging System**
  - Direct communication between farmers and buyers
  - Notification system for unread messages

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- XAMPP, WAMP, or similar local development environment

### Setup Instructions

1. **Clone the repository**
   ```
   git clone https://github.com/your-username/agrosmart-market.git
   ```

2. **Database Setup**
   - Create a new MySQL database named `agrosmart_market`
   - Import the `database.sql` file from the project root directory:
     ```
     mysql -u username -p agrosmart_market < database.sql
     ```
     Or use phpMyAdmin to import the file

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update the database credentials as needed:
     ```php
     $db_host = 'localhost';
     $db_user = 'your_username';
     $db_pass = 'your_password';
     $db_name = 'agrosmart_market';
     ```

4. **Set Up Project in Web Server**
   - Place the project folder in your web server's document root (e.g., `htdocs` for XAMPP)
   - Ensure the web server has read/write permissions for the project directory

5. **Load Sample Data (Optional)**
   - To populate the database with sample data, navigate to:
     ```
     http://localhost/AgroSmart Market/setup_sample_data.php
     ```

6. **Access the Application**
   - Open your web browser and navigate to:
     ```
     http://localhost/AgroSmart Market
     ```

## 👨‍💻 Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, jQuery
- **Backend**: PHP
- **Database**: MySQL
- **Icons**: Font Awesome
- **Charting**: Chart.js

## 📂 Project Structure

```
AgroSmart Market/
├── admin/                  # Admin-specific controllers
├── config/                 # Configuration files
├── models/                 # Data models
├── public/                 # Public assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   ├── images/             # Image assets
│   └── uploads/            # User-uploaded content
├── views/                  # View templates
│   ├── admin/              # Admin view templates
│   ├── partials/           # Shared view components
│   └── ...                 # Other view templates
├── database.sql            # Database schema and sample data
├── index.php               # Main entry point
└── README.md               # Project documentation
```

## 🔐 Default Login Credentials

After setup, you can use these default login credentials:

### Administrator
- Email: admin@agrosmartmarket.com
- Password: admin123

### Farmer
- Email: farmer@example.com
- Password: password

### Buyer
- Email: buyer@example.com
- Password: password

## 🔄 Recent Updates

- Changed currency from USD to Zambian Kwacha (K) throughout the application
- Fixed undefined array key errors in the farmer dashboard
- Enhanced navbar with improved visibility and consistent styling
- Added dynamic scroll effects to the header
- Improved the hero section on the landing page
- Fixed issues with the admin dashboard
- Added "remember me" functionality for login
- Resolved database structure issues

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Contributors

- Your Name - Initial work and development

## 📧 Contact

If you have any questions or suggestions about this project, please reach out to me at [your-email@example.com].
