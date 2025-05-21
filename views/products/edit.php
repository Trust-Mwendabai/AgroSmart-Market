<?php
// Set page title
$page_title = 'Edit Product - AgroSmart Market';

// Get correct path for includes
$root_path = dirname(dirname(dirname(__FILE__)));

// Include header
include_once $root_path . '/views/partials/header.php';
?>

<style>
    .product-image {
        max-height: 200px;
        max-width: 100%;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    
    #imagePreview {
        max-height: 200px;
        max-width: 100%;
        margin-top: 10px;
        display: none;
        border-radius: 8px;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Product</h2>
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
        
        <!-- Edit Product Form -->
        <div class="card">
            <div class="card-body">
                <form action="product.php?action=edit&id=<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name*</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description*</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $product['description']; ?></textarea>
                                <div class="form-text">Provide a detailed description of your product including quality, size, variety, etc.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price ($)*</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Available Quantity*</label>
                                        <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category*</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled>Select a category</option>
                                    <option value="vegetables" <?php echo ($product['category'] == 'vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                                    <option value="fruits" <?php echo ($product['category'] == 'fruits') ? 'selected' : ''; ?>>Fruits</option>
                                    <option value="grains" <?php echo ($product['category'] == 'grains') ? 'selected' : ''; ?>>Grains & Cereals</option>
                                    <option value="dairy" <?php echo ($product['category'] == 'dairy') ? 'selected' : ''; ?>>Dairy Products</option>
                                    <option value="meat" <?php echo ($product['category'] == 'meat') ? 'selected' : ''; ?>>Meat & Poultry</option>
                                    <option value="herbs" <?php echo ($product['category'] == 'herbs') ? 'selected' : ''; ?>>Herbs & Spices</option>
                                    <option value="nuts" <?php echo ($product['category'] == 'nuts') ? 'selected' : ''; ?>>Nuts & Seeds</option>
                                    <option value="honey" <?php echo ($product['category'] == 'honey') ? 'selected' : ''; ?>>Honey & Sweeteners</option>
                                    <option value="other" <?php echo ($product['category'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Upload a new image or leave empty to keep the current image.</div>
                                
                                <?php if (!empty($product['image'])): ?>
                                    <div class="mt-3">
                                        <p class="mb-2">Current Image:</p>
                                        <img id="currentImage" src="../public/uploads/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="Current Product Image" style="max-height: 200px;">
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3">
                                        <p class="mb-2">No current image</p>
                                    </div>
                                <?php endif; ?>
                                
                                <img id="imagePreview" class="img-fluid mt-3 rounded" alt="New Image Preview" style="display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <a href="product.php" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Image Preview Script -->
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
                if (currentImage) {
                    currentImage.style.display = 'block';
                }
            }
        }
    </script>
</body>
</html>
