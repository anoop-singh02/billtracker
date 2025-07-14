<?php
/**
 * Bills Page
 * ------------------------------------------------------------
 * - Starts session and checks authentication
 * - Handles add, edit, delete actions for bills
 * - Fetches and filters bills for display
 */
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

$userId = $_SESSION['user_id'];
$isAdminUser = isAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $dueDate = $_POST['due_date'];
            $category = $_POST['category'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("INSERT INTO bills (title, amount, due_date, category, status, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsssi", $title, $amount, $dueDate, $category, $status, $userId);
            
            if ($stmt->execute()) {
                header('Location: index.php?page=bills&success=1');
            } else {
                header('Location: index.php?page=bills&error=1');
            }
            exit();
            break;

        case 'edit':
            $billId = $_POST['bill_id'];
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $dueDate = $_POST['due_date'];
            $category = $_POST['category'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE bills SET title = ?, amount = ?, due_date = ?, category = ?, status = ? WHERE id = ? AND (user_id = ? OR ? = 1)");
            $stmt->bind_param("sdsssiii", $title, $amount, $dueDate, $category, $status, $billId, $userId, $isAdminUser);
            
            if ($stmt->execute()) {
                header('Location: index.php?page=bills&success=1');
            } else {
                header('Location: index.php?page=bills&error=1');
            }
            exit();
            break;

        case 'delete':
            $billId = $_POST['bill_id'];
            $stmt = $conn->prepare("DELETE FROM bills WHERE id = ? AND (user_id = ? OR ? = 1)");
            $stmt->bind_param("iii", $billId, $userId, $isAdminUser);
            
            if ($stmt->execute()) {
                header('Location: index.php?page=bills&success=1');
            } else {
                header('Location: index.php?page=bills&error=1');
            }
            exit();
            break;
    }
}

// Get bills with filters
$where = $isAdminUser ? "1" : "user_id = ?";
$params = [];
$types = "";

if (!$isAdminUser) {
    $params[] = $userId;
    $types .= "i";
}

if (isset($_GET['category']) && $_GET['category'] !== 'All') {
    $where .= " AND category = ?";
    $params[] = $_GET['category'];
    $types .= "s";
}

if (isset($_GET['status']) && $_GET['status'] !== 'All') {
    $where .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (isset($_GET['search']) && $_GET['search']) {
    $search = "%" . $_GET['search'] . "%";
    $where .= " AND (title LIKE ? OR category LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

$query = "SELECT * FROM bills WHERE $where ORDER BY due_date ASC";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$bills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories for filter
$categories = array_unique(array_column($bills, 'category'));
sort($categories);
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operation completed successfully
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            An error occurred. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">Bills</h1>
            <p class="text-muted">Manage your bills and payments</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBillModal">
            <i data-lucide="plus" class="me-2"></i> Add New Bill
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="bills">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i data-lucide="search"></i>
                        </span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search bills..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="All">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" 
                                    <?= (($_GET['category'] ?? '') === $category) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="All">All Statuses</option>
                        <option value="paid" <?= (($_GET['status'] ?? '') === 'paid') ? 'selected' : '' ?>>Paid</option>
                        <option value="unpaid" <?= (($_GET['status'] ?? '') === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i data-lucide="filter" class="me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bills List -->
    <?php if (empty($bills)): ?>
        <div class="text-center py-5">
            <i data-lucide="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
            <h3 class="h4">No Bills Found</h3>
            <p class="text-muted">Start by adding your first bill or try different filters.</p>
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBillModal">
                <i data-lucide="plus" class="me-2"></i> Add New Bill
            </button>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($bills as $bill): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($bill['title']) ?></h5>
                                <span class="status-badge <?= $bill['status'] ?>">
                                    <?= ucfirst($bill['status']) ?>
                                </span>
                            </div>
                            <p class="card-text">
                                <strong>Amount:</strong> $<?= number_format($bill['amount'], 2) ?><br>
                                <strong>Due Date:</strong> <?= date('M d, Y', strtotime($bill['due_date'])) ?><br>
                                <strong>Category:</strong> <?= htmlspecialchars($bill['category']) ?>
                            </p>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="toggleBillStatus(<?= $bill['id'] ?>)">
                                    <i data-lucide="<?= $bill['status'] === 'paid' ? 'x' : 'check' ?>"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick='editBill(<?= json_encode($bill) ?>)'>
                                    <i data-lucide="edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteBill(<?= $bill['id'] ?>)">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Bill Modal -->
<div class="modal fade" id="addBillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="billForm" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="bill_id" value="">
                <input type="hidden" name="page" value="bills">
                
                <div class="modal-header">
                    <h5 class="modal-title">Add New Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Housing">Housing</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="unpaid" checked>
                                <label class="form-check-label">Unpaid</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="paid">
                                <label class="form-check-label">Paid</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleBillStatus(billId) {
    fetch('api/toggle_bill_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ billId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function editBill(bill) {
    const form = document.getElementById('billForm');
    form.elements['action'].value = 'edit';
    form.elements['bill_id'].value = bill.id;
    form.elements['title'].value = bill.title;
    form.elements['amount'].value = bill.amount;
    form.elements['due_date'].value = bill.due_date;
    form.elements['category'].value = bill.category;
    
    Array.from(form.elements['status']).forEach(radio => {
        radio.checked = (radio.value === bill.status);
    });
    
    document.querySelector('#addBillModal .modal-title').textContent = 'Edit Bill';
    new bootstrap.Modal(document.getElementById('addBillModal')).show();
}

function deleteBill(billId) {
    if (confirm('Are you sure you want to delete this bill?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="bill_id" value="${billId}">
            <input type="hidden" name="page" value="bills">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form when adding new bill
document.querySelector('[data-bs-target="#addBillModal"]').addEventListener('click', function() {
    const form = document.getElementById('billForm');
    form.reset();
    form.elements['action'].value = 'add';
    form.elements['bill_id'].value = '';
    document.querySelector('#addBillModal .modal-title').textContent = 'Add New Bill';
});
</script>

<?php include 'includes/footer.php'; ?>
