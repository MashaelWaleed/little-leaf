<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Block unauthorized access
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
?>