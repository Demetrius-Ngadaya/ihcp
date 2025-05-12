<?php
// Start session at the absolute beginning
session_start();

// Include database connection
include '../db.php';

// Check admin status before any output
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$uploadDir = '../uploads/';
$pageTitle = "Team Management";

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add']) && isset($_FILES['image'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $role = $conn->real_escape_string($_POST['role']);
        
        // Handle file upload
        $imageName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
        } elseif ($_FILES['image']['size'] > 5000000) {
            $_SESSION['error'] = "Sorry, your file is too large (max 5MB).";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $conn->query("INSERT INTO team (name, role, image_path, created_date, updated_date) 
                             VALUES ('$name', '$role', '$imageName', NOW(), NOW())");
                $_SESSION['success'] = "Team member added successfully!";
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            }
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $name = $conn->real_escape_string($_POST['name']);
        $role = $conn->real_escape_string($_POST['role']);
        
        // Handle image update if new image is uploaded
        if (!empty($_FILES['image']['name'])) {
            $imageName = basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $imageName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                $_SESSION['error'] = "File is not an image.";
            } elseif ($_FILES['image']['size'] > 5000000) {
                $_SESSION['error'] = "Sorry, your file is too large (max 5MB).";
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    // Delete old image
                    $oldImage = $conn->query("SELECT image_path FROM team WHERE id=$id")->fetch_assoc()['image_path'];
                    if (file_exists($uploadDir . $oldImage)) {
                        unlink($uploadDir . $oldImage);
                    }
                    
                    $conn->query("UPDATE team SET name='$name', role='$role', image_path='$imageName', 
                                 updated_date=NOW() WHERE id=$id");
                    $_SESSION['success'] = "Team member updated successfully!";
                } else {
                    $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $conn->query("UPDATE team SET name='$name', role='$role', updated_date=NOW() WHERE id=$id");
            $_SESSION['success'] = "Team member updated successfully!";
        }
    }
    
    header("Location: manage_team.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Delete image file
    $image = $conn->query("SELECT image_path FROM team WHERE id=$id")->fetch_assoc()['image_path'];
    if (file_exists($uploadDir . $image)) {
        unlink($uploadDir . $image);
    }
    
    $conn->query("DELETE FROM team WHERE id=$id");
    
    $_SESSION['success'] = "Team member deleted successfully!";
    header("Location: manage_team.php");
    exit();
}
// Now include the header after all possible redirects
include 'header.php';

// Fetch team members
$team = $conn->query("SELECT * FROM team ORDER BY name");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Team Members</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
            <i class="fas fa-plus me-1"></i> Add Member
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
        
        <div class="row">
            <?php while($member = $team->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="../uploads/<?= htmlspecialchars($member['image_path']) ?>" 
                         class="card-img-top" alt="<?= htmlspecialchars($member['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($member['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($member['role']) ?></p>
                        <small class="text-muted">Updated: <?= date('M d, Y', strtotime($member['updated_date'])) ?></small>
                    </div>
                    <div class="card-footer bg-white">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                data-bs-target="#editTeamModal<?= $member['id'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="manage_team.php?delete=<?= $member['id'] ?>" 
                           class="btn btn-sm btn-danger delete-btn">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
                
                <!-- Edit Team Modal -->
                <div class="modal fade" id="editTeamModal<?= $member['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Team Member</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" 
                                               value="<?= htmlspecialchars($member['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" name="role" 
                                               value="<?= htmlspecialchars($member['role']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" name="image">
                                        <small class="text-muted">Current image: <?= htmlspecialchars($member['image_path']) ?></small>
                                    </div>
                                    <div class="text-center">
                                        <img src="../uploads/<?= htmlspecialchars($member['image_path']) ?>" 
                                             class="img-thumbnail" style="max-width: 150px;">
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
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Add Team Modal -->
<div class="modal fade" id="addTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                        <div class="invalid-feedback">Please provide a name.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" name="role" required>
                        <div class="invalid-feedback">Please provide a role.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="image" required>
                        <div class="invalid-feedback">Please select an image.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>