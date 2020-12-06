<?php ?>
<nav class="navbar fixed-top navbar-expand-lg navbar-dark" style="background-color: #000033;">
	<a class="navbar-brand" href="/" title="Автохозяйство">
		<img style="max-width: 60px; margin-top: -7px;" src="assets/images/brand.png" alt="Автохозяйство">
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
		
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<?= $left_menu; ?>
			<!--<li class='nav-item dropdown'>
				<a class='text-white nav-link dropdown-toggle' href='#' id='dropdownCars' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title=''>ЦФО</a>
					<div class='dropdown-menu' aria-labelledby='dropdownCars'>
						<a class='dropdown-item' id='cfo_search_card' href='/cfo_search_card' title=''><span class='fa fa-search'></span>&nbsp;Поиск</a>
						<a class='dropdown-item' id='cfo_card' href='/cfo_card' title=''><span class='fa fa-edit'></span>&nbsp;Добавить/скорректировать</a>
					</div>
			</li>

			<li class="nav-item">
				<a class="text-white nav-link" id="contacts" href="contacts" title="Переход на страницу с контактными данными сотрудников">Контакты</a>
			</li>-->
		</ul>
		<ul class="navbar-nav navbar-right">
			<li class="nav-item dropdown">
				<a class="text-white nav-link dropdown-toggle" href="#" id="user" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Меню</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user">
					<?= $right_menu; ?>
					<!--<a class="dropdown-item" id="documentation" href=""><span class="fa fa-book"></span>&nbsp;Документация</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" id="autorization" href="/login"><span class="fa fa-power-off"></span>&nbsp;Войти на веб-ресурс</a>-->
				</div>
			</li>
		</ul>
	</div>
</nav>