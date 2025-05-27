<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// ====================
// REKAP ABSENSI BULANAN
// ====================

// Tentukan periode bulan berjalan berdasarkan tanggal hari ini
$today     = date('Y-m-d');
$startDate = date('Y-m-01', strtotime($today));
$endDate   = date('Y-m-t', strtotime($today));

// Query untuk rekap absensi bulanan (Izin, Sakit, Alpha) dari absensi masuk (tipe_id = 1)
$queryCardMonth = "SELECT status, COUNT(*) as total 
                   FROM absensi 
                   WHERE tipe_id = 1 
                     AND tanggal BETWEEN ? AND ? 
                   GROUP BY status";
$stmtCardMonth = $conn->prepare($queryCardMonth);
$stmtCardMonth->bind_param("ss", $startDate, $endDate);
$stmtCardMonth->execute();
$resultCardMonth = $stmtCardMonth->get_result();

// Inisialisasi data card dengan default 0
$cardDataBulanan = [
    'izin'  => 0,
    'sakit' => 0,
    'alpha' => 0
];

while ($row = $resultCardMonth->fetch_assoc()) {
    $status = strtolower($row['status']);
    if (isset($cardDataBulanan[$status])) {
        $cardDataBulanan[$status] = $row['total'];
    }
}

// ====================
// DAFTAR KELAS YANG BELUM MELAKUKAN ABSENSI HARI INI
// ====================

// Ambil data semua kelas
$stmt_kelas = $conn->query("SELECT * FROM kelas");
if (!$stmt_kelas) {
    die("Error saat mengambil data kelas: " . $conn->error);
}
$kelas_list = $stmt_kelas->fetch_all(MYSQLI_ASSOC);

// Ambil data absensi hari ini untuk mendapatkan kelas yang sudah melakukan absensi
$queryKelasAbsensi = "SELECT DISTINCT s.kelas_id FROM siswa s
                      JOIN absensi a ON a.siswa_id = s.id
                      WHERE a.tanggal = ?";
$stmtKelasAbsensi = $conn->prepare($queryKelasAbsensi);
$stmtKelasAbsensi->bind_param("s", $today);
$stmtKelasAbsensi->execute();
$resultKelasAbsensi = $stmtKelasAbsensi->get_result();

$kelasAbsensi = [];
while ($row = $resultKelasAbsensi->fetch_assoc()) {
    $kelasAbsensi[] = $row['kelas_id'];
}

// Filter kelas yang belum melakukan absensi hari ini
$kelasBelumAbsensi = [];
foreach ($kelas_list as $kelas) {
    if (!in_array($kelas['id'], $kelasAbsensi)) {
        $kelasBelumAbsensi[] = $kelas;
    }
}
?>
<div class="ml-[226px] pt-20 pl-8">
    <!-- Rekap Absensi Bulanan -->
    <h2 class="text-2xl font-semibold mb-4">
        Rekap Absensi Bulanan (<?= date('F Y', strtotime($today)) ?>)
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <!-- Card Izin -->
        <div class="bg-green-100 p-4 rounded-lg shadow-md border-l-4 border-green-500 h-48 flex flex-col justify-center items-center">
            <h3 class="text-xl font-bold text-green-800">Izin</h3>
            <p class="mt-2 text-3xl font-semibold text-green-900"><?= htmlspecialchars($cardDataBulanan['izin']) ?></p>
            <p class="text-sm text-green-700">Siswa Izin</p>
        </div>
        <!-- Card Sakit -->
        <div class="bg-yellow-100 p-4 rounded-lg shadow-md border-l-4 border-yellow-500 h-48 flex flex-col justify-center items-center">
            <h3 class="text-xl font-bold text-yellow-800">Sakit</h3>
            <p class="mt-2 text-3xl font-semibold text-yellow-900"><?= htmlspecialchars($cardDataBulanan['sakit']) ?></p>
            <p class="text-sm text-yellow-700">Siswa Sakit</p>
        </div>
        <!-- Card Alpha -->
        <div class="bg-red-100 p-4 rounded-lg shadow-md border-l-4 border-red-500 h-48 flex flex-col justify-center items-center">
            <h3 class="text-xl font-bold text-red-800">Alpha</h3>
            <p class="mt-2 text-3xl font-semibold text-red-900"><?= htmlspecialchars($cardDataBulanan['alpha']) ?></p>
            <p class="text-sm text-red-700">Siswa Alpha</p>
        </div>
    </div>

    <!-- Daftar Kelas yang Belum Absensi -->
    <div class="mt-10 max-w-md">
        <div class="bg-white shadow-md border-l-4 border-gray-400 h-48 rounded-lg p-6 flex flex-col justify-start">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Kelas Belum Absensi</h3>
            <div class="overflow-y-auto">
                <?php if (!empty($kelasBelumAbsensi)): ?>
                    <ul class="space-y-1">
                        <?php foreach ($kelasBelumAbsensi as $kelas): ?>
                            <li class="text-gray-700 text-sm border-b pb-1">
                                <?= htmlspecialchars($kelas['singkatan']) ?> - Belum melakukan absensi
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-green-600 text-sm">Semua kelas telah melakukan absensi hari ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
