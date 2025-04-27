<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
	$_SESSION['message'] = "Для просмотра заказов необходимо войти в систему";
	header("Location: login.php");
	exit();
}

$orders = getUserOrders($_SESSION['user_id']);
?>

<h2>Мои заказы</h2>

<?php if (empty($orders)): ?>
	<p>У вас пока нет заказов</p>
<?php else: ?>
	<table class="orders-table">
		<thead>
			<tr>
				<th>Номер заказа</th>
				<th>Дата</th>
				<th>Количество товаров</th>
				<th>Сумма</th>
				<th>Статус</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($orders as $order): ?>
				<tr>
					<td>#<?php echo $order['id']; ?></td>
					<td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
					<td><?php echo $order['items_count']; ?></td>
					<td><?php echo number_format($order['total'], 2); ?> ₽</td>
					<td><?php echo ucfirst($order['status']); ?></td>
					<td>
						<a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn">Подробнее</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>