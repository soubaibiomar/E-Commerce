<?php
session_start();
include('include/config.php');
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    header("location:edit-products.php?id=$id");
} else {
    header("location:manage-products.php");
}
exit();
?>
