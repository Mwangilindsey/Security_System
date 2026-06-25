<?php

$host = "localhost";
$user = "root";
$password = "1224";
$database = "sys_sec";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully";

?>