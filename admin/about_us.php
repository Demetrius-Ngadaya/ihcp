<?php
session_start();
include '../db.php';

if (isset($_POST['update'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    
    $conn->query("UPDATE about SET title='$title', content='$content', updated_date=NOW() WHERE id=1");
    
    $_SESSION['success'] = "About Us content updated successfully!";
    header("Location: about_us.php");
    exit();
}
$pageTitle = "About Us Management";
include 'header.php';
$about = $conn->query("SELECT * FROM about WHERE id=1")->fetch_assoc();
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit About Us Content</h5>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?= htmlspecialchars($about['title']) ?>" required>
                <div class="invalid-feedback">Please provide a title.</div>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" 
                          required><?= htmlspecialchars($about['content']) ?></textarea>
                <div class="invalid-feedback">Please provide content.</div>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update Content</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>