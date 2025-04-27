<?php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lunara_marketplace');

// Настройки сайта
define('SITE_NAME', 'Lunara Marketplace');
define('SITE_URL', 'http://localhost/lunara-marketplace');

// Инициализация сессии
session_start();

// Подключение к базе данных
require_once 'db.php';
?>