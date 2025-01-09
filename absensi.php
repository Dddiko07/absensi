<?php
require_once 'db_connection.php'; // Pastikan file ini sudah sesuai dengan koneksi database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Ukhti_Khadijah</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="title">Absensi Ukhti_Khadijah</h1>
        </div>
    </header>
    <main>
        <div class="form-container">
            <h2>Form Absensi</h2>
            <form id="absensiForm" method="POST" action="proses_absensi.php">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="checkin">Waktu Check-In</label>
                    <input type="time" id="checkin" name="checkin" required>
                </div>
                <div class="form-group">
                    <label for="checkout">Waktu Check-Out</label>
                    <input type="time" id="checkout" name="checkout" required>
                </div>
                <div class="form-group">
                    <label for="status">Status Kehadiran</label>
                    <select id="status" name="status">
                        <option value="Hadir">Hadir</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Alpha">Alpha</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Kirim</button>
            </form>
        </div>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Ukhti_Khadijah - All rights reserved.</p>
    </footer>
</body>
</html>