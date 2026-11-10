<?php

declare(strict_types=1);

/**
 * Экранирует строку для вывода в HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Форматирует дату вида "15 августа".
 */
function formatDate(string $dateTime): string
{
    static $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
    ];

    $timestamp = strtotime($dateTime);

    return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)];
}

/**
 * Подключает шаблон из папки templates и передаёт ему переменные.
 *
 * @param array<string, mixed> $vars
 */
function render(string $template, array $vars = []): void
{
    extract($vars, EXTR_SKIP);

    require __DIR__ . '/../templates/' . $template . '.php';
}

/**
 * Показывает страницу ошибки и завершает работу скрипта.
 */
function abort(int $status, string $message): void
{
    http_response_code($status);
    render('error', ['status' => $status, 'message' => $message]);
    exit;
}
