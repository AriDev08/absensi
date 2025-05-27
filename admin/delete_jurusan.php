<?php
require '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM jurusan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); 

    if ($stmt->execute()) {
        $page = isset($_GET['page']) ? $_GET['page'] : 'tampil_jurusan'; 
        header("Location: admin.php?page=$page"); 
    } else {
        die("Gagal menghapus jurusan.");
    }
}
?>
