<?php
// Include language helper
require_once '../helpers/language.php';

// Get correct path for includes
$root_path = dirname(dirname(dirname(__FILE__)));

// Include header
include_once $root_path . '/views/partials/header.php';
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-seedling text-success me-2"></i><?php echo __('manage_products', 'Manage Products'); ?></h2>
        <a href="product.php?action=add" class="btn btn-success">
            <i class="fas fa-plus me-2"></i><?php echo __('add_product', 'Add New Product'); ?>
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
    
    <!-- Products Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="product.php" method="GET" class="row align-items-end">
                <input type="hidden" name="action" value="manage">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="category" class="form-label"><?php echo __('filter_category', 'Filter by Category'); ?></label>
                    <select class="form-select" id="category" name="category">
                        <option value=""><?php echo __('all_categories', 'All Categories'); ?></option>
                        <option value="vegetables" <?php echo (isset($_GET['category']) && $_GET['category'] == 'vegetables') ? 'selected' : ''; ?>><?php echo __('vegetables', 'Vegetables'); ?></option>
                        <option value="fruits" <?php echo (isset($_GET['category']) && $_GET['category'] == 'fruits') ? 'selected' : ''; ?>><?php echo __('fruits', 'Fruits'); ?></option>
                        <option value="grains" <?php echo (isset($_GET['category']) && $_GET['category'] == 'grains') ? 'selected' : ''; ?>><?php echo __('grains', 'Grains'); ?></option>
                        <option value="dairy" <?php echo (isset($_GET['category']) && $_GET['category'] == 'dairy') ? 'selected' : ''; ?>><?php echo __('dairy', 'Dairy'); ?></option>
                        <option value="meat" <?php echo (isset($_GET['category']) && $_GET['category'] == 'meat') ? 'selected' : ''; ?>><?php echo __('meat', 'Meat'); ?></option>
                        <option value="other" <?php echo (isset($_GET['category']) && $_GET['category'] == 'other') ? 'selected' : ''; ?>><?php echo __('other', 'Other'); ?></option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="stock" class="form-label"><?php echo __('filter_stock', 'Filter by Stock'); ?></label>
                    <select class="form-select" id="stock" name="stock">
                        <option value=""><?php echo __('all_stock', 'All Stock'); ?></option>
                        <option value="in_stock" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'in_stock') ? 'selected' : ''; ?>><?php echo __('in_stock', 'In Stock'); ?></option>
                        <option value="low_stock" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'low_stock') ? 'selected' : ''; ?>><?php echo __('low_stock', 'Low Stock (< 10)'); ?></option>
                        <option value="out_of_stock" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'out_of_stock') ? 'selected' : ''; ?>><?php echo __('out_of_stock', 'Out of Stock'); ?></option>
                    </select>
                </div>
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1 me-2">
                        <i class="fas fa-filter me-2"></i><?php echo __('apply_filters', 'Apply Filters'); ?>
                    </button>
                    <a href="product.php?action=manage" class="btn btn-outline-secondary flex-grow-1">
                        <i class="fas fa-redo me-2"></i><?php echo __('reset', 'Reset'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th><?php echo __('product', 'Product'); ?></th>
                                <th class="text-center"><?php echo __('price', 'Price'); ?></th>
                                <th class="text-center"><?php echo __('stock', 'Stock'); ?></th>
                                <th class="text-center"><?php echo __('category', 'Category'); ?></th>
                                <th class="text-center"><?php echo __('organic', 'Organic'); ?></th>
                                <th class="text-center"><?php echo __('actions', 'Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="product-img me-3" style="width: 60px; height: 60px; overflow: hidden; border-radius: 8px;">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="public/uploads/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid h-100 w-100 object-fit-cover">
                                                <?php else: ?>
                                                    <div class="bg-light h-100 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-seedling text-success fa-2x"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                <small class="text-muted"><?php echo substr(htmlspecialchars($product['description']), 0, 60) . (strlen($product['description']) > 60 ? '...' : ''); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo format_price($product['price']); ?> / <?php echo htmlspecialchars($product['unit']); ?></td>
                                    <td class="text-center">
                                        <?php if ($product['stock'] <= 0): ?>
                                            <span class="badge bg-danger"><?php echo __('out_of_stock', 'Out of Stock'); ?></span>
                                        <?php elseif ($product['stock'] < 10): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $product['stock']; ?> <?php echo __('units_left', 'units left'); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['stock']; ?> <?php echo __('in_stock', 'in stock'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark"><?php echo ucfirst(htmlspecialchars($product['category'])); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($product['is_organic']): ?>
                                            <span class="badge bg-success"><i class="fas fa-leaf me-1"></i><?php echo __('organic', 'Organic'); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo __('non_organic', 'Non-Organic'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary" title="<?php echo __('view_product', 'View Product'); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="product.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-success" title="<?php echo __('edit_product', 'Edit Product'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="product.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" title="<?php echo __('delete_product', 'Delete Product'); ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state p-4 text-center">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-seedling fa-4x text-muted"></i>
                    </div>
                    <h4><?php echo __('no_products', 'No Products Yet'); ?></h4>
                    <p class="text-muted mb-4"><?php echo __('start_adding', 'Start adding your products to sell them in the marketplace'); ?></p>
                    <a href="product.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i><?php echo __('add_first_product', 'Add Your First Product'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include_once $root_path . '/views/partials/footer.php';
?>
