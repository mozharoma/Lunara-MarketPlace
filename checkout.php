<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
	$_SESSION['message'] = "Для оформления заказа необходимо войти в систему";
	header("Location: login.php");
	exit();
}

$cart_items = getCart();
if (empty($cart_items)) {
	$_SESSION['message'] = "Ваша корзина пуста";
	header("Location: cart.php");
	exit();
}

$user = getCurrentUser();
$total = 0;
foreach ($cart_items as $item) {
	$total += $item['price'] * $item['quantity'];
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$order_id = createOrder($_SESSION['user_id'], $cart_items);
	if ($order_id) {
		$_SESSION['message'] = "Заказ #$order_id успешно оформлен!";
		header("Location: orders.php");
		exit();
	} else {
		$_SESSION['message'] = "Ошибка при оформлении заказа";
	}
}
?>

<h2>Оформление заказа</h2>

<div class="checkout-container">
	<div class="order-summary">
		<h3>Ваш заказ</h3>
		<ul>
			<?php foreach ($cart_items as $item): ?>
				<li>
					<?php echo $item['name']; ?> × <?php echo $item['quantity']; ?> =
					<?php echo number_format($item['price'] * $item['quantity'], 2); ?> ₽
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="total">Итого: <?php echo number_format($total, 2); ?> ₽</p>
	</div>

	<div class="shipping-details">
		<h3>Данные для доставки</h3>
		<form action="checkout.php" method="post">
			<div class="form-group">
				<label for="first_name">Имя:</label>
				<input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
			</div>

			<div class="form-group">
				<label for="last_name">Фамилия:</label>
				<input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
			</div>

			<div class="form-group">
				<label for="email">Email:</label>
				<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
			</div>

			<div class="form-group">
				<label for="phone">Телефон:</label>
				<input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
			</div>

			<div class="form-group">
				<label for="address">Адрес доставки:</label>
				<textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
			</div>

			<button type="submit" class="btn btn-primary">Подтвердить заказ</button>
		</form>
	</div>
</div>

<?php
require_once 'includes/footer.php';
?>