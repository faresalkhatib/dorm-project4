<?php 
require_once 'protection.php'; // Add this line to every PHP file
?><?php
require_once __DIR__ . "/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$bookingId || !in_array($status, ['approved', 'rejected'])) {
        http_response_code(400);
        echo "Invalid request";
        exit;
    }

    $conn = (new connection())->get_mysql();
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $bookingId);

    if ($stmt->execute()) {
        echo "Status updated to $status";
    } else {
        http_response_code(500);
        echo "Failed to update";
    }

    $stmt->close();
    $conn->close();
}
