<?php
require '../koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!$conn) die('Koneksi gagal');


$kelas_list = $conn->query("SELECT * FROM kelas")->fetch_all(MYSQLI_ASSOC);
$selected_kelas = $_POST['kelas'] ?? '';
$selected_tanggal = $_POST['tanggal'] ?? date('Y-m-d');


$sql = "
SELECT s.id as siswa_id, s.nis, s.nama, k.singkatan,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 1 LIMIT 1) AS status_masuk,
    (SELECT jam FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 1 LIMIT 1) AS jam_masuk,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 2 LIMIT 1) AS status_pulang,
    (SELECT jam FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 2 LIMIT 1) AS jam_pulang
FROM siswa s
LEFT JOIN kelas k ON s.kelas_id = k.id
WHERE 1";

$params = [$selected_tanggal, $selected_tanggal, $selected_tanggal, $selected_tanggal];
$types  = 'ssss';

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


$cardData = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'terlambat' => 0];


$dataList = [];
while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['status_masuk'] ?? '');
    $jamMasuk = $row['jam_masuk'];
    $siswaRow = $row;

 
    if ($status === 'hadir' && $jamMasuk && strtotime($jamMasuk) > strtotime('07:30:00')) {
        $cardData['terlambat']++;
        $siswaRow['status_masuk'] = 'Terlambat';
    } elseif (isset($cardData[$status])) {
        $cardData[$status]++;
    }

   
    if (in_array($status, ['izin', 'sakit', 'alpha'])) {
        $siswaRow['status_pulang'] = ucfirst($status);
        $siswaRow['jam_masuk'] = '-';
        $siswaRow['jam_pulang'] = '-';
    }

    $dataList[] = $siswaRow;
}
?>

<div class="shadow-lg rounded-lg bg-white p-6 w-[83%] h-auto ml-64 mt-20">
    <h2 class="text-center text-2xl font-bold mb-4">Rekap Absensi - <?= htmlspecialchars($selected_tanggal) ?></h2>

   
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <?php
        $status_labels = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
        $colors = ['hadir' => 'blue', 'terlambat' => 'indigo', 'izin' => 'green', 'sakit' => 'yellow', 'alpha' => 'red'];
        foreach ($status_labels as $key => $label):
        ?>
        <div class="bg-<?= $colors[$key] ?>-100 p-4 rounded-lg shadow-md h-32 flex flex-col justify-center">
            <h3 class="text-xl font-bold text-<?= $colors[$key] ?>-800 text-center"><?= $label ?></h3>
            <p class="text-3xl font-semibold text-<?= $colors[$key] ?>-900 text-center"><?= $cardData[$key] ?></p>
            <p class="text-sm text-<?= $colors[$key] ?>-700 text-center">Siswa</p>
        </div>
        <?php endforeach; ?>
    </div>

   
    <form method="post" action="" class="mb-6 flex flex-wrap items-end space-x-4">
        <div>
            <label class="block font-medium">Pilih Kelas:</label>
            <select name="kelas" class="p-2 border rounded-md">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelas_list as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['singkatan']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block font-medium">Tanggal:</label>
            <input type="date" name="tanggal" class="p-2 border rounded-md" value="<?= htmlspecialchars($selected_tanggal) ?>">
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filter</button>
        </div>
    </form>


    <form method="post" action="export_excel.php" class="mb-4">
        <input type="hidden" name="kelas" value="<?= htmlspecialchars($selected_kelas) ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($selected_tanggal) ?>">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            Export ke Excel
        </button>
    </form>

 
    <table class="min-w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="border border-gray-300 px-4 py-2">NIS</th>
                <th class="border border-gray-300 px-4 py-2">Nama</th>
                <th class="border border-gray-300 px-4 py-2">Kelas</th>
                <th class="border border-gray-300 px-4 py-2">Masuk</th>
                <th class="border border-gray-300 px-4 py-2">Pulang</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($dataList)): ?>
                <?php foreach ($dataList as $row): ?>
                <tr>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['nis']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['singkatan']) ?></td>
                    <td class="border px-4 py-2">
                        <?php
                        if ($row['status_masuk']) {
                            echo ($row['jam_masuk'] !== '-') 
                                ? ucfirst($row['status_masuk']) . ' - ' . date('H.i', strtotime($row['jam_masuk'])) 
                                : ucfirst($row['status_masuk']) . ' - -';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="border px-4 py-2">
                        <?php
                        if ($row['status_pulang']) {
                            echo ($row['jam_pulang'] !== '-') 
                                ? ucfirst($row['status_pulang']) . ' - ' . date('H.i', strtotime($row['jam_pulang'])) 
                                : ucfirst($row['status_pulang']) . ' - -';
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
