<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($conn === false) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kelas = trim($_POST["nama_kelas"]);
    $singkatan = trim($_POST["singkatan"]);
    $angkatan = trim($_POST["angkatan"]);
    
    if (empty($nama_kelas) || empty($singkatan) || empty($angkatan)) {
        $error = "Semua field wajib diisi!";
    } else {

        $checkQuery = "SELECT nama_kelas FROM kelas WHERE nama_kelas = ? AND angkatan = ?";
        $stmt = $conn->prepare($checkQuery);
        
        if ($stmt === false) {
            die('Error saat menyiapkan kueri: ' . $conn->error);
        }

        $stmt->bind_param("ss", $nama_kelas, $angkatan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Kelas dengan angkatan ini sudah terdaftar!";
        } else {

            $query = "INSERT INTO kelas (nama_kelas, singkatan, angkatan) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                die('Error saat menyiapkan kueri: ' . $conn->error);
            }

            $stmt->bind_param("sss", $nama_kelas, $singkatan, $angkatan);
            
            if ($stmt->execute()) {
                $success = "Data kelas berhasil disimpan.";
            } else {
                $error = "Terjadi kesalahan: " . $stmt->error;
            }
        }
    }
}
?>



<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-center text-xl font-bold mb-4 bg-gray-800 text-white p-3 rounded-md">Input Kelas</h2>

    <?php if (isset($error)) echo "<p class='text-red-500 text-center'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='text-green-500 text-center'>$success</p>"; ?>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Nama Kelas:</label>
            <input type="text" name="nama_kelas" class="w-full p-2 border rounded-md" required>
        </div>
        <div>
            <label class="block font-medium">Singkatan Kelas:</label>
            <input type="text" name="singkatan" class="w-full p-2 border rounded-md" required>
        </div>
        <div>
            <label class="block font-medium">Angkatan:</label>
            <input type="text" name="angkatan" class="w-full p-2 border rounded-md" required>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
        </div>
    </form>
</div>
