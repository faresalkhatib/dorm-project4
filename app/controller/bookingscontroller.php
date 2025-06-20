<?php 
require_once 'protection.php'; // Add this line to every PHP file
?><?php   require_once __DIR__ . '/../model/connection.php';



 // ------------------ this controller is manly used in admin panel ------------------
// ------------------ this controller is manly used in landlord panel ------------------

class Bookingscontroler{
    private $conn;
    public function __construct()
    {
        $db=new connection();
        $this->conn=$db->get_mysql();

    }
    public function Bookingsreq($oid,$state)
    {

        
        $sql = $this->conn->prepare("SELECT * FROM bookings WHERE owner_id = ? ");
        $sql->bind_param("i", $oid);  // "i" for integer parameter
        $sql->execute();
        return $sql->get_result();
    }
     public function Bookingsreq2()
    {

        
        $sql = $this->conn->prepare("SELECT * FROM bookings ");
        // admin panel use only
        $sql->execute();
        return $sql->get_result();
    }
    
    public function listings1($oid)
    {

        
        $sql = $this->conn->prepare("SELECT * FROM listings WHERE owner_id = ?");
        $sql->bind_param("i", $oid);  // "i" for integer parameter
        $sql->execute();
        return $sql->get_result();
    }
     public function listings2()
    {
   // admin panel use only
        
        $sql = $this->conn->prepare("SELECT * FROM listings ");
       
        $sql->execute();
        return $sql->get_result();
    }

    // In your bookingscontroller.php
public function getBookingStats($landlord_id) {
    $query = $this->conn->prepare("
        SELECT 
            DATE(booking_date) AS booking_day, 
            COUNT(*) AS booking_count
        FROM bookings
        WHERE owner_id = ?
        GROUP BY DATE(booking_date)
        ORDER BY booking_day ASC
    ");
    $query->bind_param("i", $landlord_id);
    $query->execute();
    return $query->get_result();
}
public function GetAllusers()
{   // admin panel use only
    $sql = $this->conn->prepare("SELECT * FROM users");
    $sql->execute();
    return $sql->get_result();


}    
public function Getuserinfo($id)
{
    $sql = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
    $sql->bind_param("i", $id);  // "i" for integer parameter
    $sql->execute();
    return $sql->get_result();

}
public function getComplaints()
{   // admin panel use only
    $sql = $this->conn->prepare("SELECT * FROM complaints");
    $sql->execute();
    return $sql->get_result();
}

}