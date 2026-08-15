<?php 
session_start();
include_once('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    header('location:order-history.php');
    exit();
}
?>
