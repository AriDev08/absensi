<?php
include '../koneksi.php';


$query = "
    SELECT jadwal.*, 
       mapel.nama_mapel, 
       users.username AS nama_guru, 
       kelas.nama_kelas
FROM jadwal
JOIN mapel ON jadwal.mapel_id = mapel.id
JOIN users ON jadwal.user_id = users.id
JOIN kelas ON jadwal.kelas_id = kelas.id

";


$result = mysqli_query($conn, $query);
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
    <h2 class="text-2xl font-bold mb-4 text-center bg-[#001F3F] text-white py-3 rounded">Daftar Jadwal</h2>

    <table class="table-auto w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="border px-4 py-2">No</th>
                <th class="border px-4 py-2">Hari</th>
                <th class="border px-4 py-2">Jam</th>
                <th class="border px-4 py-2">Mata Pelajaran</th>
                <th class="border px-4 py-2">Guru</th>
                <th class="border px-4 py-2">Kelas</th>
                <th class="border px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2"><?= $no++ ?></td>
                    <td class="border px-4 py-2"><?= $row['hari'] ?></td>
                    <?php $jam_mulai = date("H:i", strtotime($row['jam_mulai'])); ?>
                    <?php $jam_selesai = date("H:i", strtotime($row['jam_selesai'])); ?>
                    <td class="border px-4 py-2"><?= $jam_mulai ?> - <?= $jam_selesai ?></td>
                    <td class="border px-4 py-2"><?= $row['nama_mapel'] ?></td>
                    <td class="border px-4 py-2"><?= $row['nama_guru'] ?></td>
                    <td class="border px-4 py-2"><?= $row['nama_kelas'] ?></td>
                    <td class="border px-4 py-2 text-center">
                        <div class="flex justify-center space-x-2">

                            <a href="?page=edit_jadwal&id=<?php echo $row['id']; ?>"
                                class="bg-blue-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M11 5h2m2.121 2.121l2.121 2.121m-2.121-2.121L6 18H4v-2l9.243-9.243z" />
                                </svg>
                                <span>Edit</span>
                            </a>
                            <a href="delete_jadwal.php?id=<?php echo $row['id']; ?>"
                                class="bg-red-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
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
            <?php } ?>
        </tbody>
    </table>
</div>