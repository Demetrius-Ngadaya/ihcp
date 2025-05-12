<?php
session_start();
include '../db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $conn->query("INSERT INTO projects (title, description, status, created_date, updated_date) 
                     VALUES ('$title', '$description', '$status', NOW(), NOW())");
        
        $_SESSION['success'] = "Project added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $conn->query("UPDATE projects SET title='$title', description='$description', 
                     status='$status', updated_date=NOW() WHERE id=$id");
        
        $_SESSION['success'] = "Project updated successfully!";
    }
    
    header("Location: manage_projects.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM projects WHERE id=$id");
    
    $_SESSION['success'] = "Project deleted successfully!";
    header("Location: manage_projects.php");
    exit();
}

$pageTitle = "Projects Management";
include 'header.php';
$projects = $conn->query("SELECT * FROM projects ORDER BY status, updated_date DESC");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Projects</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            <i class="fas fa-plus me-1"></i> Add Project
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
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($project = $projects->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($project['title']) ?></td>
                        <td><?= htmlspecialchars(substr($project['description'], 0, 50)) ?>...</td>
                        <td>
                            <span class="badge bg-<?= 
                                $project['status'] == 'current' ? 'primary' : 
                                ($project['status'] == 'upcoming' ? 'warning' : 'success')
                            ?>">
                                <?= ucfirst($project['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($project['updated_date'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#editProjectModal<?= $project['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="manage_projects.php?delete=<?= $project['id'] ?>" 
                               class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Edit Project Modal -->
                    <div class="modal fade" id="editProjectModal<?= $project['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Project</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title" 
                                                   value="<?= htmlspecialchars($project['title']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="4" 
                                                      required><?= htmlspecialchars($project['description']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="current" <?= $project['status'] == 'current' ? 'selected' : '' ?>>Current</option>
                                                <option value="upcoming" <?= $project['status'] == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                                                <option value="completed" <?= $project['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
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

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                        <div class="invalid-feedback">Please provide a title.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                        <div class="invalid-feedback">Please provide a description.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="current">Current</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="completed">Completed</option>
                        </select>
                        <div class="invalid-feedback">Please select a status.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>