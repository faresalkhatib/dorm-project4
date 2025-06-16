<?php
if (function_exists('password_hash')) {
    echo "password_hash() is available!";
} else {
    echo "password_hash() is NOT available!";
}
phpinfo();
?>
<?php 
require_once 'protection.php'; // Add this line to every PHP file
?>