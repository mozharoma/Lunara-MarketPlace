<?php
require_once '../includes/header.php';

if (!isAdmin()) {
	$_SESSION['message'] = "Доступ запрещен";
	header("Location: ../index.php");
	exit();
}

// Обработка формы добавления товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'];
	$description = $_POST['description'];
	$price = $_POST['price'];
	$category = $_POST['category'];
	$stock = $_POST['stock'];

	// Обработка загрузки изображения
	$image = '';
	if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
		$upload_dir = '../assets/images/products/';
		if (!file_exists($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}

		$file_name = uniqid() . '_' . basename($_FILES['image']['name']);
		$target_file = $upload_dir . $file_name;

		// Проверка типа файла
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

		if (in_array($imageFileType, $allowed_types)) {
			if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
				$image = 'assets/images/products/' . $file_name;
			} else {
				$_SESSION['message'] = "Ошибка при загрузке изображения";
			}
		} else {
			$_SESSION['message'] = "Разрешены только JPG, JPEG, PNG и GIF файлы";
		}
	}

	// Добавление товара в базу данных
	if (empty($_SESSION['message'])) {
		global $pdo;
		try {
			$stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, image, category, stock) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
			$stmt->execute([$name, $description, $price, $image, $category, $stock]);

			$_SESSION['message'] = "Товар успешно добавлен";
			header("Location: products.php");
			exit();
		} catch (PDOException $e) {
			$_SESSION['message'] = "Ошибка при добавлении товара: " . $e->getMessage();
		}
	}
}
?>

<h2>Добавить новый товар</h2>

<form action="add_product.php" method="post" enctype="multipart/form-data" class="product-form">
	<div class="form-group">
		<label for="name">Название товара:</label>
		<input type="text" id="name" name="name" required>
	</div>

	<div class="form-group">
		<label for="description">Описание:</label>
		<textarea id="description" name="description" rows="4" required></textarea>
	</div>

	<div class="form-group">
		<label for="price">Цена (₽):</label>
		<input type="number" id="price" name="price" step="0.01" min="0" required>
	</div>

	<div class="form-group">
		<label for="category">Категория:</label>
		<input type="text" id="category" name="category" required>
	</div>

	<div class="form-group">
		<label for="stock">Количество на складе:</label>
		<input type="number" id="stock" name="stock" min="0" required>
	</div>

	<div class="form-group">
		<label for="image">Изображение товара:</label>
		<input type="file" id="image" name="image" accept="image/*" required>
	</div>

	<button type="submit" class="btn">Добавить товар</button>
	<a href="products.php" class="btn btn-secondary">Отмена</a>
</form>

<?php
require_once '../includes/footer.php';
?>