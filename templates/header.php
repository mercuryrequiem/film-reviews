<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <header class="header">
    <div class="container header-inner">
      <a class="logo" href="index.php">
        <img src="images/logo.svg" alt="" width="40" height="40">
        <span class="logo-text">Сайт с обзорами фильмов</span>
      </a>
      <nav class="nav">
        <div class="nav-guest">
          <a href="#registration">Регистрация</a>
          <a href="#login">Вход</a>
        </div>
        <div class="nav-user">
          <span><span class="user-hello">Здравствуйте, </span><span id="user-name"></span></span>
          <a class="logout" id="logout" href="#">Выход</a>
        </div>
      </nav>
    </div>
  </header>

