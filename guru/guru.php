<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "guru") {
    header("Location: ../login.php");
    exit();
}

?>


<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    
  
        
       
        <main class="flex-1 p-6">
            <?php
            include "../partial/navbar_guru.php"; 
            include "../partial/sidebar_guru.php"; 
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                switch ($page) { 
                    case 'input_absensi':
                        include "input_absensi.php";
                        break;
                    case 'tampil_absensi':
                        include "tampil_absensi.php";
                        break;
                    case "jadwal":
                        include "jadwal.php";
                        break;
                  
                            
                            default:
                            include "../isi_dashboard.php"; 
                            break;
                    }
                } else {
                    include "../isi_dashboard_guru.php"; 
                }
            ?>
        </main>
    </div>
    
</body>
</html>