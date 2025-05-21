<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Required functions
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token() {
        return md5(uniqid(mt_rand(), true));
    }
}

// Set page title
$page_title = 'Add Product - AgroSmart Market';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root {
        --primary-color: #4CAF50;
        --secondary-color: #FFC107;
        --dark-color: #333;
        --light-color: #f4f4f4;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--light-color);
    }
    
    .navbar {
        background-color: var(--primary-color);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
    }
    
    #imagePreview {
        max-height: 200px;
        max-width: 100%;
        margin-top: 10px;
        display: none;
    }
    
    .product-preview {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Product</h2>
            <a href="product.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
        
        <!-- Alerts -->
        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Add Product Form -->
        <div class="card">
            <div class="card-body">
                <form action="product.php?action=add" method="POST" enctype="multipart/form-data">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name*</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description*</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                <div class="form-text">Provide a detailed description of your product including quality, size, variety, etc.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price ($)*</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Available Quantity*</label>
                                        <input type="number" class="form-control" id="stock" name="stock" min="1" value="1" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category*</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Select a category</option>
                                    <option value="vegetables">Vegetables</option>
                                    <option value="fruits">Fruits</option>
                                    <option value="grains">Grains & Cereals</option>
                                    <option value="dairy">Dairy Products</option>
                                    <option value="meat">Meat & Poultry</option>
                                    <option value="herbs">Herbs & Spices</option>
                                    <option value="nuts">Nuts & Seeds</option>
                                    <option value="honey">Honey & Sweeteners</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Upload a clear image of your product. Maximum size: 5MB.</div>
                                <img id="imagePreview" class="img-fluid mt-3 rounded" alt="Image Preview">
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Tips for Good Product Listings</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="small mb-0">
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
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-leaf me-2"></i>AgroSmart Market</h5>
                    <p class="small">Connecting farmers directly with buyers</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small">&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Image Preview Script -->
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
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
