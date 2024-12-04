<?php
$servername = "localhost";
$username = "root";  // XAMPP ile varsayılan kullanıcı adı
$password = "";      // XAMPP ile varsayılan şifre boş
$dbname = "movie_database";  // Kendi veritabanı adınızı buraya yazın

// Veritabanı bağlantısı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}
?>
