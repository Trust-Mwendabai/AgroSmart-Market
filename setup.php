<?php
// Database setup script for AgroSmart Market
require_once 'config/database.php';

// Create sample data for the marketplace
function populate_sample_data($conn) {
    // Sample user data (1 admin, 2 farmers, 2 buyers)
    $users = [
        // Admin
        [
            'name' => 'Admin User',
            'email' => 'admin@agrosmart.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'user_type' => 'admin',
            'location' => 'Lusaka, Zambia',
            'profile_image' => 'admin.jpg',
            'verified' => 1
        ],
        // Farmers
        [
            'name' => 'John Mulenga',
            'email' => 'john@farmer.com',
            'password' => password_hash('farmer123', PASSWORD_DEFAULT),
            'user_type' => 'farmer',
            'location' => 'Chipata, Zambia',
            'profile_image' => 'farmer1.jpg',
            'verified' => 1
        ],
        [
            'name' => 'Maria Banda',
            'email' => 'maria@farmer.com',
            'password' => password_hash('farmer123', PASSWORD_DEFAULT),
            'user_type' => 'farmer',
            'location' => 'Livingstone, Zambia',
            'profile_image' => 'farmer2.jpg',
            'verified' => 1
        ],
        // Buyers
        [
            'name' => 'David Phiri',
            'email' => 'david@buyer.com',
            'password' => password_hash('buyer123', PASSWORD_DEFAULT),
            'user_type' => 'buyer',
            'location' => 'Lusaka, Zambia',
            'profile_image' => 'buyer1.jpg',
            'verified' => 1
        ],
        [
            'name' => 'Sarah Tembo',
            'email' => 'sarah@buyer.com',
            'password' => password_hash('buyer123', PASSWORD_DEFAULT),
            'user_type' => 'buyer',
            'location' => 'Ndola, Zambia',
            'profile_image' => 'buyer2.jpg',
            'verified' => 1
        ]
    ];
    
    // Sample product data
    $products = [
        [
            'name' => 'Fresh Organic Tomatoes',
            'description' => 'Freshly harvested organic tomatoes, grown without any chemical pesticides. Perfect for salads and cooking.',
            'price' => 5.99,
            'category' => 'Vegetables',
            'stock' => 50,
            'location' => 'Chipata, Zambia',
            'image' => 'tomatoes.jpg',
            'farmer_id' => 2, // John Mulenga
            'is_organic' => 1
        ],
        [
            'name' => 'Green Bell Peppers',
            'description' => 'Fresh green bell peppers, crisp and full of flavor. Great for stir-fries and salads.',
            'price' => 4.50,
            'category' => 'Vegetables',
            'stock' => 35,
            'location' => 'Chipata, Zambia',
            'image' => 'peppers.jpg',
            'farmer_id' => 2, // John Mulenga
            'is_organic' => 1
        ],
        [
            'name' => 'Sweet Maize (Corn)',
            'description' => 'Delicious sweet corn, perfect for boiling or grilling. A favorite local snack or side dish.',
            'price' => 3.25,
            'category' => 'Grains',
            'stock' => 100,
            'location' => 'Chipata, Zambia',
            'image' => 'corn.jpg',
            'farmer_id' => 2, // John Mulenga
            'is_organic' => 0
        ],
        [
            'name' => 'Freshly Harvested Rice',
            'description' => 'Locally grown rice, freshly harvested and processed. Perfect for all your rice dishes.',
            'price' => 8.99,
            'category' => 'Grains',
            'stock' => 80,
            'location' => 'Livingstone, Zambia',
            'image' => 'rice.jpg',
            'farmer_id' => 3, // Maria Banda
            'is_organic' => 0
        ],
        [
            'name' => 'Organic Bananas',
            'description' => 'Sweet and ripe organic bananas. Great for snacking, smoothies, or baking.',
            'price' => 4.50,
            'category' => 'Fruits',
            'stock' => 60,
            'location' => 'Livingstone, Zambia',
            'image' => 'bananas.jpg',
            'farmer_id' => 3, // Maria Banda
            'is_organic' => 1
        ],
        [
            'name' => 'Fresh Pineapples',
            'description' => 'Sweet and juicy pineapples, freshly harvested at peak ripeness. Perfect tropical fruit.',
            'price' => 6.75,
            'category' => 'Fruits',
            'stock' => 25,
            'location' => 'Livingstone, Zambia',
            'image' => 'pineapples.jpg',
            'farmer_id' => 3, // Maria Banda
            'is_organic' => 0
        ],
        [
            'name' => 'Free-Range Chicken Eggs',
            'description' => 'Farm-fresh eggs from free-range chickens. Rich in flavor and nutrients.',
            'price' => 5.25,
            'category' => 'Dairy & Eggs',
            'stock' => 100,
            'location' => 'Chipata, Zambia',
            'image' => 'eggs.jpg',
            'farmer_id' => 2, // John Mulenga
            'is_organic' => 1
        ],
        [
            'name' => 'Organic Potatoes',
            'description' => 'Fresh organic potatoes, perfect for boiling, mashing, or roasting. Versatile and delicious.',
            'price' => 3.99,
            'category' => 'Vegetables',
            'stock' => 70,
            'location' => 'Livingstone, Zambia',
            'image' => 'potatoes.jpg',
            'farmer_id' => 3, // Maria Banda
            'is_organic' => 1
        ]
    ];
    
    // Insert users
    $user_ids = [];
    foreach ($users as $user) {
        $sql = "INSERT INTO users (name, email, password, user_type, location, profile_image, is_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", 
            $user['name'], 
            $user['email'], 
            $user['password'],
            $user['user_type'],
            $user['location'],
            $user['profile_image'],
            $user['verified']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $user_ids[] = mysqli_insert_id($conn);
            echo "Added user: " . $user['name'] . " (" . $user['user_type'] . ")<br>";
        } else {
            echo "Error adding user " . $user['name'] . ": " . mysqli_error($conn) . "<br>";
        }
    }
    
    // Insert products
    foreach ($products as $product) {
        $sql = "INSERT INTO products (name, description, price, category, stock, location, image, farmer_id, is_organic, date_added) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsisiii", 
            $product['name'], 
            $product['description'], 
            $product['price'],
            $product['category'],
            $product['stock'],
            $product['location'],
            $product['image'],
            $product['farmer_id'],
            $product['is_organic']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Added product: " . $product['name'] . "<br>";
        } else {
            echo "Error adding product " . $product['name'] . ": " . mysqli_error($conn) . "<br>";
        }
    }
    
    echo "<p>Sample data has been added successfully!</p>";
    echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
}

// Create database tables if they don't exist
function setup_database($conn) {
    // Users table
    $users_sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('admin', 'farmer', 'buyer') NOT NULL,
        location VARCHAR(100),
        profile_image VARCHAR(255),
        bio TEXT,
        phone VARCHAR(20),
        date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_verified TINYINT(1) DEFAULT 0,
        verification_token VARCHAR(255),
        last_login TIMESTAMP NULL
    )";
    
    // Products table
    $products_sql = "CREATE TABLE IF NOT EXISTS products (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT(11) NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category VARCHAR(50),
        stock INT(11) NOT NULL DEFAULT 0,
        location VARCHAR(100),
        is_organic TINYINT(1) DEFAULT 0,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    // Orders table
    $orders_sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT(11) NOT NULL,
        farmer_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'accepted', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
        date_ordered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_updated TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    
    // Messages table
    $messages_sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        sender_id INT(11) NOT NULL,
        receiver_id INT(11) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        related_product_id INT(11) NULL,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (related_product_id) REFERENCES products(id) ON DELETE SET NULL
    )";
    
    // Reviews table
    $reviews_sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        product_id INT(11) NOT NULL,
        buyer_id INT(11) NOT NULL,
        rating INT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
        comment TEXT,
        date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    // Execute all SQL statements
    $tables = [
        'users' => $users_sql,
        'products' => $products_sql,
        'orders' => $orders_sql,
        'messages' => $messages_sql,
        'reviews' => $reviews_sql
    ];
    
    $success = true;
    foreach ($tables as $table => $sql) {
        if (mysqli_query($conn, $sql)) {
            echo "Table '$table' created or already exists.<br>";
        } else {
            echo "Error creating table '$table': " . mysqli_error($conn) . "<br>";
            $success = false;
        }
    }
    
    return $success;
}

// Check if this is a setup request
if (isset($_GET['setup']) && $_GET['setup'] === 'true') {
    $success = setup_database($conn);
    
    if ($success) {
        echo "<h3>Database setup completed successfully!</h3>";
        echo "<p>Would you like to populate the database with sample data?</p>";
        echo "<a href='setup.php?populate=true' class='btn btn-success'>Yes, add sample data</a> ";
        echo "<a href='index.php' class='btn btn-primary'>No, go to homepage</a>";
    } else {
        echo "<h3>Database setup encountered some errors.</h3>";
        echo "<p>Please check the error messages above and try again.</p>";
        echo "<a href='setup.php?setup=true' class='btn btn-warning'>Try Again</a>";
    }
} else if (isset($_GET['populate']) && $_GET['populate'] === 'true') {
    echo "<h3>Adding sample data to the database...</h3>";
    populate_sample_data($conn);
} else {
    // Display setup form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AgroSmart Market - Setup</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0">AgroSmart Market - Initial Setup</h3>
                        </div>
                        <div class="card-body">
                            <h4>Welcome to AgroSmart Market!</h4>
                            <p>This setup wizard will help you configure your database and add some sample data to get started.</p>
                            <hr>
                            <h5>Setup Options:</h5>
                            <div class="d-grid gap-3">
                                <a href="setup.php?setup=true" class="btn btn-primary">Setup Database Tables</a>
                                <a href="setup.php?populate=true" class="btn btn-success">Add Sample Data</a>
                                <a href="index.php" class="btn btn-outline-secondary">Skip Setup</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>
