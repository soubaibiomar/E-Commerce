<?php
session_start();
include_once('includes/config.php');
$cid = intval($_GET['cid'] ?? 0);
if ($cid > 0) {
    header("location:category.php?cid=$cid");
} else {
    header("location:index.php");
}
exit();
?>
