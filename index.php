<!DOCTYPE html>
<html lang="en">
    <?php
        session_start();
        include "connection.php";
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | SPK TOPSIS</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-sm">
            <img src="gambar/topsis.png" class="w-full mb-6" alt="logo">
            <form method="POST">
                <!-- USERNAME -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">
                            <i class="fa fa-user"></i>
                        </span>
                        <input type="text" name="username" required placeholder="Masukkan username"
                        class="w-full border rounded px-10 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <!-- PASSWORD -->
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input id="password" type="password" name="password" required placeholder="Masukkan password"
                        class="w-full border rounded px-10 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-2 text-gray-500">
                            <i id="eyeIcon" class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <!-- BUTTON -->
                <button type="submit" name="login"
                    class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">LOGIN
                </button>
            </form>
            <?php
                if (isset($_POST['login'])) {
                    $username = mysqli_real_escape_string($koneksi,$_POST['username']);
                    $password = mysqli_real_escape_string($koneksi,$_POST['password']);
                    $sql = mysqli_query($koneksi,
                    "SELECT * FROM pengguna WHERE username='$username' AND password='$password'");
                    $cek = mysqli_num_rows($sql);
                    if ($cek > 0){
                        $user = mysqli_fetch_assoc($sql);
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = $user['full_name'];
                        echo "<script>window.location='menu.php'</script>";
                    }else{
                        echo "<script>alert('Username atau Password Salah')</script>";
                    }
                }
            ?>
        </div>
        <script>
            function togglePassword(){
                const password = document.getElementById("password");
                const icon = document.getElementById("eyeIcon");
                if(password.type === "password"){
                    password.type = "text";
                    icon.classList.replace("fa-eye","fa-eye-slash");
                }else{
                    password.type = "password";
                    icon.classList.replace("fa-eye-slash","fa-eye");
                }
            }
        </script>
    </body>
</html>