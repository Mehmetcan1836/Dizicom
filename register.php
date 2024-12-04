<?php
session_start();
require_once 'config.php';

// Kayıt işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $passwordConfirm = mysqli_real_escape_string($conn, $_POST['password_confirm']);

    // Şifre doğrulaması
    if ($password !== $passwordConfirm) {
        $error = "Şifreler uyuşmuyor!";
    } else {
        // Şifreyi hash'le
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Kullanıcıyı veritabanına ekle
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            header("Location: index.php");  // Kayıt başarılı, ana sayfaya yönlendir
            exit();
        } else {
            $error = "Kayıt işlemi sırasında bir hata oluştu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Custom CSS -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Film&Dizi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Ana Sayfa</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Kullanıcı giriş yaptıysa çıkış yap butonu görünsün -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?logout=true">Çıkış Yap</a>
                    </li>
                <?php else: ?>
                    <!-- Kullanıcı giriş yapmamışsa giriş ve kayıt ol butonları görünsün -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Kayıt Ol</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h2 class="mt-5">Kayıt Ol</h2>
    <form method="POST" class="register-form">
        <div class="mb-3">
            <label for="username" class="form-label">Kullanıcı Adı</label>
            <input type="text" name="username" class="form-control" id="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-posta</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Şifreyi Tekrar Girin</label>
            <input type="password" name="password_confirm" class="form-control" id="password_confirm" required>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Kayıt Ol</button>
    </form>
    <p class="mt-3">Hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
</div>
</body>
</html>