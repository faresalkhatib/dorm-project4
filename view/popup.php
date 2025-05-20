<?php
session_start();
require_once __DIR__ . '/../app/controller/DormController.php';

if (!isset($_GET['id'])) {
    header("Location: listings.php");
    exit();
}

$dormId = intval($_GET['id']);
$controller = new DormController();
$data = $controller->getDormDetails($dormId);

if (!$data) {
    die("Dorm not found.");
}

$dorm = $data['dorm'];
$owner = $data['owner'];
$amenities = !empty($dorm['amenities']) ? explode(',', $dorm['amenities']) : [];

// Get student info if logged in
// Get student info if logged in
$student = [];
if (isset($_SESSION['user_id'])) {
    $student = $controller->getStudentInfo($_SESSION['user_id']);

    // Set these variables to prevent undefined variable errors
    $std_name = $student['name'] ?? '';
    $std_number = $student['phone_number'] ?? '';
    $std_email = $student['email'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($dorm['title']) ?> | Premium Student Housing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/popup.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
      
    </style>
</head>

<body>
    <div class="container">
        <div class="dorm-header">
            <h1 class="dorm-title"><?= htmlspecialchars($dorm['title']) ?></h1>
            <p class="location"><i class="fas fa-map-marker-alt"></i> Amman, Jordan</p>
            <div class="price-tag">$<?= htmlspecialchars($dorm['price']) ?> / month</div>
        </div>

        <div class="gallery">
            <?php
            $images = explode(',', $dorm['image']);
            if (!empty($images) && !empty($images[0])) {
                foreach ($images as $index => $image) {
                    $image = trim($image);
                    if (!empty($image)) {
                        echo '<div class="gallery-item" onclick="openLightbox(' . $index . ')">';
                        echo '<img src="/../app/model/uploads/' . htmlspecialchars($image) . '" alt="Dorm Photo">';
                        echo '</div>';
                    }
                }
            } else {
                echo '<div class="no-images">No images available for this dorm</div>';
            }
            ?>
        </div>

        <div class="details-grid">
            <div class="details-card">
                  <h2 class="section-title">Room Type</h2>
                  <p class="description"><?= htmlspecialchars($dorm['room_type']) ?></p>
                <h2 class="section-title">Description</h2>
                <p class="description"><?= htmlspecialchars($dorm['description']) ?></p>

                <?php if (!empty($amenities)): ?>
                    <h2 class="section-title">Amenities</h2>
                    <div class="amenities-grid">
                        <?php foreach ($amenities as $amenity): ?>
                            <div class="amenity-item">
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlspecialchars(trim($amenity)) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="owner-card">
                <h2 class="section-title">Owner Details</h2>
                <div class="owner-info">
                    <p><i class="fas fa-user"></i> <?= htmlspecialchars($owner['name']) ?></p>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($owner['email']) ?></p>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($owner['phone_number'] ?? 'Not provided') ?>
                    </p>
                </div>

                <?php if (!empty($student)): ?>
                    <div class="booking-form">
                        <h3 class="section-title">Book This Dorm</h3>
                        <form id="bookingForm">
                            <input type="hidden" name="dorm_id" value="<?= htmlspecialchars($dorm['id']) ?>">
                            <input type="hidden" name="owner_id" value="<?= htmlspecialchars($owner['id']) ?>">
                            <input type="hidden" name="dorm_name" value="<?= htmlspecialchars($dorm['title']) ?>">
                            <input type="hidden" name="std_name" value="<?= htmlspecialchars($std_name ?? '') ?>">
                            <input type="hidden" name="std_number" value="<?= htmlspecialchars($std_number ?? '') ?>">
                            <input type="hidden" name="std_email" value="<?= htmlspecialchars($std_email ?? '') ?>">
                            <input type="hidden" name="price1" value="<?= htmlspecialchars($dorm['price']) ?>">
                            <input type="hidden" name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                            <?php if (!empty($std_name) && !empty($std_number)): ?>
                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                    <p><strong>Your Name:</strong> <?= htmlspecialchars($std_name) ?></p>
                                    <p><strong>Your Phone:</strong> <?= htmlspecialchars($std_number) ?></p>
                                </div>
                                <button type="submit" class="btn" id="submitBtn">
                                    <span id="btnText">Send Booking Request</span>
                                    <div id="spinner" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </button>
                            <?php else: ?>
                                <p class="text-warning">Please complete your profile information before booking.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lightbox HTML -->
    <div id="lightbox" class="lightbox">
        <span class="close-lightbox" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="Enlarged dorm photo">
        </div>
        <div class="lightbox-nav">
            <button onclick="changeImage(-1)"><i class="fas fa-chevron-left"></i></button>
            <button onclick="changeImage(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <script>
        document.getElementById("bookingForm")?.addEventListener("submit", async function (e) {
            e.preventDefault();

            if (!confirm("Are you sure you want to send a booking request?")) {
                return;
            }

            const submitBtn = document.getElementById("submitBtn");
            const btnText = document.getElementById("btnText");
            const spinner = document.getElementById("spinner");

            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = "none";
            spinner.style.display = "block";

            try {
                const formData = new FormData(this);
                const response = await fetch("../app/model/send_request.php", {
                    method: "POST",
                    body: formData
                });

                const data = await response.text();

                if (data.includes("success")) {
                    alert("Booking request sent successfully!");
                    window.location.reload();
                } else {
                    alert("Error: " + data);
                }
            } catch (error) {
                alert("Network error: " + error);
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                btnText.style.display = "block";
                spinner.style.display = "none";
            }
        });
    </script>
</body>

</html>