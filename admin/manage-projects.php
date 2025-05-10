<?php
require_once 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        redirect('manage-projects.php');
    }
    
    // Handle project deletion
    if (isset($_POST['delete_project'])) {
        $project_id = (int)$_POST['project_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            
            $_SESSION['success_message'] = "Project deleted successfully!";
            redirect('manage-projects.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error deleting project: " . $e->getMessage();
            redirect('manage-projects.php');
        }
    }
    
    // Handle project creation/update
    if (isset($_POST['save_project'])) {
        $project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : null;
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['description']);
        $category = sanitize_input($_POST['category']);
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)$_POST['display_order'];
        
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            list($success, $result) = handle_file_upload($_FILES['image']);
            if ($success) {
                $image_path = $result;
            } else {
                $_SESSION['error_message'] = implode("<br>", $result);
                redirect('manage-projects.php');
            }
        }
        
        try {
            if ($project_id) {
                // Update existing project
                if ($image_path) {
                    $stmt = $conn->prepare("UPDATE projects SET 
                                          title = ?, description = ?, category = ?, 
                                          image_path = ?, start_date = ?, end_date = ?, 
                                          is_featured = ?, display_order = ?
                                          WHERE id = ?");
                    $stmt->execute([$title, $description, $category, $image_path, 
                                   $start_date, $end_date, $is_featured, $display_order, $project_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE projects SET 
                                          title = ?, description = ?, category = ?, 
                                          start_date = ?, end_date = ?, 
                                          is_featured = ?, display_order = ?
                                          WHERE id = ?");
                    $stmt->execute([$title, $description, $category, 
                                   $start_date, $end_date, $is_featured, $display_order, $project_id]);
                }
                
                $message = "Project updated successfully!";
            } else {
                // Create new project
                $stmt = $conn->prepare("INSERT INTO projects 
                                      (title, description, category, image_path, 
                                       start_date, end_date, is_featured, display_order)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category, $image_path, 
                               $start_date, $end_date, $is_featured, $display_order]);
                
                $message = "Project created successfully!";
            }
            
            $_SESSION['success_message'] = $message;
            redirect('manage-projects.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error saving project: " . $e->getMessage();
            redirect('manage-projects.php');
        }
    }
}

// Get all projects
$stmt = $conn->query("SELECT * FROM projects ORDER BY display_order, title");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get project for editing
$edit_project = null;
if (isset($_GET['edit'])) {
    $project_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $edit_project = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="row">
    <div class="col-md-<?= $edit_project ? '6' : '12'; ?>">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Projects List</h5>
                <a href="manage-projects.php?add" class="btn btn-sm btn-primary">Add New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Featured</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?= htmlspecialchars($project['title']); ?></td>
                                    <td><?= ucfirst($project['category']); ?></td>
                                    <td><?= $project['is_featured'] ? 'Yes' : 'No'; ?></td>
                                    <td><?= $project['display_order']; ?></td>
                                    <td>
                                        <a href="manage-projects.php?edit=<?= $project['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                            <input type="hidden" name="project_id" value="<?= $project['id']; ?>">
                                            <button type="submit" name="delete_project" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this project?')">Delete</button>
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
    
    <?php if ($edit_project || isset($_GET['add'])): ?>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $edit_project ? 'Edit Project' : 'Add New Project'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <?php if ($edit_project): ?>
                        <input type="hidden" name="project_id" value="<?= $edit_project['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= 
                            $edit_project ? htmlspecialchars($edit_project['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="current" <?= $edit_project && $edit_project['category'] === 'current' ? 'selected' : ''; ?>>Current</option>
                                <option value="upcoming" <?= $edit_project && $edit_project['category'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                <option value="completed" <?= $edit_project && $edit_project['category'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="display_order" name="display_order" 
                                   value="<?= $edit_project ? $edit_project['display_order'] : 0; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= $edit_project && $edit_project['start_date'] ? $edit_project['start_date'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= $edit_project && $edit_project['end_date'] ? $edit_project['end_date'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   <?= $edit_project && $edit_project['is_featured'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">Featured Project</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Project Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($edit_project && $edit_project['image_path']): ?>
                            <div class="mt-2">
                                <img src="../<?= htmlspecialchars($edit_project['image_path']); ?>" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="save_project" class="btn btn-primary">Save Project</button>
                    <a href="manage-projects.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>