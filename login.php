<?php
session_start();
include "db_connect.php";

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == "admin") {
        header("Location: admin_dashboard.php");
        exit();
    }

    if ($user['role'] == "security") {
        header("Location: security_dashboard.php");
        exit();
    }

} else {
    header("Location: index.php?error=Invalid username or password");
    exit();
}

$stmt->close();
$conn->close();
?>