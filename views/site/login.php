<?php
?>
<div class="row">
		<div class="col-sm-2"></div>
		<div class='col-sm-8'>
			<div class='card text-center border-dark' style='margin-top: 70px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<ul class="nav nav-pills card-header-pills justify-content-center">
						<li class='nav-item'>
							<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#Section1" role="tab" aria-controls="pills-home" aria-selected="true">АВТОРИЗАЦИЯ НА ВЕБ-РЕСУРСЕ</a>
						</li>
						<li class='nav-item'>
							<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#Section2" role="tab" aria-controls="pills-profile" aria-selected="false">ЗАРЕГИСТРИРОВАТЬСЯ НА ВЕБ-РЕСУРСЕ</a>
						</li>
					</ul>
				</div>
				<div class="card-body" style='margin: 0px 0px 0px 0px; padding: 0px;'>
					<div class="tab-content">
					
						<div id="Section1" class="tab-pane fade show active" role="tabpanel" aria-labelledby="pills-home-tab" style='margin: 0px; padding: 0px;'>
							<div class="card-body" id="user-auth">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Авторизация на веб-ресурсе</h5></span>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-1"></div>
									<div class="col col-sm-10">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="LOGIN"><span class="fa fa-user-circle-o"></span></label></div>
											<input type="text" class="form-control black-text" id="LOGIN_USER" placeholder="Логин пользователя" maxlength='100' value="" autofocus>
										</div>
									</div>
									<div class="col col-sm-1"></div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-1"></div>
									<div class="col col-sm-10">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="PASSWORD"><span class="fa fa-key"></span></label></div>
											<input type="password" class="form-control black-text" id="PASSWORD_USER" placeholder="Пароль" maxlength='100'>
											<div class="input-group-append">
												<button class="btn btn-outline-primary eye-password" title="Показать/скрыть пароль"><span class="fa fa-eye"></span></button>
											</div>
										</div>
									</div>
									<div class="col col-sm-1"></div>
								</div>
									
							</div>
							
							<div class="card-footer text-center">
								<button type="button" class="btn btn-success btn-login" title="Войти на веб-ресурс">ВХОД</button>
							</div>
						</div>
						
						<div id="Section2" class="tab-pane fade show">
							<div class="card-body" id="user-registration">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Регистрация нового пользователя</h5></span>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="LASTNAME" class="font-weight-bold" style="font-size: 17px;">Фамилия</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="LASTNAME"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="LASTNAME" placeholder="Фамилия пользователя" maxlength='100' autofocus>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="FIRSTNAME" class="font-weight-bold" style="font-size: 17px;">Имя</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="FIRSTNAME"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="FIRSTNAME" placeholder="Имя пользователя" maxlength='100'>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="MIDDLENAME" class="font-weight-bold" style="font-size: 17px;">Отчество</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="MIDDLENAME"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="MIDDLENAME" placeholder="Отчество пользователя" maxlength='100'>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-3 text-right align-middle">
										<label for="LOGIN" class="font-weight-bold" style="font-size: 17px;">Логин</label>
									</div>
									<div class="col col-sm-9">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="LOGIN"><span class="fa fa-user"></span></label></div>
											<input type="text" class="form-control black-text" id="LOGIN" placeholder="Логин пользователя" maxlength='20'>
										</div>
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
								<button type="button" class="btn btn-success btn-registration" title="Зарегистрироваться">Зарегистрироваться</button>
							</div>
						</div>
						
					</div>
					
					
				</div>
			</div>
		</div>
		<div class='col-sm-2'></div>
	</div>