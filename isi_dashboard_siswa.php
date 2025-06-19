<?php
require '../koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];

// Ambil kelas_id user (siswa)
$queryUser = $conn->prepare("SELECT kelas_id FROM users WHERE id = ? AND role = 'siswa'");
$queryUser->bind_param("i", $user_id);
$queryUser->execute();
$resultUser = $queryUser->get_result();
$userData = $resultUser->fetch_assoc();

$kelas_id = $userData['kelas_id'] ?? null;

$totalSiswaKelas = 0;
$jadwalHariIni = [];

// Jika kelas_id valid
if ($kelas_id) {
    // Hitung total siswa di kelas ini
    $queryTotalSiswa = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE kelas_id = ? AND role = 'siswa'");
    $queryTotalSiswa->bind_param("i", $kelas_id);
    $queryTotalSiswa->execute();
    $resultSiswa = $queryTotalSiswa->get_result();
    $totalSiswaKelas = $resultSiswa->fetch_assoc()['total'];

    // Ambil jadwal hari ini berdasarkan kelas
    $hariIni = date("l"); // Contoh: Monday
    $mapHari = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
    $hariIndonesia = $mapHari[$hariIni] ?? '';

    $queryJadwal = $conn->prepare("SELECT jadwal.jam_mulai, jadwal.jam_selesai, mapel.nama_mapel 
                                   FROM jadwal 
                                   INNER JOIN mapel ON jadwal.mapel_id = mapel.id 
                                   WHERE jadwal.kelas_id = ? AND jadwal.hari = ?
                                   ORDER BY jam_mulai ASC");
    $queryJadwal->bind_param("is", $kelas_id, $hariIndonesia);
    $queryJadwal->execute();
    $resultJadwal = $queryJadwal->get_result();

    while ($row = $resultJadwal->fetch_assoc()) {
        $jadwalHariIni[] = $row;
    }
}
?>

<!-- Card Tampilan -->
<div class="ml-[226px] pt-10 pl-8 grid grid-cols-1 md:grid-cols-2 gap-4 mt-10">

  <!-- Card Total Siswa -->
  <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-indigo-500 h-48 flex flex-col justify-between">
    <h3 class="text-xl font-bold text-gray-800">Total Teman Sekelas</h3>
    <p class="mt-2 text-4xl font-semibold text-gray-700">
      <?= $totalSiswaKelas ?> <span class="text-sm font-normal">siswa</span>
    </p>

  </div>


  <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-purple-500 h-48 overflow-y-auto">
    <h3 class="text-xl font-bold text-gray-800 mb-2">Jadwal Hari Ini</h3>
    <?php if (!empty($jadwalHariIni)): ?>
      <ul class="text-sm text-gray-700 space-y-1">
        <?php foreach ($jadwalHariIni as $jadwal): ?>
          <li>
            <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?> :
            <span class="font-semibold"><?= htmlspecialchars($jadwal['nama_mapel']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-gray-500 text-sm">Tidak ada jadwal hari ini</p>
    <?php endif; ?>
  </div>

</div>
