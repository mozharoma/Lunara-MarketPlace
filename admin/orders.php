<?php
require_once '../includes/header.php';

if (!isAdmin()) {
	$_SESSION['message'] = "Доступ запрещен";
	header("Location: ../index.php");
	exit();
}

$orders = getAllOrders();
?>

<h2>Управление заказами</h2>

<table class="admin-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Пользователь</th>
			<th>Дата</th>
			<th>Товаров</th>
			<th>Сумма</th>
			<th>Статус</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($orders as $order): ?>
			<tr>
				<td>#<?php echo $order['id']; ?></td>
				<td><?php echo $order['username']; ?></td>
				<td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
				<td><?php echo $order['items_count']; ?></td>
				<td><?php echo number_format($order['total'], 2); ?> ₽</td>
				<td>
					<form action="update_order_status.php" method="post" class="status-form">
						<input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
						<select name="status" onchange="this.form.submit()">
							<option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Ожидание</option>
							<option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>В обработке</option>
							<option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Отправлен</option>
							<option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Завершен</option>
							<option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
						</select>
					</form>
				</td>
				<td>
					<a href="../order_details.php?id=<?php echo $order['id']; ?>" class="btn">Подробнее</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
require_once '../includes/footer.php';
?>