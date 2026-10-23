<?php
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$wasLoggedIn = isLoggedIn();

session_unset();
session_destroy();

session_start();

if ($wasLoggedIn) {
    $_SESSION['logout_message'] = 'Anda telah berhasil keluar dari sistem.';
}

header('Location: login.php');
exit;
?>
