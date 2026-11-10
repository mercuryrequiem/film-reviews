<div class="card" data-id="<?= (int) $review['id'] ?>">
  <img class="poster" src="uploads/posters/<?= e($review['poster']) ?>" alt="<?= e($review['title']) ?>">
  <div class="card-text">
    <h2 class="card-title">
      <a href="review.php?id=<?= (int) $review['id'] ?>"><?= e($review['title']) ?> (<?= (int) $review['release_year'] ?>)</a>
    </h2>
    <p class="date"><img src="images/clock.svg" alt="" width="12" height="12"><span class="date-word">Добавлено </span><?= e(formatDate($review['created_at'])) ?></p>
  </div>
  <a class="button button-small" href="review.php?id=<?= (int) $review['id'] ?>">Подробнее</a>
</div>
