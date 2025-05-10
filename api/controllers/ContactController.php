<?php
class ContactController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createSubmission($data) {
        $query = "INSERT INTO contact_submissions 
                  (name, email, message, ip_address) 
                  VALUES (:name, :email, :message, :ip_address)";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":message", $data['message']);
        $stmt->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>