<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "security") {
    header("Location: index.php");
    exit();
}

$action = $_POST['action'];

if ($action == "entry") {

    $full_name = $_POST['full_name'];
    $category = $_POST['category'];
    $purpose = $_POST['purpose'];

    if ($category == "Visitor") {
        $prefix = "VIS";
    } elseif ($category == "Staff") {
        $prefix = "STF";
    } else {
        $prefix = "BUS";
    }

    $sql = "SELECT id_number
            FROM gate_records
            WHERE category = ?
            ORDER BY record_id DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lastID = $result->fetch_assoc()['id_number'];
        $number = (int)substr($lastID, 3);
        $number++;
    } else {
        $number = 1;
    }

    $id_number = $prefix . str_pad($number, 3, "0", STR_PAD_LEFT);

    $stmt->close();

    $sql = "INSERT INTO gate_records
            (category, full_name, id_number, purpose, time_in)
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $category, $full_name, $id_number, $purpose);

    if ($stmt->execute()) {
        header("Location: security_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

} elseif ($action == "exit") {

    $record_id = $_POST['record_id'];

    $sql = "UPDATE gate_records
            SET time_out = NOW()
            WHERE record_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        header("Location: security_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>