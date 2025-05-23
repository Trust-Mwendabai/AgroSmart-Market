<!-- Revenue Widget for Admin Dashboard -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line text-danger me-2"></i>Revenue Streams</h5>
        <a href="reports.php?type=revenue" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-file-alt me-1"></i>View Full Report
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="text-center p-3 rounded bg-light">
                    <h6 class="text-muted mb-2">Total Revenue</h6>
                    <h2 class="display-6 fw-bold text-danger mb-0"><?php echo $revenue_widget['total']; ?></h2>
                </div>
            </div>
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Revenue Stream</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenue_widget['streams'] as $stream): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-<?php echo $stream['icon']; ?> me-2 text-muted"></i>
                                        <?php echo $stream['name']; ?>
                                    </td>
                                    <td class="text-end fw-bold"><?php echo format_price($stream['amount']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
