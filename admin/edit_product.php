<?php
require_once '../includes/header.php';

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
$product = getProductById($product_id);

if (!$product) {
	$_SESSION['message'] = "Товар не найден";
	header("Location: products.php");
	exit();
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'];
	$description = $_POST['description'];
	$price = $_POST['price'];
	$category = $_POST['category'];
	$stock = $_POST['stock'];

	$image = $product['image'];

	// Обработка загрузки нового изображения
	if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
		$upload_dir = '../assets/images/products/';
		$file_name = uniqid() . '_' . basename($_FILES['image']['name']);
		$target_file = $upload_dir . $file_name;

		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

		if (in_array($imageFileType, $allowed_types)) {
			if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
				// Удаляем старое изображение, если оно существует
				if ($image && file_exists('../' . $image)) {
					unlink('../' . $image);
				}
				$image = 'assets/images/products/' . $file_name;
			} else {
				$_SESSION['message'] = "Ошибка при загрузке изображения";
			}
		} else {
			$_SESSION['message'] = "Разрешены только JPG, JPEG, PNG и GIF файлы";
		}
	}

	// Обновление товара в базе данных
	if (empty($_SESSION['message'])) {
		global $pdo;
		try {
			$stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, image = ?, category = ?, stock = ? 
                WHERE id = ?
            ");
			$stmt->execute([$name, $description, $price, $image, $category, $stock, $product_id]);

			$_SESSION['message'] = "Товар успешно обновлен";
			header("Location: products.php");
			exit();
		} catch (PDOException $e) {
			$_SESSION['message'] = "Ошибка при обновлении товара: " . $e->getMessage();
		}
	}
}
?>

<h2>Редактировать товар</h2>

<form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data" class="product-form">
	<div class="form-group">
		<label for="name">Название товара:</label>
		<input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
	</div>

	<div class="form-group">
		<label for="description">Описание:</label>
		<textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
	</div>

	<div class="form-group">
		<label for="price">Цена (₽):</label>
		<input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
	</div>

	<div class="form-group">
		<label for="category">Категория:</label>
		<input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
	</div>

	<div class="form-group">
		<label for="stock">Количество на складе:</label>
		<input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
	</div>

	<div class="form-group">
		<label for="image">Изображение товара:</label>
		<input type="file" id="image" name="image" accept="image/*">
		<?php if ($product['image']): ?>
			<div class="current-image">
				<p>Текущее изображение:</p>
				<img src="../<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 200px;">
			</div>
		<?php endif; ?>
	</div>

	<button type="submit" class="btn">Сохранить изменения</button>
	<a href="products.php" class="btn btn-secondary">Отмена</a>
</form>

<?php
require_once '../includes/footer.php';
?>