<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID jurusan tidak valid.");
}

$id = $_GET['id'];

$query = "SELECT * FROM jurusan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Jurusan tidak ditemukan.");
}

$jurusan = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_jurusan = trim($_POST["nama_jurusan"]);
    $singkatan    = trim($_POST["singkatan"]);

    $query = "UPDATE jurusan SET nama_jurusan = ?, singkatan = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $nama_jurusan, $singkatan, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data jurusan berhasil diperbarui!";
        echo "<script>window.location.href='admin.php?page=tampil_jurusan';</script>";
        exit();
    } else {
        $error = "Terjadi kesalahan: " . $stmt->error;
    }
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-gray-800 text-white p-3 rounded-md">Edit Jurusan</h2>

    <?php if (isset($error)) : ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '<?= $error ?>'
            });
        </script>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Nama Jurusan:</label>
            <input type="text" name="nama_jurusan" value="<?= htmlspecialchars($jurusan['nama_jurusan']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Singkatan:</label>
            <input type="text" name="singkatan" value="<?= htmlspecialchars($jurusan['singkatan']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update</button>
        </div>
    </form>
</div>
