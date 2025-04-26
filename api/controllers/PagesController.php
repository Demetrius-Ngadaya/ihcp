<?php
class PagesController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPageContent($page_name) {
        $query = "SELECT s.section_name, s.section_title, s.section_content 
                  FROM sections s
                  JOIN pages p ON s.page_id = p.id
                  WHERE p.page_name = :page_name AND s.is_active = 1
                  ORDER BY s.display_order";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":page_name", $page_name);
        $stmt->execute();

        $sections = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sections[$row['section_name']] = array(
                'title' => $row['section_title'],
                'content' => $row['section_content']
            );
        }

        return $sections;
    }
}
?>