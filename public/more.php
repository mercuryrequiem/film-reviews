<?php

declare(strict_types=1);

use App\Database;
use App\ReviewRepository;

require __DIR__ . '/../src/bootstrap.php';

// Отдаёт следующую порцию карточек (HTML) после записи с id = last_id
$lastId = filter_input(INPUT_GET, 'last_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$lastId) {
    http_response_code(400);
    exit('Некорректный параметр last_id');
}

try {
    $page = (new ReviewRepository(Database::connection()))->findPage($lastId);
} catch (Throwable $exception) {
    http_response_code(500);
    exit('Не удалось получить данные');
}

header('Content-Type: text/html; charset=utf-8');
header('X-Has-More: ' . ($page['hasMore'] ? '1' : '0'));

foreach ($page['items'] as $review) {
    render('card', ['review' => $review]);
}
