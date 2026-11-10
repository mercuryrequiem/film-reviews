# Сайт с обзорами фильмов (лабораторная работа №2)

PHP + MySQL (PDO). Главная страница выводит обзоры из БД с подгрузкой по кнопке «Показать еще» (AJAX),
детальная страница показывает обзор и комментарии. Требуется PHP 8.0+ (в XAMPP 8.x есть).

## Структура

```
config/parameters.ini.dist   образец настроек БД (копия parameters.ini в репозиторий не попадает)
database/schema.sql          создание БД film_reviews и таблиц
database/seed.sql            тестовые данные
public/                      корень сайта (index.php, review.php, more.php, css, js, images, uploads)
src/                         код: подключение к БД, запросы, вспомогательные функции
templates/                   шапка, подвал, карточка обзора
```

## Запуск на другом компьютере с XAMPP

1. Запустите в XAMPP Control Panel модули **Apache** и **MySQL**.
2. Скопируйте папку `lab2` целиком в `C:\xampp\htdocs\` (должно получиться `C:\xampp\htdocs\lab2`).
3. Откройте http://localhost/phpmyadmin → вкладка **Импорт** → выберите `database/schema.sql` → **Вперёд**.
   Затем так же импортируйте `database/seed.sql`.
4. Проверьте `config/parameters.ini` (если файла нет, скопируйте `parameters.ini.dist` и заполните).
   В XAMPP по умолчанию: `host=localhost`, `login=root`, пароль пустой, `name=film_reviews`.
5. Откройте http://localhost/lab2/

Если сайт клонирован из git, постеров в нём не будет (папка `public/uploads` в `.gitignore`):
положите картинки в `public/uploads/posters/` из архива.

## Если localhost/lab2 не открывается

Например, у владельца компьютера в Apache настроены свои виртуальные хосты, и `localhost` ведёт на его сайт,
или порт 80 занят. Тогда, не трогая его настройки, запустите сайт встроенным сервером PHP (MySQL должен работать):

```
C:\xampp\php\php.exe -S localhost:8080 -t C:\xampp\htdocs\lab2\public
```

и откройте http://localhost:8080/. Если у MySQL есть пароль, впишите его в `config/parameters.ini`.

## Свой домен (необязательно)

В `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName site.local
    DocumentRoot "C:/xampp/htdocs/lab2/public"
    <Directory "C:/xampp/htdocs/lab2/public">
        Require all granted
    </Directory>
</VirtualHost>
```

В `C:\Windows\System32\drivers\etc\hosts` (от администратора) добавьте строку `127.0.0.1 site.local`,
перезапустите Apache и откройте http://site.local/.

## Проверка стиля PSR-12

Конфиг: `.php-cs-fixer.dist.php`. Скачайте `php-cs-fixer.phar` в папку `tools/` (она в `.gitignore`) и запустите:

```
C:\xampp\php\php.exe tools/php-cs-fixer.phar fix
```

Либо через composer: `composer install`, затем `vendor/bin/php-cs-fixer fix`.
