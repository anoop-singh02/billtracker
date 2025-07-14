<?php
if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php?page=dashboard');
    exit();
}

// Handle user management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_role':
                $userId = $_POST['user_id'];
                $role = $_POST['role'];
                if ($userId != $_SESSION['user_id']) { // Prevent changing own role
                    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->bind_param("si", $role, $userId);
                    $stmt->execute();
                }
                break;
                
            case 'delete_user':
                $userId = $_POST['user_id'];
                if ($userId != $_SESSION['user_id']) { // Prevent deleting own account
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                }
                break;
                
            case 'add_user':
                $username = $_POST['username'];
                $password = $_POST['password'];
                $role = $_POST['role'];
                
                // Check if username exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $password_hash, $role);
                    $stmt->execute();
                }
                break;
        }
    }
}

// Get all users
$stmt = $conn->prepare("SELECT id, username, role FROM users ORDER BY username");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user statistics
$stmt = $conn->prepare("
    SELECT 
        u.id,
        COUNT(b.id) as total_bills,
        COALESCE(SUM(b.amount), 0) as total_amount,
        COUNT(CASE WHEN b.status = 'unpaid' THEN 1 END) as unpaid_bills
    FROM users u
    LEFT JOIN bills b ON u.id = b.user_id
    GROUP BY u.id
");
$stmt->execute();
$userStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$userStats = array_column($userStats, null, 'id');
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">Admin Panel</h1>
            <p class="text-muted">Manage users and system settings</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i data-lucide="user-plus" class="me-1"></i> Add New User
        </button>
    </div>

    <!-- Users List -->
    <div class="card">
        <div class="card-header bg-white">
            <h3 class="h5 mb-0">
                <i data-lucide="users" class="me-2"></i>
                User Management
            </h3>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">No users found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Total Bills</th>
                                <th>Total Amount</th>
                                <th>Unpaid Bills</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-primary">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <select class="form-select form-select-sm w-auto" 
                                                    onchange="updateUserRole(<?php echo $user['id']; ?>, this.value)">
                                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo ucfirst($user['role']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $userStats[$user['id']]['total_bills'] ?? 0; ?></td>
                                    <td>$<?php echo number_format($userStats[$user['id']]['total_amount'] ?? 0, 2); ?></td>
                                    <td><?php echo $userStats[$user['id']]['unpaid_bills'] ?? 0; ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        <?php endif; ?>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_user">
                
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <p class="mb-0">Username: <strong id="deleteUserName"></strong></p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateUserRole(userId, role) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="update_role">
        <input type="hidden" name="user_id" value="${userId}">
        <input type="hidden" name="role" value="${role}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function deleteUser(userId, username) {
    document.querySelector('#deleteUserModal [name="user_id"]').value = userId;
    document.getElementById('deleteUserName').textContent = username;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>