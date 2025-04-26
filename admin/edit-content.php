<?php
require_once 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        redirect('edit-content.php');
    }
    
    try {
        $conn->beginTransaction();
        
        foreach ($_POST['sections'] as $section_id => $content) {
            $stmt = $conn->prepare("UPDATE sections SET section_title = :title, section_content = :content WHERE id = :id");
            $stmt->bindParam(':title', sanitize_input($content['title']));
            $stmt->bindParam(':content', sanitize_input($content['content']));
            $stmt->bindParam(':id', $section_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        $conn->commit();
        $_SESSION['success_message'] = "Content updated successfully!";
        redirect('edit-content.php');
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Error updating content: " . $e->getMessage();
        redirect('edit-content.php');
    }
}

// Get all sections
$stmt = $conn->query("SELECT s.id, s.section_name, s.section_title, s.section_content, p.page_name 
                      FROM sections s JOIN pages p ON s.page_id = p.id 
                      ORDER BY p.page_name, s.display_order");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Edit Website Content</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
            
            <?php foreach ($sections as $section): ?>
                <div class="mb-4">
                    <h6><?= ucfirst(str_replace('-', ' ', $section['section_name'])); ?> (<?= $section['page_name']; ?>)</h6>
                    <div class="mb-3">
                        <label for="title_<?= $section['id']; ?>" class="form-label">Section Title</label>
                        <input type="text" class="form-control" id="title_<?= $section['id']; ?>" 
                               name="sections[<?= $section['id']; ?>][title]" 
                               value="<?= htmlspecialchars($section['section_title']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="content_<?= $section['id']; ?>" class="form-label">Section Content</label>
                        <textarea class="form-control" id="content_<?= $section['id']; ?>" 
                                  name="sections[<?= $section['id']; ?>][content]" 
                                  rows="5"><?= htmlspecialchars($section['section_content']); ?></textarea>
                    </div>
                </div>
                <hr>
            <?php endforeach; ?>
            
            <button type="submit" name="update_content" class="btn btn-primary">Update Content</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>