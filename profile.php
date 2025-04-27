<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
	$_SESSION['message'] = "Для просмотра профиля необходимо войти в систему";
	header("Location: login.php");
	exit();
}

$user = getCurrentUser();

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];

	// Обновление пароля, если предоставлен новый
	$password_update = '';
	if (!empty($_POST['new_password'])) {
		if ($_POST['new_password'] !== $_POST['confirm_password']) {
			$_SESSION['message'] = "Пароли не совпадают";
		} else {
			$hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
			$password_update = ", password = '$hashed_password'";
		}
	}

	if (empty($_SESSION['message'])) {
		global $pdo;
		$stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? $password_update 
            WHERE id = ?
        ");

		$params = [$first_name, $last_name, $email, $phone, $address, $_SESSION['user_id']];

		if ($stmt->execute($params)) {
			$_SESSION['message'] = "Профиль успешно обновлен";
			header("Location: profile.php");
			exit();
		} else {
			$_SESSION['message'] = "Ошибка при обновлении профиля";
		}
	}
}
?>

<h2>Мой профиль</h2>

<form action="profile.php" method="post" class="profile-form">
	<div class="form-group">
		<label for="username">Имя пользователя:</label>
		<input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
	</div>

	<div class="form-group">
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
	</div>

	<div class="form-group">
		<label for="first_name">Имя:</label>
		<input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
	</div>

	<div class="form-group">
		<label for="last_name">Фамилия:</label>
		<input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
	</div>

	<div class="form-group">
		<label for="phone">Телефон:</label>
		<input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
	</div>

	<div class="form-group">
		<label for="address">Адрес:</label>
		<textarea id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
	</div>

	<h3>Смена пароля</h3>

	<div class="form-group">
		<label for="new_password">Новый пароль:</label>
		<input type="password" id="new_password" name="new_password">
	</div>

	<div class="form-group">
		<label for="confirm_password">Подтвердите новый пароль:</label>
		<input type="password" id="confirm_password" name="confirm_password">
	</div>

	<button type="submit" class="btn">Сохранить изменения</button>
</form>

<?php
require_once 'includes/footer.php';
?>