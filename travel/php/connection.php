<?php

    $server_name = "localhost";
    $user = "root";
    $password = "";
    $dbName = "travel_management";

    $connect = new mysqli($server_name, $user, $password, $dbName);

    if (!$connect) {
        die("Error in to connect MySQL");
    }
?>