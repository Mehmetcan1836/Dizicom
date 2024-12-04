<?php
session_start();
require_once 'config.php';

// Yorum yapabilmek için kullanıcı giriş yapmış olmalı
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Yorum verisi ve movie_id kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['movie_id'])) {
    $userId = $_SESSION['user_id'];
    $movieId = $_POST['movie_id'];
    $commentContent = trim($_POST['comment']);

    // Yorum boş olmamalı
    if (!empty($commentContent)) {
        // SQL sorgusuyla veritabanına yorum ekleme
        $query = "INSERT INTO comments (user_id, movie_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iis", $userId, $movieId, $commentContent);

        if ($stmt->execute()) {
            // Yorum başarılı bir şekilde eklendiyse, ilgili film veya dizi sayfasına yönlendir
            header("Location: detail.php?id={$movieId}&type=movie"); // ya da type=tv olabilir
            exit();
        } else {
            // Hata mesajı
            echo "Yorum eklenirken bir hata oluştu!";
        }
    } else {
        echo "Lütfen yorumunuzu girin.";
    }
} else {
    // Yorum yapılabilmesi için gerekli parametreler yoksa hata mesajı
    echo "Geçersiz istek.";
}
?>
