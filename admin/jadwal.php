<?php
require '../koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!$conn) die('Koneksi gagal');

// 1. Ambil daftar kelas untuk dropdown
$kelas_list = $conn->query("SELECT * FROM kelas")->fetch_all(MYSQLI_ASSOC);

// 2. Baca filter dari form
$selected_kelas   = $_POST['kelas']   ?? '';
$selected_tanggal = $_POST['tanggal'] ?? date('Y-m-d');

// 3. Tentukan hari bahasa Indonesia
$hariMap = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu',
];
$phpDay = date('l', strtotime($selected_tanggal));
$hari   = $hariMap[$phpDay] ?? '';

// 4. Query jadwal untuk dapatkan jam_mulai_kelas (fallback 07:30)
$jam_mulai_kelas = '07:30:00';
if ($selected_kelas && $hari) {
    $stmt2 = $conn->prepare("
        SELECT MIN(jam_mulai) AS jam_mulai
        FROM jadwal
        WHERE kelas_id = ? AND hari = ?
    ");
    $stmt2->bind_param('is', $selected_kelas, $hari);
    $stmt2->execute();
    $res2 = $stmt2->get_result()->fetch_assoc();
    if (!empty($res2['jam_mulai'])) {
        // Pastikan format HH:MM:SS
        $jam_mulai_kelas = date('H:i:s', strtotime($res2['jam_mulai']));
    }
    $stmt2->close();
}

// 5. Siapkan query rekap absensi
$sql = "
SELECT
    s.id             AS siswa_id,
    s.nis,
    s.nama,
    k.singkatan,
    (SELECT status FROM absensi WHERE siswa_id=s.id AND tanggal=? AND tipe_id=1 LIMIT 1) AS status_masuk,
    (SELECT jam    FROM absensi WHERE siswa_id=s.id AND tanggal=? AND tipe_id=1 LIMIT 1) AS jam_masuk,
    (SELECT status FROM absensi WHERE siswa_id=s.id AND tanggal=? AND tipe_id=2 LIMIT 1) AS status_pulang,
    (SELECT jam    FROM absensi WHERE siswa_id=s.id AND tanggal=? AND tipe_id=2 LIMIT 1) AS jam_pulang
FROM siswa s
LEFT JOIN kelas k ON s.kelas_id = k.id
WHERE 1
";
$params = [
    $selected_tanggal,
    $selected_tanggal,
    $selected_tanggal,
    $selected_tanggal
];
$types = 'ssss';

if ($selected_kelas !== '') {
    $sql .= " AND s.kelas_id = ?";
    $types .= 'i';
    $params[] = $selected_kelas;
}

$sql .= " ORDER BY k.singkatan, s.nama";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// 6. Hitung kartu status & bangun dataList
$cardData = ['hadir'=>0,'terlambat'=>0,'izin'=>0,'sakit'=>0,'alpha'=>0];
$dataList = [];

while ($row = $result->fetch_assoc()) {
    $status   = strtolower($row['status_masuk'] ?? '');
    $jamMasuk = $row['jam_masuk'];

    // 6a. Lateness check against dynamic jam_mulai_kelas
    if ($status === 'hadir'
        && $jamMasuk
        && strtotime($jamMasuk) > strtotime($jam_mulai_kelas)
    ) {
        $cardData['terlambat']++;
        $row['status_masuk'] = 'Terlambat';
    }
    elseif (isset($cardData[$status])) {
        $cardData[$status]++;
    }

    // 6b. Untuk izin/sakit/alpha, override jam tampil
    if (in_array($status, ['izin','sakit','alpha'])) {
        $row['jam_masuk']    = '-';
        $row['jam_pulang']   = '-';
        $row['status_pulang']= ucfirst($status);
    }

    $dataList[] = $row;
}

$stmt->close();
?>

<!-- ====== TAMPILAN HTML ====== -->
<div class="shadow-lg rounded-lg bg-white p-6 w-[83%] h-auto ml-64 mt-20">
  <h2 class="text-center text-2xl font-bold mb-2">
    Rekap Absensi - <?= htmlspecialchars($selected_tanggal) ?>
  </h2>
  <p class="text-center text-sm text-gray-600 mb-4">
    Jam Mulai Kelas (<?= htmlspecialchars($hari) ?>):
    <?= date('H:i', strtotime($jam_mulai_kelas)) ?>
  </p>

  <!-- Ringkasan kartu -->
  <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <?php
    $labels = ['hadir'=>'Hadir','terlambat'=>'Terlambat','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha'];
    $colors = ['hadir'=>'blue','terlambat'=>'indigo','izin'=>'green','sakit'=>'yellow','alpha'=>'red'];
    foreach ($labels as $key=>$label): ?>
      <div class="bg-<?= $colors[$key] ?>-100 p-4 rounded-lg shadow-md h-32 flex flex-col justify-center">
        <h3 class="text-xl font-bold text-<?= $colors[$key] ?>-800 text-center"><?= $label ?></h3>
        <p class="text-3xl font-semibold text-<?= $colors[$key] ?>-900 text-center"><?= $cardData[$key] ?></p>
        <p class="text-sm text-<?= $colors[$key] ?>-700 text-center">Siswa</p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Filter form -->
  <form method="post" class="mb-6 flex flex-wrap items-end space-x-4">
    <div>
      <label class="block font-medium">Pilih Kelas:</label>
      <select name="kelas" class="p-2 border rounded-md">
        <option value="">Semua Kelas</option>
        <?php foreach ($kelas_list as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $selected_kelas==$k['id']?'selected':''?>>
            <?= htmlspecialchars($k['singkatan']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block font-medium">Tanggal:</label>
      <input type="date" name="tanggal" class="p-2 border rounded-md"
             value="<?= htmlspecialchars($selected_tanggal) ?>">
    </div>
    <button type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
      Filter
    </button>
  </form>

  <!-- Export Excel -->
  <form method="post" action="export_excel.php" class="mb-4">
    <input type="hidden" name="kelas"   value="<?= htmlspecialchars($selected_kelas) ?>">
    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($selected_tanggal) ?>">
    <button type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
      Export ke Excel
    </button>
  </form>

  <!-- Tabel data -->
  <table class="min-w-full border-collapse border border-gray-300">
    <thead>
      <tr class="bg-gray-200 text-left">
        <th class="border px-4 py-2">NIS</th>
        <th class="border px-4 py-2">Nama</th>
        <th class="border px-4 py-2">Kelas</th>
        <th class="border px-4 py-2">Masuk</th>
        <th class="border px-4 py-2">Pulang</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($dataList): ?>
        <?php foreach ($dataList as $r): ?>
          <tr>
            <td class="border px-4 py-2"><?= htmlspecialchars($r['nis']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($r['nama']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($r['singkatan']) ?></td>
            <td class="border px-4 py-2">
              <?php
                if ($r['status_masuk']) {
                  $jm = $r['jam_masuk'] !== '-' ? date('H.i', strtotime($r['jam_masuk'])) : '-';
                  echo ucfirst($r['status_masuk']) . ' - ' . $jm;
                } else {
                  echo '-';
                }
              ?>
            </td>
            <td class="border px-4 py-2">
              <?php
                if ($r['status_pulang']) {
                  $jp = $r['jam_pulang'] !== '-' ? date('H.i', strtotime($r['jam_pulang'])) : '-';
                  echo ucfirst($r['status_pulang']) . ' - ' . $jp;
                } else {
                  echo '-';
                }
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center py-4">Tidak ada data untuk filter yang dipilih.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
