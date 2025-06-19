<?php
include '../koneksi.php';


$queryTotalSiswa = "SELECT COUNT(*) AS total FROM siswa";
$resultTotalSiswa = $conn->query($queryTotalSiswa);
$totalSiswa = $resultTotalSiswa->fetch_assoc()['total'];

$queryTotalKelas = "SELECT COUNT(*) AS total FROM kelas";
$resultTotalKelas = $conn->query($queryTotalKelas);
$totalKelas = $resultTotalKelas->fetch_assoc()['total'];


$queryTotalUser = "SELECT COUNT(*) AS total FROM users";
$resultTotalUser = $conn->query($queryTotalUser);
$totalUser = $resultTotalUser->fetch_assoc()['total'];


$queryKelas = "SELECT id, singkatan FROM kelas";
$resultKelas = $conn->query($queryKelas);
$kelasBelumAbsensi = [];

$tanggalHariIni = date('Y-m-d');
$kelasBelumAbsensi = [];

while ($kelas = $resultKelas->fetch_assoc()) {
    $idKelas = $kelas['id'];


    $queryCekAbsensi = "
       SELECT COUNT(*) AS total 
FROM absensi a
JOIN siswa s ON a.siswa_id = s.nis
WHERE s.kelas_id = $idKelas 
AND a.tanggal = '$tanggalHariIni'

    ";

    $resultCek = $conn->query($queryCekAbsensi);

    if ($resultCek) {
        $totalAbsensi = $resultCek->fetch_assoc()['total'];
        if ($totalAbsensi == 0) {
            $kelasBelumAbsensi[] = $kelas;
        }
    } else {
        echo "Error query absensi: " . $conn->error;
    }
}




$bulanIni = date('Y-m');
$queryRekap = "
    SELECT status, COUNT(*) AS jumlah 
    FROM absensi 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulanIni' 
    GROUP BY status
";
$resultRekap = $conn->query($queryRekap);

$rekap = [
    'Izin' => 0,
    'Sakit' => 0,
    'Alpha' => 0
];
$map = ['izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];

while ($row = $resultRekap->fetch_assoc()) {
    $status = strtolower($row['status']);
    if (isset($map[$status])) {
        $rekap[$map[$status]] = $row['jumlah'];
    }
}
?>


<div class="ml-[226px] pt-20 pl-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
 
        <a href="admin.php?page=tampil_siswa" class="block">
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300 ease-in-out border-l-4 border-blue-500 cursor-pointer h-48 flex flex-col justify-between">
                <h3 class="text-xl font-bold text-gray-800">Total Siswa</h3>
                <div>
                    <p class="mt-2 text-3xl font-semibold text-gray-700">
                        <?php echo $totalSiswa; ?> <span class="text-sm font-normal">siswa</span>
                    </p>
                    <p class="mt-1 text-base text-gray-500">dari <?php echo $totalKelas; ?> kelas</p>
                </div>
            </div>
        </a>


        <a href="admin.php?page=tampil_user" class="block">
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300 ease-in-out border-l-4 border-green-500 cursor-pointer h-48 flex flex-col justify-center">
                <h3 class="text-xl font-bold text-gray-800">Total User</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-700"><?php echo $totalUser; ?></p>
            </div>
        </a>


        <a href="admin.php?page=tampil_kelas" class="block">
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300 ease-in-out border-l-4 border-yellow-500 cursor-pointer h-48 flex flex-col justify-center">
                <h3 class="text-xl font-bold text-gray-800">Total Kelas</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-700"><?php echo $totalKelas; ?></p>
            </div>
        </a>
    </div>


    <div class="mt-10 max-w-md">
        <div class="bg-white shadow-md border-l-4 border-gray-400 h-48 rounded-lg p-6 flex flex-col justify-start">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Kelas Belum Absensi</h3>
            <div class="overflow-y-auto max-h-32">
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

    <div class="grid grid-cols-3 gap-4 mt-8">
    <div class="bg-white p-6 rounded-lg shadow text-center">
        <h2 class="text-lg font-semibold text-gray-700">Izin Bulan Ini</h2>
        <p class="text-3xl font-bold"><?php echo $rekap['Izin']; ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow text-center">
        <h2 class="text-lg font-semibold text-gray-700">Sakit Bulan Ini</h2>
        <p class="text-3xl font-bold"><?php echo $rekap['Sakit']; ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow text-center">
        <h2 class="text-lg font-semibold text-gray-700">Alpha Bulan Ini</h2>
        <p class="text-3xl font-bold"><?php echo $rekap['Alpha']; ?></p>   
    </div>
</div>

