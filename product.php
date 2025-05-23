<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/Product.php';
require_once 'models/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('auth.php?action=login');
}

// Initialize models
$product_model = new Product($conn);
$user_model = new User($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

switch ($action) {
    case 'add':
        // Only farmers can add products
        if (!is_farmer()) {
            redirect("dashboard.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $name = sanitize_input($_POST['name']);
            $description = sanitize_input($_POST['description']);
            $price = (float) $_POST['price'];
            $category = sanitize_input($_POST['category']);
            $stock = (int) $_POST['stock'];
            $farmer_id = $_SESSION['user_id'];
            
            // Check for required fields
            if (empty($name) || empty($description) || empty($price)) {
                $error = "Product name, description, and price are required";
                break;
            }
            
            // Handle image upload
            $image_filename = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image'], 'public/uploads/');
                
                if (isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                    break;
                } else {
                    $image_filename = $upload_result['filename'];
                }
            }
            
            // Add product to database
            $result = $product_model->add_product(
                $farmer_id,
                $name,
                $description,
                $price,
                $image_filename,
                $category,
                $stock
            );
            
            if (isset($result['success'])) {
                $success = "Product added successfully!";
                // Redirect to product list after 2 seconds
                header("Refresh: 2; URL=products.php");
            } else {
                $error = isset($result['error']) ? $result['error'] : "Unknown error occurred";
            }
        }
        
        // Direct output approach for add product
        $page_title = 'Add Product - AgroSmart Market';
        include_once 'views/partials/header.php';
        ?>
        <div class="container py-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Add New Product</h2>
                <a href="product.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form action="product.php?action=add" method="POST" enctype="multipart/form-data">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name* </label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description* </label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                    <div class="form-text">Provide a detailed description of your product including quality, size, variety, etc.</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price ($)* </label>
                                            <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Available Quantity* </label>
                                            <input type="number" class="form-control" id="stock" name="stock" min="1" value="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category* </label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="" selected disabled>Select a category</option>
                                                <option value="vegetables">Vegetables</option>
                                                <option value="fruits">Fruits</option>
                                                <option value="grains">Grains</option>
                                                <option value="dairy">Dairy</option>
                                                <option value="livestock">Livestock</option>
                                                <option value="poultry">Poultry</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">Upload a clear image of your product. Maximum size: 5MB.</div>
                                    
                                    <div class="mt-2 text-center">
                                        <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none;">
                                    </div>
                                </div>
                                
                                <div class="card mt-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Tips for Good Product Listings</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="small">
                                            <li>Use a clear, descriptive product name</li>
                                            <li>Include detailed information about quality and freshness</li>
                                            <li>Mention harvesting dates if applicable</li>
                                            <li>Upload high-quality photos</li>
                                            <li>Set competitive but fair prices</li>
                                            <li>Keep your inventory updated</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
            function previewImage(input) {
                var preview = document.getElementById('imagePreview');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
        <?php
        include_once 'views/partials/footer.php';
        break;
        
    case 'edit':
        // Only farmers can edit products
        if (!is_farmer()) {
            redirect("dashboard.php");
        }
        
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("products.php");
        }
        
        // Get product data
        $product = $product_model->get_product($product_id);
        
        // Check if product exists and belongs to the farmer
        if (!$product || $product['farmer_id'] != $_SESSION['user_id']) {
            redirect("products.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $name = sanitize_input($_POST['name']);
            $description = sanitize_input($_POST['description']);
            $price = (float) $_POST['price'];
            $category = sanitize_input($_POST['category']);
            $stock = (int) $_POST['stock'];
            $farmer_id = $_SESSION['user_id'];
            
            // Check for required fields
            if (empty($name) || empty($description) || empty($price)) {
                $error = "Product name, description, and price are required";
                break;
            }
            
            // Prepare data for update
            $data = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'stock' => $stock
            ];
            
            // Handle image upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image'], 'public/uploads/');
                
                if (isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                    break;
                } else {
                    $data['image'] = $upload_result['filename'];
                }
            }
            
            // Update product
            $result = $product_model->update_product($product_id, $farmer_id, $data);
            
            if (isset($result['success'])) {
                $success = "Product updated successfully!";
                // Reload the page with updated data
                header("Refresh: 2; URL=products.php?action=edit&id=" . $product_id);
            } else {
                $error = isset($result['error']) ? $result['error'] : "Unknown error occurred";
            }
        }
        
        include 'views/products/edit.php';
        break;
        
    case 'delete':
        // Only farmers can delete products
        if (!is_farmer()) {
            redirect("dashboard.php");
        }
        
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("products.php");
        }
        
        // Delete product
        $result = $product_model->delete_product($product_id, $_SESSION['user_id']);
        
        if (isset($result['success'])) {
            $success = "Product deleted successfully!";
            // Redirect to product list after 2 seconds
            header("Refresh: 2; URL=products.php");
        } else {
            $error = isset($result['error']) ? $result['error'] : "Unknown error occurred";
            // Redirect to product list after 2 seconds
            header("Refresh: 2; URL=products.php");
        }
        
        include 'views/products/delete.php';
        break;
        
    case 'view':
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("marketplace.php");
        }
        
        // Get product data
        $product = $product_model->get_product($product_id);
        
        // Check if product exists
        if (!$product) {
            redirect("marketplace.php");
        }
        
        // Get farmer data
        $farmer = $user_model->get_user($product['farmer_id']);
        
        include 'views/products/view.php';
        break;
        
    default:
        // For farmers, show their products
        if (is_farmer()) {
            $products = $product_model->get_farmer_products($_SESSION['user_id']);
            include 'views/products/list.php';
        } else {
            // Redirect buyers to marketplace
            redirect("marketplace.php");
        }
        break;
}
?>
