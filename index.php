<?php
require_once 'db_connection.php'; // Pastikan file koneksi database sudah sesuai

// Query untuk mengambil data absensi dari database
$query = "SELECT * FROM absensi ORDER BY tanggal DESC";
$result = $conn->query($query);

// Statistik data absensi
$totalAbsensiQuery = "SELECT COUNT(*) as total FROM absensi";
$totalHadirQuery = "SELECT COUNT(*) as hadir FROM absensi WHERE status = 'Hadir'";
$totalAlphaQuery = "SELECT COUNT(*) as alpha FROM absensi WHERE status = 'Alpha'";
$totalIzinQuery = "SELECT COUNT(*) as izin FROM absensi WHERE status = 'Izin'";
$totalSakitQuery = "SELECT COUNT(*) as sakit FROM absensi WHERE status = 'Sakit'";

$totalAbsensi = $conn->query($totalAbsensiQuery)->fetch_assoc()['total'];
$totalHadir = $conn->query($totalHadirQuery)->fetch_assoc()['hadir'];
$totalAlpha = $conn->query($totalAlphaQuery)->fetch_assoc()['alpha'];
$totalIzin = $conn->query($totalIzinQuery)->fetch_assoc()['izin'];
$totalSakit = $conn->query($totalSakitQuery)->fetch_assoc()['sakit'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi</title>
    <script src="script.js" defer></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f7;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #FF5722; /* Changed to orange */
            color: #ffffff;
            padding: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        header .title {
            font-size: 28px;
            margin: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }

        header .nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        header .nav ul li {
            margin-right: 25px;
        }

        header .nav ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header .nav ul li a:hover {
            color: #F5A623;
        }

        main {
            padding: 40px 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
            max-width: 1200px;
        }

        .dashboard-container h2 {
            font-size: 30px;
            font-weight: 600;
            color: #333333;
            margin-bottom: 30px;
            text-align: center;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            width: 18%;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            font-size: 20px;
            font-weight: 500;
            color: #555555;
            margin-bottom: 15px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #2d3b40;
        }

        .filter-buttons {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .filter-buttons button {
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            border: none;
            border-radius: 5px;
            margin-right: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-buttons button:hover {
            background-color: #F5A623;
        }

        .table-container {
            margin-top: 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #ff5722;
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
        }

        table td {
            font-size: 16px;
            color: #333333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        footer {
            background-color: #1E2A36;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-container {
                flex-direction: column;
                align-items: center;
            }

            .stat-card {
                width: 80%;
                margin-bottom: 20px;
            }
        }
    </style>
    <script>
        function filterData(status) {
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const statusCell = row.querySelector('td:last-child'); // assuming status is in the last column
                if (status === 'all' || statusCell.textContent === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="title">Dashboard Absensi</h1>
            <nav class="nav">
                <ul>
                    <li><a href="index.php">Form Absensi</a></li>
                    <li><a href="Rekap_absensi.php">Rekap Absensi</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="dashboard-container">
            <h2>Absensi UKHTI KHADIJAH</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Absensi</h3>
                    <p><?php echo $totalAbsensi; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Hadir</h3>
                    <p><?php echo $totalHadir; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Alpha</h3>
                    <p><?php echo $totalAlpha; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Izin</h3>
                    <p><?php echo $totalIzin; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Sakit</h3>
                    <p><?php echo $totalSakit; ?></p>
                </div>
            </div>

            <div class="filter-buttons">
                <button onclick="filterData('all')">Tampilkan Semua</button>
                <button onclick="filterData('Hadir')">Hadir</button>
                <button onclick="filterData('Alpha')">Alpha</button>
                <button onclick="filterData('Izin')">Izin</button>
                <button onclick="filterData('Sakit')">Sakit</button>
            </div>

            <h2>Data Absensi</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($row['checkin']); ?></td>
                                    <td><?php echo htmlspecialchars($row['checkout']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Tidak ada data absensi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Ukhti_Khadijah - All rights reserved.</p>
    </footer>
</body>
</html>
