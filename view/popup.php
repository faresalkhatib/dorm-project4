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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($dorm['title']) ?> | Premium Student Housing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #4a6bff;
            --secondary: #f8f9fa;
            --dark: #2c3e50;
            --light: #ffffff;
            --accent: #ff6b6b;
            --gray: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dorm-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .dorm-title {
            font-size: 2.8rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .location {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .location i {
            margin-right: 0.5rem;
            color: var(--accent);
        }

        .price-tag {
            background: var(--primary);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            display: inline-block;
            font-weight: 600;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(74, 107, 255, 0.3);
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            aspect-ratio: 4/3;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2.5rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .dorm-title {
                font-size: 2.2rem;
            }
        }

        .details-card,
        .owner-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.8rem;
            margin-bottom: 1.8rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.8rem;
            font-weight: 600;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.2rem;
            margin: 2rem 0;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(74, 107, 255, 0.05);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .amenity-item:hover {
            background: rgba(74, 107, 255, 0.1);
            transform: translateY(-3px);
        }

        .amenity-item i {
            color: var(--primary);
            font-size: 1.4rem;
            min-width: 30px;
        }

        .amenity-item span {
            font-weight: 500;
        }

        .owner-info {
            margin-top: 2rem;
        }

        .owner-info p {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.1rem;
        }

        .owner-info i {
            color: var(--primary);
            font-size: 1.2rem;
            width: 30px;
            text-align: center;
        }

        .booking-form {
            margin-top: 2.5rem;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            padding: 1.2rem 2.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            width: 100%;
            box-shadow: 0 4px 15px rgba(74, 107, 255, 0.3);
        }

        .btn:hover {
            background: #3a56e0;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(74, 107, 255, 0.4);
        }

        .no-images {
            padding: 3rem;
            text-align: center;
            color: var(--gray);
            background: var(--secondary);
            border-radius: 12px;
            font-size: 1.1rem;
            grid-column: 1 / -1;
        }

        /* Lightbox Styles */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .lightbox-content {
            max-width: 90%;
            max-height: 90%;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .close-lightbox {
            position: absolute;
            top: 30px;
            right: 30px;
            color: white;
            font-size: 2.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close-lightbox:hover {
            transform: rotate(90deg);
        }

        .lightbox-nav {
            position: absolute;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            top: 50%;
            transform: translateY(-50%);
        }

        .lightbox-nav button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .lightbox-nav button:hover {
            background: var(--primary);
            transform: scale(1.1);
        }
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