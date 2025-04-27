<?php
require_once 'includes/header.php';

$products = getAllProducts();
?>

<h2>Наши товары</h2>

<div class="products-grid">
	<?php foreach ($products as $product): ?>
		<div class="product-card">
			<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
			<h3><?php echo $product['name']; ?></h3>
			<p class="price"><?php echo number_format($product['price'], 2); ?> ₽</p>
			<a href="product.php?id=<?php echo $product['id']; ?>" class="btn">Подробнее</a>
			<?php if (isLoggedIn()): ?>
				<form action="cart.php" method="post" class="add-to-cart-form">
					<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
					<input type="number" name="quantity" value="1" min="1" class="quantity-input">
					<button type="submit" class="btn">В корзину</button>
				</form>
			<?php else: ?>
				<p>Войдите, чтобы добавить в корзину</p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<?php
require_once 'includes/footer.php';
?>