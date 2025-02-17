<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['user_id'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналитика пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="styles.css" rel="stylesheet">
    <style>
        .agreement-count {
            font-size: 1.2em;
            font-weight: bold;
            color: #fff;
            background: #28a745;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .ad-card {
            margin-bottom: 20px;
        }
        .ad-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
            margin-bottom: 15px;
        }
        .ad-image:hover {
            transform: scale(1.05);
        }
        .card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            color: #333;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }
        .card-text {
            color: #666;
            margin-bottom: 15px;
        }
        .card-text strong {
            color: #333;
        }
        .info-item {
            margin-bottom: 8px;
            display: block;
        }
        strong a {
            color: #007bff;
            text-decoration: none;
            margin-right: 5px;
        }
        strong a:hover {
            color: #007bff88;
        }
    </style>

</head>
<body>

    <div class="admin-wrapper">
        <!-- Боковое меню -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>Админ панель</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="index.php" data-section="dashboard">
                        <i class='bx bxs-dashboard'></i>
                        Дашборд
                    </a>
                </li>
                <li>
                    <a href="index.php" data-section="pending-ads">
                        <i class='bx bxs-message-square-detail'></i>
                        Новые объявления
                    </a>
                </li>
                <li>
                    <a href="index.php#users" data-section="users">
                        <i class='bx bxs-user-detail'></i>
                        Пользователи
                    </a>
                </li>
                <li>
                    <a href="allAds.php">
                        <i class='bx bxs-collection'></i>
                        Все объявления
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class='bx bxs-log-out'></i>
                        Выход
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <div id="content" class="content">
            <!-- Верхняя панель -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class='bx bx-menu'></i>
                    </button>
                </div>
            </nav>

            <!-- Контент страницы -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="mb-4">Аналитика объявлений пользователя</h2>
                        <div id="adsContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Функция для загрузки данных
        async function loadUserAds() {
            const userId = <?php echo $user_id; ?>;
            try {
                const response = await fetch(`http://localhost:8888/api/admin/user-ads/${userId}`);
                const data = await response.json();
                renderAds(data);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Функция для рендеринга объявлений
        function renderAds(data) {
            const container = document.getElementById('adsContainer');
            let html = '';

            // Рендерим блогеров
            if (data.bloggers && data.bloggers.length > 0) {
                html += '<h3 class="mt-4">Объявления блогера</h3>';
                html += '<div class="row"><div class="col-12">';
                data.bloggers.forEach(ad => {
                    let tgLink = ad.telegram_link.replace('https://t.me/', '').replace('@', '');
                    let igLink = ad.instagram_link.replace('https://www.instagram.com/', '');
                    html += `
                        <div class="ad-card">

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="${ad.photo}" class="ad-image" alt="Фото" onclick="showFullImage(this.src)">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="card-title">${ad.nickname}</h5>
                                            <p class="card-text">
                                                <strong>Категория:</strong> ${ad.category}<br>
                                                <strong>Направление:</strong> ${ad.user_direction}<br>
                                                <strong>Статус:</strong> ${ad.status}<br>
                                                <strong>Комментарий:</strong> ${ad.ad_comment}<br>
                                                <strong><a href="https://t.me/${tgLink}" target="_blank">Telegram</a></strong>
                                                <strong><a href="https://www.instagram.com/${igLink}" target="_blank">Instagram</a></strong>
                                            </p>
                                            <div class="agreement-count">


                                                <i class="fa-solid fa-handshake"></i> ${ad.agreement_count} соглашений
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }

            // Рендерим компании
            if (data.companies && data.companies.length > 0) {
                html += '<h3 class="mt-4">Объявления компании</h3>';
                html += '<div class="row"><div class="col-12">';
                data.companies.forEach(ad => {
                    let tgLink = ad.telegram_link.replace('https://t.me/', '').replace('@', '');
                    let igLink = ad.instagram_link.replace('https://www.instagram.com/', '');
                    html += `
                        <div class="ad-card">
                            <div class="card">


                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="${ad.photo}" class="ad-image" alt="Фото" onclick="showFullImage(this.src)">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="card-title">${ad.name}</h5>
                                            <p class="card-text">
                                                <strong>Категория:</strong> ${ad.category}<br>
                                                <strong>Направление:</strong> ${ad.direction}<br>
                                                <strong>Бюджет:</strong> ${ad.budget}<br>
                                                <strong>Статус:</strong> ${ad.status}<br>
                                                <strong>Комментарий:</strong> ${ad.ad_comment}<br>
                                                <strong><a href="https://t.me/${tgLink}" target="_blank">Telegram</a></strong>
                                                <strong><a href="https://www.instagram.com/${igLink}" target="_blank">Instagram</a></strong>

                                            </p>
                                            <div class="agreement-count">
                                                <i class="fa-solid fa-handshake"></i> ${ad.agreement_count} соглашений
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }

            // Рендерим фрилансеров
            if (data.freelancers && data.freelancers.length > 0) {
                html += '<h3 class="mt-4">Объявления фрилансера</h3>';
                html += '<div class="row"><div class="col-12">';
                data.freelancers.forEach(ad => {
                    let tgLink = ad.telegram_link.replace('https://t.me/', '').replace('@', '');
                    let igLink = ad.instagram_link.replace('https://www.instagram.com/', '');
                    html += `
                        <div class="ad-card">

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="${ad.photo}" class="ad-image" alt="Фото" onclick="showFullImage(this.src)">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="card-title">${ad.name}</h5>
                                            <p class="card-text">
                                                <strong>Категория:</strong> ${ad.category}<br>
                                                <strong>Статус:</strong> ${ad.status}<br>
                                                <strong>Комментарий:</strong> ${ad.ad_comment}<br>
                                                <strong><a href="https://t.me/${tgLink}" target="_blank">Telegram</a></strong>
                                                <strong><a href="https://www.instagram.com/${igLink}" target="_blank">Instagram</a></strong>
                                            </p>
                                            <div class="agreement-count">


                                                <i class="fa-solid fa-handshake"></i> ${ad.agreement_count} соглашений
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }

            if (html === '') {
                html = '<div class="alert alert-info">У пользователя нет объявлений</div>';
            }

            container.innerHTML = html;
        }

        // Функция для показа полноразмерного изображения
        function showFullImage(src) {
            const modal = document.createElement('div');
            modal.className = 'image-modal';
            modal.innerHTML = `
                <div class="image-modal-content">
                    <span class="close-modal">&times;</span>
                    <img src="${src}" alt="Полное изображение">
                </div>
            `;

            document.body.appendChild(modal);

            modal.onclick = function(e) {
                if (e.target === modal || e.target.className === 'close-modal') {
                    modal.remove();
                }
            };
        }

        // Загружаем данные при загрузке страницы
        document.addEventListener('DOMContentLoaded', loadUserAds);

        // Сворачивание/разворачивание бокового меню
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
