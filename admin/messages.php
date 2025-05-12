<?php
session_start();
$pageTitle = "Messages Management";
include 'header.php';
include '../db.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id=$id");
    
    $_SESSION['success'] = "Message deleted successfully!";
    header("Location: messages.php");
    exit();
}

$messages = $conn->query("SELECT * FROM messages ORDER BY created_date DESC");
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Contact Messages</h5>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if ($messages->num_rows == 0): ?>
            <div class="alert alert-info">No messages found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($message = $messages->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($message['name']) ?></td>
                            <td><?= htmlspecialchars($message['email']) ?></td>
                            <td><?= htmlspecialchars(substr($message['message'], 0, 50)) ?>...</td>
                            <td><?= date('M d, Y', strtotime($message['created_date'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#viewMessageModal<?= $message['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="messages.php?delete=<?= $message['id'] ?>" 
                                   class="btn btn-sm btn-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        
                        <!-- View Message Modal -->
                        <div class="modal fade" id="viewMessageModal<?= $message['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Message from <?= htmlspecialchars($message['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Name:</strong> <?= htmlspecialchars($message['name']) ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Email:</strong> <?= htmlspecialchars($message['email']) ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Date:</strong> <?= date('M d, Y H:i', strtotime($message['created_date'])) ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Message:</strong>
                                            <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-primary">
                                            <i class="fas fa-reply me-1"></i> Reply
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>