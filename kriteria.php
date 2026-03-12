<?php
   include 'connection.php';
   if (session_status() == PHP_SESSION_NONE) {
      session_start();
   }
   if (!isset($_SESSION['username'])) {
      header("Location: index.php");
      exit();
   }
   // PROSES TAMBAH
   if(isset($_POST['tambah'])){
      $kode = $_POST['kode'];
      $nama = $_POST['nama'];
      $bobot = $_POST['bobot'];
      $check = mysqli_query($koneksi,"SELECT * FROM kriteria WHERE kode='$kode'");
      if(mysqli_num_rows($check)>0){
         echo "<script>alert('Kode sudah ada');location='menu.php?page=kriteria';</script>";
      }else{
         mysqli_query($koneksi,"INSERT INTO kriteria VALUES('$kode','$nama','$bobot')");
         $nama_kolom = preg_replace('/[^a-zA-Z0-9_]/','_',strtolower($nama));
         mysqli_query($koneksi,"ALTER TABLE penilaian ADD `$nama_kolom` DOUBLE");
         echo "<script>alert('Data ditambahkan');location='menu.php?page=kriteria';</script>";
      }
   }
   // PROSES UBAH
   if (isset($_POST['ubah'])) {
      $kode = $_POST['kode'];
      $nama_baru = $_POST['nama'];
      $bobot = $_POST['bobot'];
      /* ambil nama lama */
      $q = mysqli_query($koneksi,"SELECT nama FROM kriteria WHERE kode='$kode'");
      $data = mysqli_fetch_assoc($q);
      $nama_lama = $data['nama'];
      /* ubah format nama kolom */
      $kolom_lama = preg_replace('/[^a-zA-Z0-9_]/','_',strtolower($nama_lama));
      $kolom_baru = preg_replace('/[^a-zA-Z0-9_]/','_',strtolower($nama_baru));
      /* jika nama berubah → rename kolom */
      if($kolom_lama != $kolom_baru){
         $alter = "ALTER TABLE penilaian CHANGE `$kolom_lama` `$kolom_baru` DOUBLE";
         mysqli_query($koneksi,$alter);
      }
      /* update data kriteria */
      mysqli_query($koneksi,"UPDATE kriteria SET nama='$nama_baru', bobot='$bobot' WHERE kode='$kode'");
      echo "<script>alert('Data diperbarui');location='menu.php?page=kriteria';</script>";
   }
   // PROSES HAPUS
   if(isset($_GET['hapus'])){
      $kode = $_GET['hapus'];
      $get = mysqli_query($koneksi,"SELECT nama FROM kriteria WHERE kode='$kode'");
      $data = mysqli_fetch_assoc($get);
      $nama_kolom = preg_replace('/[^a-zA-Z0-9_]/','_',strtolower($data['nama']));
      mysqli_query($koneksi,"DELETE FROM kriteria WHERE kode='$kode'");
      mysqli_query($koneksi,"ALTER TABLE penilaian DROP COLUMN `$nama_kolom`");
      echo "<script>alert('Data dihapus');location='menu.php?page=kriteria';</script>";
   }
   // KODE OTOMATIS
   $q = mysqli_query($koneksi,"SELECT kode FROM kriteria ORDER BY kode DESC LIMIT 1");
   $kode_baru="C1";
   if($d=mysqli_fetch_assoc($q)){
      $last = intval(substr($d['kode'],1));
      $kode_baru = "C".($last+1);
   }
?>
<!-- ACTION BAR -->
<button onclick="openTambah()"
   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow mb-4">
   <i class="fa fa-plus mr-1"></i> Tambah Data
</button>
<!-- CARD -->
<div class="bg-white shadow rounded-lg overflow-hidden">
   <div class="p-4 border-b font-semibold text-center">
      Data Kriteria
   </div>
   <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-center">
         <thead class="bg-indigo-600 text-white">
            <tr>
               <th class="px-4 py-3">Kode</th>
               <th class="px-4 py-3">Nama</th>
               <th class="px-4 py-3">Bobot</th>
               <th class="px-4 py-3">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y">
            <?php
               $query = mysqli_query($koneksi,"SELECT * FROM kriteria");
               while($row=mysqli_fetch_assoc($query)){
            ?>
            <tr class="hover:bg-gray-50">
               <td class="px-4 py-2"><?php echo $row['kode']; ?></td>
               <td class="px-4 py-2"><?php echo $row['nama']; ?></td>
               <td class="px-4 py-2"><?php echo $row['bobot']; ?></td>
               <td class="px-4 py-2 space-x-1">
                  <button onclick="openEdit(
                     '<?php echo $row['kode']; ?>',
                     '<?php echo $row['nama']; ?>',
                     '<?php echo $row['bobot']; ?>'
                     )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">
                     <i class="fa fa-edit"></i>
                  </button>
                  <a href="menu.php?page=kriteria&hapus=<?php echo $row['kode']; ?>"
                     onclick="return confirm('Yakin hapus data?')"
                     class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                     <i class="fa fa-trash"></i>
                  </a>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
</div>
<!-- MODAL TAMBAH -->
<div id="modalTambah"
   class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
   <div class="bg-white rounded-lg shadow w-full max-w-md p-6">
      <h2 class="text-lg font-semibold mb-4">Tambah Kriteria</h2>
      <form method="POST">
         <label class="text-sm">Kode</label>
         <input type="text" name="kode" value="<?php echo $kode_baru; ?>" readonly class="border p-2 rounded w-full mb-3">
         <label class="text-sm">Nama</label>
         <input type="text" name="nama" required class="border p-2 rounded w-full mb-3">
         <label class="text-sm">Bobot</label>
         <input type="number" step="0.01" name="bobot" required class="border p-2 rounded w-full mb-4">
         <div class="flex justify-end gap-2">
            <button type="button" onclick="closeTambah()" class="px-4 py-2 bg-gray-400 text-white rounded">
               Batal
            </button>
            <button name="tambah" class="px-4 py-2 bg-green-600 text-white rounded">
               Tambah
            </button>
         </div>
      </form>
   </div>
</div>
<!-- MODAL EDIT -->
<div id="modalEdit"
   class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
   <div class="bg-white rounded-lg shadow w-full max-w-md p-6">
      <h2 class="text-lg font-semibold mb-4">Edit Kriteria</h2>
      <form method="POST">
         <label>Kode</label>
         <input type="text" id="editKode" name="kode" readonly class="border p-2 rounded w-full mb-3">
         <label>Nama</label>
         <input type="text" id="editNama" name="nama" required class="border p-2 rounded w-full mb-3">
         <label>Bobot</label>
         <input type="number" step="0.01" id="editBobot" name="bobot" required class="border p-2 rounded w-full mb-4">
         <div class="flex justify-end gap-2">
            <button type="button" onclick="closeEdit()" class="px-4 py-2 bg-gray-400 text-white rounded">
               Batal
            </button>
            <button name="ubah" class="px-4 py-2 bg-indigo-600 text-white rounded">
               Simpan
            </button>
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
   function openEdit(kode,nama,bobot){
      document.getElementById("editKode").value=kode;
      document.getElementById("editNama").value=nama;
      document.getElementById("editBobot").value=bobot;
      document.getElementById("modalEdit").classList.remove("hidden");
      document.getElementById("modalEdit").classList.add("flex");
   }
   function closeEdit(){
      document.getElementById("modalEdit").classList.add("hidden");
   }
</script>