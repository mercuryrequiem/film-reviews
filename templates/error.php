<?php

$pageTitle = 'Ошибка ' . $status;

render('header', ['pageTitle' => $pageTitle]);
?>
  <main class="container">
    <h1><?= (int) $status ?></h1>
    <p><?= e($message) ?></p>
    <a class="button back" href="index.php">На главную</a>
  </main>

<?php render('footer'); ?>
