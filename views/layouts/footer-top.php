<?php ?>
<nav class="navbar fixed-top navbar-expand-lg navbar-dark" style="background-color: #000033;">
	<a class="navbar-brand" href="/" title="Автохозяйство">
		<img style="max-width: 60px; margin-top: -7px;" src="assets/favicon/favicon.ico" alt="Автохозяйство">
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
		
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<?= $left_menu; ?>
		</ul>
		<ul class="navbar-nav navbar-right">
			<?= $notice_events_html; ?>
			<li class="nav-item dropdown">
				<a class="text-white nav-link dropdown-toggle" href="#" id="user" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Меню <?= $login; ?></a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user">
					<?= $right_menu; ?>
				</div>
			</li>
		</ul>
	</div>
</nav>