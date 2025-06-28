<?php
require 'koneksi.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';

    if (empty($role)) {
        $error = 'Silakan pilih role.';
    } elseif ($role === 'siswa') {
     
        $nis  = trim($_POST['nis'] ?? '');
        $nama = trim($_POST['nama'] ?? '');

        if (empty($nis) || empty($nama)) {
            $error = 'NIS dan Nama wajib diisi.';
        } else {
            $stmt = $conn->prepare("SELECT nis, nama FROM siswa WHERE nis = ? AND nama = ?");
            $stmt->bind_param('ss', $nis, $nama);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows === 1) {
            
                $_SESSION['role']     = 'siswa';
                $_SESSION['user_id']  = $nis;
                $_SESSION['username'] = $nama;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'NIS atau Nama siswa salah.';
            }
        }
    } elseif (in_array($role, ['guru', 'admin'])) {
        
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $error = 'Email dan Password wajib diisi.';
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? AND role = ?");
            $stmt->bind_param('ss', $email, $role);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['role']     = $user['role'];
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Email atau Password salah.';
            }
        }
    } else {
        $error = 'Role tidak dikenali.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center min-h-screen bg-cover bg-center bg-no-repeat bg-[url('asset/login-bg.png')]">
  <div class="w-full max-w-md p-8 bg-white bg-opacity-20 rounded-lg shadow-lg backdrop-blur-sm flex flex-col items-center">
    <img src="asset/wihope.png" alt="Logo" class="w-24 h-24 mb-4">
    <h2 class="text-white text-2xl font-bold text-center mb-4">LOGIN</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-500 text-white p-2 rounded mb-4 w-full text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    
    <form id="loginForm" action="" method="POST" class="w-full space-y-4">
    <!-- tester -->
      <div>
        <select id="role" name="role" required class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none">
          <option value="">Pilih Role</option>
          <option value="admin" <?= (isset($_POST['role']) && $_POST['role']==='admin')?'selected':'' ?>>Admin</option>
          <option value="guru" <?= (isset($_POST['role']) && $_POST['role']==='guru')?'selected':'' ?>>Guru</option>
          <option value="siswa" <?= (isset($_POST['role']) && $_POST['role']==='siswa')?'selected':'' ?>>Siswa</option>
        </select>
      </div>
      

      <div id="adminFields" class="hidden space-y-3">
        <input type="email" id="email" name="email" placeholder="Email" class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <input type="password" id="password" name="password" placeholder="Password" class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none" />
      </div>
      

      <div id="siswaFields" class="hidden space-y-3">
        <input type="text" id="nis" name="nis" placeholder="NIS" class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none" value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>" />
        <input type="text" id="nama" name="nama" placeholder="Nama" class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" />
      </div>
      
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Login</button>
    </form>
  </div>

  <script>
    const roleSelect  = document.getElementById('role');
    const adminFields = document.getElementById('adminFields');
    const siswaFields = document.getElementById('siswaFields');

    function toggleFields() {
   
      ['email','password','nis','nama'].forEach(id => document.getElementById(id)?.removeAttribute('required'));

      if (roleSelect.value === 'siswa') {
        siswaFields.classList.remove('hidden');
        adminFields.classList.add('hidden');
        document.getElementById('nis').setAttribute('required', '');
        document.getElementById('nama').setAttribute('required', '');
      } else if (roleSelect.value === 'admin' || roleSelect.value === 'guru') {
        adminFields.classList.remove('hidden');
        siswaFields.classList.add('hidden');
        document.getElementById('email').setAttribute('required', '');
        document.getElementById('password').setAttribute('required', '');
      } else {
        adminFields.classList.add('hidden');
        siswaFields.classList.add('hidden');
      }
    }

    roleSelect.addEventListener('change', toggleFields);
  
    window.addEventListener('DOMContentLoaded', toggleFields);
  </script>
</body>
</html>
