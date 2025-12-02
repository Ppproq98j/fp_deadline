<?php
session_start();
require "database.php";

if (isset($_SESSION["user"]) && is_array($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Gunakan prepared statement (AMAN)
    $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // Simpan hanya data yang aman
            $_SESSION["user"] = [
                "id" => $user["id"],
                "fullname" => $user["fullname"],
                "email" => $user["email"]
            ];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Deadline Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white rounded-xl shadow p-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">Deadline Manager</h1>
    <p class="text-center text-gray-500 mb-5">Kelola deadline Anda dengan efisien</p>
    <?php if (isset($error)): ?>
    <p class="text-red-500 mb-3 text-center"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label class="block text-gray-700 mb-2">Email</label>
        <input type="email" name="email" required
               class="w-full px-4 py-2 border rounded-lg mb-4 focus:outline-none"
               placeholder="email@example.com">

        <label class="block text-gray-700 mb-2">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2 border rounded-lg mb-4 focus:outline-none"
               placeholder="******">

        <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
            Masuk
        </button>
    </form>

    <p class="text-center mt-4 text-gray-600">
        Belum punya akun? <a href="register.php" class="text-blue-600 font-semibold">Daftar di sini</a>
    </p>
</div>

</body>
</html>
