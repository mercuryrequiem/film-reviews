<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

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

function posterUrl(string $file): string
{
    $path = __DIR__ . '/../public/uploads/posters/' . $file;

    return is_file($path) ? 'uploads/posters/' . rawurlencode($file) : 'images/no-poster.svg';
}

function youtubeEmbedUrl(?string $url): ?string
{
    $pattern = '~(?:youtube\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~';

    if ($url === null || !preg_match($pattern, $url, $matches)) {
        return null;
    }

    return 'https://www.youtube.com/embed/' . $matches[1];
}

function render(string $template, array $vars = []): void
{
    extract($vars, EXTR_SKIP);

    require __DIR__ . '/../templates/' . $template . '.php';
}

function abort(int $status, string $message): void
{
    http_response_code($status);
    render('error', ['status' => $status, 'message' => $message]);
    exit;
}
