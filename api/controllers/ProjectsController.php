<?php
class ProjectsController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProjectsByCategory($category) {
        $query = "SELECT * FROM projects 
                  WHERE category = :category AND is_active = 1
                  ORDER BY display_order, is_featured DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProjects() {
        $query = "SELECT * FROM projects WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>