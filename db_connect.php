<?php
$conn = new mysqli("localhost", "root", "1224", "sys_sec");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>