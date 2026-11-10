  <!-- Подвал -->
  <footer class="footer">
    <div class="container footer-inner">
      <a class="footer-mail" href="mailto:info@gmail.com">info@gmail.com</a>
      <a class="footer-info" href="#">Информация о разработчике</a>
    </div>
  </footer>

  <!-- Окно регистрации -->
  <div class="modal" id="registration">
    <div class="modal-window">
      <a class="modal-close" href="#"><img src="images/close.svg" alt="Закрыть" width="20" height="20"></a>

      <div class="tabs">
        <a class="tab tab-active" href="#registration">Регистрация</a>
        <a class="tab" href="#login">Авторизация</a>
      </div>

      <form class="form" action="#" method="post" novalidate>
        <label class="field field-wide">
          <input type="text" name="name" placeholder=" " required pattern="[А-Яа-яЁё\s\-]+" data-error="Только русские буквы, пробелы и дефисы">
          <span>Ваше имя</span>
        </label>
        <label class="field">
          <input type="email" name="email" placeholder=" " required pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}" data-error="Введите корректный email">
          <span>Email</span>
        </label>
        <label class="field">
          <input type="tel" name="phone" placeholder=" " required pattern="(\+7|8)[\s\-]?\(?9\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}" data-error="Введите мобильный телефон, например +7 912 345-67-89">
          <span>Мобильный телефон</span>
        </label>
        <label class="field">
          <input type="password" name="password" placeholder=" " required minlength="6" pattern=".*\D.*" data-error="Пароль не может состоять только из цифр">
          <span>Пароль</span>
        </label>
        <label class="field">
          <input type="password" name="password2" placeholder=" " required>
          <span>Повторите пароль</span>
        </label>

        <label class="agree">
          <input type="checkbox" name="agree" required>
          <span>Согласен на обработку <a href="#registration">персональных данных</a></span>
        </label>

        <button class="submit" type="submit">Зарегистрироваться</button>

        <p class="hint">
          <img src="images/info.svg" alt="" width="20" height="20">
          Все поля обязательны для заполнения
        </p>
      </form>
    </div>
  </div>

  <!-- Окно авторизации -->
  <div class="modal" id="login">
    <div class="modal-window">
      <a class="modal-close" href="#"><img src="images/close.svg" alt="Закрыть" width="20" height="20"></a>

      <div class="tabs">
        <a class="tab" href="#registration">Регистрация</a>
        <a class="tab tab-active" href="#login">Авторизация</a>
      </div>

      <form class="form" action="#" method="post" novalidate>
        <label class="field">
          <input type="email" name="email" placeholder=" " required pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}" data-error="Введите корректный email">
          <span>Email</span>
        </label>
        <label class="field">
          <input type="password" name="password" placeholder=" " required>
          <span>Пароль</span>
        </label>

        <button class="submit" type="submit">Войти</button>

        <p class="hint">
          <img src="images/info.svg" alt="" width="20" height="20">
          Все поля обязательны для заполнения
        </p>
      </form>
    </div>
  </div>

  <script src="js/script.js"></script>
</body>
</html>
