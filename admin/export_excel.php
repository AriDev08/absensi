<?php
require '../koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!$conn) die('Koneksi gagal');

$selected_kelas = $_POST['kelas'] ?? '';
$selected_tanggal = $_POST['tanggal'] ?? date('Y-m-d');
$judul = "Rekap Absensi Tanggal $selected_tanggal";
$nama_file = "jadwal-absensi-$selected_tanggal.xls";


$sql = "
SELECT s.id as siswa_id, s.nis, s.nama, k.singkatan,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 1 LIMIT 1) AS status_masuk,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 2 LIMIT 1) AS status_pulang
FROM siswa s
LEFT JOIN kelas k ON s.kelas_id = k.id
WHERE 1";
$params = [$selected_tanggal, $selected_tanggal];
$types  = 'ss';

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
    $siswaRow = $row;

    if ($status === 'hadir') {

        $cardData['hadir']++;
    } elseif (isset($cardData[$status])) {
        $cardData[$status]++;
    }


    if (in_array($status, ['izin', 'sakit', 'alpha'])) {
        $siswaRow['status_pulang'] = ucfirst($status);
    }

    $dataList[] = $siswaRow;
}


header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$nama_file");
?>

<h2 style="text-align: center;"><?= $judul ?></h2>

<table border="1" cellpadding="8" cellspacing="0" style="margin-bottom: 20px;">
    <tr style="background-color: #DFF0D8;">
        <th>Hadir</th>
        <th>Izin</th>
        <th>Sakit</th>
        <th>Alpha</th>
    </tr>
    <tr>
        <td align="center"><?= $cardData['hadir'] ?></td>
        <td align="center"><?= $cardData['izin'] ?></td>
        <td align="center"><?= $cardData['sakit'] ?></td>
        <td align="center"><?= $cardData['alpha'] ?></td>
    </tr>
</table>

<table border="1" cellpadding="8" cellspacing="0">
    <thead style="background-color: #f2f2f2;">
        <tr>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Status Masuk</th>
            <th>Status Pulang</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($dataList) > 0): ?>
            <?php foreach ($dataList as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nis']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['singkatan']) ?></td>
                    <td><?= $row['status_masuk'] ?? '-' ?></td>
                    <td><?= $row['status_pulang'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" align="center">Tidak ada data absensi.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
