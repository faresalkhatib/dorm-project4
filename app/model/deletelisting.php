<?php 
require_once 'protection.php'; // Add this line to every PHP file
?><?php
require_once __DIR__ . '/../model/connection.php';  

if (isset($_POST['listing_id'])) {
    $listingId = $_POST['listing_id'];

    // Create a new instance of the connection class
    $db = new connection();
    $conn = $db->get_mysql();

    // Prepare the SQL statement to delete the listing
    $stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listingId);
    

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "Listing deleted successfully.";
        header("Location: /view/landlordpanel.php#Dashboard");
 
        exit();
    } else {
        echo "Error deleting listing: {$stmt->error}";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}   
else {
    echo "No listing ID provided.";
}




