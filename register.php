<?php
require_once 'includes/header.php';

if (isLoggedIn()) {
	header("Location: index.php");
	exit();
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];

	// Валидация
	if ($password !== $confirm_password) {
		$_SESSION['message'] = "Пароли не совпадают";
	} elseif (strlen($password) < 6) {
		$_SESSION['message'] = "Пароль должен содержать минимум 6 символов";
	} else {
		if (registerUser($username, $email, $password, $first_name, $last_name)) {
			$_SESSION['message'] = "Регистрация прошла успешно! Теперь вы можете войти.";
			header("Location: login.php");
			exit();
		} else {
			$_SESSION['message'] = "Ошибка при регистрации. Возможно, пользователь с таким именем или email уже существует.";
		}
	}
}
?>

<h2>Регистрация</h2>

<form action="register.php" method="post" class="auth-form">
	<div class="form-group">
		<label for="username">Имя пользователя:</label>
		<input type="text" id="username" name="username" required>
	</div>

	<div class="form-group">
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
	</div>

	<div class="form-group">
		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required>
	</div>

	<div class="form-group">
		<label for="confirm_password">Подтвердите пароль:</label>
		<input type="password" id="confirm_password" name="confirm_password" required>
	</div>

	<div class="form-group">
		<label for="first_name">Имя:</label>
		<input type="text" id="first_name" name="first_name">
	</div>

	<div class="form-group">
		<label for="last_name">Фамилия:</label>
		<input type="text" id="last_name" name="last_name">
	</div>

	<button type="submit" class="btn">Зарегистрироваться</button>
	<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
</form>

<?php
require_once 'includes/footer.php';
?>