<?php
session_start();
    session_destroy(); //unset user session

    header('location:login.php');
?>