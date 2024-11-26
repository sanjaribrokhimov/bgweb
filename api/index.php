<?php
// Добавьте в начало файла для отладки
error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Origin: ' . $_SERVER['HTTP_ORIGIN']);

// Устанавливаем правильные заголовки CORS
header('Access-Control-Allow-Origin: http://localhost:8000'); // Точный origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Обработка preflight запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Остальной код обработки запросов...
?> 