<?php

function validate($data)
{
    $data=trim($data);
    $data=stripslashes($data);
    $data=htmlspecialchars($data);
    return $data;
}


try {
 require_once __DIR__ . "/connection.php";
 $test=new connection();
 $connect = $test->get_mysql();


    if($_SERVER["REQUEST_METHOD"]=="POST")
{

    if(isset($_POST["email"]) && $_POST["password"])
{
$email=validate($_POST["email"]);
$pass=validate($_POST["password"]);



if(empty($email))
{
 echo "endter a valid email";
}
elseif(empty($pass))
{
  echo "enter a valid passorwd";
}else{
    $query = $connect->prepare("select * from users where email=?");
    $query->bind_param("s", $email);
    $query->execute(); 
    $result=$query->get_result();
    $user=$result->fetch_assoc();

    if($user&&password_verify($pass,$user["password"]))
    {   session_start();
        $_SESSION["user_id"]=$user["id"];
        $_SESSION["email"]=$user["email"];
        $_SESSION["role"]=$user["role"];

        $role = strtolower($user["role"]);
        switch ( $role) {
            case 'landlord':
                header("Location: /view/landlordpanel.php");
                exit(); // Critical: Stop script execution after redirect
                break;
                
            case 'student': // Fixed typo ("Studunt" → "student")
                header("Location:/view/browse.php");
                exit();
                break;
                
            default:
                // Fallback for invalid/unknown roles
                echo"wrong role";
                exit();
        }
       
        
    }
    else
    {
        echo"wrong Email or Username";
    }

    


    
}
   
}
    
} 
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
    //throw $th;




