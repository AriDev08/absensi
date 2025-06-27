<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../koneksi.php';

$user_id = $_SESSION['user_id']; 

$hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

$filter_hari = isset($_POST['hari']) ? $_POST['hari'] : '';


$query = "SELECT jadwal.*, mapel.nama_mapel, kelas.nama_kelas
          FROM jadwal 
          INNER JOIN mapel ON jadwal.mapel_id = mapel.id
          INNER JOIN kelas ON jadwal.kelas_id = kelas.id
          WHERE jadwal.user_id = ?";

$params = [$user_id];
$types = "i";

if ($filter_hari !== '') {
    $query .= " AND jadwal.hari = ?";
    $params[] = $filter_hari;
    $types .= "s";
}

$query .= " ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jam_mulai";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-20">
    <h2 class="text-center text-2xl font-bold mb-4">Jadwal Mengajar Anda</h2>

    <!-- Filter Hari -->
    <form method="POST" class="mb-6 flex flex-wrap items-end space-x-4">
        <div class="mb-2">
            <label class="block font-medium">Pilih Hari:</label>
            <select name="hari" class="p-2 border rounded-md">
                <option value="">Semua Hari</option>
                <?php foreach ($hariList as $hari): ?>
                    <option value="<?= $hari ?>" <?= ($filter_hari === $hari) ? 'selected' : '' ?>>
                        <?= $hari ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filter</button>
        </div>
    </form>


    <table class="min-w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">Hari</th>
                <th class="border border-gray-300 px-4 py-2">Jam</th>
                <th class="border border-gray-300 px-4 py-2">Mata Pelajaran</th>
                <th class="border border-gray-300 px-4 py-2">Kelas</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2"><?= $row['hari'] ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= $row['nama_mapel'] ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= $row['nama_kelas'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada jadwal untuk hari ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
