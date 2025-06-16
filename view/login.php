<?php 
require_once 'protection.php'; // Add this line to every PHP file
?>

<?php 
require_once __DIR__ . "/../app/model/connection.php";



?>


<?php
session_start()

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0 ">
    <title>buysters</title>
     <link rel="stylesheet" href="css/loginstyle.css">
     
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<body>
    <section id="header">
    <div class="logo"><img src="../imges/logo.png" alt="">
   
    </div>  

    
</section>
    
    <div class="container">
        <div class="form-box" id="login-form">
            <form action=" /../app/model/login_conn.php" method="post">
                <h2>Login</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
            <p>dont have an account ? <a href="../view/signup.php">Rigester</a></p>
            </form>
        </div>

    </div>
        
</body>
</html>