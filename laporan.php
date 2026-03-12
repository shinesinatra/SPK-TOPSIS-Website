<?php
include 'connection.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<div class="container mx-auto mt-10 px-4">
    <h3 class="text-2xl md:text-3xl font-bold text-center mb-8">Laporan</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Data Kriteria -->
        <div class="bg-white rounded-xl shadow hover:shadow-lg transform hover:-translate-y-2 transition p-6 flex flex-col items-center">
            <div class="text-indigo-600 text-6xl mb-4"><i class="fas fa-file-alt"></i></div>
            <a href="aksi/cetak_kriteria.php" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex items-center">
                <i class="fas fa-clipboard-list mr-2"></i> Cetak Kriteria
            </a>
        </div>

        <!-- Data Siswa -->
        <div class="bg-white rounded-xl shadow hover:shadow-lg transform hover:-translate-y-2 transition p-6 flex flex-col items-center">
            <div class="text-green-600 text-6xl mb-4"><i class="fas fa-user-graduate"></i></div>
            <a href="aksi/cetak_siswa.php" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex items-center">
                <i class="fas fa-user-graduate mr-2"></i> Cetak Alternatif
            </a>
        </div>

        <!-- Penilaian -->
        <div class="bg-white rounded-xl shadow hover:shadow-lg transform hover:-translate-y-2 transition p-6 flex flex-col items-center">
            <div class="text-yellow-500 text-6xl mb-4"><i class="fas fa-calculator"></i></div>
            <a href="aksi/cetak_penilaian.php" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex items-center">
                <i class="fas fa-calculator mr-2"></i> Cetak Penilaian
            </a>
        </div>

        <!-- Perankingan -->
        <div class="bg-white rounded-xl shadow hover:shadow-lg transform hover:-translate-y-2 transition p-6 flex flex-col items-center">
            <div class="text-red-500 text-6xl mb-4"><i class="fas fa-trophy"></i></div>
            <a href="aksi/cetak_perankingan.php" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex items-center">
                <i class="fas fa-trophy mr-2"></i> Cetak Ranking
            </a>
        </div>

    </div>
</div>