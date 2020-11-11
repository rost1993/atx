<?php
	use IcKomiApp\core\HeaderLoader;
	use IcKomiApp\widgets\FooterTop;
	use IcKomiApp\widgets\FooterBottom;
	use IcKomiApp\widgets\Modal;
	use IcKomiApp\widgets\Downloader;
?>

<!DOCTYPE html>
<html>
	<?php HeaderLoader::getHeader(); ?>
<body>
	<?php FooterTop::getFooter(); ?>
	<div class="container-fluid starter-template">
		<?= $content; ?>
	</div>
	<?= Modal::getModal(); ?>
	<?= Modal::getModalView(); ?>
	<?= Modal::getModalViewServiceInterface(); ?>
	<?= Modal::getModalDocumentView(); ?>
	<?= Downloader::getDownloader(); ?>
	<?php FooterBottom::getFooter(); ?>
</body>
</html>