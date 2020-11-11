<?php
	$fam = (empty($fam)) ? '' : $fam;
	$imj = (empty($imj)) ? '' : $imj;
	$otch = (empty($otch)) ? '' : $otch;
	$login = (empty($login)) ? '' : $login;
	$role_text = (empty($role_text)) ? '' : $role_text;
?>

<div class="row">
		<div class="col-sm-3"></div>
		<div class='col-sm-6'>
			<div class='card text-center border-dark' style='margin-top: 70px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<ul class="nav nav-pills card-header-pills justify-content-center">
						<li class='nav-item'>
							<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#Section1" role="tab" aria-controls="pills-home" aria-selected="true">НАСТРОЙКИ ПОЛЬЗОВАТЕЛЯ</a>
						</li>
						<li class='nav-item'>
							<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#Section2" role="tab" aria-controls="pills-profile" aria-selected="false">ИЗМЕНИТЬ ПАРОЛЬ</a>
						</li>
					</ul>
				</div>
				<div class="card-body" style='margin: 0px 0px 0px 0px; padding: 0px;'>
					<div class="tab-content">
					
						<div id="Section1" class="tab-pane fade show active" role="tabpanel" aria-labelledby="pills-home-tab" style='margin: 0px; padding: 0px;'>
							<div class="card-body" id="user-information">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Данные пользователя</h5></span>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="fam" class="font-weight-bold" style="font-size: 17px;">Фамилия</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="fam"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="fam" placeholder="Фамилия пользователя" maxlength='100' value="<?= $fam; ?>">
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="imj" class="font-weight-bold" style="font-size: 17px;">Имя</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="imj"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="imj" placeholder="Имя пользователя" maxlength='100' value="<?= $imj; ?>">
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="otch" class="font-weight-bold" style="font-size: 17px;">Отчество</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="otch"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="otch" placeholder="Отчество пользователя" maxlength='100' value="<?= $otch; ?>">
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="login" class="font-weight-bold" style="font-size: 17px;">Логин</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="login"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="login" placeholder="Логин пользователя" maxlength='20' value="<?= $login; ?>">
										</div>
									</div>
								</div>

								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="role_text" class="font-weight-bold" style="font-size: 17px;">Роль</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="role_text"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="role_text" placeholder="Роль" maxlength='20'  value="<?= $role_text; ?>" readonly>
										</div>
									</div>
								</div>
								
							</div>
							
							<div class="card-footer text-center">
								<button type="button" class="btn btn-success" id="btn-save-user-data" title="Сохранить">Сохранить</button>
							</div>
						</div>
						
						<div id="Section2" class="tab-pane fade show">
							<div class="card-body" id="user-information-password">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Изменить пароль</h5></span>
									</div>
								</div>
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="PASSWORD" class="font-weight-bold" style="font-size: 17px;">Пароль</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="PASSWORD"><span class="fa fa-key"></span></label></div>
											<input type="password" class="form-control black-text" id="PASSWORD" placeholder="Пароль" maxlength='20'>
											<div class="input-group-append">
												<button class="btn btn-outline-primary eye-password" title="Показать/скрыть пароль"><span class="fa fa-eye"></span></button>
											</div>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="PASSWORD2" class="font-weight-bold" style="font-size: 17px;">Пароль</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="PASSWORD2"><span class="fa fa-key"></span></label></div>
											<input type="password" class="form-control black-text" id="PASSWORD2" placeholder="Пароль" data-message-error="Введите повторно пароль" maxlength='20'>
											<div class="input-group-append">
												<button class="btn btn-outline-primary eye-password" title="Показать/скрыть пароль"><span class="fa fa-eye"></span></button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer text-center">
								<button type="button" class="btn btn-success" id="btn-change-user-password" title="Сохранить">Сохранить</button>
							</div>
						</div>
						
					</div>
					
					
				</div>
			</div>
		</div>
		<div class='col-sm-3'></div>
	</div>