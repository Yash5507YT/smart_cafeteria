<?php
    session_start();
    require_once "config.php";

    if (isset($_SESSION["user_id"])) {
        $user_id = (int)$_SESSION["user_id"];
        $link->query("UPDATE users SET last_activity = NOW() WHERE id = " . $user_id);
    }

    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Location: login_page.php?logged_out=1");
    exit;
?>
