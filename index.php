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
    <script src="scripts/main.js?v=1.0.3"></script>
    
    <!-- Добавьте перед закрывающим тегом body -->
    <script src="scripts/tutorial.js?v=1.0.4"></scrpt>
    
    <!-- После utils.js -->
    <script src="scripts/notifications.js?v=1.0.3"></script>

    <?php
    // Подключаем нужные скрипты в зависимости от страницы
    switch($page) {
        case 'advertisers':
            echo '<script src="scripts/companies.js?v=1.0.4"></script>';
            break;
        case 'freelancers':
            echo '<script src="scripts/freelancers.js?v=1.0.3"></script>';
            break;
        default:
            echo '<script src="scripts/ads.js?v=1.0.3"></script>';
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
        
        
    }
    </script>

</body>
</html>