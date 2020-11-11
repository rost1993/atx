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
		<div id='not-found'> </div>
	</div>
	<?php FooterBottom::getFooter(); ?>
</body>
</html>