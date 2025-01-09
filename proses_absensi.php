<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $status = $_POST['status'];

    $query = "INSERT INTO absensi (nama, tanggal, checkin, checkout, status) 
              VALUES ('$nama', '$tanggal', '$checkin', '$checkout', '$status')";
    
    if ($conn->query($query) === TRUE) {
        header('Location: index.php?success=1');
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$conn->close();
?>
