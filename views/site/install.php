<?php
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">

		
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header'>
					<h4>Установка базы данных</h4>
				</div>
				
				<div class="card-body" id="formInstall">
					<div class="form-row">
						<div class="col col-sm-3 mb-1 text-right">
							<label for="fam" class="font-weight-bold fs-13">Имя пользователя базы данных</label>
						</div>
						<div class="col col-sm-7 mb-1">
							<div class="input-group">
								<div class="input-group-prepend"><label class="input-group-text black-text" for="login_user"><span class="fa fa-user-circle-o"></span></label></div>
								<input type="text" class="form-control black-text" id="login_user" placeholder="Логин пользователя" maxlength='100' value="" autofocus>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col col-sm-3 mb-1 text-right">
							<label for="imj" class="font-weight-bold fs-13">Пароль</label>
						</div>
						<div class="col col-sm-7 mb-1">
							<div class="input-group">
								<div class="input-group-prepend"><label class="input-group-text black-text" for="password_user"><span class="fa fa-key"></span></label></div>
								<input type="password" class="form-control black-text" id="password_user" placeholder="Пароль" maxlength='100'>
								<div class="input-group-append">
									<button class="btn btn-outline-primary eye-password" title="Показать/скрыть пароль"><span class="fa fa-eye"></span></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="card-header">
					<button class="btn btn-success" id="btnInstallDatabase"><span class="fa fa-check">&nbsp;</span>Установить базу данных</button>
				</div>

			</div>
		</div>
	</div>
</div>