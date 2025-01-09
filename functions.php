<?php
require('db_connection.php');

function checkIn($nama, $tanggal, $checkInTime) {
    global $conn;

    // Membuat tabel baru untuk setiap pengguna jika belum ada
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `$nama` (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        Tanggal DATE NOT NULL,
        Check_In TIME DEFAULT NULL,
        Check_Out TIME DEFAULT NULL
    )";
    $conn->query($createTableQuery);

    // Masukkan data ke tabel utama
    $query = "INSERT INTO Absensi (Nama, Tanggal, Check_In) VALUES ('$nama', '$tanggal', '$checkInTime')";
    $conn->query($query);

    // Masukkan data ke tabel pengguna
    $queryIndividu = "INSERT INTO `$nama` (Tanggal, Check_In) VALUES ('$tanggal', '$checkInTime')";
    $conn->query($queryIndividu);
}

function checkOut($nama, $tanggal, $checkOutTime) {
    global $conn;

    // Update data di tabel utama
    $query = "UPDATE Absensi SET Check_Out='$checkOutTime' WHERE Nama='$nama' AND Tanggal='$tanggal'";
    $conn->query($query);

    // Update data di tabel pengguna
    $queryIndividu = "UPDATE `$nama` SET Check_Out='$checkOutTime' WHERE Tanggal='$tanggal'";
    $conn->query($queryIndividu);
}
?>
