<?php session_start();
if (isset($_SESSION['user_is_logged_in'])) {
    unset($_SESSION['user_is_logged_in']);
}
header('Location: login.php'); ?>