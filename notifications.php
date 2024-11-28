<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Уведомления</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body>
    <?php include 'components/miniHeader.php'; ?>

    <div class="container mt-4">
        <div class="notifications-page">
            <h4 class="mb-4">История уведомлений</h4>
            <div class="notifications-list">
                <!-- Уведомления будут добавлены через JavaScript -->
            </div>
        </div>
    </div>

   

    <script src="scripts/utils.js"></script>
    <script src="scripts/notifications.js"></script>
</body>
</html> 