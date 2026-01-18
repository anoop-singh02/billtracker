<?php
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

// Get user's bills
$userId = $_SESSION['user_id'];
$isAdminUser = isAdmin();

$query = "SELECT * FROM bills WHERE " . ($isAdminUser ? "1" : "user_id = ?") . " ORDER BY due_date ASC";
$stmt = $conn->prepare($query);
if (!$isAdminUser) {
    $stmt->execute([$userId]);
} else {
    $stmt->execute();
}
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$totalBills = count($bills);
$totalAmount = array_sum(array_column($bills, 'amount'));
$paidBills = array_filter($bills, fn($bill) => $bill['status'] === 'paid');
$unpaidBills = array_filter($bills, fn($bill) => $bill['status'] === 'unpaid');
$totalPaid = array_sum(array_column($paidBills, 'amount'));
$totalUnpaid = array_sum(array_column($unpaidBills, 'amount'));

// Get overdue bills
$today = date('Y-m-d');
$overdueBills = array_filter($unpaidBills, fn($bill) => $bill['due_date'] < $today);

// Get upcoming bills (next 7 days)
$nextWeek = date('Y-m-d', strtotime('+7 days'));
$upcomingBills = array_filter(
    $unpaidBills,
    fn($bill) =>
    $bill['due_date'] >= $today && $bill['due_date'] <= $nextWeek
);

// Calculate categories
$categories = [];
foreach ($bills as $bill) {
    $cat = $bill['category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = 0;
    }
    $categories[$cat] += $bill['amount'];
}
arsort($categories);
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0">Dashboard</h1>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-primary bg-opacity-10 text-primary">
                    <i data-lucide="credit-card"></i>
                </div>
                <h3 class="h5 mb-2">Total Bills</h3>
                <p class="h3 mb-0"><?php echo $totalBills; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-success bg-opacity-10 text-success">
                    <i data-lucide="dollar-sign"></i>
                </div>
                <h3 class="h5 mb-2">Total Amount</h3>
                <p class="h3 mb-0">$<?php echo number_format($totalAmount, 2); ?></p>
                <small class="text-success">
                    <?php echo $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100) : 0; ?>% paid
                </small>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-warning bg-opacity-10 text-warning">
                    <i data-lucide="clock"></i>
                </div>
                <h3 class="h5 mb-2">Unpaid Bills</h3>
                <p class="h3 mb-0"><?php echo count($unpaidBills); ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-danger bg-opacity-10 text-danger">
                    <i data-lucide="alert-triangle"></i>
                </div>
                <h3 class="h5 mb-2">Overdue Bills</h3>
                <p class="h3 mb-0"><?php echo count($overdueBills); ?></p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Bills -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <i data-lucide="calendar" class="me-2"></i>
                        Upcoming Bills
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingBills)): ?>
                        <div class="text-center py-4">
                            <i data-lucide="check-circle" class="text-success mb-3" style="width: 48px; height: 48px;"></i>
                            <p class="text-muted">No upcoming bills for the next 7 days!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingBills as $bill): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($bill['title']); ?></td>
                                            <td>$<?php echo number_format($bill['amount'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($bill['due_date'])); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $bill['status']; ?>">
                                                    <?php echo ucfirst($bill['status']); ?>
                                                </span>
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

        <!-- Expenses by Category -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <i data-lucide="pie-chart" class="me-2"></i>
                        Expenses by Category
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No categories data available</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category => $amount): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?php echo htmlspecialchars($category); ?></span>
                                    <span>$<?php echo number_format($amount, 2); ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: <?php echo ($amount / $totalAmount) * 100; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>