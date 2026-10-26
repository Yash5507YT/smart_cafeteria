<?php
    session_start();
    require_once "config.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("location: login_page.php");
        exit;
    }

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        header("location: login_page.php?error=1");
        exit;
    }

    $stmt = $link->prepare("select id, username, password, is_verified, is_admin from users where username = ? limit 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($password === $row["password"]) {
            if ((int)$row["is_verified"] === 1) {
                session_regenerate_id(true);
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = (int)$row["id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["login_time"] = time();
                $link->query("UPDATE users SET last_activity = NOW() WHERE id = " . (int)$row["id"]);

                if ((int)$row["is_admin"] === 1 || $row["username"] === "admin") {
                    header("location: admin_dashboard.php");
                } else {
                    header("location: dashboard.php");
                }
                exit;
            }
            header("location: verify_otp.php?username=" . urlencode($row["username"]));
            exit;
        }
    }

    header("location: login_page.php?error=1");
    exit;
?>
