<?php
session_start();
require_once __DIR__ . '/../app/controller/bookingscontroller.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$bookingscontroller = new Bookingscontroler();
$data = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "pending");
$data1 = $bookingscontroller->listings1($_SESSION["user_id"]);
$bookingStats = $bookingscontroller->getBookingStats($_SESSION["user_id"]);

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<script>
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
                    location.reload(); // reload to reflect changes
                })
                .catch(err => {
                    alert("Error: " + err);
                });
        });
    });
</script>
<script>function openForm() {
        document.getElementById("myForm").style.display = "block";
    }

    function closeForm() {
        document.getElementById("myForm").style.display = "none";
    }
</script>

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
        </div>

        <!-- Main Content -->
        <div class="main">
            <section id="dashboard">
                <h1>Dashboard</h1>
                <div>
                    <canvas id="myChart"></canvas>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <div class="stats">
                    <div class="stat-box">Total Listings: <span>5</span></div>
                    <div class="stat-box">Pending Bookings: <span>3</span></div>
                    <div class="stat-box">Approved Bookings: <span>12</span></div>
                </div>
                <table>

                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>amenities</th>

                        <th>Actions</th>
                    </tr>
                    <tr>
                        <?php
                        if ($data1 && $data1->num_rows >= 0) {
                            while ($row = $data1->fetch_assoc()) 
                            {

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

                                    <!-- The form  for editing         -->
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
                        } 
                        else 
                        {
                            echo "<p>No listings found.</p>";
                        } ?>



                    </tr>
                </table>
            </section>

            <section id="listings" class="hidden">


                <h2>Add New Listing</h2>
                <form action="/../app/model/submit_listing.php" method="POST" enctype="multipart/form-data"
                    class="add-listing-form">
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
                        <textarea name="description" required></textarea>


                    </div>
                    <div class="form-group">
                        <label for="amenities">Amenities:</label><br>
                        <label><input type="checkbox" name="amenities[]" value="WiFi"> WiFi</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Air Conditioning"> Air
                            Conditioning</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Laundry"> Laundry</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label><br>
                        <label><input type="checkbox" name="amenities[]" value="gym"> Gym</label><br>
                        <label><input type="checkbox" name="amenities[]" value="Cleaning"> cleaning</label><br>
                        <label for="type"> Room type</label>
                        <select name="type">
                            <option value="">Room Type</option>
                            <option value="single">Single</option>
                            <option value="shared">Shared</option>
                            <option value="entire">Entire Dorm</option>
                        </select>

                        <!-- Add more as needed -->
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Image:</label>
                        <input type="file" name="file[]" multiple>

                    </div>
                    <button type="submit" class="submit-btn">Submit Listing</button>
                    < </form>


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

                    </tr> <?php
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
                                <td>
                                    <button class="approve" data-id="<?php echo $row['id']; ?>">Approve</button>
                                    <button class="reject" data-id="<?php echo $row['id']; ?>">Reject</button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<p>No listings found.</p>";
                    } ?>
                </table>
            </section>

        </div>
    </div>

    <script>
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('section').forEach(section => section.classList.add('hidden'));
                document.querySelector(link.getAttribute('href')).classList.remove('hidden');
                document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');
            });
        });
    </script>
    <script>
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
                        location.reload(); // reload to show updated status
                    })
                    .catch(err => {
                        alert("Error: " + err);
                    });
            });
        });
    </script>

</body>

</html>