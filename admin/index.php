<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Получение данных через API
function fetchData($endpoint) {
    $api_url = "https://173.212.234.202/api/" . $endpoint;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
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
        /* Стили для модального окна с изображением */
        .image-modal {
            display: flex;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            justify-content: center;
            align-items: center;
        }

        .image-modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90vh;
        }

        .image-modal-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        .close-modal {
            position: absolute;
            top: -40px;
            right: 0;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            transition: all 0.3s ease;
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
                <li class="active">
                    <a href="#" data-section="dashboard">
                        <i class='bx bxs-dashboard'></i>
                        Дашборд
                    </a>
                </li>
                <li>
                    <a href="#" data-section="pending-ads">
                        <i class='bx bxs-message-square-detail'></i>
                        Новые объявления
                    </a>
                </li>
                <li>
                    <a href="#" data-section="users">
                        <i class='bx bxs-user-detail'></i>
                        Пользователи
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
                        <!-- Статистика по типам объявлений -->
                        
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
                                        <button class="btn btn-sm btn-success" onclick="approveAd(<?php echo $ad['id']; ?>, '<?php echo $ad['type']; ?>')">
                                            Одобрить
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejectAd(<?php echo $ad['id']; ?>, '<?php echo $ad['type']; ?>')">
                                            Отклонить
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Пользователи -->
                <section id="users" class="content-section">
                    <h2>Пользователи</h2>
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
                                <?php foreach ($users as $user): ?>
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
                                                onclick="toggleUserStatus(<?php echo $user['id']; ?>)">
                                            <?php echo $user['status'] ? 'Заблокировать' : 'Разблокировать'; ?>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                    url: 'https://173.212.234.202/api/admin/approve-ad',
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
                    url: 'https://173.212.234.202/api/admin/reject-ad',
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

        // Фу��кция для управления статусом пользователя
        function toggleUserStatus(id) {
            if (confirm('Вы уверены, что хотите изменить статус этого пользователя?')) {
                $.ajax({
                    url: 'https://173.212.234.202/api/admin/toggle-user-status',
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

        // Переключение между секциями
        document.querySelectorAll('.sidebar a[data-section]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.dataset.section;
                
                // Убираем активный класс со всех секций и ссылок
                document.querySelectorAll('.content-section, .sidebar li').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Добавляем активный класс нужной секции и ссылке
                document.getElementById(sectionId).classList.add('active');
                this.parentElement.classList.add('active');
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
                        const response = await fetch('https://173.212.234.202/api/admin/calendar-events');
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
    </script>
</body>
</html>
