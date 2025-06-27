<?php
require '../koneksi.php';

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$selected_tanggal = date('Y-m-d');
$selected_kelas   = '';
$selected_siswa   = null;

if ($_SESSION['role'] === 'siswa') {
    $nis = $_SESSION['user_id'];
    $stmtS = $conn->prepare("SELECT id, kelas_id FROM siswa WHERE nis = ? LIMIT 1");
    $stmtS->bind_param('s', $nis);
    $stmtS->execute();
    $me = $stmtS->get_result()->fetch_assoc();
    if ($me) {
        $selected_siswa = $me['id'];
        $selected_kelas = $me['kelas_id'];
    }
}

$sql = "
SELECT
    s.id AS siswa_id,
    s.nis,
    s.nama,
    k.singkatan,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 1 LIMIT 1) AS status_masuk,
    (SELECT jam    FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 1 LIMIT 1) AS jam_masuk,
    (SELECT status FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 2 LIMIT 1) AS status_pulang,
    (SELECT jam    FROM absensi WHERE siswa_id = s.id AND tanggal = ? AND tipe_id = 2 LIMIT 1) AS jam_pulang
FROM siswa s
LEFT JOIN kelas k ON s.kelas_id = k.id
WHERE 1
";
$params = [$selected_tanggal, $selected_tanggal, $selected_tanggal, $selected_tanggal];
$types  = 'ssss';

if ($_SESSION['role'] === 'siswa') {
    $sql .= " AND s.id = ?";
    $types .= 'i';
    $params[] = $selected_siswa;
}

$sql .= " ORDER BY k.singkatan, s.nama";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$cardData = ['hadir'=>0,'terlambat'=>0,'izin'=>0,'sakit'=>0,'alpha'=>0];
$dataList = [];
while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['status_masuk'] ?? '');
    $jam    = $row['jam_masuk'];

    if ($status === 'hadir' && $jam && strtotime($jam) > strtotime('07:30:00')) {
        $cardData['terlambat']++;
        $row['status_masuk'] = 'Terlambat';
    } elseif (isset($cardData[$status])) {
        $cardData[$status]++;
    }

    if (in_array($status, ['izin','sakit','alpha'])) {
        $row['jam_masuk'] = '-';
        $row['jam_pulang'] = '-';
        $row['status_pulang'] = ucfirst($status);
    }

    $dataList[] = $row;
}
?>

<div class="shadow-lg rounded-lg bg-white p-6 w-[83%] ml-64 mt-20">
    <h2 class="text-center text-2xl font-bold mb-4">Rekap Absensi - <?= htmlspecialchars($selected_tanggal) ?></h2>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <?php
        $labels = ['hadir'=>'Hadir','terlambat'=>'Terlambat','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha'];
        $colors = ['hadir'=>'blue','terlambat'=>'indigo','izin'=>'green','sakit'=>'yellow','alpha'=>'red'];
        foreach ($labels as $key=>$lbl): ?>
        <div class="bg-<?= $colors[$key] ?>-100 p-4 rounded-lg shadow-md h-32 flex flex-col justify-center">
            <h3 class="text-xl font-bold text-<?= $colors[$key] ?>-800 text-center"><?= $lbl ?></h3>
            <p class="text-3xl font-semibold text-<?= $colors[$key] ?>-900 text-center"><?= $cardData[$key] ?></p>
            <p class="text-sm text-<?= $colors[$key] ?>-700 text-center">Siswa</p>
        </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="export_excel.php" class="mb-4">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($selected_tanggal) ?>">
        <?php if ($_SESSION['role'] !== 'siswa'): ?>
        <input type="hidden" name="kelas" value="">
        <?php endif; ?>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Export ke Excel</button>
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
            <?php if ($dataList): foreach($dataList as $r): ?>
            <tr>
                <td class="border px-4 py-2"><?= htmlspecialchars($r['nis']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($r['nama']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($r['singkatan']) ?></td>
                <td class="border px-4 py-2">
                    <?php
                    if ($r['status_masuk']):
                        echo ucfirst($r['status_masuk']) . ' - ' .
                             ($r['jam_masuk']!=='-' ? date('H.i',strtotime($r['jam_masuk'])) : '-');
                    else:
                        echo '-';
                    endif;
                    ?>
                </td>
                <td class="border px-4 py-2">
                    <?php
                    if (!empty($r['status_pulang'])):
                        echo ucfirst($r['status_pulang']) . ' - ' .
                             ($r['jam_pulang']!=='-' ? date('H.i',strtotime($r['jam_pulang'])) : '-');
                    else:
                        echo '-';
                    endif;
                    ?>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="5" class="text-center py-4">Tidak ada data absensi.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
