<?php
session_start();
include '../db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $category = $conn->real_escape_string($_POST['category']);
        
        $conn->query("INSERT INTO blogs (title, content, category, created_date, updated_date) 
                     VALUES ('$title', '$content', '$category', NOW(), NOW())");
        
        $_SESSION['success'] = "Blog post added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $category = $conn->real_escape_string($_POST['category']);
        
        $conn->query("UPDATE blogs SET title='$title', content='$content', 
                     category='$category', updated_date=NOW() WHERE id=$id");
        
        $_SESSION['success'] = "Blog post updated successfully!";
    }
    
    header("Location: manage_blogs.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM blogs WHERE id=$id");
    
    $_SESSION['success'] = "Blog post deleted successfully!";
    header("Location: manage_blogs.php");
    exit();
}

$pageTitle = "Blog Management";
include 'header.php';
$blogs = $conn->query("SELECT * FROM blogs ORDER BY updated_date DESC");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Blog Posts</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
            <i class="fas fa-plus me-1"></i> Add Post
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
                        <th>Category</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($blog = $blogs->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($blog['title']) ?></td>
                        <td>
                            <span class="badge bg-<?= $blog['category'] == 'primary' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($blog['category']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($blog['updated_date'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#editBlogModal<?= $blog['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="manage_blogs.php?delete=<?= $blog['id'] ?>" 
                               class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Edit Blog Modal -->
                    <div class="modal fade" id="editBlogModal<?= $blog['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Blog Post</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $blog['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title" 
                                                   value="<?= htmlspecialchars($blog['title']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Content</label>
                                            <textarea class="form-control" name="content" rows="8" 
                                                      required><?= htmlspecialchars($blog['content']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category" required>
                                                <option value="primary" <?= $blog['category'] == 'primary' ? 'selected' : '' ?>>Primary</option>
                                                <option value="secondary" <?= $blog['category'] == 'secondary' ? 'selected' : '' ?>>Secondary</option>
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

<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Blog Post</h5>
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
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="8" required></textarea>
                        <div class="invalid-feedback">Please provide content.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="primary">Primary</option>
                            <option value="secondary">Secondary</option>
                        </select>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>