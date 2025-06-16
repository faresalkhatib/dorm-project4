<?php 
require_once 'protection.php'; // Add this line to every PHP file
?><?php
session_start();
require_once __DIR__ . "/connection.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$test = new connection();
$connect = $test->get_mysql();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $owner_id = $_SESSION['user_id'];
    $amenities = $_POST['amenities'] ?? [];
$amenitiesString = implode(',', $amenities); // Combine selected amenities into a comma-separated string


    // Validate required fields
    if (empty($title) || empty($price) || empty($description)) {
        die("Error: All fields are required.");
    }

    // Debug: check the $_FILES array
    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';

    // Check if files are uploaded and if $_FILES['file'] is an array
    if (isset($_FILES['file']) && is_array($_FILES['file']['name']) && !empty($_FILES['file']['name'][0])) {
        $fileCount = count($_FILES['file']['name']); // Number of files
        $uploadedFiles = []; // Array to store the names of successfully uploaded files

        for ($i = 0; $i < $fileCount; $i++) {
            $filename = $_FILES['file']['name'][$i];
            $filetmpname = $_FILES['file']['tmp_name'][$i];
            $filesize = $_FILES['file']['size'][$i];
            $fileError = $_FILES['file']['error'][$i];
            $fileType = $_FILES['file']['type'][$i];

            // Get file extension
            $fileExt = explode('.', $filename);
            $fileAcutualExt = strtolower(end($fileExt));
            $allow = ['jpg', 'jpeg', 'png', 'pdf'];

            if (in_array($fileAcutualExt, $allow)) {
                if ($fileError === 0) {
                    $filenamenew = uniqid('', true) . "." . $fileAcutualExt;
                    $filedestination = "uploads/{$filenamenew}";
                    if (move_uploaded_file($filetmpname, $filedestination)) {
                        // Add the filename to the uploaded files array
                        $uploadedFiles[] = $filenamenew;
                    } else {
                        echo "Error uploading file $filename.";
                    }
                } else {
                    echo "There was an error uploading your file $filename.";
                }
            } else {
                echo "You cannot upload files of this type ($filename).";
            }
        }

        // If files were successfully uploaded, proceed with saving listing
        if (!empty($uploadedFiles)) {
            // Here you might want to store multiple filenames in the database or link them to the listing
            $images = implode(',', $uploadedFiles); // Combine the filenames for storage

            // Insert listing into database
            $stmt = $connect->prepare("INSERT INTO listings (title, price, description, amenities, image, owner_id,room_type) VALUES (?, ?, ?, ?, ?, ?,?)");
$stmt->bind_param("sdsssis", $title, $price, $description, $amenitiesString, $images, $owner_id, $type);


            if ($stmt->execute()) {
                header("Location: /../view/landlordpanel.php");
                exit();
            } else {
                echo "Error adding listing: {$stmt->error}";
            }

            $stmt->close();
        } else {
            echo "No files uploaded.";
        }
    } else {
        echo "No files uploaded.";
    }
}
