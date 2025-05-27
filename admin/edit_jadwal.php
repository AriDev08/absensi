<?php
include '../koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan!");
}

$query = "SELECT * FROM jadwal WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$jadwal = $result->fetch_assoc();

if (!$jadwal) {
    die("Jadwal tidak ditemukan!");
}

$mapel = $conn->query("SELECT * FROM mapel");
$guru = $conn->query("SELECT * FROM users");
$kelas = $conn->query("SELECT * FROM kelas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $mapel_id = $_POST['mapel_id'];
    $user_id = $_POST['user_id'];
    $kelas_id = $_POST['kelas_id'];

    $updateQuery = "UPDATE jadwal SET hari=?, jam_mulai=?, jam_selesai=?, mapel_id=?, user_id=?, kelas_id=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssiii", $hari, $jam_mulai, $jam_selesai, $mapel_id, $user_id, $kelas_id, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil diperbarui!'); window.location='?page=tampil_jadwal';</script>";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-gray-800 text-white p-3 rounded-md">Edit Jadwal</h2>

    <?php if (isset($error)) : ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '<?= $error ?>'
            });
        </script>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Hari:</label>
            <input type="text" name="hari" value="<?= htmlspecialchars($jadwal['hari']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Jam Mulai:</label>
            <input type="time" name="jam_mulai" value="<?= htmlspecialchars($jadwal['jam_mulai']) ?>" class="w-full p-2 border rounded-md" required>
        </div>
        
        <div>
            <label class="block font-medium">Jam Selesai:</label>
            <input type="time" name="jam_selesai" value="<?= htmlspecialchars($jadwal['jam_selesai']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Mata Pelajaran:</label>
            <select name="mapel_id" class="w-full p-2 border rounded-md" required>
                <?php while ($row = $mapel->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $jadwal['mapel_id'] ? 'selected' : '' ?>>
                        <?= $row['nama_mapel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">Guru:</label>
            <select name="user_id" class="w-full p-2 border rounded-md" required>
                <?php while ($row = $guru->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $jadwal['user_id'] ? 'selected' : '' ?>>
                        <?= $row['username'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">Kelas:</label>
            <select name="kelas_id" class="w-full p-2 border rounded-md" required>
                <?php while ($row = $kelas->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $jadwal['kelas_id'] ? 'selected' : '' ?>>
                        <?= $row['nama_kelas'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update</button>
        </div>
    </form>
</div>
