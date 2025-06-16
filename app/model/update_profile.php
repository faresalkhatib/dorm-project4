
<?php 
require_once 'protection.php'; // Add this line to every PHP file
?>
<?php   
require_once __DIR__ . '/../model/connection.php';  

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password= $_POST['password'];
    $phone_number = $_POST['phone_number'];
     $Cliq = $_POST['Cliq'];
      $hash=password_hash($password,PASSWORD_DEFAULT);
    // Create a new instance of the connection class
    $db = new connection();
    $conn = $db->get_mysql();

    // Prepare the SQL statement to update the user profile
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, phone_number = ?, Cliq = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $email, $hash, $phone_number, $Cliq, $user_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        header("Location: /view/landlordpanel.php");
        exit();
    } else {
        echo "Error updating profile: {$stmt->error}";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "No user ID provided.";
}   