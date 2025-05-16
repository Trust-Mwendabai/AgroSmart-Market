<?php
/**
 * AgroSmart Market - Sample Data Setup Script
 * 
 * This script populates the database with sample farmers, buyers, and products
 * for testing and demonstration purposes.
 */

// Include necessary files
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'config/utils.php';

// Ensure the uploads directories exist
$directories = [
    'public/uploads/products',
    'public/uploads/users',
    'public/images'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);

// Function to create a user if it doesn't exist
function create_user($user_model, $data) {
    // Check if user exists using the private email_exists method indirectly
    // We'll try to register and if we get an error about email existing, we'll handle it
    $result = $user_model->register(
        $data['name'],
        $data['email'],
        $data['password'],
        $data['user_type'],
        $data['location']
    );
    
    if (isset($result['error']) && strpos($result['error'], 'Email already exists') !== false) {
        // Email exists, try to find the user ID by querying all users
        $all_users = $user_model->get_all_users(100, 0); // Get up to 100 users
        $user_id = null;
        
        foreach ($all_users as $user) {
            if ($user['email'] === $data['email']) {
                $user_id = $user['id'];
                break;
            }
        }
        
        if ($user_id) {
            echo "User already exists: {$data['name']} (ID: {$user_id})<br>";
            return $user_id;
        } else {
            echo "User with email {$data['email']} exists but could not be retrieved.<br>";
            return false;
        }
    } elseif (isset($result['success']) && $result['success']) {
        // Successfully registered
        $user_id = $result['user_id'];
        
        // Mark email as verified for sample users
        // Using the global connection variable instead of trying to access private property
        global $conn;
        $sql = "UPDATE users SET email_verified = 1, is_active = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        echo "Created {$data['user_type']}: {$data['name']} (ID: $user_id)<br>";
        return $user_id;
    } else {
        echo "Failed to create user: {$data['name']}" . (isset($result['error']) ? " - {$result['error']}" : "") . "<br>";
        return false;
    }
}

// Function to create a product if it doesn't exist
function create_product($product_model, $data) {
    // Use global connection instead of trying to access private property
    global $conn;
    
    // Check if product exists (by name and farmer_id)
    $sql = "SELECT id FROM products WHERE name = ? AND farmer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $data['name'], $data['farmer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Product doesn't exist, create it
        // First check if is_organic column exists
        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'is_organic'");
        
        if (mysqli_num_rows($check_column) > 0) {
            // is_organic column exists, include it in the query
            $sql = "INSERT INTO products (name, description, price, stock, category, image, farmer_id, is_organic, location, date_added)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $is_organic = $data['is_organic'] ?? 0;
            $stmt->bind_param("ssdissiis", 
                $data['name'],
                $data['description'],
                $data['price'],
                $data['stock'],
                $data['category'],
                $data['image'],
                $data['farmer_id'],
                $is_organic,
                $data['location']
            );
        } else {
            // Check if location column exists
            $check_location = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'location'");
            
            if (mysqli_num_rows($check_location) > 0) {
                // Both columns are missing but location exists
                $sql = "INSERT INTO products (name, description, price, stock, category, image, farmer_id, location, date_added)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdissss", 
                    $data['name'],
                    $data['description'],
                    $data['price'],
                    $data['stock'],
                    $data['category'],
                    $data['image'],
                    $data['farmer_id'],
                    $data['location']
                );
            } else {
                // Both columns are missing
                $sql = "INSERT INTO products (name, description, price, stock, category, image, farmer_id, date_added)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdissi", 
                    $data['name'],
                    $data['description'],
                    $data['price'],
                    $data['stock'],
                    $data['category'],
                    $data['image'],
                    $data['farmer_id']
                );
                
                // Try to add the location column
                $alter_location = "ALTER TABLE products ADD COLUMN location VARCHAR(255) NULL DEFAULT NULL";
                mysqli_query($conn, $alter_location);
            }
            
            // Try to add the column for future use
            $alter_query = "ALTER TABLE products ADD COLUMN is_organic TINYINT(1) NOT NULL DEFAULT 0";
            mysqli_query($conn, $alter_query);
        }
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            echo "Created product: {$data['name']} (ID: $product_id)<br>";
            return $product_id;
        } else {
            echo "Failed to create product: {$data['name']} - " . $conn->error . "<br>";
            return false;
        }
    } else {
        $product_id = $result->fetch_assoc()['id'];
        echo "Product already exists: {$data['name']} (ID: $product_id)<br>";
        return $product_id;
    }
}

// Sample Users Data
$users = [
    // Farmers
    [
        'name' => 'John Chanda',
        'email' => 'john.chanda@example.com',
        'password' => 'password123',
        'user_type' => 'farmer',
        'phone' => '+260 97 1234567',
        'location' => 'Lusaka, Zambia'
    ],
    [
        'name' => 'Maria Mbewe',
        'email' => 'maria.mbewe@example.com',
        'password' => 'password123',
        'user_type' => 'farmer',
        'phone' => '+260 96 5432198',
        'location' => 'Kitwe, Zambia'
    ],
    
    // Buyers
    [
        'name' => 'David Tembo',
        'email' => 'david.tembo@example.com',
        'password' => 'password123',
        'user_type' => 'buyer',
        'phone' => '+260 95 8765432',
        'location' => 'Ndola, Zambia'
    ],
    [
        'name' => 'Sarah Banda',
        'email' => 'sarah.banda@example.com',
        'password' => 'password123',
        'user_type' => 'buyer',
        'phone' => '+260 96 1122334',
        'location' => 'Livingstone, Zambia'
    ]
];

// Create users and store their IDs
$user_ids = [];
echo "<h2>Creating Sample Users</h2>";

foreach ($users as $user_data) {
    $user_id = create_user($user_model, $user_data);
    if ($user_id) {
        $user_ids[$user_data['name']] = $user_id;
    }
}

// Sample Products Data
if (isset($user_ids['John Chanda']) && isset($user_ids['Maria Mbewe'])) {
    $products = [
        // Products for John Chanda
        [
            'name' => 'Organic Tomatoes',
            'description' => 'Fresh, organic tomatoes grown without pesticides. Perfect for salads and cooking.',
            'price' => 5.99,
            'stock' => 50,
            'category' => 'Vegetables',
            'image' => 'tomatoes.jpg',
            'farmer_id' => $user_ids['John Chanda'],
            'is_organic' => 1,
            'location' => 'Lusaka, Zambia'
        ],
        [
            'name' => 'Fresh Cabbage',
            'description' => 'Locally grown cabbage. Perfect for salads and cooking.',
            'price' => 3.49,
            'stock' => 100,
            'category' => 'Vegetables',
            'image' => 'cabbage.jpg',
            'farmer_id' => $user_ids['John Chanda'],
            'is_organic' => 0,
            'location' => 'Lusaka, Zambia'
        ],
        [
            'name' => 'Organic Carrots',
            'description' => 'Fresh, crisp carrots harvested daily from our farm.',
            'price' => 4.29,
            'stock' => 75,
            'category' => 'Vegetables',
            'image' => 'carrots.jpg',
            'farmer_id' => $user_ids['John Chanda'],
            'is_organic' => 1,
            'location' => 'Lusaka, Zambia'
        ],
        
        // Products for Maria Mbewe
        [
            'name' => 'Fresh Mangoes',
            'description' => 'Sweet, juicy mangoes from Kitwe. Perfect for eating fresh or making smoothies.',
            'price' => 6.99,
            'stock' => 40,
            'category' => 'Fruits',
            'image' => 'mangoes.jpg',
            'farmer_id' => $user_ids['Maria Mbewe'],
            'is_organic' => 1,
            'location' => 'Kitwe, Zambia'
        ],
        [
            'name' => 'Yellow Onions',
            'description' => 'Medium-sized onions with strong flavor. Excellent for salads and cooking.',
            'price' => 2.49,
            'stock' => 120,
            'category' => 'Vegetables',
            'image' => 'onions.jpg',
            'farmer_id' => $user_ids['Maria Mbewe'],
            'is_organic' => 0,
            'location' => 'Kitwe, Zambia'
        ],
        [
            'name' => 'Sweet Pineapples',
            'description' => 'Farm fresh pineapples, sweet and juicy. Perfect for desserts and smoothies.',
            'price' => 4.99,
            'stock' => 30,
            'category' => 'Fruits',
            'image' => 'pineapples.jpg',
            'farmer_id' => $user_ids['Maria Mbewe'],
            'is_organic' => 1,
            'location' => 'Kitwe, Zambia'
        ],
        [
            'name' => 'Fresh Oranges',
            'description' => 'Sweet and juicy oranges. Perfect for fresh juice or eating.',
            'price' => 7.99,
            'stock' => 25,
            'category' => 'Fruits',
            'image' => 'oranges.jpg',
            'farmer_id' => $user_ids['Maria Mbewe'],
            'is_organic' => 1,
            'location' => 'Kitwe, Zambia'
        ]
    ];
    
    // Create products
    echo "<h2>Creating Sample Products</h2>";
    foreach ($products as $product_data) {
        create_product($product_model, $product_data);
    }
} else {
    echo "<p>Error: Could not create products because farmer IDs were not found.</p>";
}

echo "<p>Sample data setup complete! <a href='index.php'>Go to Homepage</a></p>";
?>
