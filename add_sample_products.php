<?php
// Include database configuration
require_once 'config/database.php';
require_once 'config/utils.php';

// Sample products data with matching images
$products = [
    [
        'name' => 'Fresh Organic Tomatoes',
        'description' => 'Freshly harvested organic tomatoes, rich in flavor and perfect for salads or cooking.',
        'price' => 25.99,
        'category' => 'Vegetables',
        'image' => 'public/images/tomatoes.jpg',
        'stock' => 50,
        'is_organic' => 1,
        'location' => 'Lusaka'
    ],
    [
        'name' => 'Sweet Pineapples',
        'description' => 'Juicy and sweet pineapples, perfect for fresh eating or juicing.',
        'price' => 35.50,
        'category' => 'Fruits',
        'image' => 'public/images/pineapples.jpg',
        'stock' => 30,
        'is_organic' => 1,
        'location' => 'Copperbelt'
    ],
    [
        'name' => 'Fresh Carrots',
        'description' => 'Crunchy and sweet carrots, packed with vitamins and perfect for snacking or cooking.',
        'price' => 18.75,
        'category' => 'Vegetables',
        'image' => 'public/images/carrots.jpg',
        'stock' => 75,
        'is_organic' => 1,
        'location' => 'Central Province'
    ],
    [
        'name' => 'Ripe Mangoes',
        'description' => 'Sweet and juicy mangoes, perfect for desserts or eating fresh.',
        'price' => 28.00,
        'category' => 'Fruits',
        'image' => 'public/images/mangoes.jpg',
        'stock' => 40,
        'is_organic' => 0,
        'location' => 'Eastern Province'
    ],
    [
        'name' => 'Fresh Green Cabbage',
        'description' => 'Crisp and fresh green cabbage, great for coleslaw or cooking.',
        'price' => 15.50,
        'category' => 'Vegetables',
        'image' => 'public/images/cabbage.jpg',
        'stock' => 35,
        'is_organic' => 1,
        'location' => 'Lusaka'
    ],
    [
        'name' => 'Organic Mint Leaves',
        'description' => 'Fresh organic mint leaves, perfect for teas, cocktails, and cooking.',
        'price' => 22.00,
        'category' => 'Herbs',
        'image' => 'public/images/mint-herb.jpg',
        'stock' => 25,
        'is_organic' => 1,
        'location' => 'Copperbelt'
    ],
    [
        'name' => 'Sweet Potatoes',
        'description' => 'Naturally sweet and nutritious sweet potatoes, great for baking or boiling.',
        'price' => 20.00,
        'category' => 'Tubers',
        'image' => 'public/images/sweet-potatoes.webp',
        'stock' => 60,
        'is_organic' => 1,
        'location' => 'Southern Province'
    ],
    [
        'name' => 'Fresh Bananas',
        'description' => 'Ripe and sweet bananas, a perfect healthy snack.',
        'price' => 30.00,
        'category' => 'Fruits',
        'image' => 'public/images/bananas.jpg',
        'stock' => 55,
        'is_organic' => 0,
        'location' => 'Northern Province'
    ],
    [
        'name' => 'Green Bell Peppers',
        'description' => 'Fresh green bell peppers, crunchy and full of flavor.',
        'price' => 25.50,
        'category' => 'Vegetables',
        'image' => 'public/images/green_pepper.jpg',
        'stock' => 45,
        'is_organic' => 1,
        'location' => 'Lusaka'
    ],
    [
        'name' => 'Juicy Oranges',
        'description' => 'Sweet and juicy oranges, packed with vitamin C.',
        'price' => 32.00,
        'category' => 'Fruits',
        'image' => 'public/images/oranges.jpg',
        'stock' => 38,
        'is_organic' => 0,
        'location' => 'Eastern Province'
    ]
];

// Function to add sample products
function add_sample_products($conn, $products) {
    // Check if products already exist
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        return [
            'success' => false,
            'message' => 'Products already exist in the database. Please remove existing products first.'
        ];
    }
    
    // Get a farmer user ID (assuming there's at least one farmer in the database)
    $farmer_query = "SELECT id FROM users WHERE user_type = 'farmer' LIMIT 1";
    $farmer_result = $conn->query($farmer_query);
    
    if ($farmer_result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'No farmer accounts found. Please create a farmer account first.'
        ];
    }
    
    $farmer = $farmer_result->fetch_assoc();
    $farmer_id = $farmer['id'];
    $added = 0;
    $errors = [];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        foreach ($products as $product) {
            // Check if image exists
            if (!file_exists($product['image'])) {
                $errors[] = "Image not found: " . $product['image'];
                continue;
            }
            
            // Insert product
            $stmt = $conn->prepare("INSERT INTO products (
                farmer_id, name, description, price, image, category, stock, is_organic, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            
            $stmt->bind_param(
                'dssdssdi',
                $farmer_id,
                $product['name'],
                $product['description'],
                $product['price'],
                $product['image'],
                $product['category'],
                $product['stock'],
                $product['is_organic']
            );
            
            if ($stmt->execute()) {
                $added++;
            } else {
                $errors[] = "Error adding product " . $product['name'] . ": " . $conn->error;
            }
            
            $stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true,
            'added' => $added,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Process the request
$result = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = add_sample_products($conn, $products);
}

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sample Products - AgroSmart Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 20px; 
            margin: 20px 0;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Add Sample Products</h1>
        
        <?php if (!empty($result)): ?>
            <?php if ($result['success']): ?>
                <div class="alert alert-success">
                    <h4>Success!</h4>
                    <p>Successfully added <?php echo $result['added']; ?> sample products.</p>
                    <?php if (!empty($result['errors'])): ?>
                        <div class="mt-3">
                            <h5>Errors:</h5>
                            <ul>
                                <?php foreach ($result['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="marketplace.php" class="btn btn-primary">View Marketplace</a>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h4>Error</h4>
                    <p><?php echo htmlspecialchars($result['message']); ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Sample Products to be Added</h5>
                    <p class="card-text">This will add 10 sample products to your marketplace.</p>
                    
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                                <p class="mb-1">K<?php echo number_format($product['price'], 2); ?></p>
                                <small class="text-muted"><?php echo $product['category']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <form method="post" class="mt-4">
                        <button type="submit" class="btn btn-primary">Add Sample Products</button>
                        <a href="marketplace.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
