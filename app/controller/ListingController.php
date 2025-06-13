<?php

require_once __DIR__ . '/../model/connection.php';
// ------------------ this controller is manly used in browse page  ------------------

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

            // Title Search Filter
            if (!empty($filters['title'])) {
                $baseSql .= " AND title LIKE ?";
                $params[] = "%" . $filters['title'] . "%";
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

            // Order by creation date (newest first)
            $baseSql .= " ORDER BY created_at DESC";

            // Add pagination (LIMIT and OFFSET)
            if (isset($filters['limit']) && isset($filters['offset'])) {
                $baseSql .= " LIMIT ? OFFSET ?";
                $params[] = (int)$filters['limit'];
                $params[] = (int)$filters['offset'];
                $types .= 'ii'; // integer types
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

    // New method to get total count of listings (for pagination)
    public function getListingsCount($filters = [])
    {
        try {
            $baseSql = "SELECT COUNT(*) as total FROM listings WHERE 1=1";
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

            // Title Search Filter
            if (!empty($filters['title'])) {
                $baseSql .= " AND title LIKE ?";
                $params[] = "%" . $filters['title'] . "%";
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
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return (int)$row['total'];

        } catch (Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // Optional: Method to get listings for a specific page (convenience method)
    public function getListingsForPage($page = 1, $itemsPerPage = 12, $filters = [])
    {
        $offset = ($page - 1) * $itemsPerPage;
        
        $filters['limit'] = $itemsPerPage;
        $filters['offset'] = $offset;
        
        return $this->getAllListings($filters);
    }
}
?>