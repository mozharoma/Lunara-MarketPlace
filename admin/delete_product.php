<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
	$_SESSION['message'] = "Доступ запрещен";
	header("Location: ../index.php");
	exit();
}

if (!isset($_GET['id'])) {
	header("Location: products.php");
	exit();
}

$product_id = $_GET['id'];

// Получаем информацию о товаре для удаления его изображения
global $pdo;
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Удаляем товар из базы данных
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
if ($stmt->execute([$product_id])) {
	// Удаляем изображение товара, если оно существует
	if ($product['image'] && file_exists('../' . $product['image'])) {
		unlink('../' . $product['image']);
	}
	$_SESSION['message'] = "Товар успешно удален";
} else {
	$_SESSION['message'] = "Ошибка при удалении товара";
}

header("Location: products.php");
exit();
?>