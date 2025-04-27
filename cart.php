<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
	$_SESSION['message'] = "Для просмотра корзины необходимо войти в систему";
	header("Location: login.php");
	exit();
}

// Обработка добавления товара в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
	$product_id = $_POST['product_id'];
	$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

	if (addToCart($product_id, $quantity)) {
		$_SESSION['message'] = "Товар добавлен в корзину";
	} else {
		$_SESSION['message'] = "Ошибка при добавлении товара в корзину";
	}
}

// Обработка удаления товара из корзины
if (isset($_GET['remove'])) {
	$cart_id = $_GET['remove'];
	global $pdo;
	$stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
	if ($stmt->execute([$cart_id, $_SESSION['user_id']])) {
		$_SESSION['message'] = "Товар удален из корзины";
	}
}

// Обработка обновления количества
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
	foreach ($_POST['quantities'] as $cart_id => $quantity) {
		$quantity = (int) $quantity;
		if ($quantity > 0) {
			global $pdo;
			$stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
			$stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
		}
	}
	$_SESSION['message'] = "Корзина обновлена";
}

$cart_items = getCart();
$total = 0;
?>

<h2>Ваша корзина</h2>

<?php if (empty($cart_items)): ?>
	<p>Ваша корзина пуста</p>
<?php else: ?>
	<form action="cart.php" method="post">
		<table class="cart-table">
			<thead>
				<tr>
					<th>Товар</th>
					<th>Цена</th>
					<th>Количество</th>
					<th>Итого</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cart_items as $item):
					$item_total = $item['price'] * $item['quantity'];
					$total += $item_total;
					?>
					<tr>
						<td>
							<img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-image">
							<?php echo $item['name']; ?>
						</td>
						<td><?php echo number_format($item['price'], 2); ?> ₽</td>
						<td>
							<input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
						</td>
						<td><?php echo number_format($item_total, 2); ?> ₽</td>
						<td>
							<a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger">Удалить</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">Общая сумма:</td>
					<td colspan="2"><?php echo number_format($total, 2); ?> ₽</td>
				</tr>
			</tfoot>
		</table>

		<div class="cart-actions">
			<button type="submit" name="update_cart" class="btn">Обновить корзину</button>
			<a href="checkout.php" class="btn btn-primary">Оформить заказ</a>
		</div>
	</form>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>