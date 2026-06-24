<?php
include "db_connect.php";

$full_name = $_POST['full_name'];
$category = $_POST['category'];
$id_number = $_POST['id_number'];

$sql = "INSERT INTO gate_records
        (category, full_name, id_number, time_in)
        VALUES (?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $category, $full_name, $id_number);

if ($stmt->execute()) {

    header("Location: index.php");
    exit();

} else {

    echo "Error: " . $stmt->error;

}

$stmt->close();
$conn->close();
?>