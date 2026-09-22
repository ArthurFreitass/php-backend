<?php 

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "supermarket";

$connection = new mysqli($servername, $username, $password, $dbName);

if ($connection->connect_error) {
    die("Error to connect in MySQL: ". $connection->connect_error);
}

?>