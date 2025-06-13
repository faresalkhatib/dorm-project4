   
   <?php
session_start();


require_once __DIR__ . '/../app/controller/bookingscontroller.php';
$bookingscontroller = new Bookingscontroler();
$user_info=$bookingscontroller->Getuserinfo($_SESSION["user_id"]);
if (!$user_info || $user_info->num_rows === 0) {
    echo "User not found.";
    exit();
}
$user_info = $user_info->fetch_assoc();
   ?>

   
   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Rental - My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/profile.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
       
    </style>
</head>
<body>
    <header>
        <div class="auth-buttons">
            <span class="user-info"></span>
            <a href="profile.php" class="auth-btn profile">
                <i class="fas fa-user"></i>
                Profile
            </a>
            <a href="?logout=1" class="auth-btn logout">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
        
        <h1>My Profile</h1>
        <p>Manage your account settings</p>
    </header>

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
                        <label for="password">New Password:</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Enter new password (optional)">
                    </div>
                    <button type="submit" class="form-control">Update Profile</button>        
                </form>

        </div>

        
        </div>
    </div>
</body>
</html>