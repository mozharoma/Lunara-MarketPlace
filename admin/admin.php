<?php
require_once '../includes/header.php';

if (!isAdmin()) {
	$_SESSION['message'] = "Доступ запрещен";
	header("Location: ../index.php");
	exit();
}
?>

<h2>Админ-панель</h2>

<div class="admin-menu">
	<a href="products.php" class="btn">Управление товарами</a>
	<a href="orders.php" class="btn">Управление заказами</a>
	<a href="users.php" class="btn">Управление пользователями</a>
</div>

<?php
require_once '../includes/footer.php';
?>