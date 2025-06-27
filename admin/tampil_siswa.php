<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_POST['naik_kelas'])) {
    $tahun_lulus = date("Y");
    $siswa_query = mysqli_query($conn, "SELECT * FROM siswa");

    while ($siswa = mysqli_fetch_assoc($siswa_query)) {
        $id_siswa = $siswa['id'];
        $nis = $siswa['nis'];
        $nama = $siswa['nama'];
        $jurusan_id = $siswa['jurusan_id'];
        $kelas_id = $siswa['kelas_id'];

        $kelas_now = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kelas WHERE id = '$kelas_id'"));
        $angkatan = $kelas_now['angkatan'];
        $singkatan = $kelas_now['singkatan'];


        $next_angkatan = match ($angkatan) {
            'X' => 'XI',
            'XI' => 'XII',
            'XII' => 'LULUS',
            default => null
        };

        if ($next_angkatan === 'LULUS') {

            $jurusan_nama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_jurusan FROM jurusan WHERE id = '$jurusan_id'"))['nama_jurusan'];
            mysqli_query($conn, "INSERT INTO alumni (nama, nis, jurusan, tahun_lulus) VALUES ('$nama', '$nis', '$jurusan_nama', '$tahun_lulus')");
            mysqli_query($conn, "DELETE FROM siswa WHERE id = '$id_siswa'");
        } else {
            $singkatan_baru = preg_replace('/^'.$angkatan.'/', $next_angkatan, $singkatan);
            $kelas_baru_query = mysqli_query($conn, "SELECT id FROM kelas WHERE singkatan = '$singkatan_baru'");

            if (mysqli_num_rows($kelas_baru_query) > 0) {
                $kelas_baru_id = mysqli_fetch_assoc($kelas_baru_query)['id'];
                mysqli_query($conn, "UPDATE siswa SET kelas_id = '$kelas_baru_id' WHERE id = '$id_siswa'");
            }
        }
    }

    echo "<script>alert('Semua siswa berhasil dinaikkan kelas!'); window.location.href = window.location.href;</script>";
}

$query = "SELECT 
            siswa.id,
            siswa.nis, 
            siswa.nama, 
            kelas.angkatan AS angkatan, 
            siswa.absen, 
            jurusan.nama_jurusan AS jurusan, 
            kelas.singkatan 
          FROM siswa
          LEFT JOIN kelas ON siswa.kelas_id = kelas.id
          LEFT JOIN jurusan ON siswa.jurusan_id = jurusan.id";

$result = $conn->query($query);

if (!$result) {
    die("Query gagal dijalankan: " . $conn->error);
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-20">
    <h2 class="text-2xl font-bold mb-4 text-center bg-[#001F3F] text-white py-3 rounded"> Data Siswa</h2>

  
    <form method="post">
        <button type="submit" name="naik_kelas" class="mb-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-md"
            onclick="return confirm('Yakin ingin menaikkan semua siswa ke kelas berikutnya?')">
            Naikkan Semua Siswa
        </button>
    </form>

    <div class="border border-gray-300 rounded-lg shadow-lg overflow-y-auto max-h-96">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left border">No</th>
                    <th class="px-6 py-3 text-left border">NIS</th>
                    <th class="px-6 py-3 text-left border">Nama</th>
                    <th class="px-6 py-3 text-left border">Kelas</th>
                    <th class="px-6 py-3 text-left border">Angkatan</th>
                    <th class="px-6 py-3 text-left border">Absen</th>
                    <th class="px-6 py-3 text-left border">Jurusan</th>
                    <th class="px-6 py-3 text-left border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-3 border"><?php echo $no++; ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['nis']; ?></td>
                            <td class="px-6 py-3 border"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['singkatan'] ?? "-"; ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['angkatan'] ?? "-"; ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['absen'] ?? "-"; ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['jurusan'] ?? "-"; ?></td>
                            <td class="px-6 py-3 border text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="?page=edit_siswa&nis=<?php echo $row['nis']; ?>"
                                        class="bg-blue-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M11 5h2m2.121 2.121l2.121 2.121m-2.121-2.121L6 18H4v-2l9.243-9.243z" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>
                                    <a href="delete_siswa.php?nis=<?php echo $row['nis']; ?>"
                                        class="bg-red-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-3 text-center border">Tidak ada data siswa</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
