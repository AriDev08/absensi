<?php
require '../koneksi.php';

if (isset($_GET['nis'])) {
    $nis = $_GET['nis'];

    $query = "DELETE FROM siswa WHERE nis = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nis);

    if ($stmt->execute()) {
        $page = isset($_GET['page']) ? $_GET['page'] : 'tampil_siswa'; 
        header("Location: admin.php?page=$page"); 
    } else {
        die("Gagal menghapus siswa.");
    }
}
?>
