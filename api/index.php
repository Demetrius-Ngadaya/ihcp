<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/Database.php';
include_once 'controllers/PagesController.php';
include_once 'controllers/ProjectsController.php';
include_once 'controllers/TeamController.php';
include_once 'controllers/BlogController.php';
include_once 'controllers/StatsController.php';
include_once 'controllers/ContactController.php';

$database = new Database();
$db = $database->getConnection();

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// API endpoints
if ($uri[1] === 'api') {
    $endpoint = $uri[2] ?? '';
    
    switch ($endpoint) {
        case 'pages':
            $pageName = $_GET['name'] ?? 'home';
            $controller = new PagesController($db);
            $content = $controller->getPageContent($pageName);
            echo json_encode($content);
            break;
            
        case 'projects':
            $category = $_GET['category'] ?? null;
            $controller = new ProjectsController($db);
            
            if ($category) {
                $projects = $controller->getProjectsByCategory($category);
            } else {
                $projects = $controller->getAllProjects();
            }
            
            echo json_encode($projects);
            break;
            
        case 'team':
            $controller = new TeamController($db);
            $team = $controller->getTeamMembers();
            echo json_encode($team);
            break;
            
        case 'blog':
            $limit = $_GET['limit'] ?? 6;
            $controller = new BlogController($db);
            $posts = $controller->getLatestPosts($limit);
            echo json_encode($posts);
            break;
            
        case 'stats':
            $controller = new StatsController($db);
            $stats = $controller->getStatistics();
            echo json_encode($stats);
            break;
            
        case 'contact':
            if ($requestMethod === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $controller = new ContactController($db);
                
                if ($controller->createSubmission($data)) {
                    http_response_code(201);
                    echo json_encode(array("message" => "Submission created."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to create submission."));
                }
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found."));
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Invalid API path."));
}
?>