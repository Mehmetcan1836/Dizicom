<?php
session_start();
require_once 'config.php';

// Giriş işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Veritabanından kullanıcıyı bul
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kullanıcıyı bul
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Şifreyi doğrula
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");  // Başarıyla giriş yaptıysa ana sayfaya yönlendir
            exit();
        } else {
            $error = "Geçersiz şifre!";
        }
    } else {
        $error = "Kullanıcı bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Custom CSS -->
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2 class="mt-5">Giriş Yap</h2>
    <form method="POST" class="login-form">
        <div class="mb-3">
            <label for="email" class="form-label">E-posta</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Giriş Yap</button>
    </form>
    <p class="mt-3">Henüz kaydınız yok mu? <a href="register.php">Kayıt Ol</a></p>
</div>
</body>
</html>
