<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID user tidak valid.");
}

$id = $_GET['id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User tidak ditemukan.");
}

$user = $result->fetch_assoc();

$stmt_kelas = $conn->query("SELECT * FROM kelas");
$kelas_list = $stmt_kelas->fetch_all(MYSQLI_ASSOC);

$stmt_jabatan = $conn->query("SELECT * FROM jabatan");
$jabatan_list = $stmt_jabatan->fetch_all(MYSQLI_ASSOC);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $role = $_POST["role"];
    $kelas_id = isset($_POST["kelas_id"]) && $_POST["kelas_id"] !== "" ? $_POST["kelas_id"] : NULL;
    $jabatan_id = isset($_POST["jabatan_id"]) && $_POST["jabatan_id"] !== "" ? $_POST["jabatan_id"] : NULL;
    $no_wa = isset($_POST["no_wa"]) ? trim($_POST["no_wa"]) : null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid!";
    } elseif (($role === 'guru' || $role === 'admin') && empty($no_wa)) {
        $error = "Nomor WhatsApp wajib diisi untuk guru atau admin!";
    } else {
        $query = "UPDATE users SET username = ?, email = ?, role = ?, kelas_id = ?, jabatan_id = ?, no_wa = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssisi", $username, $email, $role, $kelas_id, $jabatan_id, $no_wa, $id);

        if ($stmt->execute()) {
            $success = "Data user berhasil diperbarui!";
        } else {
            $error = "Terjadi kesalahan: " . $stmt->error;
        }
    }
}
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($success): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= $success ?>',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = 'admin.php?page=tampil_user';
    });
</script>
<?php elseif ($error): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '<?= $error ?>'
    });
</script>
<?php endif; ?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-gray-800 text-white p-3 rounded-md">Edit User</h2>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Role:</label>
            <select name="role" class="w-full p-2 border rounded-md" required>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="siswa" <?= $user['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
            </select>
        </div>

        <div id="kelasField">
            <label class="block font-medium">Kelas:</label>
            <select name="kelas_id" class="w-full p-2 border rounded-md">
                <option value="">Pilih Kelas</option>
                <?php foreach ($kelas_list as $kelas) { ?>
                    <option value="<?= $kelas['id'] ?>" <?= $kelas['id'] == $user['kelas_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">Jabatan:</label>
            <select name="jabatan_id" class="w-full p-2 border rounded-md">
                <option value="">-----</option>
                <?php foreach ($jabatan_list as $jabatan) { ?>
                    <option value="<?= $jabatan['id'] ?>" <?= $jabatan['id'] == $user['jabatan_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label class="block font-medium">No WhatsApp (62...):</label>
            <input type="text" name="no_wa" value="<?= htmlspecialchars($user['no_wa']) ?>" class="w-full p-2 border rounded-md" placeholder="Contoh: 6281234567890">
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update</button>
        </div>
    </form>
</div>
