<?php
require_once __DIR__ . '/connection.php';

class Dorm {
    private $connect;

    public function __construct() {
        $db = new connection();
        $this->connect = $db->get_mysql();
        
        if (!$this->connect) {
            die("Database connection failed");
        }
    }

    public function getDormById($id) {
        // Convert to integer
        $id = (int)$id;
        
        $stmt = $this->connect->prepare("SELECT * FROM listings WHERE id = ?");
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function sendBookingRequest($dorm_id, $user_id, $message) {
        $dorm_id = (int)$dorm_id;
        $user_id = (int)$user_id;
        
        $stmt = $this->connect->prepare("INSERT INTO bookings (dorm_id, user_id, message) VALUES (?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("iis", $dorm_id, $user_id, $message);
        return $stmt->execute();
    }
}