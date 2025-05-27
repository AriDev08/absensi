<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($conn === false) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}


$stmt_kelas = $conn->query("SELECT * FROM kelas");
if ($stmt_kelas === false) {
    die("Error saat mengambil data kelas: " . $conn->error);
}
$kelas_list = $stmt_kelas->fetch_all(MYSQLI_ASSOC);


$stmt_jurusan = $conn->query("SELECT * FROM jurusan");
if ($stmt_jurusan === false) {
    die("Error saat mengambil data jurusan: " . $conn->error);
}
$jurusan_list = $stmt_jurusan->fetch_all(MYSQLI_ASSOC);

$sql_rfid = mysqli_query($conn, "SELECT * FROM rfid LIMIT 1");
$data_rfid = mysqli_fetch_array($sql_rfid);
$nokartu = $data_rfid ? $data_rfid['nokartu'] : '';

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nis = trim($_POST["nis"]);
    $nama = trim($_POST["nama"]);
    $kelas_id = $_POST["kelas_id"];
    $absen = trim($_POST["absen"]);
    $jurusan_id = $_POST["jurusan"]; 
    $nokartu_post = trim($_POST["nokartu"]);

    if (empty($nis) || empty($nama) || empty($absen) || empty($jurusan_id) || empty($nokartu_post)) {
        $error = "Semua field wajib diisi!";
    } else {
        $checkQuery = "SELECT nis FROM siswa WHERE nis = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $nis);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "NIS sudah terdaftar!";
        } else {
            $cek_kartu = $conn->prepare("SELECT * FROM siswa WHERE nokartu = ?");
            $cek_kartu->bind_param("s", $nokartu_post);
            $cek_kartu->execute();
            $res_kartu = $cek_kartu->get_result();
            if ($res_kartu->num_rows > 0) {
                $error = "Kartu sudah digunakan oleh siswa lain!";
            } else {
                $query = "INSERT INTO siswa (nis, nama, kelas_id, absen, jurusan_id, nokartu) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssss", $nis, $nama, $kelas_id, $absen, $jurusan_id, $nokartu_post);

                if ($stmt->execute()) {
                    // Kosongkan kartu
                    $conn->query("UPDATE rfid SET nokartu = ''");
                    
                    // --- Tambahan: Daftarkan siswa sebagai user ---
                    // Username: NIS, Password: NIS (sesuai preferensi), Role: siswa
                    $passwordHash = password_hash($nis, PASSWORD_DEFAULT);
                    $insertUser = $conn->prepare("
                    INSERT INTO users (username, no_wa, password, role, jabatan_id)
                    VALUES (?, '', ?, 'siswa', NULL)
                ");
                $insertUser->bind_param("ss", $nis, $passwordHash);
                    // -------------------------------------------------
                    
                    $success = "Data siswa berhasil disimpan, kartu terdaftar, dan akun siswa dibuat.";
                    $nokartu = '';
                }
                 else {
                    $error = "Terjadi kesalahan: " . $stmt->error;
                }
            }
        }
    }
}
?>


<script type="text/javascript">
    $(document).ready(function(){
        setInterval(function(){
            $("#nokartu").load('nokartu.php');
        }, 1000);
    });
</script>
    

<?php if (isset($success)) { ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $success ?>',
        showConfirmButton: false,
        timer: 2000
    });
</script>
<?php } elseif (isset($error)) { ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= $error ?>',
        showConfirmButton: true
    });
</script>
<?php } ?>


<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-[#001F3F] text-white p-3 rounded-md">Input Siswa</h2>

    <form action="" method="POST" class="space-y-4">
        <div id="nokartu"></div>

        <div>
            <label class="block font-medium">NIS:</label>
            <input type="text" name="nis" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Nama:</label>
            <input type="text" name="nama" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Kelas:</label>
            <select name="kelas_id" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Kelas</option>
                <?php foreach ($kelas_list as $kelas) { ?>
                    <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">Absen:</label>
            <input type="text" name="absen" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Jurusan:</label>
            <select name="jurusan" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Jurusan</option>
                <?php foreach ($jurusan_list as $jurusan) { ?>
                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                <?php } ?>
            </select>
        </div>

        <input type="hidden" name="nokartu" value="<?= htmlspecialchars($nokartu) ?>">

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
        </div>
    </form>
</div>
