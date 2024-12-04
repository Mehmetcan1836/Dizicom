<?php
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı ID'sini session'dan alıyoruz
$userId = $_SESSION['user_id']; // Kullanıcı oturum bilgisi

// Veritabanı bağlantısı
include 'config.php'; // Veritabanı bağlantısı (config.php dosyasını kullanıyoruz)

$sql = "SELECT movie_title, movie_poster FROM favorites WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Favori filmleri bir diziye almak
$favoriteMovies = [];
while ($row = $result->fetch_assoc()) {
    $favoriteMovies[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favori Filmler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .movie-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .movie-item {
            width: 150px;
            text-align: center;
        }

        .movie-item img {
            width: 100%;
            border-radius: 10px;
        }

        .movie-item h5 {
            font-size: 1rem;
            color: #343a40;
        }

        .container {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Favori Filmleriniz</h2>

        <?php if (count($favoriteMovies) > 0): ?>
            <div class="movie-list">
                <?php foreach ($favoriteMovies as $movie): ?>
                    <div class="movie-item">
                        <img src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['movie_poster']) ?>" alt="<?= htmlspecialchars($movie['movie_title']) ?>">
                        <h5><?= htmlspecialchars($movie['movie_title']) ?></h5>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Henüz favori filminiz yok.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
