<?php
// В начале файла добавим:
$params = array(
    'telegram_chat_id' => $_GET['chat_id'] ?? null,
    'telegram_phone' => $_GET['phone'] ?? null,
    'telegram_username' => $_GET['username'] ?? null,
    'telegram_user_id' => $_GET['user_id'] ?? null
);

// Логируем параметры в файл
file_put_contents(
    'telegram_params.log', 
    date('Y-m-d H:i:s') . ' - ' . json_encode($params) . "\n", 
    FILE_APPEND
);

// Добавляем JavaScript для сохранения всех параметров
if (array_filter($params)) {
    echo "<script>
        const telegramParams = " . json_encode($params) . ";
        
        // Добавляем отладочный вывод
        console.log('Received Telegram params:', telegramParams);
        
        Object.entries(telegramParams).forEach(([key, value]) => {
            if (value) {
                localStorage.setItem(key, value);
                console.log('Saved ' + key + ':', value);
            }
        });

        // Проверяем что сохранилось
        setTimeout(() => {
            console.log('LocalStorage contents:');
            Object.entries(telegramParams).forEach(([key]) => {
                console.log(key + ':', localStorage.getItem(key));
            });
        }, 100);
    </script>";
}

// Если нет параметра page, перенаправляем на страницу блогеров
if (!isset($_GET['page'])) {
    header('Location: index.php?page=bloggers');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloger Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <button onclick="checkTelegramData()" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        Check Telegram Data
    </button>

    <?php include 'components/header.php'; ?>

    <?php
    $page = $_GET['page'];
    
    switch($page) {
        case 'advertisers':
            include 'advertisers.php';
            break;
        case 'freelancers':
            include 'freelancers.php';
            break;
        case 'bloggers':
        default:
            include 'bloggers.php';
    }
    ?>

    <?php include 'components/footer.php'; ?>

    <!-- Общие скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="translations.js"></script>
    <script src="scripts/utils.js"></script>
    <script src="scripts/main.js"></script>
    <!-- После utils.js -->
<script src="scripts/notifications.js"></script>
    <?php
    // Подключаем нужные скрипты в зависимости от страницы
    switch($page) {
        case 'advertisers':
            echo '<script src="scripts/companies.js"></script>';
            break;
        case 'freelancers':
            echo '<script src="scripts/freelancers.js"></script>';
            break;
        default:
            echo '<script src="scripts/ads.js"></script>';
    }
    ?>

    <script>
    function checkTelegramData() {
        const telegramData = {
            chat_id: localStorage.getItem('telegram_chat_id'),
            phone: localStorage.getItem('telegram_phone'),
            username: localStorage.getItem('telegram_username'),
            user_id: localStorage.getItem('telegram_user_id')
        };
        
        // Создаем модальное окно с данными
        const pre = document.createElement('pre');
        pre.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); ' +
                           'background: white; padding: 20px; border-radius: 10px; z-index: 10000; ' +
                           'max-width: 90%; max-height: 90%; overflow: auto; box-shadow: 0 0 10px rgba(0,0,0,0.5);';
        pre.textContent = JSON.stringify(telegramData, null, 2);
        
        // Добавляем кнопку закрытия
        const closeBtn = document.createElement('button');
        closeBtn.textContent = '×';
        closeBtn.style.cssText = 'position: absolute; top: 10px; right: 10px; border: none; ' +
                                'background: none; font-size: 20px; cursor: pointer;';
        closeBtn.onclick = () => pre.remove();
        
        pre.appendChild(closeBtn);
        document.body.appendChild(pre);
        
        // Также выводим в консоль
        console.log('Current Telegram Data:', telegramData);
    }
    </script>
</body>
</html>