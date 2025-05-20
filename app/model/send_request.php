<?php
session_start();
require_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate session and required fields
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in");
        }

        // Validate required POST data
        $required_fields = ['price1', 'dorm_id', 'owner_id', 'dorm_name', 'std_name', 'std_email', 'std_number'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                throw new Exception("Missing required field: $field");
            }
        }

        // Get database connection
        $conn = (new connection())->get_mysql();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Prepare data
        $booking_date = date('Y-m-d H:i:s'); // Current timestamp
        $price = (int)$_POST['price1']; // Ensure price is integer
        $dorm_id = (int)$_POST['dorm_id'];
        $owner_id = (int)$_POST['owner_id'];
        $dorm_name = $conn->real_escape_string($_POST['dorm_name']);
        $std_name = $conn->real_escape_string($_POST['std_name']);
        $std_email = $_POST['std_email'];
        $std_number = $conn->real_escape_string($_POST['std_number']);
        $user_id = (int)$_SESSION['user_id'];

        // Prepare and execute SQL
        $stmt = $conn->prepare("INSERT INTO bookings 
            (user_id, dorm_id, owner_id, dorm_name, std_name,std_email, std_number, status, price, booking_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");

        if (!$stmt) {
            throw new Exception("Prepare failed: {$conn->error}");
        }

        // Corrected bind_param - added 's' for booking_date (string)
        $stmt->bind_param("iiissssis", $user_id, $dorm_id, $owner_id, $dorm_name, $std_name, $std_email, $std_number, $price, $booking_date);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        echo "Booking request sent successfully!";
        
    } catch (Exception $e) {
        http_response_code(400); // Bad request
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method not allowed
    echo "Invalid request method";
}
?>