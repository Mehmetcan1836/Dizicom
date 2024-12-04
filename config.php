<?php
// Veritabanı bağlantısı
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_database');

// TMDB API bilgileri
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_API_KEY', '995d04f68f5dc7d1299752f1510bc93d');
define('TMDB_IMAGE_BASE_URL', 'https://image.tmdb.org/t/p');

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı hatalı: " . $conn->connect_error);
}

// Oturum başlatma
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


/**
 * TMDb API'den veri çeker.
 *
 * @param string $endpoint API çağrısının uç noktası
 * @param array $query Ek sorgu parametreleri
 * @return array|null API'den dönen veri (başarısızsa null)
 */
function fetch_from_tmdb($endpoint, $query = [])
{
    // Sorgu parametrelerini birleştir
    $query['api_key'] = TMDB_API_KEY;
    $query['language'] = 'en-US';
    $query_string = http_build_query($query);

    // Tam URL oluştur
    $url = TMDB_BASE_URL . $endpoint . '?' . $query_string;

    // API çağrısı
    $response = @file_get_contents($url);

    // API'den geçerli veri dönerse
    if ($response !== false) {
        $data = json_decode($response, true);
        if (!isset($data['status_code']) || $data['status_code'] === 200) {
            return $data;
        }
    }

    // Geçersiz bir durumda null döndür
    return null;
}
?>
