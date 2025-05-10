<?php
class BlogController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getLatestPosts($limit = 6) {
        $query = "SELECT b.*, t.name as author_name 
                  FROM blog_posts b
                  LEFT JOIN team_members t ON b.author_id = t.id
                  WHERE b.is_published = 1
                  ORDER BY b.published_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>