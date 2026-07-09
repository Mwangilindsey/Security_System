<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

$username = trim($_POST['username']);
if (!preg_match('/^[A-Za-z ]+$/', $username)) {
    die("Wrong input! Username can only contain letters and spaces.");
}


$password = $_POST['password'];

$email = trim($_POST['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}
$phone = trim($_POST['phone']);
if (!preg_match('/^(07|01)[0-9]{8}$/', $phone)) {
    die("Invalid phone number.");
}

$check = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=?");;
$check->bind_param("ss", $email, $phone);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Email or Phone Number already exists.");
}

$sql = "INSERT INTO users (username, password, email, phone, role)
        VALUES (?, ?, ?, ?, 'security')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password, $email, $phone);

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