<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "INSERT INTO users (username, password, role)
        VALUES (?, ?, 'security')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?success=Security officer account created successfully");
    exit();
} else {
    header("Location: admin_dashboard.php?error=Username already exists");
    exit();
}

$stmt->close();
$conn->close();
?>