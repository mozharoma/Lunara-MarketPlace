<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
	die("Доступ запрещен");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
	$order_id = $_POST['order_id'];
	$status = $_POST['status'];

	global $pdo;
	$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
	if ($stmt->execute([$status, $order_id])) {
		$_SESSION['message'] = "Статус заказа обновлен";
	} else {
		$_SESSION['message'] = "Ошибка при обновлении статуса";
	}
}

header("Location: orders.php");
exit();
?>