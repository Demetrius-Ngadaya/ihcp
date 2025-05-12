<?php
session_start();

// Include database connection
include '../db.php';

// Check admin status before any output
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$uploadDir = '../uploads/videos/';
$pageTitle = "Video Management";

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add']) && isset($_FILES['video'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        
        // Handle file upload
        $videoName = basename($_FILES['video']['name']);
        $targetFile = $uploadDir . $videoName;
        $videoFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if file is a video
        $validExtensions = ['mp4', 'webm', 'ogg', 'mov'];
        
        if (!in_array($videoFileType, $validExtensions)) {
            $_SESSION['error'] = "Sorry, only MP4, WEBM, OGG & MOV files are allowed.";
        } elseif ($_FILES['video']['size'] > 100000000) { // 100MB limit
            $_SESSION['error'] = "Sorry, your file is too large (max 100MB).";
        } else {
            if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
                $conn->query("INSERT INTO videos (title, description, video_path, created_date, updated_date) 
                             VALUES ('$title', '$description', '$videoName', NOW(), NOW())");
                $_SESSION['success'] = "Video added successfully!";
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            }
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        
        // Handle video update if new video is uploaded
        if (!empty($_FILES['video']['name'])) {
            $videoName = basename($_FILES['video']['name']);
            $targetFile = $uploadDir . $videoName;
            $videoFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            $validExtensions = ['mp4', 'webm', 'ogg', 'mov'];
            
            if (!in_array($videoFileType, $validExtensions)) {
                $_SESSION['error'] = "Sorry, only MP4, WEBM, OGG & MOV files are allowed.";
            } elseif ($_FILES['video']['size'] > 100000000) { // 100MB limit
                $_SESSION['error'] = "Sorry, your file is too large (max 100MB).";
            } else {
                if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
                    // Delete old video
                    $oldVideo = $conn->query("SELECT video_path FROM videos WHERE id=$id")->fetch_assoc()['video_path'];
                    if (file_exists($uploadDir . $oldVideo)) {
                        unlink($uploadDir . $oldVideo);
                    }
                    
                    $conn->query("UPDATE videos SET title='$title', description='$description', video_path='$videoName', 
                                 updated_date=NOW() WHERE id=$id");
                    $_SESSION['success'] = "Video updated successfully!";
                } else {
                    $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $conn->query("UPDATE videos SET title='$title', description='$description', updated_date=NOW() WHERE id=$id");
            $_SESSION['success'] = "Video updated successfully!";
        }
    }
    
    header("Location: manage_video.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Delete video file
    $video = $conn->query("SELECT video_path FROM videos WHERE id=$id")->fetch_assoc()['video_path'];
    if (file_exists($uploadDir . $video)) {
        unlink($uploadDir . $video);
    }
    
    $conn->query("DELETE FROM videos WHERE id=$id");
    
    $_SESSION['success'] = "Video deleted successfully!";
    header("Location: manage_video.php");
    exit();
}

// Now include the header after all possible redirects
include 'header.php';

// Fetch videos
$videos = $conn->query("SELECT * FROM videos ORDER BY created_date DESC");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Videos</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVideoModal">
            <i class="fas fa-plus me-1"></i> Add Video
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
            <?php while($video = $videos->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <video class="card-img-top w-100" controls preload="metadata" style="height: 300px; background: #000;">
                            <source src="../uploads/videos/<?= htmlspecialchars($video['video_path']) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <h5 class="card-title mt-3"><?= htmlspecialchars($video['title']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($video['description']) ?></p>
                        <small class="text-muted">Uploaded: <?= date('M d, Y', strtotime($video['created_date'])) ?></small>
                    </div>
                    <div class="card-footer bg-white">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                data-bs-target="#editVideoModal<?= $video['id'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="manage_video.php?delete=<?= $video['id'] ?>" 
                           class="btn btn-sm btn-danger delete-btn">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
                
                <!-- Edit Video Modal -->
                <div class="modal fade" id="editVideoModal<?= $video['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Video</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $video['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" 
                                               value="<?= htmlspecialchars($video['title']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($video['description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Video</label>
                                        <input type="file" class="form-control" name="video">
                                        <small class="text-muted">Current video: <?= htmlspecialchars($video['video_path']) ?></small>
                                    </div>
                                    <div class="text-center">
                                        <video class="img-thumbnail" controls style="max-width: 100%;">
                                            <source src="../uploads/videos/<?= htmlspecialchars($video['video_path']) ?>" type="video/mp4">
                                        </video>
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

<!-- Add Video Modal -->
<div class="modal fade" id="addVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                        <div class="invalid-feedback">Please provide a title.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Video</label>
                        <input type="file" class="form-control" name="video" required>
                        <div class="invalid-feedback">Please select a video file.</div>
                        <small class="text-muted">Supported formats: MP4, WEBM, OGG, MOV (max 100MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Video</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Confirmation for delete
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this video?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'footer.php'; ?>