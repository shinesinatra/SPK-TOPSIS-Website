<?php
include 'connection.php';

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['username'])) header("Location: index.php");

$search_nisn = isset($_POST['search_nisn']) ? $_POST['search_nisn'] : '';

$query = "SELECT * FROM alternatif 
WHERE nisn LIKE '%$search_nisn%'
ORDER BY FIELD(kelas,'I','II','III','IV','V','VI'), nama ASC";
$result = mysqli_query($koneksi,$query);

if(isset($_POST['tambah'])){
    $kelas = $_POST['kelas'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal_lahir'];
    $jk = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $cek = mysqli_query($koneksi,"SELECT * FROM alternatif WHERE nisn='$nisn'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('NISN sudah terdaftar');location='menu.php?page=alternatif';</script>";
    }else{
        mysqli_query($koneksi,"INSERT INTO alternatif (kelas,nisn,nama,tanggal_lahir,jenis_kelamin,alamat) VALUES ('$kelas','$nisn','$nama','$tanggal','$jk','$alamat')");
        echo "<script>alert('Data ditambahkan');location='menu.php?page=alternatif';</script>";
    }
}

if(isset($_POST['ubah'])){
    $kelas = $_POST['kelas'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal_lahir'];
    $jk = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    mysqli_query($koneksi,"UPDATE alternatif SET kelas='$kelas', nama='$nama', tanggal_lahir='$tanggal', jenis_kelamin='$jk', alamat='$alamat' WHERE nisn='$nisn'");
    echo "<script>alert('Data diperbarui');location='menu.php?page=alternatif';</script>";
}

if(isset($_GET['hapus'])){
    $nisn = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM alternatif WHERE nisn='$nisn'");
    echo "<script>alert('Data dihapus');location='menu.php?page=alternatif';</script>";
}
?>

<div class="flex flex-row items-center justify-center md:justify-between gap-4 mb-6">    
    <div class="flex-none">
        <button onclick="openTambah()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition duration-200">
            <i class="fa fa-plus mr-1"></i> Tambah Data
        </button>
    </div>
    <div class="w-full md:w-auto flex justify-center">
        <form method="POST" action="menu.php?page=alternatif" class="flex shadow-sm">
            <input type="text" name="search_nisn" 
                placeholder="Cari NISN..." 
                value="<?php echo $search_nisn; ?>" 
                class="border border-gray-300 rounded-l px-4 py-2 w-full max-w-[200px] md:w-36 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 rounded-r transition duration-200">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>
</div>
<!-- CARD -->
<div class="w-full bg-white shadow rounded-lg overflow-hidden">
    <div class="p-4 border-b font-semibold text-center bg-gray-50 text-gray-700">
        Data Alternatif
    </div>
    <div class="overflow-x-auto w-full">
        <table class="min-w-[800px] w-full text-sm text-center border-collapse">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Kelas</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">NISN</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Tanggal Lahir</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Alamat</th>
                    <th class="px-4 py-3 font-medium uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while($row=mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-indigo-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap"><?php echo $row['kelas']; ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900"><?php echo $row['nisn']; ?></td>
                    <td class="px-4 py-3 whitespace-nowrap"><?php echo $row['nama']; ?></td>
                    <td class="px-4 py-3 whitespace-nowrap"><?php echo date('d-m-Y',strtotime($row['tanggal_lahir'])); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap"><?php echo $row['jenis_kelamin']; ?></td>
                    <td class="px-4 py-3"><?php echo $row['alamat']; ?></td>
                    <td class="px-4 py-3 whitespace-nowrap space-x-1">
                        <button onclick="openEdit('<?php echo $row['nisn']; ?>','<?php echo $row['kelas']; ?>','<?php echo $row['nama']; ?>','<?php echo $row['tanggal_lahir']; ?>','<?php echo $row['jenis_kelamin']; ?>','<?php echo $row['alamat']; ?>')" 
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1.5 rounded-md shadow-sm transition">
                            <i class="fa fa-edit"></i>
                        </button>
                        <a href="menu.php?page=alternatif&hapus=<?php echo $row['nisn']; ?>" 
                            onclick="return confirm('Yakin hapus data?')" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md shadow-sm transition">
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
        <h2 class="text-lg font-semibold mb-4">Tambah Alternatif</h2>
        <form method="POST">
            <label class="text-sm">Pilih Kelas</label>
            <select name="kelas" required class="border p-2 rounded w-full mb-3">
                <option value=""> </option>
                <option>I</option>
                <option>II</option>
                <option>III</option>
                <option>IV</option>
                <option>V</option>
                <option>VI</option>
            </select>
            
            <label class="text-sm">NISN</label>
            <input type="number" name="nisn" required class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Nama</label>
            <input type="text" name="nama" required class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="border p-2 rounded w-full mb-3">
                <option> </option>
                <option>Laki-laki</option>
                <option>Perempuan</option>
            </select>
            
            <label class="text-sm">Alamat</label>
            <textarea name="alamat" class="border p-2 rounded w-full mb-4"></textarea>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeTambah()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button name="tambah" class="px-4 py-2 bg-green-600 text-white rounded">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Edit Data</h2>
        <form method="POST">
            <label class="text-sm">Pilih Kelas</label>
            <select name="kelas" id="editKelas" class="border p-2 rounded w-full mb-3">
                <option>I</option>
                <option>II</option>
                <option>III</option>
                <option>IV</option>
                <option>V</option>
                <option>VI</option>
            </select>
            
            <label class="text-sm">NISN</label>
            <input type="number" id="editNisn" name="nisn" readonly class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Nama</label>
            <input type="text" id="editNama" name="nama" class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Tanggal Lahir</label>
            <input type="date" id="editTanggal" name="tanggal_lahir" class="border p-2 rounded w-full mb-3">
            
            <label class="text-sm">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="editJK" class="border p-2 rounded w-full mb-3">
                <option>Laki-laki</option>
                <option>Perempuan</option>
            </select>
            
            <label class="text-sm">Alamat</label>
            <textarea name="alamat" id="editAlamat" class="border p-2 rounded w-full mb-4"></textarea>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEdit()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button name="ubah" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
function openTambah(){
    document.getElementById("modalTambah").classList.remove("hidden");
    document.getElementById("modalTambah").classList.add("flex");
}
function closeTambah(){
    document.getElementById("modalTambah").classList.add("hidden");
}
function openEdit(nisn,kelas,nama,tanggal,jk,alamat){
    document.getElementById("editNisn").value = nisn;
    document.getElementById("editKelas").value = kelas;
    document.getElementById("editNama").value = nama;
    document.getElementById("editTanggal").value = tanggal;
    document.getElementById("editJK").value = jk;
    document.getElementById("editAlamat").value = alamat;
    document.getElementById("modalEdit").classList.remove("hidden");
    document.getElementById("modalEdit").classList.add("flex");
}
function closeEdit(){
    document.getElementById("modalEdit").classList.add("hidden");
}
</script>