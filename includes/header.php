<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo SITE_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
	<header>
		<div class="container">
			<h1><a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a></h1>
			<nav>
				<ul>
					<li><a href="<?php echo SITE_URL; ?>">Главная</a></li>
					<?php if (isLoggedIn()): ?>
						<li><a href="<?php echo SITE_URL; ?>/cart.php">Корзина</a></li>
						<li><a href="<?php echo SITE_URL; ?>/profile.php">Профиль</a></li>
						<li><a href="<?php echo SITE_URL; ?>/orders.php">Мои заказы</a></li>
						<?php if (isAdmin()): ?>
							<li><a href="<?php echo SITE_URL; ?>/admin">Админ-панель</a></li>
						<?php endif; ?>
						<li><a href="<?php echo SITE_URL; ?>/logout.php">Выйти</a></li>
					<?php else: ?>
						<li><a href="<?php echo SITE_URL; ?>/login.php">Войти</a></li>
						<li><a href="<?php echo SITE_URL; ?>/register.php">Регистрация</a></li>
					<?php endif; ?>
				</ul>
			</nav>
		</div>
	</header>

	<main class="container">
		<?php if (isset($_SESSION['message'])): ?>
			<div class="alert"><?php echo $_SESSION['message'];
			unset($_SESSION['message']); ?></div>
		<?php endif; ?>