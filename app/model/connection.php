<?php
class connection {
    private $db;
    private $host;
    private $user;
    private $password;
    private $mysqli;

    public function __construct($host = "localhost", $user = "root", $password = "24681234", $db = "dorm_project") {
        $this->db = $db;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->mysqli = null;
        try {
            $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->db);
            
            if ($this->mysqli->connect_error)
             {
                throw new Exception("Connection failed: " . $this->mysqli->connect_error);
             }
         
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function get_mysql(): ?mysqli {
        return $this->mysqli; 
    }
} 

?>
