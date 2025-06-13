<?php
session_start();
require_once __DIR__ . '/../app/controller/ListingController.php';

$listingController = new ListingController();

// Pagination settings
$itemsPerPage = 12; // Number of listings per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get filter values from GET parameters
$priceRange = $_GET['price_range'] ?? '';
$roomType = $_GET['room_type'] ?? '';
$checkedAmenities = $_GET['amenities'] ?? [];
$title = $_GET['title'] ?? '';

// Build filters array
$filters = [
    'price_range' => $priceRange,
    'room_type' => $roomType,
    'amenities' => $checkedAmenities,
    'title' => $title,
    'limit' => $itemsPerPage,
    'offset' => $offset
];

// Get listings and total count
$listings = $listingController->getAllListings($filters);
$totalListings = $listingController->getListingsCount($filters);
$totalPages = ceil($totalListings / $itemsPerPage);

$allAmenities = [
    'WiFi' => 'WiFi',
    'Parking' => 'Parking',
    'Air Conditioning' => 'AC',
    'Laundry' => 'Laundry',
    'Cleaning' => 'Cleaning',
    'gym' => 'Gym'
];

// Function to build URL with current filters
function buildUrl($page, $currentFilters) {
    $params = array_filter([
        'page' => $page,
        'price_range' => $currentFilters['price_range'] ?? '',
        'room_type' => $currentFilters['room_type'] ?? '',
        'title' => $currentFilters['title'] ?? ''
    ]);
    
    // Add amenities
    if (!empty($currentFilters['amenities'])) {
        foreach ($currentFilters['amenities'] as $amenity) {
            $params['amenities[]'] = $amenity;
        }
    }
    
    return '?' . http_build_query($params);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: home.php'); // Redirect to the home page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Rental - Browse Listings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/browse.css">
    <style>
        /* Header Styles */
        header {
            position: relative;
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .auth-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .auth-btn {
            padding: 8px 16px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .auth-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .auth-btn.logout {
            background-color: #4E51A2;
            border-color: #4E51A2;
        }

        .auth-btn.logout:hover {
            background-color: #3b3e7a;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-right: 10px;
        }

        /* Pagination Styles */
        .results-info {
            margin: 20px 0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 40px 0;
            flex-wrap: wrap;
        }

        .pagination-btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 2px;
            text-decoration: none;
            color: #333;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 14px;
            min-width: 40px;
            text-align: center;
        }

        .pagination-btn:hover:not(.disabled):not(.active) {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #495057;
        }

        .pagination-btn.active {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .pagination-btn.disabled {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-dots {
            padding: 10px 5px;
            color: #6c757d;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-buttons {
                position: static;
                justify-content: center;
                margin-bottom: 20px;
            }
            
            header {
                padding: 20px;
            }
            
            .user-info {
                text-align: center;
                margin-bottom: 10px;
                margin-right: 0;
            }
            /* Profile Button Styles */
.auth-btn.profile {
    background-color: #5D6D7E;
    border-color: #5D6D7E;
}

.auth-btn.profile:hover {
    background-color: #4A5C6B;
    transform: translateY(-1px);
}
        }
    </style>
</head>
<body>
       <header>
        <div class="auth-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User is logged in -->
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="user-info">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                <?php endif; ?>
                <a href="profile.php" class="auth-btn profile">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
                <a href="?logout=1" class="auth-btn logout" onclick="return confirm('Are you sure you want to log out?')">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            <?php else: ?>
                <!-- User is not logged in -->
                <a href="login.php" class="auth-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
                <a href="signup.php" class="auth-btn">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
            <?php endif; ?>
        </div>
        
        <h1>Find Your Perfect Dorm</h1>
        <p>Affordable student housing near campuses</p>
    </header>

    <div class="container">
        <!-- Search and Filters -->
        <div class="search-container">
            <form method="GET" action="" id="filterForm">
                <!-- Hidden field to reset page when filtering -->
                <input type="hidden" name="page" value="1">
                
                <div class="search-bar">
                    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Search by dorm name">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </div>
                
                <div class="filters">
                    <!-- Price Range Filter -->
                    <select name="price_range" onchange="resetPageAndSubmit()">
                        <option value="">Price Range</option>
                        <option value="0-300" <?= $priceRange === '0-300' ? 'selected' : '' ?>>$0 - $300</option>
                        <option value="300-600" <?= $priceRange === '300-600' ? 'selected' : '' ?>>$300 - $600</option>
                        <option value="600-900" <?= $priceRange === '600-900' ? 'selected' : '' ?>>$600 - $900</option>
                    </select>
                    
                    <!-- Room Type Filter -->
                    <select name="room_type" onchange="resetPageAndSubmit()">
                        <option value="">Room Type</option>
                        <option value="single" <?= $roomType === 'single' ? 'selected' : '' ?>>Single</option>
                        <option value="shared" <?= $roomType === 'shared' ? 'selected' : '' ?>>Shared</option>
                        <option value="entire" <?= $roomType === 'entire' ? 'selected' : '' ?>>Entire Dorm</option>
                    </select>
                    
                    <!-- Amenities Filter -->
                    <div class="dropdown-checkbox">
                        <button type="button" class="dropdown-toggle" onclick="toggleDropdown()">
                            <?= count($checkedAmenities) > 0 ? 'Amenities ('.count($checkedAmenities).') ▼' : 'Select Amenities ▼' ?>
                        </button>
                        <div class="dropdown-content" id="amenitiesDropdown">
                            <?php foreach ($allAmenities as $value => $label): ?>
                                <label>
                                    <input type="checkbox" name="amenities[]" 
                                           value="<?= htmlspecialchars($value) ?>" 
                                           <?= in_array($value, $checkedAmenities) ? 'checked' : '' ?>
                                           onchange="updateAmenitiesCount()">
                                    <?= htmlspecialchars($label) ?>
                                </label>
                            <?php endforeach; ?>
                            <button type="submit" class="apply-btn" onclick="closeDropdown(); resetPage();">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <?php if ($totalListings > 0): ?>
        <div class="results-info">
            <p>Showing <?= ($offset + 1) ?> - <?= min($offset + $itemsPerPage, $totalListings) ?> of <?= $totalListings ?> results</p>
        </div>
        <?php endif; ?>

        <!-- Dorm Listings Grid -->
        <div class="listings-container">
            <?php if ($listings && $listings->num_rows > 0): ?>
                <?php while ($row = $listings->fetch_assoc()): ?>
                    <?php 
                    $images = explode(',', $row['image']);
                    $firstImage = $images[0] ?? 'default.jpg';
                    ?>
                    <div class="dorm-card">
                        <img src="/../app/model/uploads/<?= htmlspecialchars($firstImage) ?>" alt="Dorm Image">
                        <div class="card-details">
                            <h3><?= htmlspecialchars($row['title']) ?></h3>
                            <div class="price-rating">
                                <span class="price">$<?= htmlspecialchars($row['price']) ?>/month</span>
                            </div>
                            <a href="popup.php?id=<?= $row['id'] ?>" class="view-btn">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No listings found matching your criteria.</p>
                    <a href="?" class="view-btn">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <!-- Previous Button -->
            <?php if ($currentPage > 1): ?>
                <a href="<?= buildUrl($currentPage - 1, $filters) ?>" class="pagination-btn">Previous</a>
            <?php else: ?>
                <span class="pagination-btn disabled">Previous</span>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            // Show first page if we're not close to it
            if ($startPage > 1): ?>
                <a href="<?= buildUrl(1, $filters) ?>" class="pagination-btn">1</a>
                <?php if ($startPage > 2): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Page number buttons -->
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="pagination-btn active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= buildUrl($i, $filters) ?>" class="pagination-btn"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Show last page if we're not close to it -->
            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="pagination-dots">...</span>
                <?php endif; ?>
                <a href="<?= buildUrl($totalPages, $filters) ?>" class="pagination-btn"><?= $totalPages ?></a>
            <?php endif; ?>

            <!-- Next Button -->
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= buildUrl($currentPage + 1, $filters) ?>" class="pagination-btn">Next</a>
            <?php else: ?>
                <span class="pagination-btn disabled">Next</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Call-to-Action -->
        <div class="cta">
            <h2>Have a dorm to list?</h2>
            <p>Join our platform and connect with thousands of students</p>
            <button class="cta-btn">List Your Dorm</button>

            <!-- Complaint Button and Form -->
            <div id="complaintSection">
                <button type="button" class="btn" id="complaintBtn">
                    <span id="btnText">Complaint</span>
                    <div id="spinner" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>
               <form id="complaintForm" style="display:none; margin-top: 15px;" method="POST" action="../app/model/complaints.php">
                    <input type="text" name="user_id" value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>" hidden>
                    <input type="text" name="subject" placeholder="Subject" required style="width:100%;margin-bottom:8px;">
                    <textarea name="message" placeholder="Describe your complaint..." required style="width:100%;height:80px;margin-bottom:8px;"></textarea>
                    <button type="submit" class="btn">Submit Complaint</button>
                </form>
             
            </div>
            <script>
            document.getElementById('complaintBtn').addEventListener('click', function() {
                var form = document.getElementById('complaintForm');
                form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            });
            </script>
        </div>
    </div>

    <script>
        // Reset page to 1 when filters change
        function resetPage() {
            document.querySelector('input[name="page"]').value = 1;
        }
        
        function resetPageAndSubmit() {
            resetPage();
            document.getElementById('filterForm').submit();
        }

        // Toggle dropdown visibility
        function toggleDropdown() {
            document.getElementById("amenitiesDropdown").classList.toggle("show");
        }
        
        // Close dropdown when Apply button is clicked
        function closeDropdown() {
            document.getElementById("amenitiesDropdown").classList.remove("show");
        }
        
        // Update the button text with selected count
        function updateAmenitiesCount() {
            const checkboxes = document.querySelectorAll('#amenitiesDropdown input[type="checkbox"]:checked');
            const toggleBtn = document.querySelector('.dropdown-toggle');
            toggleBtn.textContent = checkboxes.length > 0 
                ? `Amenities (${checkboxes.length}) ▼` 
                : 'Select Amenities ▼';
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById("amenitiesDropdown");
            const toggleBtn = document.querySelector('.dropdown-toggle');
            
            if (!dropdown.contains(event.target) && !toggleBtn.contains(event.target)) {
                dropdown.classList.remove("show");
            }
        });
    </script>
</body>
</html>