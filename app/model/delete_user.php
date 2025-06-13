<?php
require_once __DIR__ . '/../model/connection.php';  

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Create a new instance of the connection class
    $db = new connection();
    $conn = $db->get_mysql();

    // Prepare the SQL statement to delete the listing
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "user deleted successfully.";
        header("Location: /view/admin_panel.php#Dashboard");
 
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




