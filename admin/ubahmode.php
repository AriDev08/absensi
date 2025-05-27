<?php
require "../koneksi.php";


$modeQuery = mysqli_query($conn, "SELECT * FROM tipe");
if (!$modeQuery) {
    die("Query error: " . mysqli_error($conn));
}

$data_mode = mysqli_fetch_array($modeQuery);
$mode_absen = $data_mode['mode'];


$mode_absen = $mode_absen + 1;
if ($mode_absen > 2) {
    $mode_absen = 1;
}


$simpan = mysqli_query($conn, "UPDATE tipe SET mode='$mode_absen'");
if ($simpan) {
    echo "Berhasil mengubah mode ke: " . $mode_absen;
} else {
    echo "Gagal mengubah mode: " . mysqli_error($conn);
}
?>
