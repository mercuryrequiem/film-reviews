<?php

// Если проект открыт как http://localhost/lab2/, а не через виртуальный хост, отправляем в public/
header('Location: public/');
