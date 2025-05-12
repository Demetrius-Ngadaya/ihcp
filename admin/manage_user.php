<?php
session_start();
include '../db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

        $conn->query("INSERT INTO admin (username, password, created_date, updated_date) 
                     VALUES ('$username', '$password', NOW(), NOW())");

        $_SESSION['success'] = "User added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $username = $conn->real_escape_string($_POST['username']);

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE admin SET username='$username', password='$password', 
                         updated_date=NOW() WHERE id=$id");
        } else {
            $conn->query("UPDATE admin SET username='$username', updated_date=NOW() WHERE id=$id");
        }

        $_SESSION['success'] = "User updated successfully!";
    }

    header("Location: manage_user.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Prevent deleting current logged-in admin
    if (isset($_SESSION['id']) && $id != $_SESSION['id']) {
        $conn->query("DELETE FROM admin WHERE id=$id");
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "You cannot delete your own account!";
    }

    header("Location: manage_user.php");
    exit();
}

$pageTitle = "User Management";
include 'header.php';
$admins = $conn->query("SELECT * FROM admin ORDER BY username");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Admin Users</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-1"></i> Add User
        </button>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($admin = $admins->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= date('M d, Y', strtotime($admin['created_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($admin['updated_date'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#editUserModal<?= $admin['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if (!isset($_SESSION['id']) || $admin['id'] != $_SESSION['id']): ?>
                            <a href="manage_user.php?delete=<?= $admin['id'] ?>" 
                               class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?= $admin['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" 
                                                   value="<?= htmlspecialchars($admin['username']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="password" 
                                                   placeholder="Leave blank to keep current password">
                                            <small class="text-muted">Password must be at least 8 characters</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                        <div class="invalid-feedback">Please provide a username.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
