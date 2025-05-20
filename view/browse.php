<?php
require_once __DIR__ . '/../app/controller/ListingController.php';

$listingController = new ListingController();

// Get filter values from GET parameters
$priceRange = $_GET['price_range'] ?? '';
$roomType = $_GET['room_type'] ?? '';
$checkedAmenities = $_GET['amenities'] ?? [];

// Pass filters to controller
$listings = $listingController->getAllListings([
    'price_range' => $priceRange,
    'room_type' => $roomType,
    'amenities' => $checkedAmenities
]);

$allAmenities = [
    'WiFi' => 'WiFi',
    'Parking' => 'Parking',
    'Air Conditioning' => 'AC',
    'Laundry' => 'Laundry',
    'Cleaning' => 'Cleaning',
    'gym' => 'Gym'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Rental - Browse Listings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/browse.css">
</head>
<body>
    <header>
        <h1>Find Your Perfect Dorm</h1>
        <p>Affordable student housing near campuses</p>
    </header>

    <div class="container">
        <!-- Search and Filters -->
        <div class="search-container">
            <?php
            $searchTitle = $_GET['title'] ?? '';
            $searchBarHtml = '
                <div class="search-bar">
                    <input type="text" name="title" value="' . htmlspecialchars($searchTitle) . '" placeholder="Search by dorm name">
                    <button><i class="fas fa-search"></i> Search</button>
                </div>
            ';
            echo $searchBarHtml;
            ?>
            <div class="filters">
                <form method="GET" action="" id="filterForm">
                    <!-- Price Range Filter -->
                    <select name="price_range" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Price Range</option>
                        <option value="0-300" <?= $priceRange === '0-300' ? 'selected' : '' ?>>$0 - $300</option>
                        <option value="300-600" <?= $priceRange === '300-600' ? 'selected' : '' ?>>$300 - $600</option>
                        <option value="600-900" <?= $priceRange === '600-900' ? 'selected' : '' ?>>$600 - $900</option>
                    </select>
                    
                    <!-- Room Type Filter -->
                    <select name="room_type" onchange="document.getElementById('filterForm').submit()">
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
                            <button type="submit" class="apply-btn" onclick="closeDropdown()">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rest of your HTML remains the same... -->
    </div>


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
        <div class="pagination">
            <button>Previous</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Next</button>
        </div>

        <!-- Call-to-Action -->
        <div class="cta">
            <h2>Have a dorm to list?</h2>
            <p>Join our platform and connect with thousands of students</p>
            <button class="cta-btn">List Your Dorm</button>
        </div>
    </div>

    <script>
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