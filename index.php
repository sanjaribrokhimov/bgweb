<?php
// В начале файла добавим:
$params = array(
    'telegram_chat_id' => $_GET['chat_id'] ?? null,
    'telegram_phone' => $_GET['phone'] ?? null,
    'telegram_username' => $_GET['username'] ?? null,
    'telegram_user_id' => $_GET['user_id'] ?? null
);

// Добавляем JavaScript для сохранения всех параметров
if (array_filter($params)) {
    echo "<script>
        const telegramParams = " . json_encode($params) . ";
        Object.entries(telegramParams).forEach(([key, value]) => {
            if (value) {
                localStorage.setItem(key, value);
                console.log('Saved ' + key + ':', value);
            }
        });
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
</body>
</html>