<?php
// API Anahtarı ve Temel Ayarlar
$apiKey = '995d04f68f5dc7d1299752f1510bc93d'; // Buraya kendi API anahtarınızı ekleyin
$genres = [
    '28' => 'Aksiyon', 
    '16' => 'Animasyon', 
    '35' => 'Komedi', 
    '80' => 'Suç', 
    '99' => 'Belgesel', 
    '18' => 'Dram', 
    '10751' => 'Aile', 
    '14' => 'Fantastik', 
    '36' => 'Tarih', 
    '27' => 'Korku', 
    '10402' => 'Müzik', 
    '9648' => 'Gizem', 
    '10749' => 'Romantik', 
    '53' => 'Gerilim', 
    '10770' => 'Reality TV', 
    '878' => 'Bilim Kurgu', 
    '10752' => 'Savaş', 
    '37' => 'Western'
]; // Filmler için türler
$yearRange = range(2000, 2024); // Yıl aralığı örneği
$mediaType = isset($_GET['type']) && $_GET['type'] == 'tv' ? 'tv' : 'movie'; // Film veya dizi seçimi

// Parametreler
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : ''; // Sıralama

// TMDB API URL
$baseUrl = "https://api.themoviedb.org/3/discover/$mediaType?api_key=$apiKey&language=tr-TR";
if ($genre) {
    $baseUrl .= "&with_genres=" . urlencode($genre);
}
if ($year) {
    $baseUrl .= "&year=" . urlencode($year);
}
if ($sortBy) {
    if ($sortBy == 'high_rating') {
        $baseUrl .= "&sort_by=vote_average.desc"; // IMDb Puanına Göre Yüksekten Düşüğe
    } elseif ($sortBy == 'low_rating') {
        $baseUrl .= "&sort_by=vote_average.asc"; // IMDb Puanına Göre Düşükten Yükseğe
    } elseif ($sortBy == 'year_desc') {
        $baseUrl .= "&sort_by=release_date.desc"; // Yıla Göre (Yeniden Eskiye)
    }
}

$response = file_get_contents($baseUrl);
$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Sağ Panel (Film/Dizi Arama Botu) -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Film/Dizi Arama Botu</h5>
                    </div>
                    <div class="card-body">
                        <form action="movies.php" method="GET">
                            <!-- Film/Dizi Seçimi -->
                            <div class="mb-3">
                                <label for="type" class="form-label">İçerik Türü</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="movie" <?= $mediaType == 'movie' ? 'selected' : '' ?>>Film</option>
                                    <option value="tv" <?= $mediaType == 'tv' ? 'selected' : '' ?>>Dizi</option>
                                </select>
                            </div>

                            <!-- Tür Seçimi -->
                            <div class="mb-3">
                                <label for="genre" class="form-label">Tür Seçin</label>
                                <select class="form-select" id="genre" name="genre">
                                    <option value="">Tümü</option>
                                    <?php foreach ($genres as $key => $genreName): ?>
                                        <option value="<?= $key ?>" <?= $key == $genre ? 'selected' : '' ?>><?= $genreName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Yıl Seçimi -->
                            <div class="mb-3">
                                <label for="year" class="form-label">Yıl Seçin</label>
                                <select class="form-select" id="year" name="year">
                                    <option value="">Tümü</option>
                                    <?php foreach ($yearRange as $yr): ?>
                                        <option value="<?= $yr ?>" <?= $yr == $year ? 'selected' : '' ?>><?= $yr ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Sıralama Seçimi -->
                            <div class="mb-3">
                                <label for="sort_by" class="form-label">Sıralama Seçimi</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="">Sıralama Seçin</option>
                                    <option value="high_rating" <?= $sortBy == 'high_rating' ? 'selected' : '' ?>>IMDb Puanına Göre Yüksek</option>
                                    <option value="low_rating" <?= $sortBy == 'low_rating' ? 'selected' : '' ?>>IMDb Puanına Göre Düşük</option>
                                    <option value="year_desc" <?= $sortBy == 'year_desc' ? 'selected' : '' ?>>Yıla Göre (Yeniden Eskiye)</option>
                                </select>
                            </div>

                            <!-- Arama Butonu -->
                            <button type="submit" class="btn btn-primary">Ara</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Film Listesi -->
            <div class="col-md-8">
                <h2>Filmler</h2>
                <div class="row">
                    <?php if (!empty($data['results'])): ?>
                        <?php foreach ($data['results'] as $movie): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <!-- Film Poster Görseli -->
                                    <?php if (isset($movie['poster_path'])): ?>
                                        <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" class="card-img-top" alt="<?= isset($movie['title']) ? htmlspecialchars($movie['title']) : 'Film Resmi' ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/500x750?text=Resim+Bulunamadı" class="card-img-top" alt="Resim Bulunamadı">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <!-- Film Başlık -->
                                        <h5 class="card-title">
                                            <?= isset($movie['title']) ? htmlspecialchars($movie['title']) : 'Başlık Bulunamadı' ?>
                                        </h5>
                                        <a href="detail.php?id=<?= $movie['id'] ?>&type=movie" class="btn btn-primary">Detaylar</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aradığınız kriterlere uygun film bulunamadı.</p>
                    <?php endif; ?>
                </div>
            </div>

            
        </div>
        <!-- Yukarı Çık Butonu -->
    <button id="scrollToTopBtn" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; display: none; z-index: 1000;">
        <img src="img/arrow-up.png" alt="Up">
    </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
