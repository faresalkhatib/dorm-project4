<?php 
require_once __DIR__ . "/../app/model/connection.php";

// $test = new connection();

// $connect = $test->get_mysql();

// $sql = $connect->prepare("SELECT * FROM admins");
// $sql->execute();

// $result = $sql->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0 ">
    <title>buysters</title>
     <link rel="stylesheet" href="css/loginstyle.css"
     
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<body>
    <section id="header">
    <div class="logo"><img src="../imges/logo.png" alt="">
   
    </div>  
    

 
  
    
</section>
   

    <div class="container">
      

        <div class="form-box" id="signup-form">


            <form action="/../app/model/logindb.php" method="post">
                <h2>Login</h2>
                <input type="name" name="name" placeholder="Name" required>
                
                <input type="email" name="email" placeholder="Email" required>
                
                
                <input type="password" name="password" placeholder="Password" required>
                <input type="number" name="phone_number" placeholder="phone number" required>
                <label for="role">Choose Role:</label>             
                <select name="role" id="role" required>
        <option value="student">Student</option>
        <option value="landlord">Landlord</option>
        <option value="admin"> Admin</option>
    </select>
                    <button type="submit" name="regester">regester</button>
            <p>already have an account ? <a href="../view/login.php">Log in</a></p>
            </form>
        </div>
    </div>
        
</body>
</html>
<?php  



?>