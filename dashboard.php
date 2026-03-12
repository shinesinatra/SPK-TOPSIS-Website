<?php
    include 'connection.php';
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }
    //Jumlah Kriteria
    $queryKriteria = "SELECT COUNT(*) as total FROM kriteria";
    $resultKriteria = mysqli_query($koneksi, $queryKriteria);
    $dataKriteria = mysqli_fetch_assoc($resultKriteria);
    $totalKriteria = $dataKriteria['total'];
    //Jumlah Data Siswa
    $querySiswa = "SELECT COUNT(*) as total FROM alternatif";
    $resultSiswa = mysqli_query($koneksi, $querySiswa);
    $dataSiswa = mysqli_fetch_assoc($resultSiswa);
    $totalSiswa = $dataSiswa['total'];
    // Jumlah Periode
    $queryPeriode = "SELECT COUNT(*) as total FROM periode";
    $resultPeriode = mysqli_query($koneksi, $queryPeriode);
    $dataPeriode = mysqli_fetch_assoc($resultPeriode);
    $totalPeriode = $dataPeriode['total'];


    // Data Bar Chart
    $queryRanking = "SELECT nama, nilai FROM perankingan ORDER BY nilai DESC LIMIT 5";
    $resultRanking = mysqli_query($koneksi, $queryRanking);

    $labels = [];
    $values = [];
    while($row = mysqli_fetch_assoc($resultRanking)){
        $labels[] = $row['nama'];
        $values[] = $row['nilai'];
    }
    mysqli_close($koneksi);
?>
<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <div class="bg-cyan-600 text-white p-6 rounded shadow flex items-center space-x-4">
        <div class="text-4xl">
            <i class="fa fa-list"></i> <!-- Ikon Kriteria -->
        </div>
        <div>
            <h2 class="text-3xl font-bold"><?php echo $totalKriteria; ?></h2>
            <p>Data Kriteria</p>
        </div>
    </div>

    <div class="bg-green-600 text-white p-6 rounded shadow flex items-center space-x-4">
        <div class="text-4xl">
            <i class="fa fa-users"></i> <!-- Ikon Alternatif -->
        </div>
        <div>
            <h2 class="text-3xl font-bold"><?php echo $totalSiswa; ?></h2>
            <p>Data Alternatif</p>
        </div>
    </div>

    <div class="bg-yellow-500 text-white p-6 rounded shadow flex items-center space-x-4">
        <div class="text-4xl">
            <i class="fa fa-calendar"></i> <!-- Ikon Periode -->
        </div>
        <div>
            <h2 class="text-3xl font-bold"><?php echo $totalPeriode; ?></h2>
            <p>Data Periode</p>
        </div>
    </div>

</div>

<div class="bg-white shadow rounded mt-8 p-6">

<h2 class="font-semibold mb-4">
Grafik 5 Teratas
</h2>

<canvas id="chart"></canvas>

</div>

<script>
const ctx = document.getElementById('chart');

// Data dari PHP
const labels = <?php echo json_encode($labels); ?>;
const dataValues = <?php echo json_encode($values); ?>;

// Warna berbeda tiap bar
const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nilai Preferensi',
            data: dataValues,
            backgroundColor: colors
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                // max otomatis mengikuti nilai tertinggi
            }
        },
        plugins: {
            legend: {
                display: false // sembunyikan legend agar lebih rapi
            }
        }
    }
});
</script>