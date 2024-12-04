<?php
require_once 'config.php';

// Gerekli fonksiyonları ekleyelim
function fetchGenres($type = 'movie') {
    $url = TMDB_BASE_URL . "/genre/{$type}/list?api_key=" . TMDB_API_KEY . "&language=en-US";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['genres'] ?? [];
}
// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    // Kullanıcı oturum açmamışsa, öneri gösterme
    echo "Lütfen giriş yapın.";
    header("Location: login.php");
    exit;
}
// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
// Parametreleri alalım
$selectedType = $_GET['type'] ?? 'movie';
$selectedGenre = $_GET['genre'] ?? '';
$selectedYear = $_GET['year'] ?? '';
$selectedVote = $_GET['vote'] ?? '';
$selectedLanguage = $_GET['language'] ?? '';
$resultsCount = $_GET['results'] ?? 10;

// Sonuçları almak için TMDB API'ye istekte bulunalım
$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($selectedGenre)) {
    $url = TMDB_BASE_URL . "/discover/{$selectedType}?api_key=" . TMDB_API_KEY .
        "&with_genres={$selectedGenre}" . 
        (!empty($selectedYear) ? "&primary_release_year={$selectedYear}" : '') .
        (!empty($selectedVote) ? "&vote_average.gte={$selectedVote}" : '') .
        (!empty($selectedLanguage) ? "&with_original_language={$selectedLanguage}" : '') .
        "&language=en-US&page=1";

    $response = file_get_contents($url);
    $data = json_decode($response, true);
    $results = $data['results'] ?? [];
    shuffle($results); // Rastgele sıralama
    $results = array_slice($results, 0, $resultsCount);
}

$movieGenres = fetchGenres('movie');
$tvGenres = fetchGenres('tv');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rulet - Film ve Dizi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="style.css">  <!-- Dışa bağlanmış CSS -->
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h1 class="mb-4">Film ve Dizi Ruleti</h1>
        <form method="GET" action="roulette.php" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label">Tür:</label>
                <select id="type" name="type" class="form-select">
                    <option value="movie" <?php echo $selectedType === 'movie' ? 'selected' : ''; ?>>Film</option>
                    <option value="tv" <?php echo $selectedType === 'tv' ? 'selected' : ''; ?>>Dizi</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="genre" class="form-label">Kategori:</label>
                <select id="genre" name="genre" class="form-select">
                    <option value="">Tüm Türler</option>
                    <?php
                    $genres = $selectedType === 'movie' ? $movieGenres : $tvGenres;
                    foreach ($genres as $genre) {
                        $selected = $selectedGenre == $genre['id'] ? 'selected' : '';
                        echo "<option value='{$genre['id']}' {$selected}>{$genre['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">Yıl:</label>
                <input type="number" id="year" name="year" class="form-control" value="<?php echo htmlspecialchars($selectedYear); ?>" placeholder="Örn: 2023">
            </div>
            <div class="col-md-2">
                <label for="vote" class="form-label">Min. Puan:</label>
                <input type="number" id="vote" name="vote" class="form-control" step="0.1" max="10" min="0" value="<?php echo htmlspecialchars($selectedVote); ?>" placeholder="0-10">
            </div>
            <div class="col-md-2">
                <label for="language" class="form-label">Dil:</label>
                <input type="text" id="language" name="language" class="form-control" value="<?php echo htmlspecialchars($selectedLanguage); ?>" placeholder="Örn: en">
            </div>
            <div class="col-md-2">
                <label for="results" class="form-label">Sonuç Sayısı:</label>
                <input type="number" id="results" name="results" class="form-control" value="<?php echo htmlspecialchars($resultsCount); ?>" min="1" max="20">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Ruleti Çevir</button>
            </div>
        </form>

        <?php if (!empty($results)): ?>
            <h2 class="mt-5">Sonuçlar:</h2>
            <div class="row g-3">
                <?php foreach ($results as $item): ?>
                    <?php
                    $itemTitle = $item['title'] ?? $item['name'] ?? 'Bilinmiyor';
                    $itemPoster = !empty($item['poster_path']) ? TMDB_IMAGE_BASE_URL . '/w300' . $item['poster_path'] : 'placeholder.jpg';
                    $itemOverview = $item['overview'] ?? 'Açıklama yok.';
                    $itemId = $item['id'];
                    ?>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($itemPoster); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($itemTitle); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($itemTitle); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($itemOverview, 0, 100)); ?>...</p>
                                <a href="detail.php?id=<?php echo htmlspecialchars($itemId); ?>&type=<?php echo htmlspecialchars($selectedType); ?>" class="btn btn-primary">Detaylar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center mt-4">Sonuç bulunamadı. Lütfen kriterlerinizi kontrol edin.</p>
        <?php endif; ?>
    </div>

    <button id="scrollToTopBtn" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; display: none; z-index: 1000;">
        <img src="img/arrow-up.png" alt="Up">
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
