// Основные скрипты для улучшения UX
document.addEventListener('DOMContentLoaded', function () {
	// Подтверждение удаления
	const deleteButtons = document.querySelectorAll('.btn-danger');
	deleteButtons.forEach(button => {
		button.addEventListener('click', function (e) {
			if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
				e.preventDefault();
			}
		});
	});

	// Динамическое обновление количества в корзине
	const quantityInputs = document.querySelectorAll('.quantity-input');
	quantityInputs.forEach(input => {
		input.addEventListener('change', function () {
			this.closest('form').submit();
		});
	});

	// Плавная прокрутка
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function (e) {
			e.preventDefault();
			document.querySelector(this.getAttribute('href')).scrollIntoView({
				behavior: 'smooth'
			});
		});
	});
});