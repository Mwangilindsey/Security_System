<?php
session_start();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ob_start();

include "db_connect.php";
require "vendor/autoload.php";

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Jonathan Gloag Academy Gate Records Report', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, 'Generated On: ' . date('Y-m-d H:i:s'), 0, 1, 'C');


$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(20, 10, 'Record ID', 1);
$pdf->Cell(30, 10, 'Category', 1);
$pdf->Cell(45, 10, 'Full Name', 1);
$pdf->Cell(35, 10, 'ID Number', 1);
$pdf->Cell(45, 10, 'Email', 1);
$pdf->Cell(35, 10, 'Phone', 1);
$pdf->Cell(45, 10, 'Purpose', 1);
$pdf->Cell(45, 10, 'Time In', 1);
$pdf->Cell(45, 10, 'Time Out', 1);
$pdf->Ln();

$sql = "SELECT record_id, category, full_name, id_number, email, phone, purpose, time_in, time_out
        FROM gate_records
        ORDER BY record_id DESC";

$result = $conn->query($sql);

$pdf->SetFont('Arial', '', 8);

while ($row = $result->fetch_assoc()) {

    $record_id = $row['record_id'] ?? '';
    $category = $row['category'] ?? '';
    $full_name = $row['full_name'] ?? '';
    $id_number = $row['id_number'] ?? '';
    $email = $row['email'] ?? '';
    $phone = $row['phone'] ?? '';
    $purpose = $row['purpose'] ?? '';
    $time_in = $row['time_in'] ?? '';
    $time_out = $row['time_out'] ?? '';

    $pdf->Cell(20, 8, $record_id, 1);
    $pdf->Cell(30, 8, $category, 1);
    $pdf->Cell(45, 8, $full_name, 1);
    $pdf->Cell(35, 8, $id_number, 1);
    $pdf->Cell(45, 8, $email, 1);
    $pdf->Cell(35, 8, $phone, 1);
    $pdf->Cell(45, 8, $purpose, 1);
    $pdf->Cell(45, 8, $time_in, 1);
    $pdf->Cell(45, 8, $time_out, 1);
    $pdf->Ln();
}

$conn->close();

ob_end_clean();

$pdf->Output('D', 'JGA_Gate_Records_Report.pdf');
exit();
?>