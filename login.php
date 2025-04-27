<?php
require_once 'includes/header.php';

if (isLoggedIn()) {
	header("Location: index.php");
	exit();
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (loginUser($username, $password)) {
		$_SESSION['message'] = "Вы успешно вошли в систему";
		header("Location: index.php");
		exit();
	} else {
		$_SESSION['message'] = "Неверное имя пользователя или пароль";
	}
}
?>

<h2>Вход в систему</h2>

<form action="login.php" method="post" class="auth-form">
	<div class="form-group">
		<label for="username">Имя пользователя:</label>
		<input type="text" id="username" name="username" required>
	</div>

	<div class="form-group">
		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required>
	</div>

	<button type="submit" class="btn">Войти</button>
	<p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</form>

<?php
require_once 'includes/footer.php';
?>