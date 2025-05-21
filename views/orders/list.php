<?php
// Set page title
$page_title = is_farmer() ? 'Orders from Buyers - AgroSmart Market' : 'My Orders - AgroSmart Market';

// Get correct path for includes
$root_path = dirname(dirname(dirname(__FILE__)));

// Include header
include_once $root_path . '/views/partials/header.php';

// Add page-specific styles
?>
<style>
    .product-img {
        height: 50px;
        width: 50px;
        object-fit: cover;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo is_farmer() ? 'Orders from Buyers' : 'My Orders'; ?></h2>
            <?php if (!is_farmer()): ?>
                <a href="marketplace.php" class="btn btn-primary">
                    <i class="fas fa-shopping-basket me-2"></i>Browse More Products
                </a>
            <?php endif; ?>
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
        
        <!-- Orders Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="order.php" method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Orders</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="shipped" <?php echo (isset($_GET['status']) && $_GET['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo (isset($_GET['status']) && $_GET['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="date" class="form-label">Filter by Date</label>
                        <select class="form-select" id="date" name="date">
                            <option value="">All Time</option>
                            <option value="today" <?php echo (isset($_GET['date']) && $_GET['date'] == 'today') ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo (isset($_GET['date']) && $_GET['date'] == 'week') ? 'selected' : ''; ?>>This Week</option>
                            <option value="month" <?php echo (isset($_GET['date']) && $_GET['date'] == 'month') ? 'selected' : ''; ?>>This Month</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex">
                        <button type="submit" class="btn btn-primary flex-grow-1 me-2">Apply Filters</button>
                        <a href="order.php" class="btn btn-outline-secondary flex-grow-1">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th><?php echo is_farmer() ? 'Buyer' : 'Seller'; ?></th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($order['product_image'])): ?>
                                                    <img src="../public/uploads/<?php echo $order['product_image']; ?>" class="product-img rounded me-3" alt="<?php echo $order['product_name']; ?>">
                                                <?php else: ?>
                                                    <img src="../images/default-product.jpg" class="product-img rounded me-3" alt="<?php echo $order['product_name']; ?>">
                                                <?php endif; ?>
                                                <span><?php echo $order['product_name']; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo is_farmer() ? $order['buyer_name'] : $order['farmer_name']; ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><?php echo format_price($order['price'] * $order['quantity']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['date_ordered'])); ?></td>
                                        <td>
                                            <?php
                                                $status_class = '';
                                                switch ($order['status']) {
                                                    case 'pending':
                                                        $status_class = 'bg-warning';
                                                        break;
                                                    case 'confirmed':
                                                        $status_class = 'bg-info';
                                                        break;
                                                    case 'shipped':
                                                        $status_class = 'bg-primary';
                                                        break;
                                                    case 'delivered':
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = 'bg-danger';
                                                        break;
                                                    default:
                                                        $status_class = 'bg-secondary';
                                                }
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="order.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View Order Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (is_farmer() && $order['status'] === 'pending'): ?>
                                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success" title="Update Status">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php elseif (!is_farmer() && $order['status'] === 'pending'): ?>
                                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger" title="Cancel Order">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h3>No Orders Yet</h3>
                        <?php if (is_farmer()): ?>
                            <p class="text-muted">You haven't received any orders from buyers yet.</p>
                        <?php else: ?>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="../marketplace.php" class="btn btn-primary mt-3">Browse Products</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
<?php
// Include footer
include_once $root_path . '/views/partials/footer.php';
?>
