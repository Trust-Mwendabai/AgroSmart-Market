<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Include models
require_once '../models/User.php';
require_once '../models/Product.php';

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);

// Handle product deletion
if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    if ($product_model->delete_product($product_id, $_SESSION['user_id'])) {
        $_SESSION['success_message'] = "Product has been deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete product. Please try again.";
    }
    header('Location: products.php');
    exit();
}

// Handle product approval/rejection
if (isset($_POST['update_status']) && isset($_POST['product_id']) && isset($_POST['status'])) {
    $product_id = $_POST['product_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE products SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product status has been updated to " . ucfirst($status) . ".";
    } else {
        $_SESSION['error_message'] = "Failed to update product status. Please try again.";
    }
    header('Location: products.php');
    exit();
}

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$farmer_id = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get all products with optional filters
$filters = [];
if (!empty($category)) $filters['category'] = $category;
if (!empty($farmer_id)) $filters['farmer_id'] = $farmer_id;
if (!empty($search)) $filters['search'] = $search;

// Use the correct method name get_all_products instead of getAll
$products = $product_model->get_all_products(100, 0, $filters);

// Get all categories for filter dropdown
$query = "SELECT DISTINCT category FROM products ORDER BY category";
$result = mysqli_query($conn, $query);
$categories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['category'];
    }
}

// Get all farmers for filter dropdown
$query = "SELECT id, name FROM users WHERE user_type = 'farmer' ORDER BY name";
$result = mysqli_query($conn, $query);
$farmers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $farmers[] = $row;
    }
}

// Set page title
$page_title = "Manage Products - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manage Products</h1>
    <p class="mb-4">View, filter, approve, or delete products listed on AgroSmart Market.</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Products</h6>
        </div>
        <div class="card-body">
            <form method="get" action="" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="farmer_id" class="form-label">Farmer</label>
                    <select name="farmer_id" id="farmer_id" class="form-select">
                        <option value="">All Farmers</option>
                        <?php foreach ($farmers as $farmer): ?>
                            <option value="<?php echo $farmer['id']; ?>" <?php echo ($farmer_id == $farmer['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($farmer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or description" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="products.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Product Listings</h6>
            <div>
                <span class="badge bg-primary"><?php echo count($products); ?> Products</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="text-center p-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p>No products found matching your criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="productsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="70">Image</th>
                                <th>Product Name</th>
                                <th>Farmer</th>
                                <th>Category</th>
                                <th>Price (ZMW)</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image_url'])): ?>
                                            <img src="<?php echo '../' . $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="max-height: 50px;">
                                        <?php else: ?>
                                            <div class="product-placeholder">
                                                <i class="fas fa-seedling"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>
                                        <?php
                                        $farmer = $user_model->get_user_by_id($product['farmer_id']);
                                        echo htmlspecialchars($farmer['name'] ?? 'Unknown');
                                        ?>
                                    </td>
                                    <td><?php echo ucfirst($product['category']); ?></td>
                                    <td><?php echo format_currency($product['price']); ?></td>
                                    <td><?php echo ($product['quantity'] ?? '0') . ' ' . ($product['unit'] ?? 'units'); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = '';
                                        switch($product['status'] ?? 'pending') {
                                            case 'pending': $status_class = 'bg-warning'; break;
                                            case 'approved': $status_class = 'bg-success'; break;
                                            case 'rejected': $status_class = 'bg-danger'; break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($product['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($product['date_added'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $product['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (($product['status'] ?? 'pending') == 'pending'): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this product?')">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this product?')">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" name="delete_product" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewModalLabel<?php echo $product['id']; ?>">
                                                            <?php echo htmlspecialchars($product['name']); ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <?php if (!empty($product['image_url'])): ?>
                                                                    <img src="<?php echo '../' . $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                                                <?php else: ?>
                                                                    <div class="product-placeholder">
                                                                        <i class="fas fa-seedling fa-5x"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                                                <p class="text-muted">
                                                                    <strong>Farmer:</strong> 
                                                                    <?php
                                                                    $farmer = $user_model->get_user_by_id($product['farmer_id']);
                                                                    echo htmlspecialchars($farmer['name'] ?? 'Unknown');
                                                                    ?>
                                                                </p>
                                                                <p><strong>Category:</strong> <?php echo ucfirst($product['category']); ?></p>
                                                                <p><strong>Price:</strong> <?php echo format_currency($product['price']); ?></p>
                                                                <p><strong>Quantity Available:</strong> <?php echo $product['quantity']; ?> <?php echo $product['unit']; ?></p>
                                                                <p><strong>Status:</strong> 
                                                                    <span class="badge <?php echo $status_class; ?>">
                                                                        <?php echo ucfirst($product['status']); ?>
                                                                    </span>
                                                                </p>
                                                                <p><strong>Date Added:</strong> <?php echo date('F d, Y', strtotime($product['date_added'])); ?></p>
                                                                <div class="mt-3">
                                                                    <h5>Description</h5>
                                                                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable for better user experience
    $(document).ready(function() {
        $('#productsTable').DataTable({
            "order": [[ 7, "desc" ]], // Sort by date added by default
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
</script>

<?php
// Include admin footer
include '../views/admin/partials/footer.php';
?>
