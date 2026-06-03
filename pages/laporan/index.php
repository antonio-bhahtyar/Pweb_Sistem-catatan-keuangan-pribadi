<?php
// pages/laporan/index.php
$judul_halaman = "Laporan Keuangan";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$user_id = $_SESSION['user_id'];

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Ringkasan bulan ini
$stmt = $pdo->prepare("
    SELECT
        COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) as pemasukan,
        COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as pengeluaran
    FROM transaksi
    WHERE user_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
");
$stmt->execute([$user_id, $bulan, $tahun]);
$ringkasan = $stmt->fetch();
$selisih = $ringkasan['pemasukan'] - $ringkasan['pengeluaran'];

// Chart 1: 6 bulan terakhir
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(tanggal, '%Y-%m') as bulan,
           SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END) as pemasukan,
           SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran
    FROM transaksi
    WHERE user_id = ? AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan ASC
");
$stmt->execute([$user_id]);
$chart_6bulan = $stmt->fetchAll();

// Chart 2: Pengeluaran per kategori bulan ini
$stmt = $pdo->prepare("
    SELECT k.nama_kategori, k.warna, SUM(t.jumlah) as total
    FROM transaksi t
    JOIN kategori k ON t.kategori_id = k.id
    WHERE t.user_id = ? AND t.tipe = 'pengeluaran'
      AND MONTH(t.tanggal) = ? AND YEAR(t.tanggal) = ?
    GROUP BY k.id, k.nama_kategori, k.warna
    ORDER BY total DESC
");
$stmt->execute([$user_id, $bulan, $tahun]);
$chart_kategori = $stmt->fetchAll();

// Chart 3: Budget vs Aktual
$stmt = $pdo->prepare("
    SELECT k.nama_kategori, a.jumlah as anggaran,
           COALESCE(SUM(t.jumlah), 0) as pengeluaran
    FROM anggaran a
    JOIN kategori k ON a.kategori_id = k.id
    LEFT JOIN transaksi t ON t.kategori_id = a.kategori_id
        AND t.tipe = 'pengeluaran' AND t.user_id = a.user_id
        AND MONTH(t.tanggal) = a.bulan AND YEAR(t.tanggal) = a.tahun
    WHERE a.user_id = ? AND a.bulan = ? AND a.tahun = ?
    GROUP BY a.id, k.nama_kategori, a.jumlah
");
$stmt->execute([$user_id, $bulan, $tahun]);
$chart_budget = $stmt->fetchAll();
?>

<div class="container-fluid">

    <!-- Selector Bulan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Laporan Keuangan</h4>
        <form method="GET" class="row g-2">
            <div class="col-auto">
                <select name="bulan" class="form-select">
                    <?php foreach ($nama_bulan as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>"
                       min="2020" max="<?= date('Y') + 1 ?>" style="width: 100px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Lihat</button>
            </div>
        </form>
    </div>

    <!-- Kartu Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h6>Total Pemasukan</h6>
                    <h3>Rp <?= number_format($ringkasan['pemasukan'], 0, ',', '.') ?></h3>
                    <small><?= $nama_bulan[$bulan] ?> <?= $tahun ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body text-center">
                    <h6>Total Pengeluaran</h6>
                    <h3>Rp <?= number_format($ringkasan['pengeluaran'], 0, ',', '.') ?></h3>
                    <small><?= $nama_bulan[$bulan] ?> <?= $tahun ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-<?= $selisih >= 0 ? 'primary' : 'warning' ?>">
                <div class="card-body text-center">
                    <h6>Selisih</h6>
                    <h3>Rp <?= number_format($selisih, 0, ',', '.') ?></h3>
                    <small><?= $selisih >= 0 ? 'Surplus' : 'Defisit' ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 1: 6 Bulan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5>Pemasukan vs Pengeluaran (6 Bulan Terakhir)</h5></div>
                <div class="card-body">
                    <canvas id="chart6bulan" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2 & 3 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Pengeluaran per Kategori - <?= $nama_bulan[$bulan] ?></h5></div>
                <div class="card-body">
                    <canvas id="chartKategori" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Budget vs Aktual - <?= $nama_bulan[$bulan] ?></h5></div>
                <div class="card-body">
                    <canvas id="chartBudget" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
// Chart 1: Pemasukan vs Pengeluaran 6 Bulan
(function() {
    var data = <?= json_encode($chart_6bulan) ?>;
    var labels = data.map(function(d) { return d.bulan; });
    var pemasukan = data.map(function(d) { return parseFloat(d.pemasukan); });
    var pengeluaran = data.map(function(d) { return parseFloat(d.pengeluaran); });

    new Chart(document.getElementById('chart6bulan'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Pemasukan', data: pemasukan, backgroundColor: '#198754' },
                { label: 'Pengeluaran', data: pengeluaran, backgroundColor: '#dc3545' }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } }
            }
        }
    });
})();

// Chart 2: Pengeluaran per Kategori (Doughnut)
(function() {
    var data = <?= json_encode($chart_kategori) ?>;
    new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        data: {
            labels: data.map(function(d) { return d.nama_kategori; }),
            datasets: [{
                data: data.map(function(d) { return parseFloat(d.total); }),
                backgroundColor: data.map(function(d) { return d.warna; })
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
})();

// Chart 3: Budget vs Aktual
(function() {
    var data = <?= json_encode($chart_budget) ?>;
    new Chart(document.getElementById('chartBudget'), {
        type: 'bar',
        data: {
            labels: data.map(function(d) { return d.nama_kategori; }),
            datasets: [
                { label: 'Anggaran', data: data.map(function(d) { return parseFloat(d.anggaran); }), backgroundColor: '#0d6efd' },
                { label: 'Pengeluaran', data: data.map(function(d) { return parseFloat(d.pengeluaran); }), backgroundColor: '#dc3545' }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
