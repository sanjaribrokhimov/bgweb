<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все объявления</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }
        .ad-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .ad-image:hover {
            transform: scale(1.05);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .action-buttons .btn {
            margin: 2px;
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
                    <a href="index.php">
                        <i class='bx bxs-dashboard'></i>
                        Дашборд
                    </a>
                </li>
                <li>
                    <a href="index.php#pending-ads">
                        <i class='bx bxs-message-square-detail'></i>
                        Новые объявления
                    </a>
                </li>
                <li>
                    <a href="index.php#users">
                        <i class='bx bxs-user-detail'></i>
                        Пользователи
                    </a>
                </li>
                <li class="active">
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
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class='bx bx-menu'></i>
                    </button>
                </div>
            </nav>

            <div class="container-fluid">
                <h2>Все объявления</h2>

                <!-- Фильтры -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Поиск...">
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-control">
                            <option value="all">Все статусы</option>
                            <option value="true">Активные</option>
                            <option value="false">На модерации</option>
                        </select>
                    </div>
                </div>

                <!-- Вкладки категорий -->
                <ul class="nav nav-tabs" id="adsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="bloggers-tab" data-bs-toggle="tab" href="#bloggers" role="tab">
                            Блогеры
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="companies-tab" data-bs-toggle="tab" href="#companies" role="tab">
                            Компании
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="freelancers-tab" data-bs-toggle="tab" href="#freelancers" role="tab">
                            Фрилансеры
                        </a>
                    </li>
                </ul>

                <!-- Содержимое вкладок -->
                <div class="tab-content" id="adsTabContent">
                    <!-- Блогеры -->
                    <div class="tab-pane fade show active" id="bloggers" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table" id="bloggersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Фото</th>
                                        <th>Имя</th>
                                        <th>Описание</th>
                                        <th>Категория</th>
                                        <th>Направление</th>
                                        <th>Контакты</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Компании -->
                    <div class="tab-pane fade" id="companies" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table" id="companiesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Фото</th>
                                        <th>Название</th>
                                        <th>Категория</th>
                                        <th>Направление</th>
                                        <th>Бюджет</th>
                                        <th>Контакты</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Фрилансеры -->
                    <div class="tab-pane fade" id="freelancers" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table" id="freelancersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Фото</th>
                                        <th>Имя</th>
                                        <th>Категория</th>
                                        <th>Портфолио</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра изображения -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Просмотр изображения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fullImage" src="" alt="Полное изображение" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Загрузка данных для каждой категории
        async function loadAds(type) {
            try {
                const response = await fetch(`https://blogy.uz/api/admin/all-ads?type=${type}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Ошибка при загрузке данных:', error);
                return [];
            }
        }

        // Отображение данных в таблицах
        function displayAds(ads, type) {
            console.log(ads, type);
            const table = document.getElementById(`${type}Table`).getElementsByTagName('tbody')[0];
            table.innerHTML = '';

            ads.forEach(ad => {
                const row = table.insertRow();
                const statusClass = ad.status === 'true' ? 'bg-success' : 'bg-warning';
                const statusText = ad.status === 'true' ? 'Активно' : 'На модерации';

                switch(type) {
                    case 'bloggers':
                        row.innerHTML = `
                            <td>${ad.id}</td>
                            <td><img src="${ad.photo_base64 || './img/noImage.jpg'}" class="ad-image" onclick="showFullImage(this.src)"></td>
                            <td>${ad.nickname}</td>
                            <td>${ad.ad_comment}</td>
                            <td>${ad.category}</td>
                            <td>${ad.direction || '-'}</td>
                            <td>
                                <a href="${ad.instagram_link || '#'}" target="_blank">Instagram<br></a>
                                <a href="${ad.telegram_link || '#'}" target="_blank">Telegram<br></a>
                                <a href="${ad.youtube_link || '#'}" target="_blank">YouTube<br></a>
                            </td>
                            <td><span class="badge ${statusClass} status-badge">${statusText}</span></td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="editAd(${ad.id}, 'blogger')">
                                    <i class='bx bxs-edit'></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAd(${ad.id}, 'blogger')">
                                    <i class='bx bxs-trash'></i>
                                </button>
                            </td>
                        `;
                        break;
                    case 'companies':
                        row.innerHTML = `
                            <td>${ad.ID}</td>
                            <td><img src="${ad.photo_base64 || './img/noImage.jpg'}" class="ad-image" onclick="showFullImage(this.src)"></td>
                            <td>${ad.name}</td>
                            <td>${ad.category}</td>
                            <td>${ad.direction || '-'}</td>
                            <td>${ad.budget || '-'}</td>
                            <td>
                                <a href="${ad.website_link || '#'}" target="_blank">Website<br></a>
                                <a href="${ad.instagram_link || '#'}" target="_blank">Instagram<br></a>
                                <a href="${ad.telegram_link || '#'}" target="_blank">Telegram<br></a>
                            </td>
                            <td><span class="badge ${statusClass} status-badge">${statusText}</span></td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="editAd(${ad.ID}, 'company')">
                                    <i class='bx bxs-edit'></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAd(${ad.ID}, 'company')">
                                    <i class='bx bxs-trash'></i>
                                </button>
                            </td>
                        `;
                        break;
                    case 'freelancers':
                        row.innerHTML = `
                            <td>${ad.ID}</td>
                            <td><img src="${ad.photo_base64 || './img/noImage.jpg'}" class="ad-image" onclick="showFullImage(this.src)"></td>
                            <td>${ad.name}</td>
                            <td>${ad.category}</td>
                            <td>
                                <a href="${ad.portfolio_link || '#'}" target="_blank">Portfolio<br></a>
                                <a href="${ad.github_link || '#'}" target="_blank">GitHub<br></a>
                            </td>
                            <td>
                                <a href="${ad.telegram_link || '#'}" target="_blank">Telegram<br></a>
                                <a href="${ad.instagram_link || '#'}" target="_blank">Instagram<br></a>
                            </td>
                            <td><span class="badge ${statusClass} status-badge">${statusText}</span></td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="editAd(${ad.ID}, 'freelancer')">
                                    <i class='bx bxs-edit'></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAd(${ad.ID}, 'freelancer')">
                                    <i class='bx bxs-trash'></i>
                                </button>
                            </td>
                        `;
                        break;
                }
            });
        }

        // Функции для работы с объявлениями
        function editAd(id, type) {
            window.location.href = `editCart.php?id=${id}&type=${type}`;
        }

        function deleteAd(id, type) {
            if (confirm('Вы уверены, что хотите удалить это объявление?')) {
                fetch(`https://blogy.uz/api/admin/delete-ad`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id, type })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Объявление успешно удалено');
                        loadAndDisplayAds();
                    } else {
                        alert('Ошибка при удалении объявления');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Ошибка при удалении объявления');
                });
            }
        }

        // Функция для просмотра изображения
        function showFullImage(src) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            document.getElementById('fullImage').src = src;
            modal.show();
        }

        // Загрузка и отображение всех объявлений
        async function loadAndDisplayAds() {
            const types = ['bloggers', 'companies', 'freelancers'];
            for (const type of types) {
                const ads = await loadAds(type);
                displayAds(ads, type);
            }
        }

        // Обработчики фильтров
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            filterAds(searchText);
        });

        document.getElementById('statusFilter').addEventListener('change', function(e) {
            const status = e.target.value;
            filterAds('', status);
        });

        function filterAds(searchText = '', status = 'all') {
            document.querySelectorAll('table tbody tr').forEach(row => {
                let show = true;
                
                // Фильтр по тексту
                if (searchText) {
                    const text = row.textContent.toLowerCase();
                    show = text.includes(searchText);
                }

                // Фильтр по статусу
                if (status !== 'all' && show) {
                    const statusBadge = row.querySelector('.status-badge');
                    const isActive = statusBadge.classList.contains('bg-success');
                    show = (status === 'true' && isActive) || (status === 'false' && !isActive);
                }

                row.style.display = show ? '' : 'none';
            });
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            loadAndDisplayAds();
        });

        // Сворачивание/разворачивание бокового меню
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });
    </script>
</body>
</html> 