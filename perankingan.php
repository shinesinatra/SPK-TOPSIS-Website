<?php
    include "connection.php";
    if (!isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }
    if (isset($_POST['reset'])) {
        mysqli_query($koneksi, "TRUNCATE TABLE perankingan");
        echo "<script>alert('Tabel Direset!'); window.location.href='menu.php?page=ranking';</script>";
    }
    if (isset($_POST['proses'])) {
        $kelas = $_POST['kelas'];
        if ($kelas == "Pilih Kelas") {
            echo "<script>alert('Silahkan Pilih Kelas');</script>";
        } else {
            // Ambil kriteria bertipe double
            $sql_field = "SHOW COLUMNS FROM penilaian WHERE Type LIKE '%double%'";
            $result_field = $koneksi->query($sql_field);
            $kriteria_arr = [];
            while ($row_field = $result_field->fetch_assoc()) {
                $kriteria_arr[] = $row_field['Field'];
            }
            // Ambil bobot kriteria
            $bobot_query = "SELECT bobot FROM kriteria";
            $result_bobot = $koneksi->query($bobot_query);
            $bobot_arr = [];
            while ($row_bobot = $result_bobot->fetch_assoc()) {
                $bobot_arr[] = $row_bobot['bobot'];
            }
            // Ambil data penilaian untuk kelas terpilih
            $sql_nilai = "SELECT nisn, nama, ".implode(", ", $kriteria_arr)." FROM penilaian WHERE kelas='$kelas'";
            $result_nilai = $koneksi->query($sql_nilai);
            // Bangun matriks
            $matrix = [];
            $nisn_nama = [];
            while ($row = $result_nilai->fetch_assoc()) {
                $nisn_nama[] = ['nisn'=>$row['nisn'], 'nama'=>$row['nama']];
                $vals = [];
                foreach ($kriteria_arr as $field) {
                    $vals[] = floatval($row[$field]);
                }
                $matrix[] = $vals;
            }
            $n_alt = count($matrix);
            $n_kri = count($kriteria_arr);
            // 1️⃣ Normalisasi matriks
            $norm_matrix = [];
            for ($j=0; $j<$n_kri; $j++) {
                $sum_sqr = 0;
                for ($i=0; $i<$n_alt; $i++) {
                    $sum_sqr += $matrix[$i][$j]**2;
                }
                $denom = sqrt($sum_sqr);
                for ($i=0; $i<$n_alt; $i++) {
                    $norm_matrix[$i][$j] = ($denom!=0) ? $matrix[$i][$j]/$denom : 0;
                }
            }
            // 2️⃣ Normalisasi terbobot
            $weighted_matrix = [];
            for ($i=0; $i<$n_alt; $i++) {
                for ($j=0; $j<$n_kri; $j++) {
                    $weighted_matrix[$i][$j] = $norm_matrix[$i][$j] * $bobot_arr[$j];
                }
            }
            // 3️⃣ Tentukan solusi ideal positif dan negatif
            $ideal_pos = [];
            $ideal_neg = [];
            for ($j=0; $j<$n_kri; $j++) {
                $col = array_column($weighted_matrix, $j);
                $ideal_pos[$j] = max($col); // benefit criteria
                $ideal_neg[$j] = min($col);
            }
            // 4️⃣ Hitung jarak dari solusi ideal
            $d_pos = [];
            $d_neg = [];
            for ($i=0; $i<$n_alt; $i++) {
                $sum_pos = 0;
                $sum_neg = 0;
                for ($j=0; $j<$n_kri; $j++) {
                    $sum_pos += ($weighted_matrix[$i][$j]-$ideal_pos[$j])**2;
                    $sum_neg += ($weighted_matrix[$i][$j]-$ideal_neg[$j])**2;
                }
                $d_pos[$i] = sqrt($sum_pos);
                $d_neg[$i] = sqrt($sum_neg);
            }
            // 5️⃣ Hitung nilai preferensi
            $pref = [];
            for ($i=0; $i<$n_alt; $i++) {
                $pref[$i] = ($d_pos[$i]+$d_neg[$i]!=0) ? $d_neg[$i]/($d_pos[$i]+$d_neg[$i]) : 0;
            }
            // 6️⃣ Simpan ke tabel perankingan_topsis
            mysqli_query($koneksi, "TRUNCATE TABLE perankingan");
            for ($i=0; $i<$n_alt; $i++) {
                $nisn = $nisn_nama[$i]['nisn'];
                $nama = $nisn_nama[$i]['nama'];
                $nilai = $pref[$i];
                mysqli_query($koneksi, "INSERT INTO perankingan VALUES ('$nisn','$nama',$nilai)");
            }
            echo "<script>alert('Perhitungan TOPSIS berhasil!'); window.location.href='menu.php?page=ranking';</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perhitungan TOPSIS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<form action="" method="POST">
<div class="container mx-auto py-8 px-4">
    <h2 class="text-3xl font-bold text-center mb-6">Perhitungan TOPSIS</h2>

    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <!-- Select Kelas -->
        <div class="flex justify-center flex-1 order-1 md:order-2 w-full md:w-auto">
            <select name="kelas" id="kelas" class="border rounded px-3 py-2 w-40l md:w-36">
                <option value="Pilih Kelas">Pilih Kelas</option>
                <?php
                $sql_kelas = "SELECT DISTINCT kelas FROM penilaian ORDER BY FIELD(kelas, 'I','II','III','IV','V','VI')";
                $result_kelas = $koneksi->query($sql_kelas);
                while($row_kelas = $result_kelas->fetch_assoc()) {
                    $kelas = $row_kelas['kelas'];
                    echo "<option value='$kelas'>$kelas</option>";
                }
                ?>
            </select>
        </div>

        <!-- Tombol Proses -->
        <button type="submit" name="proses" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2 order-2 md:order-1 w-40 md:w-auto justify-center">
            <i class="fas fa-hourglass-half"></i> Proses TOPSIS
        </button>

        <!-- Tombol Reset -->
        <button type="submit" name="reset" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2 order-3 w-40 md:w-auto justify-center">
            <i class="fas fa-sync-alt"></i> Reset Tabel
        </button>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-[600px] w-full text-center border-collapse">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-2">Peringkat</th>
                    <th class="px-4 py-2">NISN</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Nilai TOPSIS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php
                $query = "SELECT * FROM perankingan ORDER BY nilai DESC";
                $result = mysqli_query($koneksi, $query);
                $no=1;
                while($row = mysqli_fetch_assoc($result)):
                ?>
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="px-4 py-2"><?php echo $no; ?></td>
                    <td class="px-4 py-2"><?php echo $row['nisn']; ?></td>
                    <td class="px-4 py-2"><?php echo $row['nama']; ?></td>
                    <td class="px-4 py-2"><?php echo number_format($row['nilai'], 3); ?></td>
                </tr>
                <?php $no++; endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</form>
</body>
</html>