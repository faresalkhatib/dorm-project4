<?php
require_once __DIR__ . "/connection.php"; // Include the connection file

$test = new connection();
$connect = $test->get_mysql();

function emailExists($connect, $email): bool {
    $query = "SELECT 1 FROM users WHERE email = ? LIMIT 1"; // Only fetch 1 row
    $stmt = $connect->prepare($query);
    
    if (!$stmt) {
        die("Error preparing query: " . $connect->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Store result to check the number of rows

    $exists = $stmt->num_rows > 0; // If rows > 0, it exists
    $stmt->close();

    return $exists;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $passwordd = $_POST['password']; // Secure password hashing
    $phone_number=$_POST['phone_number'];
    $role=$_POST['role'];
    $hash=password_hash($passwordd,PASSWORD_DEFAULT);
    // Prepare the INSERT query
    $insertQuery = $connect->prepare("INSERT INTO users (name, email, password,phone_number,role) VALUES (?, ?, ?,?,?)");
    if (!$insertQuery) {
        die("Error preparing query: " . $connect->error);
    }
   
    $emailToCheck = $email; // Replace with dynamic input

    if (emailExists($connect, $emailToCheck)) {
        echo "Email already exists!";
    } else {
          $insertQuery->bind_param("sssss", $name, $email, $hash,$phone_number,$role);



    // Bind parameters
    

    // Execute the query
    if ($insertQuery->execute()) 
    {
        
        header("location: /../view/login.php");
        exit();


    } 
    else {
        echo "Error: " . $insertQuery->error;
    }

    // Close the statement
    $insertQuery->close();
}
}


// Close the connection
//$connect->close();
?>
   

      
    

