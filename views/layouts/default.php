<?php
	use IcKomiApp\widgets\Modal;
	use IcKomiApp\widgets\Downloader;
?>

<!DOCTYPE html>
<html>
	<?= $header; ?>
<body>
	<?= $footer_top; ?>
	<div class="container-fluid starter-template">
		<?= $content; ?>
	</div>
	<?= Modal::getModal(); ?>
	<?= Modal::getModalView(); ?>
	<?= Modal::getModalViewServiceInterface(); ?>
	<?= Modal::getModalDocumentView(); ?>
	<?= Downloader::getDownloader(); ?>
	<?= $footer_bottom; ?>
</body>
</html>