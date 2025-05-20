
<?php
require_once __DIR__ . '/../app/model/connection.php';
require_once __DIR__ . '/../app/controller/MailController.php';

// If MailController is namespaced, import it here, e.g.:


$mailsender = new MailController();
$result = $mailsender->send_booking_request(11, "0786125882", 33);



?>