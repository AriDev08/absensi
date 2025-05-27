<?php
require '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$query = "SELECT users.id, users.username, users.email, users.role, 
                 kelas.nama_kelas, jabatan.nama_jabatan 
          FROM users
          LEFT JOIN kelas ON users.kelas_id = kelas.id
          LEFT JOIN jabatan ON users.jabatan_id = jabatan.id";

$result = $conn->query($query);

if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-20">
<h2 class="text-2xl font-bold mb-4 text-center bg-[#001F3F] text-white py-3 rounded"> Data Kelas</h2>

    <div class="border border-gray-300 rounded-lg shadow-lg overflow-y-auto max-h-96">
        <table class="min-w-full bg-white">
        <thead class="bg-gray-100">               <tr>
                    <th class="px-6 py-3 text-left border">ID</th>
                    <th class="px-6 py-3 text-left border">Username</th>
                    <th class="px-6 py-3 text-left border">Email</th>
                    <th class="px-6 py-3 text-left border">Role</th>
                    <th class="px-6 py-3 text-left border">Kelas</th>
                    <th class="px-6 py-3 text-left border">Jabatan</th>
                    <th class="px-6 py-3 text-left border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-3 border"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-3 border"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-3 border"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="px-6 py-3 border"><?php echo htmlspecialchars($row['role']); ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['nama_kelas'] ? htmlspecialchars($row['nama_kelas']) : "-"; ?></td>
                            <td class="px-6 py-3 border"><?php echo $row['nama_jabatan'] ? htmlspecialchars($row['nama_jabatan']) : "-"; ?></td>
                            <td class="px-6 py-3 border text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="?page=edit_users&id=<?php echo $row['id']; ?>"
                                        class="bg-blue-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5h2m2.121 2.121l2.121 2.121m-2.121-2.121L6 18H4v-2l9.243-9.243z" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $row['id']; ?>"
                                        class="bg-red-500 text-white px-2 py-1 text-sm rounded-md flex items-center space-x-1"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-3 text-center border">Tidak ada data user</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
