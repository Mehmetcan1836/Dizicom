<?php
session_start();

// Veritabanı bağlantısı (db_connection.php dosyanızın içeriği)
include('db_connection.php');

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['username'];
$profileImage = $_SESSION['profile_image'] ?? 'img/default-profile.png';
$userEmail = $_SESSION['email'] ?? '';  // Email adresini almak için session kullanabilirsiniz.

// Profil resmi güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    // Profil resmi dosyasını al
    $profileImageFile = $_FILES['profile_image'];

    // Hedef dizin ve dosya adı
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($profileImageFile["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Dosya türünü kontrol et
    if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        // Dosya yükle
        if (move_uploaded_file($profileImageFile["tmp_name"], $targetFile)) {
            // Profil resmini veritabanına kaydet
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE username = ?");
            $stmt->bind_param("ss", $targetFile, $userName);
            $stmt->execute();

            // Profil resmi başarıyla güncellendiğinde session'dan yeni resmi güncelle
            $_SESSION['profile_image'] = $targetFile;

        } else {
            echo "Dosya yükleme başarısız oldu. Lütfen tekrar deneyin.";
        }
    } else {
        echo "Geçersiz dosya formatı. Lütfen jpg, jpeg, png veya gif dosyası yükleyin.";
    }
}

// İsim ve e-posta güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_name'])) {
    $newUserName = $_POST['username'];
    $newUserEmail = $_POST['email'];

    // Veritabanında güncelleme işlemi
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE username = ?");
    $stmt->bind_param("sss", $newUserName, $newUserEmail, $userName);
    $stmt->execute();

    // Güncellenen kullanıcı adını ve email adresini session'da güncelle
    $_SESSION['username'] = $newUserName;
    $_SESSION['email'] = $newUserEmail;


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Profil Düzenle</h2>
    <form action="edit-profile.php" method="POST">
        <div class="form-group">
            <label for="username">Yeni Kullanıcı Adı</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($userName) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Yeni E-posta Adresi</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($userEmail) ?>" >
        </div>

        <button type="submit" name="update_name" class="btn btn-primary">Bilgileri Güncelle</button>
    </form>
    <hr>
    <!-- Profil Resmi Güncelleme Formu -->
    <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
    <div class="mt-4">
        <h3>Mevcut Profil Resminiz</h3>
        <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profil Resmi" width="150" height="150" class="rounded-circle">
    </div>
        <div class="form-group">
            <label for="profile_image">Profil Resmi Seç</label>
            <input type="file" name="profile_image" id="profile_image" class="form-control" required>
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Profil Resmini Güncelle</button>
    </form>

    

    <hr>

    <!-- Kullanıcı Bilgilerini Güncelleme Formu -->
    

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
