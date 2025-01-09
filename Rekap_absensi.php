<?php
require_once 'db_connection.php';

// Mengatur variabel untuk filter
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$nama = isset($_GET['nama']) ? $_GET['nama'] : '';

// Logika untuk unduh CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_csv'])) {
    // Menangani unduhan CSV
    $query = "SELECT * FROM absensi";
    $conditions = [];

    if ($tanggal_awal && $tanggal_akhir) {
        $conditions[] = "tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    }

    if ($nama) {
        $conditions[] = "nama = '$nama'";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " ORDER BY tanggal DESC";
    $result = $conn->query($query);

    // Header CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="rekap_absensi.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['No', 'Nama', 'Tanggal', 'Check-In', 'Check-Out', 'Status']);

    // Tulis data ke CSV
    if ($result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $no++,
                $row['nama'],
                $row['tanggal'],
                $row['checkin'],
                $row['checkout'],
                $row['status']
            ]);
        }
    } else {
        fputcsv($output, ['Tidak ada data untuk filter yang dipilih.']);
    }

    fclose($output);
    exit;
}

// Query untuk menampilkan data absensi
$query = "SELECT * FROM absensi";
$conditions = [];

if ($tanggal_awal && $tanggal_akhir) {
    $conditions[] = "tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

if ($nama) {
    $conditions[] = "nama = '$nama'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY tanggal DESC";
$result = $conn->query($query);

// Query untuk daftar nama (dropdown filter)
$nama_query = "SELECT DISTINCT nama FROM absensi";
$nama_result = $conn->query($nama_query);

// Statistik Kehadiran
$statistik_query = "SELECT status, COUNT(*) AS jumlah FROM absensi";
if (!empty($conditions)) {
    $statistik_query .= " WHERE " . implode(' AND ', $conditions);
}
$statistik_query .= " GROUP BY status";

$statistik_result = $conn->query($statistik_query);
$statistik = [];
$total_absensi = 0;

if ($statistik_result) {
    while ($row = $statistik_result->fetch_assoc()) {
        $statistik[$row['status']] = [
            'jumlah' => $row['jumlah'],
            'persentase' => 0,
        ];
        $total_absensi += $row['jumlah'];
    }

    foreach ($statistik as $status => &$data) {
        $data['persentase'] = ($data['jumlah'] / $total_absensi) * 100;
    }
}

// Rata-rata waktu check-in dan check-out
$rata_query = "SELECT 
    AVG(TIME_TO_SEC(checkin)) AS rata_checkin, 
    AVG(TIME_TO_SEC(checkout)) AS rata_checkout 
    FROM absensi";
if (!empty($conditions)) {
    $rata_query .= " WHERE " . implode(' AND ', $conditions);
}

$rata_result = $conn->query($rata_query);
$rata_checkin = $rata_checkout = '-';

if ($rata_result && $rata_row = $rata_result->fetch_assoc()) {
    $rata_checkin = gmdate("H:i:s", $rata_row['rata_checkin']);
    $rata_checkout = gmdate("H:i:s", $rata_row['rata_checkout']);
}

// Data untuk Chart.js
$chart_labels = json_encode(array_keys($statistik));
$chart_data = json_encode(array_column($statistik, 'jumlah'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Rekap Absensi</h1>
    </header>

    <main>
        <!-- Filter Data -->
        <section>
            <h2>Filter Data Absensi</h2>
            <form method="GET" action="">
                <label for="tanggal_awal">Tanggal Awal:</label>
                <input type="date" id="tanggal_awal" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal); ?>">

                <label for="tanggal_akhir">Tanggal Akhir:</label>
                <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir); ?>">

                <label for="nama">Nama:</label>
                <select id="nama" name="nama">
                    <option value="">-- Pilih Nama --</option>
                    <?php while ($row = $nama_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['nama']); ?>" <?= $row['nama'] == $nama ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Filter</button>
            </form>

            <!-- Tombol Unduh -->
            <form method="POST" action="">
                <input type="hidden" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal); ?>">
                <input type="hidden" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir); ?>">
                <input type="hidden" name="nama" value="<?= htmlspecialchars($nama); ?>">
                <button type="submit" name="download_csv">Unduh Data CSV</button>
            </form>
        </section>

        <!-- Data Absensi -->
        <section>
            <h2>Data Absensi</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                <td><?= htmlspecialchars($row['checkin']); ?></td>
                                <td><?= htmlspecialchars($row['checkout']); ?></td>
                                <td><?= htmlspecialchars($row['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Data tidak ditemukan untuk filter yang dipilih.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Diagram Kehadiran -->
        <section>
            <h2>Diagram Kehadiran</h2>
            <canvas id="absensiChart" width="400" height="200"></canvas>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Absensi Ukhti_Khadijah. All rights reserved.</p>
    </footer>

    <script>
        const ctx = document.getElementById('absensiChart').getContext('2d');
        const absensiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $chart_labels; ?>,
                datasets: [{
                    label: 'Jumlah Kehadiran',
                    data: <?= $chart_data; ?>,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
