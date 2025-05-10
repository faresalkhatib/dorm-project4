<?php
require_once __DIR__ . '/../model/connection.php';
require_once __DIR__ . '/../model/dorm.php';

class DormController {
    private $db;

    public function __construct() {
        $conn = new connection();
        $this->db = $conn->get_mysql();
    }

    public function getDormDetails($dormId) {
        $dormModel = new Dorm();
        $dorm = $dormModel->getDormById($dormId);
        if (!$dorm) {
            return null;
        }

        // Get owner
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $dorm['owner_id']);
        $stmt->execute();
        $ownerResult = $stmt->get_result();
        $owner = $ownerResult->fetch_assoc();

        return [
            'dorm' => $dorm,
            'owner' => $owner
        ];
    }

    public function getStudentInfo($userId) {
        $stmt = $this->db->prepare("SELECT name, phone_number FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        
    }
    
    
}
