<?php 
require_once 'protection.php'; // Add this line to every PHP file
?>
<?php 
session_start();
require_once __DIR__ . '/../model/connection.php';  

if (isset($_POST['user_id'])) {
    
    $messege = $_POST['message'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $user_id = $_POST['user_id'] ?? null;

    if ($user_id === null) {
        echo "User ID is required.";
        exit();
    }

    // Create a new instance of the connection class
    $db = new connection();
    $conn = $db->get_mysql();

    // Prepare the SQL statement to insert the complaint
    $stmt = $conn->prepare("insert into complaints (message, subject, user_id) values (?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement: {$conn->error}";
        exit();
    }
    $stmt->bind_param("ssi", $messege, $subject, $user_id);
    

    // Execute the statement and check for success
    if ($stmt->execute()) {
     
        header("Location: /view/browse.php?message=Complaint submitted successfully.");
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

?>


