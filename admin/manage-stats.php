<?php
require_once 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        redirect('manage-stats.php');
    }
    
    // Handle stat deletion
    if (isset($_POST['delete_stat'])) {
        $stat_id = (int)$_POST['stat_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM statistics WHERE id = ?");
            $stmt->execute([$stat_id]);
            
            $_SESSION['success_message'] = "Statistic deleted successfully!";
            redirect('manage-stats.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error deleting statistic: " . $e->getMessage();
            redirect('manage-stats.php');
        }
    }
    
    // Handle stat creation/update
    if (isset($_POST['save_stat'])) {
        $stat_id = isset($_POST['stat_id']) ? (int)$_POST['stat_id'] : null;
        $name = sanitize_input($_POST['name']);
        $value = (int)$_POST['value'];
        $icon = sanitize_input($_POST['icon']);
        $display_order = (int)$_POST['display_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            if ($stat_id) {
                // Update existing stat
                $stmt = $conn->prepare("UPDATE statistics SET 
                                      name = ?, value = ?, icon = ?, 
                                      display_order = ?, is_active = ?
                                      WHERE id = ?");
                $stmt->execute([$name, $value, $icon, $display_order, $is_active, $stat_id]);
                
                $message = "Statistic updated successfully!";
            } else {
                // Create new stat
                $stmt = $conn->prepare("INSERT INTO statistics 
                                      (name, value, icon, display_order, is_active)
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $value, $icon, $display_order, $is_active]);
                
                $message = "Statistic created successfully!";
            }
            
            $_SESSION['success_message'] = $message;
            redirect('manage-stats.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error saving statistic: " . $e->getMessage();
            redirect('manage-stats.php');
        }
    }
}

// Get all statistics
$stmt = $conn->query("SELECT * FROM statistics ORDER BY display_order");
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stat for editing
$edit_stat = null;
if (isset($_GET['edit'])) {
    $stat_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM statistics WHERE id = ?");
    $stmt->execute([$stat_id]);
    $edit_stat = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="row">
    <div class="col-md-<?= $edit_stat ? '6' : '12'; ?>">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Statistics</h5>
                <a href="manage-stats.php?add" class="btn btn-sm btn-primary">Add New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                                <th>Icon</th>
                                <th>Order</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $stat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stat['name']); ?></td>
                                    <td><?= $stat['value']; ?></td>
                                    <td><?= htmlspecialchars($stat['icon']); ?></td>
                                    <td><?= $stat['display_order']; ?></td>
                                    <td><?= $stat['is_active'] ? 'Yes' : 'No'; ?></td>
                                    <td>
                                        <a href="manage-stats.php?edit=<?= $stat['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                            <input type="hidden" name="stat_id" value="<?= $stat['id']; ?>">
                                            <button type="submit" name="delete_stat" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this statistic?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($edit_stat || isset($_GET['add'])): ?>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $edit_stat ? 'Edit Statistic' : 'Add New Statistic'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <?php if ($edit_stat): ?>
                        <input type="hidden" name="stat_id" value="<?= $edit_stat['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $edit_stat ? htmlspecialchars($edit_stat['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="number" class="form-control" id="value" name="value" 
                               value="<?= $edit_stat ? $edit_stat['value'] : 0; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" class="form-control" id="icon" name="icon" 
                               value="<?= $edit_stat ? htmlspecialchars($edit_stat['icon']) : ''; ?>" 
                               placeholder="Example: fas fa-users">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="display_order" name="display_order" 
                                   value="<?= $edit_stat ? $edit_stat['display_order'] : 0; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= $edit_stat && $edit_stat['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="save_stat" class="btn btn-primary">Save Statistic</button>
                    <a href="manage-stats.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>