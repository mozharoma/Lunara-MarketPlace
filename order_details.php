<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
	$_SESSION['message'] = "Для просмотра заказов необходимо войти в систему";
	header("Location: login.php");
	exit();
}

if (!isset($_GET['id'])) {
	header("Location: orders.php");
	exit();
}

$order_id = $_GET['id'];
$order_details = getOrderDetails($order_id);

// Проверка, что заказ принадлежит пользователю (или это админ)
$stmt = $pdo->prepare("SELECT user_id FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order_user_id = $stmt->fetchColumn();

if ($order_user_id != $_SESSION['user_id'] && !isAdmin()) {
	$_SESSION['message'] = "У вас нет доступа к этому заказу";
	header("Location: orders.php");
	exit();
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Детали заказа #<?php echo $order_id; ?></h2>

<div class="order-info">
	<p><strong>Дата заказа:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
	<p><strong>Статус:</strong> <?php echo ucfirst($order['status']); ?></p>
	<p><strong>Общая сумма:</strong> <?php echo number_format($order['total'], 2); ?> ₽</p>
</div>

<h3>Товары в заказе</h3>

<table class="order-items-table">
	<thead>
		<tr>
			<th>Товар</th>
			<th>Цена</th>
			<th>Количество</th>
			<th>Итого</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($order_details as $item): ?>
			<tr>
				<td>
					<img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="order-item-image">
					<?php echo $item['name']; ?>
				</td>
				<td><?php echo number_format($item['price'], 2); ?> ₽</td>
				<td><?php echo $item['quantity']; ?></td>
				<td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ₽</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
require_once 'includes/footer.php';
?>