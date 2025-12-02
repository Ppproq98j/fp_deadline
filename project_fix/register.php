<?php
require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (fullname, email, password) 
                  VALUES ('$fullname', '$email', '$password')";
        mysqli_query($conn, $query);

        header("Location: login.php?registered=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Deadline Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white rounded-xl shadow p-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">Daftar Akun</h1>
    <p class="text-center text-gray-500 mb-5">Buat akun baru untuk memulai</p>

    <?php if (isset($error)): ?>
    <p class="text-red-500 mb-3 text-center"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label class="block text-gray-700 mb-2">Nama Lengkap</label>
        <input type="text" name="fullname" required
               class="w-full px-4 py-2 border rounded-lg mb-4"
               placeholder="John Doe">

        <label class="block text-gray-700 mb-2">Email</label>
        <input type="email" name="email" required
               class="w-full px-4 py-2 border rounded-lg mb-4"
               placeholder="email@example.com">

        <label class="block text-gray-700 mb-2">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2 border rounded-lg mb-4"
               placeholder="******">

        <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
            Daftar
        </button>
    </form>

    <p class="text-center mt-4 text-gray-600">
        Sudah punya akun? <a href="login.php" class="text-blue-600 font-semibold">Masuk di sini</a>
    </p>
</div>

</body>
</html>
