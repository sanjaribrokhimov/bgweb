<?php
header('Content-Type: application/json');

// Получаем все GET параметры
$get_params = $_GET;

// Получаем все заголовки
$headers = getallheaders();

// Формируем ответ
$response = [
    'get_params' => $get_params,
    'headers' => $headers,
    'server' => $_SERVER,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response, JSON_PRETTY_PRINT); 