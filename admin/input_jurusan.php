<?php
require '../koneksi.php';

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_jurusan = trim($_POST["nama_jurusan"]);
    $singkatan = trim($_POST["singkatan"]);

    if (empty($nama_jurusan) || empty($singkatan)) {
        $error = "Nama jurusan dan singkatan tidak boleh kosong!";
    } else {
        $cek = $conn->prepare("SELECT * FROM jurusan WHERE nama_jurusan = ? OR singkatan = ?");
        $cek->bind_param("ss", $nama_jurusan, $singkatan);
        $cek->execute();
        $res = $cek->get_result();

        if ($res->num_rows > 0) {
            $error = "Jurusan atau singkatan sudah ada!";
        } else {
            $stmt = $conn->prepare("INSERT INTO jurusan (nama_jurusan, singkatan) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama_jurusan, $singkatan);

            if ($stmt->execute()) {
                $success = "Jurusan berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan jurusan: " . $stmt->error;
            }
        }
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($success) { ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $success ?>',
        showConfirmButton: false,
        timer: 2000
    });
</script>
<?php } elseif ($error) { ?>
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
    <h2 class="text-center text-xl font-bold mb-4 bg-[#001F3F] text-white p-3 rounded-md">Tambah Jurusan</h2>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Nama Jurusan:</label>
            <input type="text" name="nama_jurusan" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Singkatan:</label>
            <input type="text" name="singkatan" class="w-full p-2 border rounded-md" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
    </form>
</div>
