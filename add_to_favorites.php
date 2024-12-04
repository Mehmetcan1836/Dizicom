<?php
session_start();
include 'config.php'; // Veritabanı bağlantısı

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$movieTitle = $_POST['movie_title'];
$moviePoster = $_POST['movie_poster']; // Poster URL'si

// Favori filmi veritabanına ekliyoruz
$sql = "INSERT INTO favorites (user_id, movie_title, movie_poster) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $userId, $movieTitle, $moviePoster);
$stmt->execute();
$stmt->close();

header("Location: profile.php"); // Profil sayfasına yönlendir
exit;
?>
