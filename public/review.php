<?php

declare(strict_types=1);

use App\Database;
use App\ReviewRepository;

require __DIR__ . '/../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$id) {
    abort(404, 'Обзор не найден');
}

try {
    $repository = new ReviewRepository(Database::connection());
    $review = $repository->findById($id);

    if ($review === null) {
        abort(404, 'Обзор не найден');
    }

    $comments = $repository->findComments($id);
} catch (Throwable $exception) {
    abort(500, 'Не удалось получить данные: ' . $exception->getMessage());
}

$title = $review['title'] . ' (' . $review['release_year'] . ')';
$paragraphs = preg_split('/\R{2,}/', trim($review['content']));
$embedUrl = youtubeEmbedUrl($review['trailer_url']);

render('header', ['pageTitle' => $title . ' — Сайт с обзорами фильмов']);
?>
  <main class="container">

    <article class="review">
      <img class="review-poster" src="<?= e(posterUrl($review['poster'])) ?>" alt="<?= e($review['title']) ?>">
      <h1 class="review-title"><?= e($title) ?></h1>
      <a class="button back" href="index.php">
        <svg class="icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M13 4L7 10L13 16"/></svg>
        Назад к списку
      </a>
      <p class="meta"><span>Добавлено <?= e(formatDate($review['created_at'])) ?></span><img class="dot" src="images/dot.svg" alt="" width="4" height="4"><span><?= e($review['author_name']) ?></span></p>
<?php if ($review['trailer_url']): ?>
      <a class="button trailer" href="<?= e($review['trailer_url']) ?>" target="_blank" rel="noopener"<?= $embedUrl ? ' data-modal="trailer"' : '' ?>>
        <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><path d="M14.8804 8.21738C16.2415 8.99512 16.2194 10.9652 14.8412 11.7122L6.95306 15.9878C5.62045 16.7101 4 15.7453 4 14.2295L4 5.44636C4 3.91071 5.65896 2.94798 6.99228 3.70987L14.8804 8.21738Z"/></svg>
        Трейлер
      </a>
<?php endif; ?>
      <div class="review-text">
<?php foreach ($paragraphs as $paragraph): ?>
        <p><?= e($paragraph) ?></p>
<?php endforeach; ?>
      </div>
    </article>

    <section class="comments">
      <div class="comments-head">
        <h2 class="comments-title">Комментарии<sup><?= count($comments) ?></sup></h2>
        <a class="button button-fill button-comment" href="#">
          <svg class="icon" viewBox="0 0 20 20" fill="currentColor"><rect x="2" y="9" width="16" height="2" rx="1"/><rect x="9" y="2" width="2" height="16" rx="1"/></svg>
          Добавить комментарий
        </a>
      </div>

<?php foreach ($comments as $comment): ?>
      <div class="comment">
        <p class="meta"><span>Добавлено <?= e(formatDate($comment['created_at'])) ?></span><img class="dot" src="images/dot.svg" alt="" width="4" height="4"><span><?= e($comment['author_name']) ?></span></p>
        <p class="comment-text"><?= e($comment['text']) ?></p>
      </div>
<?php endforeach; ?>
    </section>
  </main>

<?php if ($embedUrl): ?>
  <div class="modal modal-video" id="trailer">
    <div class="modal-window">
      <a class="modal-close" href="#"><img src="images/close.svg" alt="Закрыть" width="20" height="20"></a>
      <h2 class="modal-title">Трейлер: <?= e($title) ?></h2>
      <div class="video">
        <iframe data-src="<?= e($embedUrl) ?>?autoplay=1" title="Трейлер: <?= e($title) ?>" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
      </div>
    </div>
  </div>

<?php endif; ?>
<?php render('footer'); ?>
