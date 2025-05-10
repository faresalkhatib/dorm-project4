<?php
session_start();
require_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = (new connection())->get_mysql();
    $price=$_POST['price1'];
    $dorm_id = $_POST['dorm_id'];
    $owner_id = $_POST['owner_id'];
    $dorm_name = $_POST['dorm_name'];
    $std_name = $_POST['std_name'];
    $std_number = $_POST['std_number'];
    $user_id = $_SESSION['user_id'];

    // Insert all fields
    $stmt = $conn->prepare("INSERT INTO bookings 
        (user_id, dorm_id, owner_id, dorm_name, std_name, std_number, status,price) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending',?)");

    if (!$stmt) {
        echo "Error in prepare: " . $conn->error;
        exit;
    }

    $stmt->bind_param("iiisssi", $user_id, $dorm_id, $owner_id, $dorm_name, $std_name, $std_number,$price);

    if ($stmt->execute()) {
        echo "Booking request sent successfully!";
    } else {
        echo "Failed to send request: " . $stmt->error;
    }
}
?>
