<?php
session_start();
require_once 'config.php';  // Veritabanı bağlantısı ve API anahtarlarını içeren dosya

$movieId = $_GET['id'] ?? null;  // Film ID'sini URL'den alıyoruz

// Film veya dizi türünü belirlemek için $type değişkenini tanımla
$type = isset($_GET['type']) && $_GET['type'] === 'tv' ? 'tv' : 'movie';

if (!$movieId) {
    die("Film ID'si belirtilmemiş!");
}

// Yorumları ve derecelendirmeleri al
$sql = "SELECT comment, rating FROM comments WHERE movie_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();

// API istekleri için güvenli bir şekilde CURL kullanarak verileri alıyoruz
function fetchApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// API URL'lerini belirleyelim
$detailUrl = TMDB_BASE_URL . "/{$type}/{$movieId}?api_key=" . TMDB_API_KEY . "&language=en-US";
$videosUrl = TMDB_BASE_URL . "/{$type}/{$movieId}/videos?api_key=" . TMDB_API_KEY . "&language=en-US";
$similarUrl = TMDB_BASE_URL . "/{$type}/{$movieId}/similar?api_key=" . TMDB_API_KEY . "&language=en-US&page=1";

// API yanıtlarını alma
$details = fetchApiData($detailUrl);
$videos = fetchApiData($videosUrl)['results'] ?? [];
$similar = fetchApiData($similarUrl)['results'] ?? [];

// Verileri işleme
$title = $details['title'] ?? $details['name'] ?? 'Bilinmiyor';
$overview = $details['overview'] ?? 'Bu içerik için açıklama bulunmamaktadır.';
$poster = !empty($details['poster_path']) ? TMDB_IMAGE_BASE_URL . '/w500' . $details['poster_path'] : 'placeholder.jpg';
$releaseDate = $details['release_date'] ?? $details['first_air_date'] ?? 'Bilinmiyor';
$voteAverage = $details['vote_average'] ?? 'N/A';
$genres = isset($details['genres']) ? array_map(fn($genre) => $genre['name'], $details['genres']) : [];
$runtime = $details['runtime'] ?? ($details['episode_run_time'][0] ?? null);
$language = $details['original_language'] ?? 'Bilinmiyor';
$homepage = $details['homepage'] ?? null;
$tagline = $details['tagline'] ?? null;
$status = $details['status'] ?? 'Bilinmiyor';
$productionCompanies = isset($details['production_companies']) ? array_map(fn($company) => $company['name'], $details['production_companies']) : [];
$productionCountries = isset($details['production_countries']) ? array_map(fn($country) => $country['name'], $details['production_countries']) : [];

$movieId = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/logo.png">
</head>
<body>

<!-- Navbar -->
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <img src="<?php echo htmlspecialchars($poster); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <?php if (!empty($tagline)): ?>
                <p><em><?php echo htmlspecialchars($tagline); ?></em></p>
            <?php endif; ?>
            <p><strong>Çıkış Tarihi:</strong> <?php echo htmlspecialchars($releaseDate); ?></p>
            <p><strong>Oy Ortalaması:</strong> <?php echo htmlspecialchars($voteAverage); ?></p>
            <p><strong>Türler:</strong> <?php echo !empty($genres) ? implode(', ', $genres) : 'Bilinmiyor'; ?></p>
            <p><strong>Süre:</strong> <?php echo $runtime ? $runtime . ' dakika' : 'Bilinmiyor'; ?></p>
            <p><strong>Dil:</strong> <?php echo htmlspecialchars($language); ?></p>
            <p><strong>Durum:</strong> <?php echo htmlspecialchars($status); ?></p>
            <p><strong>Yapımcı Şirketler:</strong> <?php echo !empty($productionCompanies) ? implode(', ', $productionCompanies) : 'Bilinmiyor'; ?></p>
            <p><strong>Yapımcı Ülkeler:</strong> <?php echo !empty($productionCountries) ? implode(', ', $productionCountries) : 'Bilinmiyor'; ?></p>
            <p><?php echo htmlspecialchars($overview); ?></p>
            <?php if ($homepage): ?>
                <p><a href="<?php echo htmlspecialchars($homepage); ?>" target="_blank" class="btn btn-primary">Resmi Web Sitesine Git</a></p>
            <?php endif; ?>
            <!--<button class="btn btn-outline-primary favorite-btn" data-movie-id="<?php echo $movieId; ?>">
                Favorilere Ekle
            </button>-->
            <form action="add_to_favorites.php" method="POST">
    <input type="hidden" name="movie_title" value="Film Başlığı">
    <input type="hidden" name="movie_poster" value="film-poster.jpg">
    <button type="submit">Favorilere Ekle</button>
</form>

            <h3 class="mt-5">Fragmanlar</h3>
            <?php foreach ($videos as $video): ?>
                <div>
                    <a href="https://www.youtube.com/watch?v=<?php echo htmlspecialchars($video['key']); ?>" target="_blank">Fragmanı İzle</a>
                </div>
            <?php endforeach; ?>
            
        </div>
    </div>

    <!-- Yorum ve Derecelendirme Formu -->
    <form method="POST" class="mt-4">
        <h3>Yorum Yap ve Puan Ver</h3>
        <div class="mb-3">
            <textarea class="form-control" name="comment" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="rating">Puan (1-10):</label>
            <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" required>
        </div>
        <button type="submit" name="submit_comment" class="btn btn-primary">Yorum Gönder</button>
    </form>

    <h3 class="mt-5">Benzer Filmler</h3>
    <div class="row">
        <?php foreach ($similar as $sim): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <img src="<?php echo TMDB_IMAGE_BASE_URL . '/w500' . $sim['poster_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($sim['title'] ?? $sim['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($sim['title'] ?? $sim['name']); ?></h5>
                        <a href="detail.php?id=<?php echo $sim['id']; ?>" class="btn btn-primary">Detayları Gör</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Yorumlar -->
<div class="container mt-5">
    <h3>Yorumlar</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="border p-3 mb-2">
            <p><strong>Derecelendirme:</strong> <?php echo htmlspecialchars($row['rating']); ?>/10</p>
            <p><?php echo htmlspecialchars($row['comment']); ?></p>
        </div>
    <?php endwhile; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            const favoriteButtons = document.querySelectorAll('.favorite-btn');

            favoriteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const movieId = this.getAttribute('data-movie-id');

                    fetch('add_to_favorites.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ movie_id: movieId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            this.textContent = "Favorilere Eklendi";
                            this.disabled = true;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                    });
                });
            });
        });
    </script>

</body>
</html>
