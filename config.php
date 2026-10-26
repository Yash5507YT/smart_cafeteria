<?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "smart_cafeteria";

    $link = mysqli_connect($server, $username, $password, $dbname);

    if ($link === false) {
        die("Connection error: " . mysqli_connect_error());
    }
?>
