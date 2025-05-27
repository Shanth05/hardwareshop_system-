<?php
declare(strict_types=1);

include 'connect.php';

session_start();
session_unset();
session_destroy();

header('Location: ../admin/admin_login.php');
exit;
?>