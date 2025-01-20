<?php
    session_start();
    session_unset();
    session_destroy();
    if(isset($_COOKIE['password'])){
        setcookie ("usuario", "", time () - 604800);
        setcookie ("password", "", time () - 604800);
    }
    header ("Location:login.php");
?>