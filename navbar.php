<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Session'dan kullanıcı bilgilerini alın
$isLoggedIn = isset($_SESSION['username']);
$userName = $isLoggedIn ? $_SESSION['username'] : 'Misafir';
$profileImage = $isLoggedIn && isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'img/default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #343a40;
        }

        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }

        .navbar-brand:hover, .nav-link:hover {
            text-decoration: underline;
        }

        .dropdown-menu {
            min-width: 200px;
        }

        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Film&Dizi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="roulette.php">Rulet</a>
                    </li>
                    <form class="d-flex ms-3" method="get" action="search.php">
                    <input class="form-control me-2" type="search" name="q" placeholder="Film veya Dizi Ara" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Ara</button>
                </form>
                </ul>

                <!-- Kullanıcı Bilgileri -->
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profil Resmi" class="profile-image">
                                <?= htmlspecialchars($userName) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php">Profilim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>



<!-- Bootstrap Bundle with Popper (JavaScript) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
