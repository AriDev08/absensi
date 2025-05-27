<?php
require '../koneksi.php';

$sql = mysqli_query($conn, "SELECT * FROM rfid LIMIT 1");
$data = mysqli_fetch_array($sql);
$nokartu = $data ? $data['nokartu'] : '';
?>

<div>
    <label class="block font-medium">NO kartu:</label>
    <input type="text" name="nokartu" class="w-full p-2 border rounded-md bg-gray-100" readonly value="<?php echo htmlspecialchars($nokartu); ?>">
</div>
