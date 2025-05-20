<?php
require_once __DIR__ . '/../model/connection.php';

$test = new connection();
$connect = $test->get_mysql();

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $listingid = $_POST["id"];
        $title = $_POST["title"];  // Fixed from "titel" to "title"
        $price = $_POST["price"];
        $description = $_POST["description"];
        $amenities = $_POST['amenities'] ?? [];
        $amenitiesString = implode(',', $amenities);

        if (empty($title) || empty($price) || empty($description)) {
            die("Error: All fields are required.");
        }

        $query = $connect->prepare("UPDATE listings SET title=?, price=?, description=?, amenities=? WHERE id=?");
        $query->bind_param("sdssi", $title, $price, $description, $amenitiesString, $listingid);
        $query->execute();

        if ($query->affected_rows > 0) {
         header("Location: /view/landlordpanel.php#Dashboard");
            exit(); // Critical: Stop script execution after redirect
            
        } else {
            echo "Failed to update listing.";
        }
    } else {
        echo "Invalid request.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

