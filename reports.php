<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/User.php';
require_once 'models/Report.php';
require_once 'models/Product.php';
require_once 'models/Order.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

// Get user information
$user_model = new User($conn);
$user = $user_model->get_user_by_id($_SESSION['user_id']);

if (!$user) {
    redirect('login.php');
    exit;
}

// Initialize models
$report_model = new Report($conn);
$product_model = new Product($conn);
$order_model = new Order($conn);

// Set report type and period
$report_type = isset($_GET['type']) ? $_GET['type'] : 'summary';
$period = isset($_GET['period']) ? $_GET['period'] : 'all';
$valid_periods = ['all', 'month', 'year', 'custom'];
$valid_report_types = ['summary', 'products', 'categories', 'transactions', 'trend'];

// Handle custom date range
$start_date = null;
$end_date = null;

if (isset($_GET['start_date']) && !empty($_GET['start_date']) && 
    isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    // Validate date format
    $start_date = date('Y-m-d', strtotime($_GET['start_date']));
    $end_date = date('Y-m-d', strtotime($_GET['end_date']));
    
    // Set period to custom if dates are valid
    if ($start_date && $end_date) {
        $period = 'custom';
    }
}

// Validate parameters
if (!in_array($period, $valid_periods)) {
    $period = 'all';
}

if (!in_array($report_type, $valid_report_types)) {
    $report_type = 'summary';
}

// Set title based on user type
$title = ($user['user_type'] === 'farmer') ? 'Farmer Sales Reports' : 'Buyer Purchase Reports';

// Load report data based on user type and report type
$data = [];
$user_id = $user['id'];
$user_type = $user['user_type'];

if ($user_type === 'farmer') {
    switch ($report_type) {
        case 'summary':
            $data = ($period === 'custom') 
                ? $report_model->get_farmer_sales_summary($user_id, $period, $start_date, $end_date)
                : $report_model->get_farmer_sales_summary($user_id, $period);
            break;
        
        case 'products':
            $data = ($period === 'custom') 
                ? $report_model->get_farmer_product_sales($user_id, $period, $start_date, $end_date)
                : $report_model->get_farmer_product_sales($user_id, $period);
            break;
            
        case 'trend':
            $months = isset($_GET['months']) ? (int)$_GET['months'] : 6;
            $data = $report_model->get_farmer_monthly_trend($user_id, $months);
            break;
            
        case 'transactions':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $data['transactions'] = ($period === 'custom')
                ? $report_model->get_transaction_details($user_id, $user_type, $period, $limit, $offset, $start_date, $end_date)
                : $report_model->get_transaction_details($user_id, $user_type, $period, $limit, $offset);
            
            $data['total'] = ($period === 'custom')
                ? $report_model->count_transactions($user_id, $user_type, $period, $start_date, $end_date)
                : $report_model->count_transactions($user_id, $user_type, $period);
                
            $data['pages'] = ceil($data['total'] / $limit);
            $data['current_page'] = $page;
            break;
    }
} else { // Buyer
    switch ($report_type) {
        case 'summary':
            $data = ($period === 'custom') 
                ? $report_model->get_buyer_purchase_summary($user_id, $period, $start_date, $end_date)
                : $report_model->get_buyer_purchase_summary($user_id, $period);
            break;
            
        case 'categories':
            $data = ($period === 'custom') 
                ? $report_model->get_buyer_category_breakdown($user_id, $period, $start_date, $end_date)
                : $report_model->get_buyer_category_breakdown($user_id, $period);
            break;
            
        case 'transactions':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $data['transactions'] = ($period === 'custom')
                ? $report_model->get_transaction_details($user_id, $user_type, $period, $limit, $offset, $start_date, $end_date)
                : $report_model->get_transaction_details($user_id, $user_type, $period, $limit, $offset);
                
            $data['total'] = ($period === 'custom')
                ? $report_model->count_transactions($user_id, $user_type, $period, $start_date, $end_date)
                : $report_model->count_transactions($user_id, $user_type, $period);
                
            $data['pages'] = ceil($data['total'] / $limit);
            $data['current_page'] = $page;
            break;
    }
}

// Use the format_currency function from utils.php
// Temporarily keeping this here for reference:
// Original format was: 'ZMW ' . number_format($amount, 2)

// Format date values
function format_date($date_string) {
    $date = new DateTime($date_string);
    return $date->format('M d, Y');
}

// Get period name for display
function get_period_name($period, $start_date = null, $end_date = null) {
    switch ($period) {
        case 'month':
            return 'Last 30 Days';
        case 'year':
            return 'Last 12 Months';
        case 'custom':
            if ($start_date && $end_date) {
                $formatted_start = date('M d, Y', strtotime($start_date));
                $formatted_end = date('M d, Y', strtotime($end_date));
                return "$formatted_start to $formatted_end";
            }
            return 'Custom Range';
        default:
            return 'All Time';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - AgroSmart Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    
    <!-- Chart.js for generating charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    <style>
        @media print {
            #customDateForm, .btn, header, footer, .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'views/partials/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-line text-primary me-2"></i><?php echo $title; ?></h2>
            
            <!-- Export buttons -->
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" id="printReport">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <?php if ($report_type === 'transactions'): ?>
                <button type="button" class="btn btn-outline-success" id="exportCSV">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Report filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <!-- Report Type Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Report Type</label>
                        <div class="btn-group w-100" role="group">
                            <a href="?type=summary&period=<?php echo $period; ?><?php echo ($period === 'custom') ? '&start_date=' . $start_date . '&end_date=' . $end_date : ''; ?>" class="btn btn-outline-primary <?php echo $report_type === 'summary' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-pie me-1"></i> Summary
                            </a>
                            
                            <?php if ($user_type === 'farmer'): ?>
                            <a href="?type=products&period=<?php echo $period; ?><?php echo ($period === 'custom') ? '&start_date=' . $start_date . '&end_date=' . $end_date : ''; ?>" class="btn btn-outline-primary <?php echo $report_type === 'products' ? 'active' : ''; ?>">
                                <i class="fas fa-box me-1"></i> Products
                            </a>
                            <a href="?type=trend&period=<?php echo $period; ?><?php echo ($period === 'custom') ? '&start_date=' . $start_date . '&end_date=' . $end_date : ''; ?>" class="btn btn-outline-primary <?php echo $report_type === 'trend' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line me-1"></i> Trends
                            </a>
                            <?php else: ?>
                            <a href="?type=categories&period=<?php echo $period; ?><?php echo ($period === 'custom') ? '&start_date=' . $start_date . '&end_date=' . $end_date : ''; ?>" class="btn btn-outline-primary <?php echo $report_type === 'categories' ? 'active' : ''; ?>">
                                <i class="fas fa-tags me-1"></i> Categories
                            </a>
                            <?php endif; ?>
                            
                            <a href="?type=transactions&period=<?php echo $period; ?><?php echo ($period === 'custom') ? '&start_date=' . $start_date . '&end_date=' . $end_date : ''; ?>" class="btn btn-outline-primary <?php echo $report_type === 'transactions' ? 'active' : ''; ?>">
                                <i class="fas fa-list me-1"></i> Transactions
                            </a>
                        </div>
                    </div>
                    
                    <!-- Time Period Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Time Period</label>
                        <div class="btn-group w-100" role="group">
                            <a href="?type=<?php echo $report_type; ?>&period=all" class="btn btn-outline-primary <?php echo $period === 'all' ? 'active' : ''; ?>">
                                All Time
                            </a>
                            <a href="?type=<?php echo $report_type; ?>&period=month" class="btn btn-outline-primary <?php echo $period === 'month' ? 'active' : ''; ?>">
                                Last 30 Days
                            </a>
                            <a href="?type=<?php echo $report_type; ?>&period=year" class="btn btn-outline-primary <?php echo $period === 'year' ? 'active' : ''; ?>">
                                Last 12 Months
                            </a>
                            <button type="button" id="customDateBtn" class="btn btn-outline-primary <?php echo $period === 'custom' ? 'active' : ''; ?>">
                                <i class="fas fa-calendar-alt me-1"></i> Custom
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Custom Date Range Form -->
                <div class="row" id="customDateForm" <?php echo $period !== 'custom' ? 'style="display: none;"' : ''; ?>>
                    <div class="col-12">
                        <form action="" method="GET" class="row g-3 align-items-end">
                            <input type="hidden" name="type" value="<?php echo $report_type; ?>">
                            <input type="hidden" name="period" value="custom">
                            
                            <div class="col-md-5">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $start_date ?? ''; ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $end_date ?? ''; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Apply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Content -->
        <div id="reportContent">
            <?php if ($user_type === 'farmer'): ?>
                <?php include 'views/reports/farmer_' . $report_type . '.php'; ?>
            <?php else: ?>
                <?php include 'views/reports/buyer_' . $report_type . '.php'; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'views/partials/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Custom date range toggle
        document.getElementById('customDateBtn').addEventListener('click', function() {
            const customDateForm = document.getElementById('customDateForm');
            customDateForm.style.display = customDateForm.style.display === 'none' ? 'block' : 'none';
        });
        
        // Print report
        document.getElementById('printReport').addEventListener('click', function() {
            window.print();
        });
        
        // Export CSV
        <?php if ($report_type === 'transactions'): ?>
        document.getElementById('exportCSV').addEventListener('click', function() {
            exportTableToCSV('transactions-report.csv');
        });
        
        function exportTableToCSV(filename) {
            const csv = [];
            const rows = document.querySelectorAll('table tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/(\s\s)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                
                csv.push(row.join(','));
            }
            
            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            const downloadLink = document.createElement('a');
            
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        <?php endif; ?>
    });
    </script>
</body>
</html>
