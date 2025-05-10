<?php
class TeamController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTeamMembers() {
        $query = "SELECT * FROM team_members 
                  WHERE is_active = 1
                  ORDER BY display_order";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>