<?php
include("../koneksi.php");

$nokartu = $_GET['nokartu'];


mysqli_query($conn, "DELETE FROM rfid");


$simpan = mysqli_query($conn, "INSERT INTO rfid (nokartu) VALUES('$nokartu')");

if($simpan) echo "berhasil";
else echo "gagal";
?>
