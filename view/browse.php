<?php 
require_once __DIR__ . '/../app/controller/ListingController.php';

$listingController = new ListingController();
$listings = $listingController->getAllListings();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dorm Rental - Browse Listings</title>
  <link rel="stylesheet" href="css/browse.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Header -->
  <header>
    <h1>Find Your Perfect Dorm</h1>
    <p>Affordable student housing near campuses</p>
  </header>

  <!-- Search and Filters -->
  <div class="search-container">
    <div class="search-bar">
      <input type="text" placeholder="Search by location (e.g., 'Near UCLA')">
      <button><i class="fas fa-search"></i></button>
    </div>
    <div class="filters">
      <select>
        <option>Price Range</option>
        <option>$0 - $300</option>
        <option>$300 - $600</option>
        <option>$600+</option>
      </select>
      <select>
        <option>Room Type</option>
        <option>Single</option>
        <option>Shared</option>
        <option>Entire Dorm</option>
      </select>
      <select>
        <option>Amenities</option>
        <option>Wi-Fi</option>
        <option>Laundry</option>
        <option>Kitchen</option>
      </select>
    </div>
  </div>

  <!-- Dorm Listings Grid -->
  <div class="listings-container">
<?php
if ($listings && $listings->num_rows > 0) {
    while ($row = $listings->fetch_assoc()) {
        $images = explode(',', $row['image']);
        $firstImage = $images[0];
?>
    <div class="dorm-card">
        <img src="/../app/model/uploads/<?php echo htmlspecialchars($firstImage); ?>" alt="Dorm Image">
        <div class="card-details">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <div class="price-rating">
                <span class="price">$<?php echo htmlspecialchars($row['price']); ?>/month</span>
                <span class="rating">★★★★★ (18)</span>
            </div>
            <a class="view-btn" href="popup.php?id=<?php echo $row['id']; ?>">View Details</a>
        </div>
    </div>
<?php
    }
} else {
    echo "<p>No listings found.</p>";
}
?>
</div> <!-- <- this is listings-container -->

  <div class="pagination">
    <button>Previous</button>
    <button class="active">1</button>
    <button>2</button>
    <button>3</button>
    <button>Next</button>
  </div>

  <!-- Call-to-Action -->
  <div class="cta">
    <p>Have a dorm to list?</p>
    <button class="cta-btn">List Your Dorm</button>
  </div>

 

  </div>
</body>
</html>
