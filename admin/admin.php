<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /absensi/login.php");
    exit();
}
?>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-gray-100">




    <main class="flex-1 p-6">

        <?php

        include "../partial/navbar.php";
        include "../partial/sidebar.php";

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            switch ($page) {
                case 'input_siswa':
                    include "input_siswa.php";
                    break;
                case 'tampil_siswa':
                    include "tampil_siswa.php";
                    break;
                case "edit_siswa":
                    include "edit_siswa.php";
                    break;

                case "input_jurusan";
                    include "input_jurusan.php";
                    break;
                case "tampil_jurusan";
                    include "tampil_jurusan.php";
                    break;
                case "edit_jurusan";
                    include "edit_jurusan.php";
                    break;
                case "delete_jurusan";
                    include "delete_jurusan.php";
                    break;

                case 'input_user':
                    include "input_users.php";
                    break;
                case 'tampil_user':
                    include "tampil_users.php";
                    break;
                case "edit_users":
                    include "edit_user.php";
                    break;



                case "input_kelas":
                    include "input_kelas.php";
                    break;
                case "tampil_kelas":
                    include "tampil_kelas.php";
                    break;
                case "edit_kelas":
                    include "edit_kelas.php";
                    break;

                case "rekap":
                    include "rekap.php";
                    break;

                case "scan":
                    include "scan.php";
                    break;
                case "jadwal":
                    include "jadwal.php";
                    break;
                case "tampil_jadwal":
                    include "tampil_jadwal.php";
                    break;
                case "edit_jadwal";
                    include "edit_jadwal.php";
                    break;
                case "input_mapel":
                    include "input_mapel.php";
                    break;
                case "export_excel":
                    include "export_excel.php";
                    break;

                case "input_absensi";
                    include "input_absensi.php";
                    break;


                default:
                    include "../isi_dashboard.php";
                    break;
            }
        } else {
            include "../isi_dashboard.php";
        }
        ?>
    </main>
    </div>

</body>

</html>