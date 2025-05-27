<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['nis'])) {
    die("NIS tidak ditemukan.");
}

$nis = $_GET['nis'];
$query = "SELECT siswa.*, kelas.angkatan, jurusan.id AS jurusan_id 
          FROM siswa 
          LEFT JOIN kelas ON siswa.kelas_id = kelas.id 
          LEFT JOIN jurusan ON siswa.jurusan_id = jurusan.id 
          WHERE siswa.nis = ?";


$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

$kelasQuery = "SELECT id, singkatan, angkatan FROM kelas";
$kelasResult = $conn->query($kelasQuery);

$jurusanQuery = "SELECT * FROM jurusan";
$jurusanResult = $conn->query($jurusanQuery);

$success = false;
$error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $kelas_id = intval($_POST['kelas_id']);
    $absen = trim($_POST['absen']);
    $jurusan = intval($_POST['jurusan']);

 
    if (empty($nama) || empty($kelas_id) || empty($absen) || empty($jurusan)) {
        $error = true;
        $error_message = "Semua field harus diisi.";
    } else {
        $updateQuery = "UPDATE siswa SET nama = ?, kelas_id = ?, absen = ?, jurusan_id = ? WHERE nis = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sisss", $nama, $kelas_id, $absen, $jurusan, $nis);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = true;
            $error_message = $stmt->error;
        }
    }
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-gray-800 text-white p-3 rounded-md">Edit Data Siswa</h2>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Nama:</label>
            <input type="text" name="nama" value="<?= isset($siswa['nama']) ? htmlspecialchars($siswa['nama']) : '' ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Kelas:</label>
            <select name="kelas_id" id="kelas_id" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Kelas</option>
                <?php while ($kelas = $kelasResult->fetch_assoc()): ?>
                    <option value="<?= $kelas['id'] ?>" data-angkatan="<?= $kelas['angkatan'] ?>" <?= $siswa['kelas_id'] == $kelas['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kelas['singkatan']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">Angkatan:</label>
            <input type="text" id="angkatan" value="<?= isset($siswa['angkatan']) ? htmlspecialchars($siswa['angkatan']) : '' ?>" class="w-full p-2 border rounded-md bg-gray-100" readonly>
        </div>

        <div>
            <label class="block font-medium">Absen:</label>
            <input type="text" name="absen" value="<?= isset($siswa['absen']) ? htmlspecialchars($siswa['absen']) : '' ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Jurusan:</label>
            <select name="jurusan" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Jurusan</option>
                <?php while ($jurusan = $jurusanResult->fetch_assoc()): ?>
                    <option value="<?= $jurusan['id'] ?>" <?= $siswa['jurusan_id'] == $jurusan['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jurusan['nama_jurusan']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
            <a href="?page=tampil_siswa" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Batal</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('kelas_id').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        document.getElementById('angkatan').value = selectedOption.getAttribute('data-angkatan');
    });

    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data siswa berhasil diperbarui!',
            confirmButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?page=tampil_siswa";
            }
        });
    <?php elseif ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $error_message ?>',
            confirmButtonColor: '#d33'
        });
    <?php endif; ?>
</script>
