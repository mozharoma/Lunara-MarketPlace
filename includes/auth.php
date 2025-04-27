<?php
// Проверка авторизации
function isLoggedIn()
{
	return isset($_SESSION['user_id']);
}

// Проверка админских прав
function isAdmin()
{
	return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Получение информации о текущем пользователе
function getCurrentUser()
{
	global $pdo;
	if (!isLoggedIn())
		return null;

	$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->execute([$_SESSION['user_id']]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Регистрация пользователя
function registerUser($username, $email, $password, $first_name = '', $last_name = '')
{
	global $pdo;

	$hashed_password = password_hash($password, PASSWORD_DEFAULT);

	try {
		$stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$username, $email, $hashed_password, $first_name, $last_name]);
		return true;
	} catch (PDOException $e) {
		return false;
	}
}

// Авторизация пользователя
function loginUser($username, $password)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
	$stmt->execute([$username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['is_admin'] = $user['is_admin'];
		return true;
	}

	return false;
}

// Выход из системы
function logout()
{
	session_destroy();
	header("Location: login.php");
	exit();
}
?>