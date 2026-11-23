<?php

declare(strict_types=1);

namespace App;

use PDO;
use RuntimeException;

class Database
{
    private const CONFIG_PATH = __DIR__ . '/../config/parameters.ini';

    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $config = self::loadConfig();

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['name']);

            self::$connection = new PDO($dsn, $config['login'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }

    private static function loadConfig(): array
    {
        if (!is_file(self::CONFIG_PATH)) {
            throw new RuntimeException(
                'Нет файла config/parameters.ini. Запустите setup.bat или скопируйте config/parameters.ini.dist и заполните его.'
            );
        }

        return parse_ini_file(self::CONFIG_PATH) ?: [];
    }
}
