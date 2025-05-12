<?php
session_start();
$pageTitle = "Statistics Management";
include '../db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $label = $conn->real_escape_string($_POST['label']);
        $value = (int)$_POST['value'];
        
        $conn->query("INSERT INTO stats (label, value, created_date, updated_date) 
                     VALUES ('$label', $value, NOW(), NOW())");
        
        $_SESSION['success'] = "Statistic added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $label = $conn->real_escape_string($_POST['label']);
        $value = (int)$_POST['value'];
        
        $conn->query("UPDATE stats SET label='$label', value=$value, updated_date=NOW() WHERE id=$id");
        
        $_SESSION['success'] = "Statistic updated successfully!";
    }
    
    header("Location: manage_stats.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM stats WHERE id=$id");
    
    $_SESSION['success'] = "Statistic deleted successfully!";
    header("Location: manage_stats.php");
    exit();
}
include 'header.php';
$stats = $conn->query("SELECT * FROM stats ORDER BY id");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Statistics</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatModal">
            <i class="fas fa-plus me-1"></i> Add Statistic
        </button>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Value</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($stat = $stats->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($stat['label']) ?></td>
                        <td><?= number_format($stat['value']) ?></td>
                        <td><?= date('M d, Y', strtotime($stat['updated_date'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#editStatModal<?= $stat['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="manage_stats.php?delete=<?= $stat['id'] ?>" 
                               class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Edit Stat Modal -->
                    <div class="modal fade" id="editStatModal<?= $stat['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Statistic</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $stat['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="label" 
                                                   value="<?= htmlspecialchars($stat['label']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Value</label>
                                            <input type="number" class="form-control" name="value" 
                                                   value="<?= $stat['value'] ?>" required>
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

<!-- Add Stat Modal -->
<div class="modal fade" id="addStatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Statistic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" name="label" required>
                        <div class="invalid-feedback">Please provide a label.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Value</label>
                        <input type="number" class="form-control" name="value" required>
                        <div class="invalid-feedback">Please provide a value.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Statistic</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>