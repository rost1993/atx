<?php
	$company = (empty($GLOBALS['web_config']['company'])) ? '' : $GLOBALS['web_config']['company'];
	$link_company = (empty($GLOBALS['web_config']['link_company'])) ? '' : $GLOBALS['web_config']['link_company'];
?>
<footer class='navbar navbar-expand-lg navbar-dark bottom-footer-template'>
	<div class='container-fluid'>
		<div class='col-12 text-center'>
			<p class='text-brand-ic'>
				<a href="<?= $link_company; ?>" style='color: #C0C0C0;'><?= $company; ?></a>
			</p>
		</div>
	</div>
</footer>