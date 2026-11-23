<?php

declare(strict_types=1);

use App\Database;
use App\ReviewRepository;

require __DIR__ . '/../src/bootstrap.php';

try {
    $page = (new ReviewRepository(Database::connection()))->findPage();
} catch (Throwable $exception) {
    abort(500, 'Не удалось получить данные: ' . $exception->getMessage());
}

$reviews = $page['items'];
$hasMore = $page['hasMore'];

render('header', ['pageTitle' => 'Сайт с обзорами фильмов']);
?>
  <main class="container">

    <div class="top">
      <h1>Топ 20 фильмов</h1>
      <a class="button button-fill" href="#">
        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><rect x="2" y="9" width="16" height="2" rx="1"/><rect x="9" y="2" width="2" height="16" rx="1"/></svg>
        <span class="button-text">Добавить обзор</span>
      </a>
    </div>

    <div class="cards">
<?php foreach ($reviews as $review): ?>
      <?php render('card', ['review' => $review]); ?>
<?php endforeach; ?>
    </div>

    <a class="button more" href="#" <?= $hasMore ? '' : 'hidden' ?>>
      <svg class="icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7L10 13L16 7"/></svg>
      Показать еще
    </a>
  </main>

<?php render('footer'); ?>
