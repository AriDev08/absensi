<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

switch ($role) {
    case "admin":
    case "guru":
        $user_id = $_SESSION['user_id'];
        $query = "SELECT users.username, users.role, kelas.nama_kelas, jabatan.nama_jabatan
                  FROM users
                  LEFT JOIN kelas ON users.kelas_id = kelas.id
                  LEFT JOIN jabatan ON users.jabatan_id = jabatan.id
                  WHERE users.id = '$user_id'";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Query gagal: " . mysqli_error($conn));
        }
        $user = mysqli_fetch_assoc($result);
        if (!$user) {
            echo "User tidak ditemukan di database.";
            exit();
        }

        header("Location: /absensi/{$role}/{$role}.php");
        break;

    case "siswa":
        header("Location: /absensi/siswa/siswa.php");
        break;

    default:
        echo "Role tidak valid.";
        exit();
}
exit();
?>
