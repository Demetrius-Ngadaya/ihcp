<?php
require_once 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        redirect('manage-blog.php');
    }
    
    // Handle blog post deletion
    if (isset($_POST['delete_post'])) {
        $post_id = (int)$_POST['post_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);
            
            $_SESSION['success_message'] = "Blog post deleted successfully!";
            redirect('manage-blog.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error deleting blog post: " . $e->getMessage();
            redirect('manage-blog.php');
        }
    }
    
    // Handle blog post creation/update
    if (isset($_POST['save_post'])) {
        $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : null;
        $title = sanitize_input($_POST['title']);
        $content = $_POST['content']; // Don't sanitize - will be processed by CKEditor
        $excerpt = sanitize_input($_POST['excerpt']);
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $author_id = (int)$_POST['author_id'];
        
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            list($success, $result) = handle_file_upload($_FILES['image']);
            if ($success) {
                $image_path = $result;
            } else {
                $_SESSION['error_message'] = implode("<br>", $result);
                redirect('manage-blog.php');
            }
        }
        
        try {
            if ($post_id) {
                // Update existing post
                if ($image_path) {
                    $stmt = $conn->prepare("UPDATE blog_posts SET 
                                          title = ?, content = ?, excerpt = ?, 
                                          image_path = ?, is_published = ?, author_id = ?
                                          WHERE id = ?");
                    $stmt->execute([$title, $content, $excerpt, $image_path, $is_published, $author_id, $post_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE blog_posts SET 
                                          title = ?, content = ?, excerpt = ?, 
                                          is_published = ?, author_id = ?
                                          WHERE id = ?");
                    $stmt->execute([$title, $content, $excerpt, $is_published, $author_id, $post_id]);
                }
                
                $message = "Blog post updated successfully!";
            } else {
                // Create new post
                $stmt = $conn->prepare("INSERT INTO blog_posts 
                                      (title, content, excerpt, image_path, is_published, author_id)
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $content, $excerpt, $image_path, $is_published, $author_id]);
                
                $message = "Blog post created successfully!";
            }
            
            $_SESSION['success_message'] = $message;
            redirect('manage-blog.php');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error saving blog post: " . $e->getMessage();
            redirect('manage-blog.php');
        }
    }
}

// Get all blog posts
$stmt = $conn->query("SELECT b.*, t.name as author_name 
                      FROM blog_posts b
                      LEFT JOIN team_members t ON b.author_id = t.id
                      ORDER BY b.published_at DESC, b.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get team members for author dropdown
$stmt = $conn->query("SELECT id, name FROM team_members ORDER BY name");
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get post for editing
$edit_post = null;
if (isset($_GET['edit'])) {
    $post_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $edit_post = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="row">
    <div class="col-md-<?= $edit_post ? '6' : '12'; ?>">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Blog Posts</h5>
                <a href="manage-blog.php?add" class="btn btn-sm btn-primary">Add New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?= htmlspecialchars($post['title']); ?></td>
                                    <td><?= htmlspecialchars($post['author_name'] ?? 'Unknown'); ?></td>
                                    <td><?= $post['is_published'] ? 'Published' : 'Draft'; ?></td>
                                    <td><?= date('M j, Y', strtotime($post['created_at'])); ?></td>
                                    <td>
                                        <a href="manage-blog.php?edit=<?= $post['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                            <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                                            <button type="submit" name="delete_post" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this blog post?')">Delete</button>
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
    
    <?php if ($edit_post || isset($_GET['add'])): ?>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $edit_post ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <?php if ($edit_post): ?>
                        <input type="hidden" name="post_id" value="<?= $edit_post['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= $edit_post ? htmlspecialchars($edit_post['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= 
                            $edit_post ? htmlspecialchars($edit_post['excerpt']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10"><?= 
                            $edit_post ? htmlspecialchars($edit_post['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="author_id" class="form-label">Author</label>
                            <select class="form-select" id="author_id" name="author_id" required>
                                <option value="">Select Author</option>
                                <?php foreach ($authors as $author): ?>
                                    <option value="<?= $author['id']; ?>" <?= 
                                        $edit_post && $edit_post['author_id'] == $author['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($author['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-3">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                                       <?= $edit_post && $edit_post['is_published'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_published">Published</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($edit_post && $edit_post['image_path']): ?>
                            <div class="mt-2">
                                <img src="../<?= htmlspecialchars($edit_post['image_path']); ?>" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="save_post" class="btn btn-primary">Save Post</button>
                    <a href="manage-blog.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content', {
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
            { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
            { name: 'about', items: ['About'] }
        ]
    });
</script>

<?php
require_once 'includes/footer.php';
?>