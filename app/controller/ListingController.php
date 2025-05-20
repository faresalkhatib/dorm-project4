<?php

require_once __DIR__ . '/../model/connection.php';

class ListingController
{
    private $conn;

    public function __construct()
    {
        $db = new connection();
        $this->conn = $db->get_mysql();
    }

    public function getAllListings($filters = [])
{
    try {
        $baseSql = "SELECT * FROM listings WHERE 1=1";
        $conditions = [];
        $params = [];
        $types = '';

        // Price Range Filter
        if (!empty($filters['price_range'])) {
            $priceParts = explode('-', $filters['price_range']);
            if (count($priceParts) === 2) {
                $baseSql .= " AND price >= ? AND price <= ?";
                $params[] = $priceParts[0];
                $params[] = $priceParts[1];
                $types .= 'ii'; // integer type
            }
        }

        // Room Type Filter
        if (!empty($filters['room_type'])) {
            $baseSql .= " AND room_type = ?";
            $params[] = $filters['room_type'];
            $types .= 's'; // string type
        }

        // Amenities Filter
        if (!empty($filters['amenities']) && is_array($filters['amenities'])) {
            foreach ($filters['amenities'] as $amenity) {
                if (!empty($amenity)) {
                    $baseSql .= " AND FIND_IN_SET(?, amenities)";
                    $params[] = $amenity;
                    $types .= 's'; // string type
                }
            }
        }

        // Prepare and execute the query
        $stmt = $this->conn->prepare($baseSql);
        
        if (!$stmt) {
            throw new Exception("SQL prepare error: " . $this->conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();

    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
}