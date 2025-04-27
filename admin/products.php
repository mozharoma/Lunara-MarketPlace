<?php
require_once '../includes/header.php';

if (!isAdmin()) {
	$_SESSION['message'] = "Доступ запрещен";
	header("Location: ../index.php");
	exit();
}

$products = getAllProducts();
?>

<h2>Управление товарами</h2>

<a href="add_product.php" class="btn">Добавить товар</a>

<table class="admin-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Изображение</th>
			<th>Название</th>
			<th>Цена</th>
			<th>Категория</th>
			<th>На складе</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($products as $product): ?>
			<tr>
				<td><?php echo $product['id']; ?></td>
				<td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="admin-product-image"></td>
				<td><?php echo $product['name']; ?></td>
				<td><?php echo number_format($product['price'], 2); ?> ₽</td>
				<td><?php echo $product['category']; ?></td>
				<td><?php echo $product['stock']; ?></td>
				<td>
					<a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn">Редактировать</a>
					<a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот товар?')">Удалить</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
require_once '../includes/footer.php';
?>