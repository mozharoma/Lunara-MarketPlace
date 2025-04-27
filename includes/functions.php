<?php
// Получение всех товаров
function getAllProducts()
{
	global $pdo;
	$stmt = $pdo->query("SELECT * FROM products");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение товара по ID
function getProductById($id)
{
	global $pdo;
	$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->execute([$id]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Добавление товара в корзину
function addToCart($product_id, $quantity = 1)
{
	if (!isLoggedIn())
		return false;

	global $pdo;

	// Проверяем, есть ли уже такой товар в корзине
	$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
	$stmt->execute([$_SESSION['user_id'], $product_id]);
	$existing = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($existing) {
		// Обновляем количество
		$new_quantity = $existing['quantity'] + $quantity;
		$stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
		return $stmt->execute([$new_quantity, $existing['id']]);
	} else {
		// Добавляем новый товар
		$stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
		return $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
	}
}

// Получение корзины пользователя
function getCart()
{
	if (!isLoggedIn())
		return [];

	global $pdo;
	$stmt = $pdo->prepare("
        SELECT c.*, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
	$stmt->execute([$_SESSION['user_id']]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Очистка корзины
function clearCart()
{
	if (!isLoggedIn())
		return false;

	global $pdo;
	$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
	return $stmt->execute([$_SESSION['user_id']]);
}

// Создание заказа
function createOrder($user_id, $cart_items)
{
	global $pdo;

	try {
		$pdo->beginTransaction();

		// Рассчитываем общую сумму
		$total = 0;
		foreach ($cart_items as $item) {
			$total += $item['price'] * $item['quantity'];
		}

		// Создаем заказ
		$stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
		$stmt->execute([$user_id, $total]);
		$order_id = $pdo->lastInsertId();

		// Добавляем товары в заказ
		foreach ($cart_items as $item) {
			$stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
			$stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
		}

		// Очищаем корзину
		clearCart();

		$pdo->commit();
		return $order_id;
	} catch (Exception $e) {
		$pdo->rollBack();
		return false;
	}
}

// Получение заказов пользователя
function getUserOrders($user_id)
{
	global $pdo;
	$stmt = $pdo->prepare("
        SELECT o.*, COUNT(oi.id) as items_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
	$stmt->execute([$user_id]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение всех заказов (для админа)
function getAllOrders()
{
	global $pdo;
	$stmt = $pdo->query("
        SELECT o.*, u.username, COUNT(oi.id) as items_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        JOIN users u ON o.user_id = u.id 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение деталей заказа
function getOrderDetails($order_id)
{
	global $pdo;
	$stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
	$stmt->execute([$order_id]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>