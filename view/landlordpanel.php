<?php
session_start();
require_once __DIR__ . '/../app/controller/bookingscontroller.php';

$bookingscontroller = new Bookingscontroler();
$data = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "pending");
$data1 = $bookingscontroller->Bookingsreq($_SESSION["user_id"], "approved");


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="css/landlordpanel.css">
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

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Landlord Panel</h2>
            <ul>
                <li><a href="#dashboard" class="active">🏠 Dashboard</a></li>
                <li><a href="#listings"> Manage Listings</a></li>
                <li><a href="#bookings"> Booking Requests</a></li>
                <li><a href="#settings"> Settings</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <section id="dashboard">
                <h1>Dashboard</h1>
                <div class="stats">
                    <div class="stat-box">Total Listings: <span>5</span></div>
                    <div class="stat-box">Pending Bookings: <span>3</span></div>
                    <div class="stat-box">Approved Bookings: <span>12</span></div>
                </div>
            </section>

            <section id="listings" class="hidden">
                <h1>Manage Listings</h1>

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
    <label><input type="checkbox" name="amenities[]" value="Wi-Fi"> Wi-Fi</label><br>
    <label><input type="checkbox" name="amenities[]" value="Air Conditioning"> Air Conditioning</label><br>
    <label><input type="checkbox" name="amenities[]" value="Laundry"> Laundry</label><br>
    <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label><br>
    <!-- Add more as needed -->
</div>

                    <div class="form-group">
                        <label for="image">Upload Image:</label>
                        <input type="file" name="file[]" multiple>

                    </div>
                    <button type="submit" class="submit-btn">Submit Listing</button>
                </form>

                <table>

                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>student name</th>
                        <th>status</th>
                        <th>edit booking</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <?php
                        if ($data1 && $data1->num_rows >= 0) {
                            while ($row = $data1->fetch_assoc()) {

                                ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['dorm_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php echo htmlspecialchars($row['std_name']); ?></td>
                                <td class="<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </td>
                                <td>

                                    <button class="reject" data-id="<?php echo $row['id']; ?>">unbook</button>
                                </td>
                                <td>
                                    <button class="edit">Edit</button>
                                    <button class="delete">Delete</button>
                                </td>
                            </tr>
                            <?php
                            }
                        } else {
                            echo "<p>No listings found.</p>";
                        } ?>



                    </tr>
                </table>
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