<?php
require_once 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        redirect('manage-team.php');
    }
    
    // Handle team member deletion
    if (isset($_POST['delete_member'])) {
        $member_id = (int)$_POST['member_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
            $stmt->execute([$member_id]);
            
            $_SESSION['success_message'] = "Team member deleted successfully!";
            redirect('manage-team.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error deleting team member: " . $e->getMessage();
            redirect('manage-team.php');
        }
    }
    
    // Handle team member creation/update
    if (isset($_POST['save_member'])) {
        $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : null;
        $name = sanitize_input($_POST['name']);
        $position = sanitize_input($_POST['position']);
        $bio = sanitize_input($_POST['bio']);
        $display_order = (int)$_POST['display_order'];
        
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            list($success, $result) = handle_file_upload($_FILES['image']);
            if ($success) {
                $image_path = $result;
            } else {
                $_SESSION['error_message'] = implode("<br>", $result);
                redirect('manage-team.php');
            }
        }
        
        try {
            if ($member_id) {
                // Update existing member
                if ($image_path) {
                    $stmt = $conn->prepare("UPDATE team_members SET 
                                          name = ?, position = ?, bio = ?, 
                                          image_path = ?, display_order = ?
                                          WHERE id = ?");
                    $stmt->execute([$name, $position, $bio, $image_path, $display_order, $member_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE team_members SET 
                                          name = ?, position = ?, bio = ?, 
                                          display_order = ?
                                          WHERE id = ?");
                    $stmt->execute([$name, $position, $bio, $display_order, $member_id]);
                }
                
                $message = "Team member updated successfully!";
            } else {
                // Create new member
                $stmt = $conn->prepare("INSERT INTO team_members 
                                      (name, position, bio, image_path, display_order)
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $bio, $image_path, $display_order]);
                
                $message = "Team member created successfully!";
            }
            
            $_SESSION['success_message'] = $message;
            redirect('manage-team.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error saving team member: " . $e->getMessage();
            redirect('manage-team.php');
        }
    }
}

// Get all team members
$stmt = $conn->query("SELECT * FROM team_members ORDER BY display_order, name");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get member for editing
$edit_member = null;
if (isset($_GET['edit'])) {
    $member_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->execute([$member_id]);
    $edit_member = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="row">
    <div class="col-md-<?= $edit_member ? '6' : '12'; ?>">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Team Members</h5>
                <a href="manage-team.php?add" class="btn btn-sm btn-primary">Add New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['name']); ?></td>
                                    <td><?= htmlspecialchars($member['position']); ?></td>
                                    <td><?= $member['display_order']; ?></td>
                                    <td>
                                        <a href="manage-team.php?edit=<?= $member['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                            <input type="hidden" name="member_id" value="<?= $member['id']; ?>">
                                            <button type="submit" name="delete_member" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this team member?')">Delete</button>
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
    
    <?php if ($edit_member || isset($_GET['add'])): ?>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $edit_member ? 'Edit Team Member' : 'Add New Team Member'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <?php if ($edit_member): ?>
                        <input type="hidden" name="member_id" value="<?= $edit_member['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $edit_member ? htmlspecialchars($edit_member['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" 
                               value="<?= $edit_member ? htmlspecialchars($edit_member['position']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"><?= 
                            $edit_member ? htmlspecialchars($edit_member['bio']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" 
                               value="<?= $edit_member ? $edit_member['display_order'] : 0; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($edit_member && $edit_member['image_path']): ?>
                            <div class="mt-2">
                                <img src="../<?= htmlspecialchars($edit_member['image_path']); ?>" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="save_member" class="btn btn-primary">Save Member</button>
                    <a href="manage-team.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>