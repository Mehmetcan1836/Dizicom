<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Kullanıcı oturum kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['username'];
$profileImage = $_SESSION['profile_image'] ?? 'img/default-profile.png';
$userId = $_SESSION['user_id']; // Kullanıcı ID'sini session'dan alıyoruz

// Veritabanı bağlantısını dahil et
include 'db_connection.php'; // db_connection.php dosyasını dahil ediyoruz

// Favori filmleri çekmek için SQL sorgusu
$sql = "SELECT f.movie_title, f.movie_poster, g.genre_name
        FROM favorites f
        JOIN genres g ON f.genre_id = g.genre_id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Kullanıcı ID'sini parametre olarak bağlıyoruz
$stmt->execute();
$result = $stmt->get_result();

// Favori filmleri bir diziye almak
$favoriteMovies = [];
while ($row = $result->fetch_assoc()) {
    $favoriteMovies[] = $row;
}
$stmt->close();
?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['username'];
$profileImage = $_SESSION['profile_image'] ?? 'img/default-profile.png';
$userId = $_SESSION['user_id']; // Kullanıcı ID'sini session'dan alıyoruz

// Veritabanı bağlantısını dahil et
include 'db_connection.php'; // db_connection.php dosyasını dahil ediyoruz

// Favori filmleri çekmek için SQL sorgusu
$sql = "SELECT f.movie_title, f.movie_poster, g.genre_name
        FROM favorites f
        JOIN genres g ON f.genre_id = g.genre_id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Kullanıcı ID'sini parametre olarak bağlıyoruz
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
    <title>Profilim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            display: flex;
            align-items: center;
            padding: 2rem;
            background-color: #343a40;
            color: #fff;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
            margin-right: 2rem;
        }

        .profile-body {
            padding: 2rem;
        }

        .profile-body h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #343a40;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Profil Sayfası -->
    <div class="profile-container">
        <div class="profile-header">
            <!-- Profil Resmi -->
            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profil Resmi">
            <div>
                <h1><?= htmlspecialchars($userName) ?></h1>
                <p>Üyelik Tarihi: 01/12/2024</p>
                <a href="edit-profile.php" class="btn btn-primary">Profil Düzenle</a>
            </div>
        </div>

        <div class="profile-body">
            <h2>Favori Filmleriniz</h2>
            <?php if (count($favoriteMovies) > 0): ?>
                <div class="movie-list">
                    <?php foreach ($favoriteMovies as $movie): ?>
                        <div class="movie-item">
                            <img src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['movie_poster']) ?>" alt="<?= htmlspecialchars($movie['movie_title']) ?>">
                            <h5><?= htmlspecialchars($movie['movie_title']) ?></h5>
                            <p><strong>Tür:</strong> <?= htmlspecialchars($movie['genre_name']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Henüz favori filminiz yok.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
