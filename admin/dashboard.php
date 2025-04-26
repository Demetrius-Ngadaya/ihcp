<?php
require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Projects</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM projects");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-projects.php" class="text-white">View all projects</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Team Members</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM team_members");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-team.php" class="text-white">View all team members</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Blog Posts</h5>
                <?php
                $stmt = $conn->query("SELECT COUNT(*) FROM blog_posts");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count; ?></h2>
                <a href="manage-blog.php" class="text-white">View all posts</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Recent Activity</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= date('Y-m-d H:i'); ?></td>
                        <td>Admin logged in</td>
                    </tr>
                    <!-- You can add more activity logs here or fetch from a log table -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>