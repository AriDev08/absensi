<?php
require '../koneksi.php';
if (session_status() == PHP_SESSION_NONE) session_start();


$kelas_list = [];
$res_k = $conn->query("SELECT * FROM kelas ORDER BY singkatan");
while($k = $res_k->fetch_assoc()) {
    $kelas_list[] = $k;
}

$selected_kelas = $_POST['kelas'] ?? '';
$error = '';$success = '';


define('WA_INSTANCE', 'instance113723');
define('WA_TOKEN', '4sd8ktsua5evscgx');
function kirimNotifikasiWA($nomor, $pesan) {
    $url = "https://api.ultramsg.com/instance113723/" . WA_INSTANCE . "/messages/chat";
    $data = [
        'token' => WA_TOKEN,
        'to'    => $nomor,
        'body'  => $pesan
    ];
    $opts = ['http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]];
    return file_get_contents($url, false, stream_context_create($opts));
}
    

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_absensi'])) {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $absensiData = $_POST['absensi'] ?? [];

    if (!empty($absensiData)) {
        $nonHadirs = [];

        foreach ($absensiData as $siswa_id => $status) {
          
            $cekTipe = $conn->prepare("SELECT tipe_id FROM absensi WHERE siswa_id=? AND tanggal=? ORDER BY jam ASC");
            $cekTipe->bind_param("is", $siswa_id, $tanggal);
            $cekTipe->execute();
            $result = $cekTipe->get_result();
            $tipe_ids = array_column($result->fetch_all(MYSQLI_ASSOC), 'tipe_id');

            if ($status === 'hadir') {
                if (!in_array(1, $tipe_ids)) {
                    $tipe_id = 1; 
                } elseif (!in_array(2, $tipe_ids)) {
                    $tipe_id = 2;
                } else {
                    continue; 
                }

                $ist = $conn->prepare(
                    "INSERT INTO absensi (siswa_id, tanggal, status, tipe_id, jam)
                     VALUES (?,?,?,?,NOW())"
                );
                $ist->bind_param("issi", $siswa_id, $tanggal, $status, $tipe_id);
                $ist->execute();

            } else {
               
                if (empty($tipe_ids)) {
                    $tipe_id = 1;
                    $ist = $conn->prepare(
                        "INSERT INTO absensi (siswa_id, tanggal, status, tipe_id, jam)
                         VALUES (?,?,?,?,NOW())"
                    );
                    $ist->bind_param("issi", $siswa_id, $tanggal, $status, $tipe_id);
                    $ist->execute();
                }

                $pst = $conn->prepare("SELECT nama FROM siswa WHERE id=?");
                $pst->bind_param("i", $siswa_id);
                $pst->execute();
                $nama = $pst->get_result()->fetch_assoc()['nama'];
                $nonHadirs[] = "$nama ($status)";
            }
        }

     
        if ($nonHadirs) {
            $q = $conn->prepare(
                "SELECT u.no_wa, k.singkatan FROM users u
                 JOIN jabatan j ON u.jabatan_id=j.id
                 JOIN kelas k ON k.id=?
                 WHERE j.nama_jabatan='Wali Kelas' LIMIT 1"
            );
            $q->bind_param("i", $selected_kelas);
            $q->execute();
            $r = $q->get_result()->fetch_assoc();
            if (!empty($r['no_wa'])) {
                $msg = "Absensi Kelas {$r['singkatan']} - {$tanggal}:\n" . implode("\n", $nonHadirs);
                kirimNotifikasiWA($r['no_wa'], $msg);
            }
        }

        $success = 'Data absensi berhasil disimpan.';
    } else {
        $error = 'Tidak ada data absensi yang dipilih.';
    }

    $selected_kelas = $_POST['kelas'] ?? '';
}


if ($selected_kelas !== '') {
    $sst = $conn->prepare(
        "SELECT s.id,s.nis,s.nama,k.singkatan
         FROM siswa s JOIN kelas k ON s.kelas_id=k.id
         WHERE k.id=? ORDER BY s.nama"
    );
    $sst->bind_param("i", $selected_kelas);
    $sst->execute();
    $sl = $sst->get_result();
} else {
    $sl = $conn->query(
        "SELECT s.id,s.nis,s.nama,k.singkatan
         FROM siswa s JOIN kelas k ON s.kelas_id=k.id
         ORDER BY k.singkatan, s.nama"
    );
}
$siswa_list = $sl->fetch_all(MYSQLI_ASSOC);
?>


<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <form method="post" class="flex items-end space-x-4 mb-4">
        <div>
            <label class="block font-medium">Pilih Kelas:</label>
            <select name="kelas" class="p-2 border rounded-md">
                <option value="">Semua Kelas</option>
                <?php foreach($kelas_list as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($k['singkatan']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="filter_kelas"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            Filter
        </button>
    </form>
    <?php if($error): ?><p class="text-red-500 text-center"><?= $error ?></p><?php endif; ?>
    <?php if($success): ?><p class="text-green-500 text-center"><?= $success ?></p><?php endif; ?>

    <form method="post">
        <input type="hidden" name="kelas" value="<?= htmlspecialchars($selected_kelas) ?>">
        <div class="mb-4">
            <label class="block font-medium">Tanggal:</label>
            <input type="date" name="tanggal" class="w-full p-2 border rounded-md"
                   value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>" required>
        </div>
        <table class="min-w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">NIS</th>
                    <th class="border px-4 py-2">Nama</th>
                    <th class="border px-4 py-2">Kelas</th>
                    <th class="border px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($siswa_list): foreach($siswa_list as $s): ?>
                <tr>
                    <td class="border px-4 py-2"><?= htmlspecialchars($s['nis']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($s['nama']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($s['singkatan']) ?></td>
                    <td class="border px-4 py-2">
                        <label><input type="radio" name="absensi[<?= $s['id'] ?>]" value="hadir" required> Hadir</label>
                        <label class="ml-2"><input type="radio" name="absensi[<?= $s['id'] ?>]" value="izin"> Izin</label>
                        <label class="ml-2"><input type="radio" name="absensi[<?= $s['id'] ?>]" value="sakit"> Sakit</label>
                        <label class="ml-2"><input type="radio" name="absensi[<?= $s['id'] ?>]" value="alpha"> Alpha</label>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center py-4">Tidak ada siswa.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="flex justify-center mt-4">
            <button type="submit" name="save_absensi" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
              Simpan Absensi
            </button>
        </div>
    </form>
    
</div>
