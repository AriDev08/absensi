<?php
include '../koneksi.php';


if (isset($_POST['simpan'])) {
    $nama_mapel = $_POST['nama_mapel'];

    $query = "INSERT INTO mapel (nama_mapel) VALUES ('$nama_mapel')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Mata pelajaran berhasil ditambahkan!'); window.location.href='?page=input_mapel';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan!');</script>";
    }
}
?>


<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-xl font-bold mb-4 bg-[#001F3F] text-white text-center p-3 rounded">Form Tambah Mata Pelajaran</h2>

    <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_mapel">
                Nama Mata Pelajaran
            </label>
            <input type="text" name="nama_mapel" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" name="simpan" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Simpan
            </button>
        </div>
    </form>
</div>
