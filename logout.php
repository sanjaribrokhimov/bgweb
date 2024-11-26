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
    </script>
</head>
</html> 