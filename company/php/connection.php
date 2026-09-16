<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$db_name = 'company';

$connection = new mysqli($servername, $username, $password, $db_name);

if (!$connection) {
    die("Error in to connect mysql!");
}

?>