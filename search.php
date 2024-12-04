<?php
// Gerekli başlıklar ve API bağlantıları burada olacak
require_once 'config.php'; 

$searchQuery = $_GET['q'] ?? '';

// TMDb API'den arama sonuçlarını alalım
$searchUrl = TMDB_BASE_URL . "/search/multi?api_key=" . TMDB_API_KEY . "&query=" . urlencode($searchQuery) . "&language=en-US&page=1";
$response = file_get_contents($searchUrl);
$data = json_decode($response, true);

// Film ve dizi sonuçları
$results = $data['results'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-4">
        <h1 class="mb-4">Arama Sonuçları</h1>
        
        <?php if (!empty($results)): ?>
            <div class="row">
                <?php foreach ($results as $item): ?>
                    <?php
                    // Her film veya dizi için ID ve başlık
                    $itemTitle = $item['title'] ?? $item['name'] ?? 'Bilinmiyor';
                    $itemPoster = !empty($item['poster_path']) ? TMDB_IMAGE_BASE_URL . '/w300' . $item['poster_path'] : 'placeholder.jpg';
                    $itemOverview = $item['overview'] ?? 'Açıklama yok.';
                    $itemId = $item['id'];
                    $itemType = $item['media_type'] ?? 'movie'; // 'movie' veya 'tv' olabilir
                    ?>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($itemPoster); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($itemTitle); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($itemTitle); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($itemOverview, 0, 100)); ?>...</p>
                                <a href="detail.php?id=<?php echo htmlspecialchars($itemId); ?>&type=<?php echo htmlspecialchars($itemType); ?>" class="btn btn-primary">Detaylar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Hiçbir sonuç bulunamadı.</p>
        <?php endif; ?>
    </div>
    <!-- Yukarı Çık Butonu -->
    <button id="scrollToTopBtn" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; display: none; z-index: 1000;">
        <img src="img/arrow-up.png" alt="Up">
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
