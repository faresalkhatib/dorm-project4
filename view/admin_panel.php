<?php 
require_once 'protection.php'; // Add this line to every PHP file
?>

<?php
session_start();
require_once __DIR__ . '/../app/controller/bookingscontroller.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$bookingscontroller = new Bookingscontroler();

// Get existing data
$data = $bookingscontroller->Bookingsreq2();
$data1 = $bookingscontroller->listings2();


// Get stats for the cards - using separate calls for each status
$pendingBookings = $bookingscontroller->Bookingsreq2();
$approvedBookings = $bookingscontroller->Bookingsreq2();
$totalListings = $bookingscontroller->listings2();

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


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
    <link rel="stylesheet" href="css/landlordpanel.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #e1e8ed;
        }

        .header-title {
            margin: 0;
            color: #2c3e50;
            font-size: 2em;
            font-weight: 600;
        }

        .user-info-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-text {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .logout-btn {
            background: linear-gradient(#4E51A2, #3b5998);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        .logout-icon {
            font-size: 16px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-title {
                font-size: 1.5em;
            }

            .user-info-section {
                flex-direction: column;
                gap: 10px;
            }
        }

        /* Add some basic icon styles */
        .logout-btn::before {
            content: "⇥";
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>admin Panel</h2>
            <ul>
                <li><a href="#dashboard" class="active"> Dashboard</a></li>
                <li><a href="#users"> users</a></li>
                <li><a href="#bookings"> Booking Requests</a></li>
                <li><a href="#complaints"> complaints</a></li>
                <li><a href="#settings"> Settings</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <section id="dashboard">
                <div class="header-section">
                    <h1 class="header-title">Dashboard</h1>
                    <div class="user-info-section">
                        <?php if (isset($_SESSION['username'])): ?>
                            <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <p class="welcome-text">Welcome, Admin!</p>
                        <?php endif; ?>
                        <a href="?logout=1" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                            Logout
                        </a>
                    </div>
                </div>
              
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

                <table>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>amenities</th>
                        
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
                                
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>No listings found.</td></tr>";
                    } ?>
                </table>
            </section>

            <!-- Rest of your sections... -->
            <section id="users" class="hidden">
                <div class="header-section">
                    <h2 class="header-title">Users</h2>
                    <div class="user-info-section">
                        <?php if (isset($_SESSION['username'])): ?>
                            <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <p class="welcome-text">Welcome, Admin!</p>
                        <?php endif; ?>
                        <a href="?logout=1" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                            Logout
                        </a>
                    </div>
                </div>
                
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                        <th>Role</th>
                        
                    </tr>
                    <?php
                    if (method_exists($bookingscontroller, 'getAllUsers')) {
                        $users = $bookingscontroller->getAllUsers();
                        if ($users && $users->num_rows > 0) {
                            while ($user = $users->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <form method="post" action="/../app/model/delete_user.php" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="delete-user-btn" style="color:red;">Delete</button>
                                        </form>
                                    </td></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'>No users found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>User method not found.</td></tr>";
                    }
                    ?>
                </table>
            </section>

            <section id="complaints" class="hidden">
                <div class="header-section">
                    <h1 class="header-title">Complaints</h1>
                    <div class="user-info-section">
                        <?php if (isset($_SESSION['username'])): ?>
                            <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <p class="welcome-text">Welcome, Admin!</p>
                        <?php endif; ?>
                        <a href="?logout=1" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                            Logout
                        </a>
                    </div>
                </div>
                
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Subject</th>
                        <th>Complaint</th>
                        <th>action</th>
                     
                    </tr>
                    <?php
                    // Assuming you have a method to fetch complaints
                    $complaints = $bookingscontroller->getComplaints();
                    if ($complaints && $complaints->num_rows > 0) {
                        while ($row = $complaints->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>

                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                         
                                <td>
                                    <form method="post" action="/../app/model/delete_complaints.php" onsubmit="return confirm('Are you sure you want to delete this complaint?');" style="display:inline;">
                                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="delete-complaint-btn" style="color:red;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>No complaints found.</td></tr>";
                    }
                    ?>
                </table>
            </section>

            <section id="bookings" class="hidden">
                <div class="header-section">
                    <h1 class="header-title">Booking Requests</h1>
                    <div class="user-info-section">
                        <?php if (isset($_SESSION['username'])): ?>
                            <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <p class="welcome-text">Welcome, Admin!</p>
                        <?php endif; ?>
                        <a href="?logout=1" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                            Logout
                        </a>
                    </div>
                </div>
                
                <table>
                    <tr>
                        <th>Listing</th>
                        <th>student number</th>
                        <th>student name</th>
                        <th>Status</th>
                        
                    </tr>
                    <?php
                    if ($data && $data->num_rows > 0) {
                        while ($row = $data->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['dorm_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['std_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['std_name']); ?></td>
                                <td class="<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
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

            <section id="settings" class="hidden">
                <div class="header-section">
                    <h1 class="header-title">Settings</h1>
                    <div class="user-info-section">
                        <?php if (isset($_SESSION['username'])): ?>
                            <p class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <p class="welcome-text">Welcome, Admin!</p>
                        <?php endif; ?>
                        <a href="?logout=1" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                            Logout
                        </a>
                    </div>
                </div>
                
                <p>Settings functionality coming soon...</p>
            </section>
        </div>
    </div>

    <script>
        // Navigation script
      // Navigation script - fixed version
    document.addEventListener('DOMContentLoaded', function() {
        // Initially show dashboard and hide others
        document.querySelectorAll('section').forEach(section => {
            if (section.id !== 'dashboard') {
                section.classList.add('hidden');
            }
        });

        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault(); // This prevents the default anchor behavior
                
                // Get the target section
                const targetId = this.getAttribute('href').substring(1);
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
                    this.classList.add('active');
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

        // Booking approval/rejection
       
    </script>
</body>
</html>