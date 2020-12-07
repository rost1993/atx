<?php
	use IcKomiApp\core\HeaderLoader;
	use IcKomiApp\widgets\FooterTop;
	use IcKomiApp\widgets\FooterBottom;
?>

<!DOCTYPE html>
<html>
	<?php HeaderLoader::getHeader(); ?>
<body>
	<?php FooterTop::getFooter(); ?>
	<div class="container-fluid starter-template">
		<div class='access-denied-2 text-center'>Ошибка 403: Доступ к данной странице заблокирован</div>
	</div>
	<?php FooterBottom::getFooter(); ?>
</body>
</html>