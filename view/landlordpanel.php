<?php
session_start();
require_once __DIR__ . '/../app/controller/bookingscontroller.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if the user is a landlord

if ($_SESSION["role"] !== "landlord") {
   echo "Access denied. You do not have permission to view this page.";
   header("Location: /view/login.php?error=Access denied. You do not have permission to view this page.");
    exit();
}
$bookingscontroller = new Bookingscontroler();

$user_info=$bookingscontroller->Getuserinfo($_SESSION["user_id"]);
if (!$user_info || $user_info->num_rows === 0) {
    echo "User not found.";
    exit();
}
$user_info = $user_info->fetch_assoc();

// Get existing data
$data = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "pending");
$data1 = $bookingscontroller->listings1($_SESSION["user_id"]);
$bookingStats = $bookingscontroller->getBookingStats($_SESSION["user_id"]);

// Get stats for the cards - using separate calls for each status
$pendingBookings = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "pending");
$approvedBookings = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "approved");
$totalListings = $bookingscontroller->listings1($_SESSION["user_id"]);

// Count the results
$pendingCount = 0;
$approvedCount = 0;
$listingsCount = 0;

// Count pending bookings
if ($pendingBookings && $pendingBookings->num_rows > 0) {
    $pendingCount = $pendingBookings->num_rows;
    $pendingBookings->data_seek(0); // Reset pointer
}

// Count approved bookings  
if ($approvedBookings && $approvedBookings->num_rows > 0) {
    $approvedCount = $approvedBookings->num_rows;
    $approvedBookings->data_seek(0); // Reset pointer
}

// Count total listings
if ($totalListings && $totalListings->num_rows > 0) {
    $listingsCount = $totalListings->num_rows;
    $totalListings->data_seek(0); // Reset pointer
}

// Reset the main data pointers for later use
if ($data) $data->data_seek(0);
if ($data1) $data1->data_seek(0);

$labels = [];
$data2 = [];
if ($bookingStats && $bookingStats->num_rows > 0) {
    while ($row = $bookingStats->fetch_assoc()) {
        $labels[] = $row['booking_day'];
        $data2[] = $row['booking_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="css/landlordpanel.css">
            <link rel="stylesheet" href="css/profile.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<style>
    .logout-btn {
        background-color: #dc3545;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
        width: 90%;
        margin-left: 5%;
        font-size: 14px;
        transition: background-color 0.3s;
    }
    
    .logout-btn:hover {
        background-color: #c82333;
    }
    
    .logout-btn i {
        margin-right: 8px;
    }
</style>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Landlord Panel</h2>
            <ul>
                <li><a href="#dashboard" class="active">🏠 Dashboard</a></li>
                <li><a href="#listings"> list new dorm</a></li>
                <li><a href="#bookings"> Booking Requests</a></li>
                <li><a href="#settings"> Settings</a></li>
            </ul>
            <!-- Logout Button -->
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </div>

        <!-- Main Content -->
        <div class="main">
            <section id="dashboard">
                <h1>Dashboard</h1>
                <div>
                    <canvas id="myChart"></canvas>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const ctx = document.getElementById('myChart').getContext('2d');

                        const bookingChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($labels); ?>,
                                datasets: [{
                                    label: 'Bookings per Day',
                                    data: <?php echo json_encode($data2); ?>,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Number of Bookings'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Date'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>

                <!-- Updated stats cards with dynamic data and debugging -->
                <div class="stats">
                    <div class="stat-box">
                        Total Listings: <span><?php echo $listingsCount; ?></span>
                        <?php if ($listingsCount == 0) echo "<small style='color: red;'>(No listings found)</small>"; ?>
                    </div>
                    <div class="stat-box">
                         Booking requsits : <span><?php echo $pendingCount; ?></span>
                        <?php if ($pendingCount == 0) echo "<small style='color: red;'>(No pending bookings)</small>"; ?>
                    </div>
                 
                </div>

                <!--
                
                
                
                
                
                   <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;">
                    <h4>Debug Info:</h4>
                    <p>User ID: <?php echo $_SESSION["user_id"]; ?></p>
                    <p>Pending Query Result: <?php echo $pendingBookings ? "Success" : "Failed"; ?></p>
                    <p>Approved Query Result: <?php echo $approvedBookings ? "Success" : "Failed"; ?></p>
                    <p>Listings Query Result: <?php echo $totalListings ? "Success" : "Failed"; ?></p>
                </div>
                Debug information (remove this after testing) -->
             

                <!-- Rest of your existing code... -->
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>amenities</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    if ($data1 && $data1->num_rows >= 0) {
                        while ($row = $data1->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td>
                                    <?php
                                    $amenities = explode(',', $row['amenities']);
                                    foreach ($amenities as $amenity) {
                                        echo htmlspecialchars($amenity) . "<br>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button class="open-button" onclick="openForm()">Edit</button>
                                    <!-- Edit form popup -->
                                                 <div class="form-popup" id="myForm">
                                        <form action="/../app/model/Editmodel.php" class="form-container" method="post">
                                            <h1>edit dorm</h1>

                                            <label for="titel"><b>Titel</b></label>
                                            <input type="text" placeholder="Enter titel" name="title" required>
                                            <label for="price"><b>price</b></label>
                                            <input type="number" placeholder="Enter price" name="price" required>
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <label for="description"><b>description</b></label>
                                            <input type="text" placeholder="Enter description" name="description" required>
                                            <div class="form-group">
                                                <label for="amenities">Amenities:</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="Wi-Fi">
                                                    Wi-Fi</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="Air Conditioning"> Air
                                                    Conditioning</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="Laundry">
                                                    Laundry</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="Parking">
                                                    Parking</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="gym"> Gym</label><br>
                                                <label><input type="checkbox" name="amenities[]" value="Cleaning">
                                                    cleaning</label><br>
                                                <!-- Add more as needed -->
                                            </div>

                                            <button type="submit" class="btn">edti</button>
                                            <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
                                        </form>
                                    </div>
                                   
                                    <form action="/../app/model/deletelisting.php" method="post">
                                        <input type="hidden" name="listing_id" value="<?php echo $row['id'] ?>">
                                        <button type="submit" class="delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>No listings found.</td></tr>";
                    } ?>
                </table>
            </section>

            <!-- Rest of your sections... -->
            <section id="listings" class="hidden">
                <h2>Add New Listing</h2>
                <form action="/../app/model/submit_listing.php" method="POST" enctype="multipart/form-data" class="add-listing-form">
                    <div class="form-group">
                        <label for="title">Listing Title:</label>
                        <input type="text" id="title" name="title" placeholder="Enter title" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price per month:</label>
                        <input type="number" id="price" name="price" placeholder="Enter price" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" placeholder="Enter description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="amenities">Amenities:</label><br>
                        <label><input type="checkbox" name="amenities[]" value="WiFi"> WiFi</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Air Conditioning"> Air Conditioning</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Laundry"> Laundry</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label><br>
                        <label><input type="checkbox" name="amenities[]" value="gym"> Gym</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Cleaning"> Cleaning</label><br>
                        
                        <label for="type">Room Type:</label>
                        <select name="type" required>
                            <option value="">Select Room Type</option>
                            <option value="single">Single</option>
                            <option value="shared">Shared</option>
                            <option value="entire">Entire Dorm</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Upload Images:</label>
                        <input type="file" name="file[]" multiple accept="image/*" />
                    </div>
                    <button type="submit" class="submit-btn">Submit Listing</button>
                </form>
            </section>

            <section id="bookings" class="hidden">
                <h1>Booking Requests</h1>
                <table>
                    <tr>
                        <th>Listing</th>
                        <th>student number</th>
                        <th>student name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    if ($data && $data->num_rows > 0) {
                        while ($row = $data->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['dorm_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['std_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['std_name']); ?></td>
                                <td class="<?php echo htmlspecialchars($row['status']); ?> status " id="<?php echo htmlspecialchars($row['id']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </td>
                                <td>
                                    <button class="approve" data-id="<?php echo $row['id']; ?>">Approve</button>
                                    <button class="reject" data-id="<?php echo $row['id']; ?>">Reject</button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No bookings found.</td></tr>";
                    }
                    ?>
                </table>
            </section>
            <section id="settings">

                <h1>Settings</h1>

                <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user_info['name']); ?></h2>
                
            </div>
        </div>

        <div class="profile-card">
            <h3>Account Information</h3>
            <form action="/../app/model/update_profile.php" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="id"  class="form-control"value="<?php echo htmlspecialchars($_SESSION["user_id"])  ?>">
                        <label for="name">Name:</label>
                        <input type="text" id="name"  class="form-control" name="name" value="<?php echo htmlspecialchars($user_info['name']); ?>" required>          
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>  
                    </div>
                    <div class="form-group  
">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user_info['phone_number']); ?>" required>
                    </div>
                    <div class="form-group  
">
                        <label for="phone">Cliq</label>
                        <input type="text" id="phone" name="Cliq" class="form-control" value="<?php echo htmlspecialchars($user_info['Cliq']); ?>" required>
                    </div>
                    <div class="form-group      

">
                        <label for="password">New Password:</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Enter new password (optional)">
                    </div>
                    <button type="submit" class="form-control">Update Profile</button>        
                </form>

        </div>
            </section>
        </div>
    </div>

    <script>
        // Navigation script
        document.addEventListener('DOMContentLoaded', function() {
            // Initially show dashboard and hide others
            document.querySelectorAll('section').forEach(section => {
                if (section.id !== 'dashboard') {
                    section.classList.add('hidden');
                }
            });

            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    // Get the target section
                    const targetId = link.getAttribute('href').substring(1); // Remove the #
                    const targetSection = document.getElementById(targetId);
                    
                    if (targetSection) {
                        // Hide all sections
                        document.querySelectorAll('section').forEach(section => {
                            section.classList.add('hidden');
                        });
                        
                        // Show target section
                        targetSection.classList.remove('hidden');
                        
                        // Update active link
                        document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
                        link.classList.add('active');
                    }
                });
            });
        });

        // Form popup functions
        function openForm() {
            document.getElementById("myForm").style.display = "block";
        }

        function closeForm() {
            document.getElementById("myForm").style.display = "none";
        }

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'home.php';
            }
        }

        // Booking approval/rejection
        document.querySelectorAll('.approve, .reject').forEach(button => {
            button.addEventListener('click', function () {
                const bookingId = this.getAttribute('data-id');
                const status = this.classList.contains('approve') ? 'approved' : 'rejected';

                fetch('/../app/model/update_booking_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}&status=${status}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    document.getElementById(bookingId).textContent = status.charAt(0).toUpperCase() + status.slice(1);
                   
                })
                .catch(err => {
                    alert("Error: " + err);
                });
            });
        });
    </script>
</body>
</html>