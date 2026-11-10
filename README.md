# Сайт с обзорами фильмов (лабораторная работа №2)

PHP + MySQL (PDO). Главная страница выводит обзоры из БД, остальные подгружаются кнопкой «Показать еще» (AJAX).
Детальная страница показывает обзор, постер, автора и комментарии. Нужен PHP 8.0+ (в XAMPP 8.x он есть).

## Быстрый запуск на компьютере с XAMPP

1. В **XAMPP Control Panel** нажмите Start у **Apache** и **MySQL**.
2. Положите папку `lab2` в `C:\xampp\htdocs\` (получится `C:\xampp\htdocs\lab2`).
3. Дважды щёлкните по **`setup.bat`** в этой папке. Он создаст `config\parameters.ini` и загрузит базу `film_reviews`
   (создаются только её таблицы, чужие базы не затрагиваются).
4. Откройте http://localhost/lab2/

Если сайт взят с GitHub, в нём не будет постеров (картинки в `.gitignore`): вместо них показывается заглушка.
Положите файлы постеров в `public/uploads/posters/` (они есть в архиве от автора).

### Если `setup.bat` не подошёл

- XAMPP не в `C:\xampp` или у MySQL есть пароль: откройте http://localhost/phpmyadmin → **Импорт** → загрузите
  `database/schema.sql`, затем `database/seed.sql`. Скопируйте `config/parameters.ini.dist` в
  `config/parameters.ini` и впишите `host=localhost`, `login=root`, свой `password`, `name=film_reviews`.
- `http://localhost/lab2/` открывает чужой сайт или порт 80 занят: запустите встроенный сервер PHP (MySQL должен работать)

  ```
  C:\xampp\php\php.exe -S localhost:8080 -t C:\xampp\htdocs\lab2\public
  ```

  и откройте http://localhost:8080/

## Структура

```
config/parameters.ini.dist   образец настроек БД (сам parameters.ini в репозиторий не попадает)
database/schema.sql          создание БД film_reviews и таблиц users, reviews, comments
database/seed.sql            тестовые данные
public/                      корень сайта: index.php, review.php, more.php, css, js, images, uploads
src/                         подключение к БД (PDO), запросы, вспомогательные функции
templates/                   шапка, подвал, карточка обзора, страница ошибки
setup.bat                    первоначальная настройка (конфиг + импорт БД)
```

## Свой домен http://site.local/ (необязательно)

1. В `C:\xampp\apache\conf\extra\httpd-vhosts.conf` добавьте (поправьте путь на свой):

   ```apache
   <VirtualHost *:80>
       ServerName localhost
       DocumentRoot "C:/xampp/htdocs"
   </VirtualHost>

   <VirtualHost *:80>
       ServerName site.local
       DocumentRoot "C:/xampp/htdocs/lab2/public"
       <Directory "C:/xampp/htdocs/lab2/public">
           Require all granted
       </Directory>
   </VirtualHost>
   ```

   Первый блок нужен, чтобы `localhost` продолжал открывать `htdocs`.
2. В `C:\Windows\System32\drivers\etc\hosts` (редактор от имени администратора) добавьте строку
   `127.0.0.1 site.local`.
3. Перезапустите Apache и откройте http://site.local/

## Проверка стиля PSR-12

Конфиг: `.php-cs-fixer.dist.php`. Скачайте `php-cs-fixer.phar` в папку `tools/` (она в `.gitignore`) и запустите:

```
C:\xampp\php\php.exe tools/php-cs-fixer.phar fix
```

Либо через composer: `composer install`, затем `vendor/bin/php-cs-fixer fix`.

## Тестовые пользователи

В `seed.sql` четыре пользователя (модератор `moderator@example.com` и три обычных), пароль у всех `password1`.
Вход на сайте пока работает на `localStorage`, как в лабораторной №1; серверная авторизация будет в следующих работах.
