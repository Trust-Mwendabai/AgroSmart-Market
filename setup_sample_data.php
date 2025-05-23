<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Message.php';

/**
 * Sample Data Generator for AgroSmart Market
 * 
 * This script populates the database with sample data to demonstrate
 * the functionality of the AgroSmart Market platform.
 * 
 * It creates:
 * - Admin user
 * - Farmers with products
 * - Buyers
 * - Orders between buyers and farmers
 * - Messages between users
 */

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);
$order_model = new Order($conn);
$message_model = new Message($conn);

// Function to check if sample data already exists
function check_sample_data_exists($conn) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = 'admin@agrosmart.com'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

// Add a progress tracker
function show_progress($message) {
    echo "<div style='margin: 5px 0; padding: 5px; background-color: #f0f8ff;'>";
    echo "✅ $message";
    echo "</div>";
    // Flush the output buffer to show progress in real-time (only if a buffer exists)
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

// Check if sample data already exists
if (check_sample_data_exists($conn)) {
    die("
    <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;'>
        <h2 style='color: #e74c3c;'>Sample Data Already Exists</h2>
        <p>The sample data has already been added to the database. To re-run this script, you need to delete the existing sample data first.</p>
        <p>You can do this by:</p>
        <ol>
            <li>Dropping the database and recreating it</li>
            <li>OR manually deleting records with sample data emails</li>
        </ol>
        <p><a href='index.php' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Go to Homepage</a></p>
    </div>
    ");
}

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroSmart Market - Setup Sample Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .progress-bar {
            height: 30px;
            margin-bottom: 20px;
        }
        h1 {
            color: #4CAF50;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">AgroSmart Market - Setting Up Sample Data</h1>
        <div class="progress mb-4">
            <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
        </div>
        <div id="status" class="mb-4">Initializing...</div>
        <div id="log">
<?php
// Enable output buffering to show progress
ob_implicit_flush(true);
ob_end_flush();

// Set of Zambian locations
$locations = [
    'Lusaka', 
    'Kitwe', 
    'Ndola', 
    'Kabwe', 
    'Chingola', 
    'Mufulira', 
    'Livingstone', 
    'Luanshya', 
    'Kasama', 
    'Chipata', 
    'Choma', 
    'Mongu', 
    'Kafue', 
    'Mazabuka', 
    'Mansa'
];

// Product categories
$categories = [
    'Vegetables', 
    'Fruits', 
    'Grains', 
    'Dairy', 
    'Meat', 
    'Poultry', 
    'Nuts', 
    'Herbs', 
    'Roots & Tubers', 
    'Honey & Bee Products'
];

// Sample crops by category
$products_by_category = [
    'Vegetables' => [
        ['Tomatoes', 'Fresh, locally grown tomatoes. Perfect for salads and cooking.', 15.00, 'tomatoes.jpg'],
        ['Onions', 'Locally grown onions. Essential for most dishes.', 12.50, 'onions.jpg'],
        ['Cabbage', 'Fresh green cabbage. Great for salads and stews.', 10.00, 'cabbage.jpg'],
        ['Spinach', 'Leafy green spinach. Rich in iron and vitamins.', 8.00, 'spinach.jpg'],
        ['Carrots', 'Fresh carrots full of beta-carotene.', 9.50, 'carrots.jpg']
    ],
    'Fruits' => [
        ['Bananas', 'Sweet, ripe bananas. Rich in potassium.', 12.00, 'bananas.jpg'],
        ['Mangoes', 'Juicy, sweet mangoes. Perfect for desserts.', 20.00, 'mangoes.jpg'],
        ['Avocados', 'Creamy avocados. Rich in healthy fats.', 25.00, 'avocados.jpg'],
        ['Oranges', 'Sweet, juicy oranges. High in vitamin C.', 15.00, 'oranges.jpg'],
        ['Papaya', 'Sweet, tropical papaya. Good for digestion.', 18.00, 'papaya.jpg']
    ],
    'Grains' => [
        ['Maize', 'Staple food crop, can be dried or fresh.', 100.00, 'maize.jpg'],
        ['Rice', 'Locally grown rice. Perfect for main dishes.', 120.00, 'rice.jpg'],
        ['Sorghum', 'Drought-resistant grain, nutritious and versatile.', 90.00, 'sorghum.jpg'],
        ['Millet', 'Small-seeded grain, good for porridge.', 85.00, 'millet.jpg'],
        ['Wheat', 'Whole grain wheat for bread and pasta.', 110.00, 'wheat.jpg']
    ],
    'Dairy' => [
        ['Fresh Milk', 'Raw milk from free-range cows.', 8.00, 'milk.jpg'],
        ['Yogurt', 'Homemade yogurt with active cultures.', 15.00, 'yogurt.jpg'],
        ['Cottage Cheese', 'Freshly made cottage cheese.', 20.00, 'cottage_cheese.jpg'],
        ['Butter', 'Traditional farm butter. Rich and creamy.', 25.00, 'butter.jpg']
    ],
    'Meat' => [
        ['Beef', 'Free-range, grass-fed beef.', 150.00, 'beef.jpg'],
        ['Goat Meat', 'Fresh goat meat, perfect for stews.', 140.00, 'goat.jpg'],
        ['Lamb', 'Tender lamb from grass-fed sheep.', 160.00, 'lamb.jpg']
    ],
    'Poultry' => [
        ['Chicken', 'Free-range chicken, no hormones.', 60.00, 'chicken.jpg'],
        ['Eggs', 'Farm fresh eggs from free-range chickens.', 25.00, 'eggs.jpg'],
        ['Duck', 'Farm-raised duck, perfect for special meals.', 70.00, 'duck.jpg']
    ],
    'Nuts' => [
        ['Groundnuts', 'Fresh groundnuts (peanuts). High in protein.', 35.00, 'groundnuts.jpg'],
        ['Cashews', 'Locally grown cashew nuts.', 60.00, 'cashews.jpg']
    ],
    'Herbs' => [
        ['Coriander', 'Fresh coriander leaves. Perfect for garnishing.', 5.00, 'coriander.jpg'],
        ['Mint', 'Fresh mint leaves. Great for teas and cooking.', 5.00, 'mint.jpg'],
        ['Basil', 'Aromatic basil. Essential for many dishes.', 7.00, 'basil.jpg']
    ],
    'Roots & Tubers' => [
        ['Sweet Potatoes', 'Nutritious orange-fleshed sweet potatoes.', 12.00, 'sweet_potatoes.jpg'],
        ['Cassava', 'Staple root crop, versatile for many dishes.', 10.00, 'cassava.jpg'],
        ['Yams', 'Large starchy tubers, great staple food.', 15.00, 'yams.jpg']
    ],
    'Honey & Bee Products' => [
        ['Raw Honey', 'Pure, unfiltered honey from local bees.', 40.00, 'honey.jpg'],
        ['Beeswax', 'Natural beeswax, perfect for candles and cosmetics.', 30.00, 'beeswax.jpg'],
        ['Propolis', 'Bee propolis extract with health benefits.', 50.00, 'propolis.jpg']
    ]
];

// Create admin user
$admin_data = [
    'name' => 'Admin User',
    'email' => 'admin@agrosmart.com',
    'password' => 'admin123', // In a real app, use a stronger password
    'user_type' => 'admin',
    'location' => 'Lusaka'
];

// Hash the password before inserting
$admin_data['password'] = password_hash($admin_data['password'], PASSWORD_DEFAULT);

// Insert admin into database directly (bypassing the model's email verification)
$admin_sql = "INSERT INTO users (name, email, password, user_type, location, email_verified, is_active) 
              VALUES (?, ?, ?, ?, ?, 1, 1)";
$stmt = mysqli_prepare($conn, $admin_sql);
mysqli_stmt_bind_param($stmt, "sssss", 
    $admin_data['name'], 
    $admin_data['email'], 
    $admin_data['password'], 
    $admin_data['user_type'], 
    $admin_data['location']
);
mysqli_stmt_execute($stmt);
$admin_id = mysqli_insert_id($conn);

show_progress("Created admin user: {$admin_data['email']} (Password: admin123)");

// Update progress
echo "<script>document.getElementById('progressBar').style.width = '10%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Creating farmers...';</script>";

// Create farmers
$farmers = [];
$farmer_count = 5;

for ($i = 1; $i <= $farmer_count; $i++) {
    $location = $locations[array_rand($locations)];
    $literacy_levels = ['Basic', 'Primary', 'Secondary', 'Tertiary'];
    
    $farmer_data = [
        'name' => "Farmer $i",
        'email' => "farmer$i@example.com",
        'password' => password_hash("farmer$i", PASSWORD_DEFAULT),
        'user_type' => 'farmer',
        'location' => $location,
        'phone' => '+260' . rand(900000000, 999999999),
        'bio' => "I am a farmer from $location specializing in various crops. I have been farming for " . rand(2, 20) . " years.",
        'nrc_number' => rand(100000, 999999) . '/10/1',
        'literacy_level' => $literacy_levels[array_rand($literacy_levels)],
        'email_verified' => 1,
        'is_active' => 1
    ];
    
    // Insert farmer into database
    $sql = "INSERT INTO users (name, email, password, user_type, location, phone, bio, nrc_number, literacy_level, email_verified, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", 
        $farmer_data['name'], 
        $farmer_data['email'], 
        $farmer_data['password'], 
        $farmer_data['user_type'], 
        $farmer_data['location'], 
        $farmer_data['phone'],
        $farmer_data['bio'],
        $farmer_data['nrc_number'],
        $farmer_data['literacy_level']
    );
    mysqli_stmt_execute($stmt);
    
    $farmer_id = mysqli_insert_id($conn);
    $farmers[] = [
        'id' => $farmer_id,
        'name' => $farmer_data['name'],
        'email' => $farmer_data['email'],
        'location' => $farmer_data['location']
    ];
    
    show_progress("Created farmer: {$farmer_data['email']} (Password: farmer$i)");
}

// Update progress
echo "<script>document.getElementById('progressBar').style.width = '30%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Creating buyers...';</script>";

// Create buyers
$buyers = [];
$buyer_count = 5;

for ($i = 1; $i <= $buyer_count; $i++) {
    $location = $locations[array_rand($locations)];
    
    $buyer_data = [
        'name' => "Buyer $i",
        'email' => "buyer$i@example.com",
        'password' => password_hash("buyer$i", PASSWORD_DEFAULT),
        'user_type' => 'buyer',
        'location' => $location,
        'phone' => '+260' . rand(900000000, 999999999),
        'bio' => "I am a buyer from $location interested in purchasing quality agricultural products for my business.",
        'email_verified' => 1,
        'is_active' => 1
    ];
    
    // Insert buyer into database
    $sql = "INSERT INTO users (name, email, password, user_type, location, phone, bio, email_verified, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", 
        $buyer_data['name'], 
        $buyer_data['email'], 
        $buyer_data['password'], 
        $buyer_data['user_type'], 
        $buyer_data['location'], 
        $buyer_data['phone'],
        $buyer_data['bio']
    );
    mysqli_stmt_execute($stmt);
    
    $buyer_id = mysqli_insert_id($conn);
    $buyers[] = [
        'id' => $buyer_id,
        'name' => $buyer_data['name'],
        'email' => $buyer_data['email'],
        'location' => $buyer_data['location']
    ];
    
    show_progress("Created buyer: {$buyer_data['email']} (Password: buyer$i)");
}

// Update progress
echo "<script>document.getElementById('progressBar').style.width = '50%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Adding products for farmers...';</script>";

// Create products for each farmer
$all_products = [];

foreach ($farmers as $farmer) {
    // Each farmer gets 3-7 products from random categories
    $product_count = rand(3, 7);
    for ($i = 0; $i < $product_count; $i++) {
        $category = $categories[array_rand($categories)];
        $product_templates = $products_by_category[$category];
        $template = $product_templates[array_rand($product_templates)];
        
        // Add some variation to pricing and stock
        $price_variation = rand(-10, 20) / 100; // -10% to +20%
        $price = round($template[2] * (1 + $price_variation), 2);
        $stock = rand(10, 100);
        
        $product_data = [
            'farmer_id' => $farmer['id'],
            'name' => $template[0],
            'description' => $template[1],
            'price' => $price,
            'category' => $category,
            'stock' => $stock,
            'image' => $template[3]
        ];
        
        // Insert product into database
        $sql = "INSERT INTO products (farmer_id, name, description, price, category, stock, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issdsis", 
            $product_data['farmer_id'], 
            $product_data['name'], 
            $product_data['description'], 
            $product_data['price'], 
            $product_data['category'], 
            $product_data['stock'],
            $product_data['image']
        );
        mysqli_stmt_execute($stmt);
        
        $product_id = mysqli_insert_id($conn);
        $all_products[] = [
            'id' => $product_id,
            'name' => $product_data['name'],
            'farmer_id' => $farmer['id'],
            'farmer_name' => $farmer['name'],
            'price' => $product_data['price'],
            'category' => $category
        ];
        
        show_progress("Added product: {$product_data['name']} (Price: ZMW {$product_data['price']}, Stock: {$product_data['stock']}) for {$farmer['name']}");
    }
}

// Update progress
echo "<script>document.getElementById('progressBar').style.width = '70%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Creating orders...';</script>";

// Create orders (buyers purchasing from farmers)
$order_statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
$order_count = 15; // Total number of orders to create

for ($i = 0; $i < $order_count; $i++) {
    $buyer = $buyers[array_rand($buyers)];
    $product = $all_products[array_rand($all_products)];
    $quantity = rand(1, 5);
    $status = $order_statuses[array_rand($order_statuses)];
    
    // Get farmer info from product
    $farmer_id = $product['farmer_id'];
    
    // Create the order
    $sql = "INSERT INTO orders (buyer_id, farmer_id, product_id, quantity, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiis", 
        $buyer['id'], 
        $farmer_id, 
        $product['id'], 
        $quantity, 
        $status
    );
    mysqli_stmt_execute($stmt);
    
    show_progress("Created order: {$buyer['name']} ordered {$quantity} x {$product['name']} from {$product['farmer_name']} ({$status})");
}

// Update progress
echo "<script>document.getElementById('progressBar').style.width = '90%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Creating messages between users...';</script>";

// Create messages between users
$message_subjects = [
    'Product Inquiry',
    'Order Status',
    'Price Negotiation',
    'Delivery Information',
    'Product Quality',
    'Payment Confirmation',
    'Product Availability',
    'Shipping Details',
    'Discount Request',
    'Product Feedback'
];

$message_count = 20; // Total number of messages to create

for ($i = 0; $i < $message_count; $i++) {
    // Randomly select sender and receiver
    $is_farmer_to_buyer = rand(0, 1) == 1;
    
    if ($is_farmer_to_buyer) {
        $sender = $farmers[array_rand($farmers)];
        $receiver = $buyers[array_rand($buyers)];
        $context = "I wanted to inform you about my products...";
    } else {
        $sender = $buyers[array_rand($buyers)];
        $receiver = $farmers[array_rand($farmers)];
        $context = "I am interested in your products...";
    }
    
    $subject = $message_subjects[array_rand($message_subjects)];
    $is_read = rand(0, 1);
    
    // Create message body
    $message_body = "Dear {$receiver['name']},\n\n";
    $message_body .= "$context\n\n";
    $message_body .= "Please let me know if you have any questions.\n\n";
    $message_body .= "Best regards,\n{$sender['name']}";
    
    // Insert message
    $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message, is_read) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissi", 
        $sender['id'], 
        $receiver['id'], 
        $subject, 
        $message_body,
        $is_read
    );
    mysqli_stmt_execute($stmt);
    
    show_progress("Created message: From {$sender['name']} to {$receiver['name']} - Subject: {$subject}");
}

// Update progress to complete
echo "<script>document.getElementById('progressBar').style.width = '100%';</script>";
echo "<script>document.getElementById('status').innerHTML = 'Sample data setup complete!';</script>";
?>
        </div>
        
        <div class="alert alert-success mt-4">
            <h4>Setup Complete!</h4>
            <p>Sample data has been successfully added to the AgroSmart Market database.</p>
            <h5>Login Credentials:</h5>
            <ul>
                <li><strong>Admin:</strong> admin@agrosmart.com / admin123</li>
                <li><strong>Farmers:</strong> farmer1@example.com through farmer5@example.com (password: farmer1, farmer2, etc.)</li>
                <li><strong>Buyers:</strong> buyer1@example.com through buyer5@example.com (password: buyer1, buyer2, etc.)</li>
            </ul>
            <div class="mt-3">
                <a href="index.php" class="btn btn-primary">Go to Homepage</a>
                <a href="login.php" class="btn btn-success ms-2">Login</a>
            </div>
        </div>
    </div>
    
    <script>
        // Scroll to bottom to show progress
        window.onload = function() {
            window.scrollTo(0, document.body.scrollHeight);
        };
    </script>
</body>
</html>
