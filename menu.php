<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TOPSIS | Website</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <div class="flex">
            <!-- SIDEBAR -->
            <div id="sidebar" 
            class="fixed md:static z-20 w-64 bg-indigo-900 min-h-screen text-white transform -translate-x-full md:translate-x-0 transition-transform duration-200">
                <div class="p-5 text-xl font-bold border-b border-indigo-700">
                    SPK TOPSIS
                </div>
                <nav class="mt-2">
                    <a href="menu.php?page=dashboard" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-gauge mr-2"></i> Dashboard
                    </a>
                    <p class="px-6 py-2 text-xs text-gray-400 mt-4">MASTER DATA</p>
                    <a href="menu.php?page=kriteria" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-list mr-2"></i> Kriteria
                    </a>
                    <a href="menu.php?page=alternatif" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-users mr-2"></i> Alternatif
                    </a>
                    <p class="px-6 py-2 text-xs text-gray-400 mt-4">PROSES SPK</p>
                    <a href="menu.php?page=penilaian" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-edit mr-2"></i> Penilaian
                    </a>
                    <a href="menu.php?page=ranking" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-trophy mr-2"></i> Hasil & Ranking
                    </a>
                    <a href="menu.php?page=laporan" class="block px-6 py-3 hover:bg-indigo-700">
                        <i class="fa fa-file-alt mr-2"></i></i> Laporan
                    </a>
                </nav>
            </div>
            <!-- CONTENT -->
            <!-- <div class="flex-1"> -->
                <div class="flex-1 min-w-0 overflow-hidden flex flex-col">
                <!-- TOPBAR -->
                <div class="bg-white shadow px-6 py-4 flex justify-between items-center">
                    <button onclick="toggleSidebar()" class="md:hidden text-xl">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="font-semibold text-gray-600">
                        <?php echo $_SESSION['full_name']; ?>
                    </div>
                    <a href="logout.php" class="text-gray-600 hover:text-red-500">
                        <i class="fa fa-sign-out"></i> Keluar
                    </a>
                </div>
                <!-- MAIN CONTENT -->
                <div class="p-6">
                    <?php
                        switch($page){
                        case 'kriteria':
                        include "kriteria.php";
                        break;
                        case 'alternatif':
                        include "alternatif.php";
                        break;
                        case 'penilaian':
                        include "penilaian.php";
                        break;
                        case 'ranking':
                        include "perankingan.php";
                        break;
                        case 'laporan':
                        include "laporan.php";
                        break;
                        default:
                        include "dashboard.php";
                        break;
                        }
                    ?>
                </div>
            </div>
        </div>
        <script>
            function toggleSidebar(){
                document.getElementById("sidebar")
                .classList.toggle("-translate-x-full");
            }
        </script>
    </body>
</html>