<?php
session_start();
$pageTitle = "Dashboard";
include 'header.php';
include '../db.php';

// Get counts for dashboard cards
$projectsCount = $conn->query("SELECT COUNT(*) FROM projects")->fetch_row()[0];
$blogsCount = $conn->query("SELECT COUNT(*) FROM blogs")->fetch_row()[0];
$teamCount = $conn->query("SELECT COUNT(*) FROM team")->fetch_row()[0];
$messagesCount = $conn->query("SELECT COUNT(*) FROM messages")->fetch_row()[0];
$picturesCount = $conn->query("SELECT COUNT(*) FROM pictures")->fetch_row()[0];
$videosCount = $conn->query("SELECT COUNT(*) FROM videos")->fetch_row()[0];
?>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Projects</h5>
                        <h2 class="mb-0"><?= $projectsCount ?></h2>
                    </div>
                    <i class="fas fa-project-diagram fa-3x"></i>
                </div>
                <a href="manage_projects.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Blog Posts</h5>
                        <h2 class="mb-0"><?= $blogsCount ?></h2>
                    </div>
                    <i class="fas fa-blog fa-3x"></i>
                </div>
                <a href="manage_blogs.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Team Members</h5>
                        <h2 class="mb-0"><?= $teamCount ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <a href="manage_team.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Messages</h5>
                        <h2 class="mb-0"><?= $messagesCount ?></h2>
                    </div>
                    <i class="fas fa-envelope fa-3x"></i>
                </div>
                <a href="messages.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Pictures</h5>
                        <h2 class="mb-0"><?= $picturesCount ?></h2>
                    </div>
                    <i class="fas fa-image fa-3x"></i>
                </div>
                <a href="manage_pictures.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Videos</h5>
                        <h2 class="mb-0"><?= $videosCount ?></h2>
                    </div>
                    <i class="fas fa-video fa-3x"></i>
                </div>
                <a href="manage_video.php" class="text-white stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Projects</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recentProjects = $conn->query("SELECT * FROM projects ORDER BY updated_date DESC LIMIT 5");
                            while($project = $recentProjects->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($project['title']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $project['status'] == 'current' ? 'primary' : 
                                        ($project['status'] == 'upcoming' ? 'warning' : 'success')
                                    ?>">
                                        <?= ucfirst($project['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($project['updated_date'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Messages</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recentMessages = $conn->query("SELECT * FROM messages ORDER BY created_date DESC LIMIT 5");
                            while($message = $recentMessages->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($message['name']) ?></td>
                                <td><?= htmlspecialchars($message['email']) ?></td>
                                <td><?= date('M d, Y', strtotime($message['created_date'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>