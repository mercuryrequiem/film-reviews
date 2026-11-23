'use strict';

const modals = {};
document.querySelectorAll('.modal').forEach((m) => {
  modals[m.id] = m;
});


const USERS_KEY = 'users';
const CURRENT_KEY = 'currentUser';
const getUsers = () => JSON.parse(localStorage.getItem(USERS_KEY) || '[]');
const currentUser = JSON.parse(localStorage.getItem(CURRENT_KEY));

function showUser(user) {
  document.body.classList.add('logged-in');
  document.getElementById('user-name').textContent = user.name.split(' ')[0];
}

function login(user) {
  localStorage.setItem(CURRENT_KEY, JSON.stringify(user));
  showUser(user);
  closeModals();
}

if (currentUser) {
  showUser(currentUser);
}

document.getElementById('logout').addEventListener('click', (e) => {
  e.preventDefault();
  localStorage.removeItem(CURRENT_KEY);
  location.href = 'index.html';
});


function stopVideos() {
  document.querySelectorAll('.modal iframe[data-src]').forEach((frame) => frame.removeAttribute('src'));
}

function openModal(id) {
  Object.values(modals).forEach((m) => m.classList.remove('is-open'));
  stopVideos();
  modals[id].classList.add('is-open');
  modals[id].querySelectorAll('iframe[data-src]').forEach((frame) => {
    frame.src = frame.dataset.src;
  });
  document.body.style.overflow = 'hidden';
}

function closeModals() {
  Object.values(modals).forEach((m) => m.classList.remove('is-open'));
  stopVideos();
  document.body.style.overflow = '';
}

document.querySelectorAll('a[href="#registration"], a[href="#login"], [data-modal]').forEach((link) => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    openModal(link.dataset.modal || link.getAttribute('href').slice(1));
  });
});

document.querySelectorAll('.modal-close').forEach((btn) => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    closeModals();
  });
});

Object.values(modals).forEach((m) => {
  m.addEventListener('click', (e) => {
    if (e.target === m) closeModals();
  });
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModals();
});



function setError(input, message) {
  const field = input.closest('.field') || input.closest('.agree');

  let box = field.querySelector('.error');
  if (!box && field.nextElementSibling && field.nextElementSibling.matches('.agree-error')) {
    box = field.nextElementSibling;
  }

  if (!box) {
    box = document.createElement('small');
    box.className = 'error';
    if (field.classList.contains('agree')) {
      box.classList.add('agree-error');
      field.after(box);
    } else {
      field.append(box);
    }
  }
  box.textContent = message;
  box.hidden = !message;
  field.classList.toggle('has-error', Boolean(message));
}

function checkField(form, input) {
  input.setCustomValidity('');

  if (form.closest('.modal').id === 'registration') {
    if (input.name === 'email' && getUsers().some((u) => u.email === input.value.toLowerCase())) {
      input.setCustomValidity('Пользователь с таким email уже зарегистрирован');
    }
    if (input.name === 'password2' && input.value !== form.elements.password.value) {
      input.setCustomValidity('Пароли не совпадают');
    }
  }

  const v = input.validity;
  if (v.valid) return '';
  if (input.type === 'checkbox') return 'Необходимо согласие на обработку персональных данных';
  if (v.valueMissing) return 'Поле обязательно для заполнения';
  if (v.customError) return input.validationMessage;
  if (v.tooShort) return 'Минимум 6 символов';
  return input.dataset.error;
}

function validateField(form, input) {
  const error = checkField(form, input);
  setError(input, error);
  return !error;
}

document.querySelectorAll('.modal .form').forEach((form) => {
  const inputs = [...form.querySelectorAll('input')];

  inputs.forEach((input) => {
    input.addEventListener('blur', () => {
      if (input.type !== 'checkbox') validateField(form, input);
    });
    input.addEventListener('input', () => {
      if (input.closest('.has-error')) validateField(form, input);
      else checkField(form, input);

      const repeat = form.elements.password2;
      if (input.name === 'password' && repeat && repeat.value) validateField(form, repeat);
    });
    input.addEventListener('change', () => {
      if (input.type === 'checkbox') validateField(form, input);
    });
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const valid = inputs.map((i) => validateField(form, i)).every(Boolean);
    if (!valid) return;

    const data = Object.fromEntries(new FormData(form));
    const isRegistration = form.closest('.modal').id === 'registration';
    console.log(isRegistration ? 'Регистрация:' : 'Авторизация:', data);

    if (isRegistration) {
      const { name, email, phone, password } = data;
      const user = { name, email: email.toLowerCase(), phone, password };
      localStorage.setItem(USERS_KEY, JSON.stringify([...getUsers(), user]));
      console.log('Пользователь зарегистрирован:', user);
      login(user);
      form.reset();
      return;
    }

    const user = getUsers().find((u) => u.email === data.email.toLowerCase() && u.password === data.password);
    if (!user) {
      setError(form.elements.password, 'Неверный email или пароль');
      return;
    }
    console.log('Пользователь вошёл:', user);
    login(user);
    form.reset();
  });
});


const cards = document.querySelector('.cards');
const more = document.querySelector('.more');

if (more) {
  more.addEventListener('click', async (e) => {
    e.preventDefault();
    if (more.classList.contains('is-loading')) return;

    const lastId = cards.lastElementChild.dataset.id;
    more.classList.add('is-loading');

    try {
      const response = await fetch(`more.php?last_id=${encodeURIComponent(lastId)}`);
      if (!response.ok) throw new Error(`Ошибка ${response.status}`);

      cards.insertAdjacentHTML('beforeend', await response.text());
      more.hidden = response.headers.get('X-Has-More') !== '1';
    } catch (error) {
      console.error('Не удалось загрузить обзоры:', error);
    } finally {
      more.classList.remove('is-loading');
    }
  });
}
