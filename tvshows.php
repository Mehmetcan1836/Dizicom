<?php
session_start();

// API'den dizi verilerini alalım
$apiKey = '995d04f68f5dc7d1299752f1510bc93d'; // API anahtarınızı buraya ekleyin
$url = "https://api.themoviedb.org/3/discover/tv?api_key=$apiKey&language=tr-TR";
$response = file_get_contents($url);
$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diziler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Diziler</h2>
        <div class="row">
            <?php
            foreach ($data['results'] as $tvShow) {
                echo '<div class="col-md-3 mb-4">';
                echo '<div class="card">';
                echo '<img src="https://image.tmdb.org/t/p/w500' . $tvShow['poster_path'] . '" class="card-img-top" alt="' . $tvShow['name'] . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $tvShow['name'] . '</h5>';
                echo '<p class="card-text">' . substr($tvShow['overview'], 0, 100) . '...</p>';
                echo '<a href="tvshow-details.php?id=' . $tvShow['id'] . '" class="btn btn-primary">Detaylar</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
