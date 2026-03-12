<?php
include 'connection.php';

// AJAX NISN & NAMA
if (isset($_GET['ajax'])) {
    ob_clean();
    header('Content-Type: application/json');

    if ($_GET['ajax'] === 'get_nisn' && isset($_GET['kelas'])) {
        $kelas = mysqli_real_escape_string($koneksi, $_GET['kelas']);
        $query = "SELECT nisn FROM alternatif WHERE kelas = '$kelas'";
        $result = mysqli_query($koneksi, $query);
        $nisn_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $nisn_list[] = $row['nisn'];
        }
        echo json_encode($nisn_list);
        exit();
    }

    if ($_GET['ajax'] === 'get_nama' && isset($_GET['nisn'])) {
        $nisn = mysqli_real_escape_string($koneksi, $_GET['nisn']);
        $query = "SELECT nama FROM alternatif WHERE nisn = '$nisn'";
        $result = mysqli_query($koneksi, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['nama' => $row['nama']]);
        } else {
            echo json_encode(['nama' => null]);
        }
        exit();
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Pencarian
$search_nisn = '';
if (isset($_POST['search_nisn'])) {
    $search_nisn = $_POST['search_nisn'];
}
$query = "SELECT * FROM penilaian WHERE nisn LIKE '%$search_nisn%' ORDER BY FIELD(kelas, 'I','II','III','IV','V','VI'), nama ASC";
$result = mysqli_query($koneksi, $query);

// Proses tambah
if (isset($_POST['tambah'])) {
    $kelas = $_POST['kelas'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];

    $sql_kriteria = "SELECT * FROM kriteria";
    $result_kriteria = $koneksi->query($sql_kriteria);

    $kolom_array = [];
    while ($row_kriteria = $result_kriteria->fetch_assoc()) {
        $nama_kriteria = $row_kriteria['nama'];
        $kolom_penilaian = strtolower(preg_replace('/[^a-z0-9]+/', '_', $nama_kriteria));
        $kolom_array[] = $kolom_penilaian;
    }

    $nilai_array = [];
    foreach ($kolom_array as $kolom) {
        $nilai_array[] = $_POST[$kolom];
    }

    $pisah_nilai = "'" . implode("','", $nilai_array) . "'";
    $insert_query = "INSERT INTO penilaian VALUES ('$kelas', '$nisn', '$nama', $pisah_nilai)";
    if (mysqli_query($koneksi, $insert_query)) {
        echo "<script>alert('Data Ditambahkan!'); window.location.href = 'menu.php?page=penilaian';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($koneksi) . "'); window.location.href = 'menu.php?page=penilaian';</script>";
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $nisn = $_GET['hapus'];
    $query = "DELETE FROM penilaian WHERE nisn=?";
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $nisn);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data Dihapus!'); window.location.href='menu.php?page=penilaian';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// Proses ubah
    if (isset($_POST['ubah'])) {
        $nisn = $_POST['nisn'];
        $nama = $_POST['nama'];
        $sql_kriteria = "SELECT * FROM kriteria";
        $result_kriteria = $koneksi->query($sql_kriteria);
        $update_parts = [];
        while($row_kriteria = $result_kriteria->fetch_assoc()) {
            $nama_kriteria = $row_kriteria['nama'];
            $kriteria = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $nama_kriteria));
            
            if (isset($_POST[$kriteria])) {
                $nilai = $_POST[$kriteria];
                $update_parts[] = "$kriteria = '" . mysqli_real_escape_string($koneksi, $nilai) . "'";
            }
        }
        $update_string = implode(', ', $update_parts);
        $nama_escaped = mysqli_real_escape_string($koneksi, $nama);
        $update_query = "UPDATE penilaian SET nama = '$nama_escaped', $update_string WHERE nisn = '$nisn'";
        if (mysqli_query($koneksi, $update_query)) {
            echo "<script>alert('Data diubah!'); window.location.href = 'menu.php?page=penilaian';</script>";
        } else {
            echo "<script>alert('Gagal mengubah data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
?>

<!-- HTML + Modal & Table -->
<div class="flex flex-row items-center justify-center md:justify-between gap-4 mb-6">    
    <div class="flex-none">
        <button onclick="openTambah()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition duration-200">
            <i class="fa fa-plus mr-1"></i> Tambah Data
        </button>
    </div>
    <div class="w-full md:w-auto flex justify-center">
        <form method="POST" class="flex shadow-sm">
            <input type="text" name="search_nisn" placeholder="Cari NISN..." value="<?php echo $search_nisn; ?>" class="border border-gray-300 rounded-l px-4 py-2 w-full max-w-[200px] md:w-36 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 rounded-r transition duration-200">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="w-full bg-white shadow rounded-lg overflow-hidden">
    <div class="p-4 border-b font-semibold text-center bg-gray-50 text-gray-700">
        Penilaian
    </div>
    <div class="overflow-x-auto w-full">
        <table class="min-w-[1000px] w-full text-sm text-center border-collapse">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">NISN</th>
                    <th class="px-4 py-3">Nama</th>
                    <?php
                    $sql_kriteria = "SELECT * FROM kriteria";
                    $result_kriteria = $koneksi->query($sql_kriteria);
                    while($row_kriteria = $result_kriteria->fetch_assoc()){
                        echo '<th class="px-4 py-3">'.$row_kriteria['nama'].'</th>';
                    }
                    ?>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $columns_result = mysqli_query($koneksi, "SHOW COLUMNS FROM penilaian");
                $columns = [];
                while ($col = mysqli_fetch_assoc($columns_result)) {
                    $columns[] = $col['Field'];
                }
                $exclude_columns = ['kelas', 'nisn', 'nama'];
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr class="hover:bg-indigo-50 transition-colors">
                    <?php foreach ($exclude_columns as $ex_col): ?>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row[$ex_col]); ?></td>
                    <?php endforeach; ?>
                    <?php foreach ($columns as $col):
                        if (!in_array($col, $exclude_columns)): ?>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row[$col]); ?></td>
                    <?php endif; endforeach; ?>
                    <td class="px-4 py-2 whitespace-nowrap space-x-1">
                        <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded btn-edit"
    data-nisn="<?php echo htmlspecialchars($row['nisn']); ?>"
    data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
    <?php
    foreach ($columns as $col) {
        if (!in_array($col, $exclude_columns)) {
            $val = htmlspecialchars($row[$col]);
            echo "data-$col=\"$val\" ";
        }
    }
    ?>>
    <i class="fa fa-edit"></i>
</button>
                        <a href="penilaian.php?hapus=<?php echo $row['nisn']; ?>" onclick="return confirm('Yakin Hapus Data?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md shadow-sm transition">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Tambah Penilaian</h2>
        <form method="POST">
            <label class="text-sm">Kelas</label>
            <select name="kelas" id="tambahKelas" required class="border p-2 rounded w-full mb-3">
                <option value=""> </option>
                <?php
                    $sql_kelas = "SELECT DISTINCT kelas FROM alternatif ORDER BY FIELD(kelas, 'I', 'II', 'III' , 'IV' , 'V', 'VI')";
                    $result_kelas = mysqli_query($koneksi, $sql_kelas);
                    while($row_kelas = mysqli_fetch_assoc($result_kelas)){
                        echo "<option value='{$row_kelas['kelas']}'>{$row_kelas['kelas']}</option>";
                    }
                ?>
            </select>
            <label class="text-sm">NISN</label>
            <select name="nisn" id="tambahNisn" required class="border p-2 rounded w-full mb-3"><option value=""> </option></select>
            <label class="text-sm">Nama</label>
            <input type="text" name="nama" required class="border p-2 rounded w-full mb-3">
            <?php
            $result_kriteria = $koneksi->query("SELECT * FROM kriteria");
            while($row_kriteria = $result_kriteria->fetch_assoc()){
                $col = strtolower(preg_replace('/[^a-z0-9]+/', '_', $row_kriteria['nama']));
                echo "<label class='text-sm'>{$row_kriteria['nama']}</label>";
                echo "<input type='number' name='$col' class='border p-2 rounded w-full mb-3'>";
            }
            ?>
            <div class="flex justify-end gap-2 sticky bottom-0 bg-white pt-3">
                <button type="button" onclick="closeTambah()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button name="tambah" class="px-4 py-2 bg-green-600 text-white rounded">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL UBAH -->
<div id="modalUbah" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Ubah Penilaian</h2>
        <form action="penilaian.php" method="POST">
            <input type="hidden" id="nisn_ubah" name="nisn">
            <div class="mb-3">
                <label for="nama_ubah" class="text-sm">Nama</label>
                <input type="text" class="border p-2 rounded w-full mb-3" id="nama_ubah" name="nama" required>
            </div>
            <?php
                $sql_kriteria = "SELECT * FROM kriteria";
                $result_kriteria = $koneksi->query($sql_kriteria);
                while($row_kriteria = $result_kriteria->fetch_assoc()){
                    $kolom = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $row_kriteria['nama']));
                    echo "<label class='text-sm'>{$row_kriteria['nama']}</label>";
                    echo "<input type='number' step='0.01' min='0' class='border p-2 rounded w-full mb-3' id='ubah_$kolom' name='$kolom' required>";
                }
            ?>
            <div class="flex justify-end gap-2 sticky bottom-0 bg-white pt-3">
                <button type="button" onclick="closeUbah()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button type="submit" name="ubah" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTambah(){
    const modal = document.getElementById("modalTambah");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}
function closeTambah(){
    const modal = document.getElementById("modalTambah");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}
// Script Modal Ubah
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', () => {
        const nisn = button.getAttribute('data-nisn');
        const nama = button.getAttribute('data-nama');
        document.getElementById('nisn_ubah').value = nisn;
        document.getElementById('nama_ubah').value = nama;

        <?php
            $sql_kriteria = "SELECT * FROM kriteria";
            $result_kriteria = $koneksi->query($sql_kriteria);
            while($row_kriteria = $result_kriteria->fetch_assoc()){
                $kolom = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $row_kriteria['nama']));
                echo "document.getElementById('ubah_$kolom').value = button.getAttribute('data-$kolom') || '';\n";
            }
        ?>

        const modal = document.getElementById('modalUbah');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});
// Fungsi untuk menutup modal Ubah
function closeUbah(){
    const modal = document.getElementById('modalUbah');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
// AJAX tambahNisn
document.addEventListener('DOMContentLoaded', function(){
    const tambahKelas = document.getElementById('tambahKelas');
    const tambahNisn = document.getElementById('tambahNisn');
    const namaInput = document.querySelector('input[name="nama"]');

    tambahKelas.addEventListener('change', function(){
        const kelas = this.value;
        tambahNisn.innerHTML = '<option value=""> </option>';
        if(kelas){
            fetch(`menu.php?page=penilaian&ajax=get_nisn&kelas=${encodeURIComponent(kelas)}`)
                .then(res=>res.json())
                .then(data=>{
                    data.forEach(nisn=>{
                        const option = document.createElement('option');
                        option.value = nisn;
                        option.text = nisn;
                        tambahNisn.appendChild(option);
                    });
                }).catch(err=>console.error(err));
        }
    });

    tambahNisn.addEventListener('change', function(){
        const nisn = this.value;
        if(nisn){
            fetch(`menu.php?page=penilaian&ajax=get_nama&nisn=${nisn}`)
                .then(res=>res.json())
                .then(data=>{
                    namaInput.value = data.nama || '';
                }).catch(err=>console.error(err));
        } else {
            namaInput.value = '';
        }
    });
});
</script>