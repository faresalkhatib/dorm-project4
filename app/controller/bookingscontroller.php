<?php   require_once __DIR__ . '/../model/connection.php';
class Bookingscontroler{
    private $conn;
    public function __construct()
    {
        $db=new connection();
        $this->conn=$db->get_mysql();

    }
    public function Bookingsreq($oid,$state)
    {

        
        $sql = $this->conn->prepare("SELECT * FROM bookings WHERE owner_id = ? AND status = ?");
        $sql->bind_param("is", $oid,$state);  // "i" for integer parameter
        $sql->execute();
        return $sql->get_result();
    }

}