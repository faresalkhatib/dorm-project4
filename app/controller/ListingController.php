<?php

// /app/controller/ListingController.php
require_once __DIR__ . '/../model/connection.php';

class ListingController {
    private $conn;

    public function __construct() {
        $db = new connection();
        $this->conn = $db->get_mysql();
    }

    public function getAllListings() {
        $sql = $this->conn->prepare("SELECT * FROM listings");
        $sql->execute();
        return $sql->get_result();
    }
}
