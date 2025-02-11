<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Получение данных через API
function fetchData($endpoint) {
    $api_url = "https://blogy.uz/api/" . $endpoint;
    $response = file_get_contents($api_url);
    return json_decode($response, true);
}

// Получаем данные для разных разделов
$pending_ads = fetchData("admin/pending-ads");
$users = fetchData("admin/users");
$statistics = fetchData("admin/statistics");

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <script src="modalForms.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="styles.css" rel="stylesheet">
    <style>
        .ad-image {
            transition: transform 0.2s;
        }
        .ad-image:hover {
            transform: scale(1.1);
        }
        #imageModal .modal-body {
            padding: 0;
        }
        #fullImage {
            max-height: 80vh;
            object-fit: contain;
        }
        

        .close-modal:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(90deg);
        }

        /* Стили для превью изображений в таблице */
        .table img {
            transition: transform 0.3s ease;
        }

        .table img:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .card {
            background: #2c2c2c;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card h5 {
            color: #8c8c8c;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .card h3 {
            color: #fff;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .stats-details p {
            color: #fff;
            margin: 5px 0;
            font-size: 0.9rem;
        }

        /* Стили для календаря */
        .calendar-card {
            background: #2c2c2c;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .fc {
            background: #2c2c2c;
            border-radius: 15px;
            padding: 15px;
        }
        
        .fc-theme-standard td, 
        .fc-theme-standard th {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .fc-theme-standard .fc-scrollgrid {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .fc-daygrid-day {
            background: #333;
            transition: all 0.3s ease;
        }
        
        .fc-daygrid-day:hover {
            background: #444;
        }
        
        .fc-daygrid-day-number {
            color: #fff;
            padding: 8px !important;
        }
        
        .fc-button-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        
        .fc-button-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0b5ed7 !important;
        }
        
        .fc-event {
            background-color: #0d6efd;
            border: none;
            padding: 3px 5px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .action-buttons {
            display: flex;
            gap: 10px; /* Увеличиваем расстояние между кнопками */
        }
        .action-btn {
            min-width: 120px; /* Фиксированная ширина для всех кнопок */
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        .action-btn i {
            font-size: 16px;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
        .btn-edit {
            background-color: #007bff;
            color: white;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-reject {
            background-color: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background-color: #c82333;
        }
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-tabs .nav-link {
            color: #8c8c8c;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 10px 20px;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #fff;
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background: var(--accent-blue);
            border-bottom: 2px solid #0d6efd;
        }

        .tab-content {
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <!-- После существующих модальных окон, перед закрывающим тегом body -->
    <!-- Модальное окно для добавления контента -->
    <div class="modal fade" id="addContentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить контент</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="dynamicModalContent">
                    <!-- Сюда будет динамически добавляться содержимое из modalForms.js -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Отлично!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle success-icon"></i>
                    <p class="mt-3">Ваше объявление успешно добавлено и отправлено на модерацию.</p>
                    <p>Уведомление будет отправлено на почту.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">
                        <i class="fas fa-check"></i> Понятно
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Индикатор загрузки -->
    <div id="loadingIndicator" class="loading-indicator">
        <div class="spinner-wrapper">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text">Пожалуйста, подождите...</div>
        </div>
    </div>

    <div class="admin-wrapper">
        <!-- Боковое меню -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>Админ панель</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#dashboard" data-section="dashboard">
                        <i class='bx bxs-dashboard'></i>
                        Дашборд
                    </a>
                </li>
                <li>
                    <a href="#pending-ads" data-section="pending-ads">
                        <i class='bx bxs-message-square-detail'></i>
                        Новые объявления
                    </a>
                </li>
                <li>
                    <a href="#users" data-section="users">
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

            <!-- Секции контента -->
            <div class="container-fluid">
                <!-- Дашборд -->
                <section id="dashboard" class="content-section active">
                    <h2>Дашборд</h2>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Новые объявления</h5>
                                    <h3><?php echo $statistics['pending_ads']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Активные пользователи</h5>
                                    <h3><?php echo $statistics['active_users']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <!-- Общее количество объявлений -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Всего объявлений</h5>
                                    <h3><?php echo $statistics['total_ads']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Управление пользователями</h5>
                                    <button class="btn btn-danger w-100" onclick="cleanupInactiveUsers()">
                                        <i class="fas fa-user-minus"></i>
                                        Удалить неактивных
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- После существующих карточек статистики добавляем календарь -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="calendar-card">
                                <h5 class="mb-4" style="color: #8c8c8c;">Календарь активности</h5>
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Новые объявления -->
                <section id="pending-ads" class="content-section">
                    <h2>Новые объявления</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Тип</th>
                                    <th>Фото</th>
                                    <th>Пользователь</th>
                                    <th>Контакты</th>
                                    <th>Категория</th>
                                    <th>Описание</th>
                                    
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($pending_ads) && is_array($pending_ads)): ?>
                                    <?php foreach ($pending_ads as $ad): ?>
                                    <tr>
                                        <td><?php echo $ad['id']; ?></td>
                                        <td><?php echo $ad['type']; ?></td>
                                        <td>
                                            <?php if(!empty($ad['photo'])): ?>
                                                <img src="<?php echo $ad['photo']; ?>" 
                                                     alt="Фото объявления" 
                                                     style="width: 150px; height: 150px; object-fit: cover; cursor: pointer; border-radius: 8px;"
                                                     onclick="showFullImage(this.src)"
                                                />
                                            <?php else: ?>
                                                <img src="./img/noImage.jpg" 
                                                     alt="Нет фото" 
                                                     style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px;"
                                                />
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $ad['user_name']; ?></td>
                                        <td>
                                            Email: <?php echo $ad['user_email']; ?><br>
                                            Телефон: <?php echo $ad['user_phone']; ?><br>
                                            Telegram: <?php echo $ad['user_telegram']; ?><br>
                                            Instagram: <?php echo $ad['user_instagram']; ?>
                                        </td>
                                        <td>
                                            <?php echo $ad['category']; ?><br>
                                            <?php echo isset($ad['direction']) ? $ad['direction'] : ''; ?>
                                        </td>
                                        <td><?php echo $ad['ad_comment']; ?></td>
                                        
                                        <td><?php echo $ad['created_at']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-approve action-btn" onclick="approveAd(<?php echo $ad['id']; ?>, '<?php echo $ad['type']; ?>')">
                                                    <i class='bx bxs-check-circle'></i>
                                                    Одобрить
                                                </button>
                                                <button class="btn btn-sm btn-reject action-btn" onclick="rejectAd(<?php echo $ad['id']; ?>, '<?php echo $ad['type']; ?>')">
                                                    <i class='bx bxs-x-circle'></i>
                                                    Отклонить
                                                </button>
                                                <button class="btn btn-sm btn-edit action-btn" onclick="editAd(<?php echo $ad['id']; ?>, '<?php echo $ad['type']; ?>')">
                                                    <i class='bx bxs-edit'></i>
                                                    Редактировать
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <p class="my-3">Новых объявлений пока нет</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Пользователи -->
                <section id="users" class="content-section">
                    <h2>Пользователи</h2>
                    
                    <!-- Добавляем табы для категорий -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#all">Все</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bloggers">Блогеры</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#companies">Компании</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#freelancers">Фрилансеры</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <script>
                            const Allusers = <?php echo json_encode($users); ?>;
                        </script>
                        <?php
                        // Группируем пользователей по категориям
                        $categorized_users = [
                            'all' => $users,
                            'bloggers' => array_filter($users, function($user) { return $user['category'] === 'blogger'; }),
                            'companies' => array_filter($users, function($user) { return $user['category'] === 'company'; }),
                            'freelancers' => array_filter($users, function($user) { return $user['category'] === 'freelancer'; })
                        ];

                        $tab_ids = ['all', 'bloggers', 'companies', 'freelancers'];
                        
                        foreach ($tab_ids as $tab_id):
                            $is_active = $tab_id === 'all' ? ' show active' : '';
                        ?>
                            <div class="tab-pane fade<?php echo $is_active; ?>" id="<?php echo $tab_id; ?>">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Имя</th>
                                                <th>Контакты</th>
                                                <th>Категория</th>
                                                <th>Направление</th>
                                                <th>Объявления</th>
                                                <th>Статус</th>
                                                <th>Дата регистрации</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categorized_users[$tab_id] as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['name']; ?></td>
                                                <td>
                                                    Email: <?php echo $user['email']; ?><br>
                                                    Телефон: <?php echo $user['phone']; ?><br>
                                                    Telegram: <?php echo $user['telegram']; ?><br>
                                                    Instagram: <?php echo $user['instagram']; ?>
                                                </td>
                                                <td><?php echo $user['category']; ?></td>
                                                <td><?php echo $user['direction']; ?></td>
                                                <td>
                                                    Блогер: <?php echo $user['posts_count']['blogger']; ?><br>
                                                    Компания: <?php echo $user['posts_count']['company']; ?><br>
                                                    Фрилансер: <?php echo $user['posts_count']['freelancer']; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user['status'] ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $user['status'] ? 'Активен' : 'Заблокирован'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $user['created_at']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm <?php echo $user['status'] ? 'btn-danger' : 'btn-success'; ?>"
                                                            style="margin-bottom: 10px;"
                                                            onclick="toggleUserStatus(<?php echo $user['id']; ?>)">
                                                        <?php echo $user['status'] ? 'Заблокировать' : 'Разблокировать'; ?>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" 
                                                            style="margin-bottom: 10px"
                                                            onclick="addSomething(<?php echo $user['id']; ?>, '<?php echo $user['category']; ?>', '<?php echo $user['direction']; ?>', '<?php echo $user['telegram']; ?>', '<?php echo $user['instagram']; ?>')">
                                                        <i class='bx bxs-plus-circle'></i>
                                                        Добавить
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" 
                                                            style="margin-bottom: 10px"
                                                            onclick="editUserProfile('<?php echo $user['email']; ?>')">
                                                        <i class="fas fa-user-edit"></i> 
                                                        Редактировать
                                                    </button>
                                                    <a href="userAds.php?user_id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-chart-line"></i> Аналитика
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра изображения -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Просмотр изображения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fullImage" src="" alt="Полное изображение" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
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

            // Закрытие модального окна
            modal.onclick = function(e) {
                if (e.target === modal || e.target.className === 'close-modal') {
                    modal.remove();
                }
            };
        }

        // Функции для работы с объявлениями
        function approveAd(id, type) {
            if (confirm('Вы уверены, что хотите одобрить это объявление?')) {
                $.ajax({
                    url: 'https://blogy.uz/api/admin/approve-ad',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        id: id,
                        type: type 
                    }),
                    success: function(response) {
                        alert('Объявление одобрено');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.responseJSON.error);
                    }
                });
            }
        }

        function rejectAd(id, type) {
            const reason = prompt('Укажите причину отклонения:');
            if (reason) {
                $.ajax({
                    url: 'https://blogy.uz/api/admin/reject-ad',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        id: id,
                        type: type,
                        reason: reason 
                    }),
                    success: function(response) {
                        alert('Объявление отклонено');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.responseJSON.error);
                    }
                });
            }
        }

        // Функция для управления статусом пользователя
        function toggleUserStatus(id) {
            if (confirm('Вы уверены, что хотите изменить статус этого пользователя?')) {
                $.ajax({
                    url: 'https://blogy.uz/api/admin/toggle-user-status',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: id }),
                    success: function(response) {
                        alert('Статус пользователя изменен');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Ошибка: ' + xhr.responseJSON.error);
                    }
                });
            }
        }

        function addSomething(id, type, user_direction, userTelegram, userInstagram) {
            // Получаем соответствующую форму из modalForms.js
            const form = getFormByType(type);
            // Вставляем форму в модальное окно
            document.getElementById('dynamicModalContent').innerHTML = form;
            // Показываем модальное окно
            new bootstrap.Modal(document.getElementById('addContentModal')).show();

            domContentOnMinimalize(id, type, user_direction, userTelegram, userInstagram);
        }


        // Функция для переключения секций
        function switchSection(sectionId) {
            // Убираем активный класс со всех секций и ссылок
            document.querySelectorAll('.content-section, .sidebar li').forEach(el => {
                el.classList.remove('active');
            });
            
            // Добавляем активный класс нужной секции и соответствующей ссылке
            document.getElementById(sectionId).classList.add('active');
            document.querySelector(`.sidebar a[data-section="${sectionId}"]`).parentElement.classList.add('active');
        }

        // Проверяем хэш при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.substring(1); // Убираем # из начала
            if (hash) {
                switchSection(hash);
            }
        });

        // Обработчик кликов по ссылкам в сайдбаре
        document.querySelectorAll('.sidebar a[data-section]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.dataset.section;
                window.location.hash = sectionId; // Обновляем хэш в URL
                switchSection(sectionId);
            });
        });

        // Сворачивание/разворачивание бокового меню
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'ru',
                events: async function(info, successCallback, failureCallback) {
                    try {
                        const response = await fetch('https://blogy.uz/api/admin/calendar-events');
                        if (!response.ok) throw new Error('Network response was not ok');
                        const events = await response.json();
                        
                        // Преобразуем данные в формат событий календаря
                        const calendarEvents = events.map(event => ({
                            title: event.title,
                            start: event.date,
                            backgroundColor: event.type === 'blogger' ? '#0d6efd' :
                                           event.type === 'company' ? '#198754' :
                                           '#dc3545',
                            borderColor: 'transparent',
                            url: event.url // если нужно добавить ссылку на событие
                        }));
                        
                        successCallback(calendarEvents);
                    } catch (error) {
                        console.error('Error fetching calendar events:', error);
                        failureCallback(error);
                    }
                },
                eventClick: function(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.open(info.event.url);
                    }
                },
                dayMaxEvents: true, // Разрешаем показывать "more" ссылку
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                }
            });
            
            calendar.render();
        });

        function editAd(id, type) {
            window.location.href = `editCart.php?id=${id}&type=${type}`;
        }

        // Добавляем JavaScript функцию для редактирования профиля (в секцию <script>)
        function editUserProfile(userEmail) {
            window.location.href = `editProfile.php?email=${encodeURIComponent(userEmail)}`;
        }

        // Функция для удаления неактивных пользователей
        function cleanupInactiveUsers() {
            if (confirm('Вы уверены, что хотите удалить всех неактивных пользователей? Это действие нельзя отменить.')) {
                // Показываем индикатор загрузки
                document.getElementById('loadingIndicator').style.display = 'flex';
                
                $.ajax({
                    url: 'https://blogy.uz/api/admin/cleanup-users',
                    method: 'DELETE',
                    success: function(response) {
                        // Скрываем индикатор загрузки
                        document.getElementById('loadingIndicator').style.display = 'none';
                        alert('Неактивные пользователи успешно удалены');
                        location.reload();
                    },
                    error: function(xhr) {
                        // Скрываем индикатор загрузки
                        document.getElementById('loadingIndicator').style.display = 'none';
                        alert('Ошибка: ' + (xhr.responseJSON ? xhr.responseJSON.error : 'Не удалось удалить неактивных пользователей'));
                    }
                });
            }
        }
    </script>
</body>
</html>
