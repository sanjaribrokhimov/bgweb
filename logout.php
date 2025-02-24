<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <script>
    // Очищаем localStorage перед выходом
    localStorage.clear();
    // Перенаправляем на страницу входа
    window.location.href = 'login.php';

    function closeTelegramWebApp() {
        if (window.TelegramWebviewProxy) {
        window.TelegramWebviewProxy.postEvent('web_app_close');
        } else if(window.Telegram && window.Telegram.WebApp) {
            window.Telegram.WebApp.close();
        }
    }
    closeTelegramWebApp();

    </script>
</head>
</html> 