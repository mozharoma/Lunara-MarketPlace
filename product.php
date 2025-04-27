<?php
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
	header("Location: index.php");
	exit();
}

$product = getProductById($_GET['id']);
if (!$product) {
	$_SESSION['message'] = "Товар не найден";
	header("Location: index.php");
	exit();
}
?>

<div class="product-details">
	<div class="product-image">
		<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
	</div>
	<div class="product-info">
		<h2><?php echo $product['name']; ?></h2>
		<p class="price"><?php echo number_format($product['price'], 2); ?> ₽</p>
		<p><?php echo $product['description']; ?></p>
		<p>На складе: <?php echo $product['stock']; ?> шт.</p>

		<?php if (isLoggedIn()): ?>
			<form action="cart.php" method="post">
				<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
				<label for="quantity">Количество:</label>
				<input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
				<button type="submit" class="btn">Добавить в корзину</button>
			</form>
		<?php else: ?>
			<p>Войдите, чтобы добавить товар в корзину</p>
		<?php endif; ?>
	</div>
</div>

<?php
require_once 'includes/footer.php';
?>