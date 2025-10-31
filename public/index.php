<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TenderService\Database\Connection;
use TenderService\Services\TenderService;
use TenderService\Services\JsonLoaderService;

// Загрузка конфигурации
$config = require __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Инициализация подключения к БД
    $db = Connection::getInstance($config);
    $tenderService = new TenderService($db);
    $jsonLoader = new JsonLoaderService();

    // Получаем URL из параметра запроса
    $jsonUrl = $_GET['url'] ?? null;
    
    if (!$jsonUrl) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Parameter "url" is required'
        ]);
        exit;
    }

    // Загружаем JSON
    $tendersData = $jsonLoader->loadFromUrl($jsonUrl);

    // Сохраняем в БД
    $results = $tenderService->saveTenders($tendersData);

    // Формируем ответ
    $response = [
        'success' => true,
        'results' => $results,
        'summary' => [
            'total_tenders_in_db' => $tenderService->getTendersCount(),
            'total_docs_in_db' => $tenderService->getDocsCount()
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
