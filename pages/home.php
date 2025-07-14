<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 mb-4">Manage Your Bills Efficiently</h1>
            <p class="lead mb-4">Track, organize, and never miss a payment deadline with our intuitive bill management system.</p>
            <?php if (!isLoggedIn()): ?>
                <div class="d-flex gap-3">
                    <a href="?page=login" class="btn btn-primary btn-lg">Login</a>
                    <a href="?page=register" class="btn btn-outline-primary btn-lg">Register</a>
                </div>
            <?php else: ?>
                <a href="?page=dashboard" class="btn btn-primary btn-lg">Go to Dashboard</a>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <img src="https://images.pexels.com/photos/3943716/pexels-photo-3943716.jpeg" 
                 alt="Bill Management" 
                 class="img-fluid rounded shadow-lg">
        </div>
    </div>

    <!-- Features -->
    <div class="row mt-5 pt-5">
        <div class="col-12 text-center mb-5">
            <h2 class="h1">Why Choose BillTracker?</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4">
                <div class="text-center mb-3">
                    <i data-lucide="layout-dashboard" class="text-primary" style="width: 48px; height: 48px;"></i>
                </div>
                <h3 class="h4 text-center">Organized Dashboard</h3>
                <p class="text-muted">Get a clear overview of your upcoming bills, payment history, and spending patterns.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4">
                <div class="text-center mb-3">
                    <i data-lucide="credit-card" class="text-success" style="width: 48px; height: 48px;"></i>
                </div>
                <h3 class="h4 text-center">Bill Categorization</h3>
                <p class="text-muted">Categorize your bills for better expense tracking and financial planning.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4">
                <div class="text-center mb-3">
                    <i data-lucide="bell" class="text-warning" style="width: 48px; height: 48px;"></i>
                </div>
                <h3 class="h4 text-center">Due Date Tracking</h3>
                <p class="text-muted">Never miss a payment with clear due date indicators and status updates.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
