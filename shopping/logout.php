<?php
session_start();
include("includes/config.php");

if (!empty($_SESSION['login'])) {
    $email = $_SESSION['login'];
    $ldate = date('d-m-Y h:i:s A');
    db_query("UPDATE userlog SET logout = ? WHERE userEmail = ? ORDER BY id DESC LIMIT 1", [$ldate, $email], "ss");
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: index.php");
exit();
?>
