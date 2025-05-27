<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($conn === false) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$stmt_jabatan = $conn->query("SELECT * FROM jabatan");
if ($stmt_jabatan === false) {
    die("Error saat mengambil data jabatan: " . $conn->error);
}

$jabatan_list = $stmt_jabatan->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $no_wa = trim($_POST["no_wa"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];
    $jabatan_id = $_POST["jabatan_id"];

    if (empty($username) || empty($email) || empty($no_wa) || empty($password) || empty($role) || empty($jabatan_id)) {
        $error = "Semua field wajib diisi!";
    } else {
        $checkQuery = "SELECT username FROM users WHERE username = ?";
        $stmt = $conn->prepare($checkQuery);
        if ($stmt === false) {
            die('Error saat menyiapkan kueri: ' . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, no_wa, password, role, jabatan_id) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die('Error saat menyiapkan kueri: ' . $conn->error);
            }
            $stmt->bind_param("sssssi", $username, $email, $no_wa, $passwordHash, $role, $jabatan_id);

            if ($stmt->execute()) {
                $success = "Data user berhasil disimpan.";
            } else {
                $error = "Terjadi kesalahan: " . $stmt->error;
            }
        }
    }
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-[#001F3F] text-white p-3 rounded-md">Input User</h2>

    <?php if (isset($error)) echo "<p class='text-red-500 text-center'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='text-green-500 text-center'>$success</p>"; ?>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Username:</label>
            <input type="text" name="username" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Gmail:</label>
            <input type="email" name="email" class="w-full p-2 border rounded-md" required placeholder="contoh@gmail.com">
        </div>

        <div>
            <label class="block font-medium">No WhatsApp (62...):</label>
            <input type="text" name="no_wa" class="w-full p-2 border rounded-md" required placeholder="Contoh: 6281234567890">
        </div>

        <div>
            <label class="block font-medium">Password:</label>
            <input type="password" name="password" class="w-full p-2 border rounded-md" required>
        </div>

        <div>
            <label class="block font-medium">Role:</label>
            <select name="role" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
            </select>
        </div>

        <div>
            <label class="block font-medium">Jabatan:</label>
            <select name="jabatan_id" class="w-full p-2 border rounded-md" required>
                <option value="">Pilih Jabatan</option>
                <?php foreach ($jabatan_list as $jabatan) { ?>
                    <option value="<?= $jabatan['id'] ?>"><?= $jabatan['nama_jabatan'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
        </div>
    </form>
</div>
