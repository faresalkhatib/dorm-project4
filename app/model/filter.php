<?php 
require_once 'protection.php'; // Add this line to every PHP file
?><?php

require_once __DIR__ . '/../model/connection.php';

class ListingController
{
    private $conn;

    public function __construct()
    {
        $db = new connection();
        $this->conn = $db->get_mysql();
    }

    public function getFilteredListings()
    {
        $sql = "SELECT * FROM listings";
        $params = [];
        $conditions = [];

        if (!empty($_GET['amenities'])) {
            $amenities = $_GET['amenities'];

            foreach ($amenities as $amenity) {
                $safeAmenity = $this->conn->real_escape_string($amenity);
                $conditions[] = "amenities LIKE '%$safeAmenity%'";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->get_result();
        } else {
             $sql = "SELECT * FROM listings";
            $query = $this->conn->prepare($sql);
            $query->execute();
            return $query->get_result();
        }

        // Prepare and execute the query
        
    }

   
}
