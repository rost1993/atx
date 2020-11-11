<?php
	use IcKomiApp\widgets\Directory;

	$directory_select = Directory::get_list_directory();

	$role = 9;
?>

<div class="row">
		<div class="col-sm-2"></div>
		<div class='col-sm-8'>
			<div class='card text-center border-dark' style='margin-top: 70px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<ul class="nav nav-pills card-header-pills justify-content-center">
						<li class='nav-item'>
							<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#Section1" role="tab" aria-controls="pills-home" aria-selected="true">РЕДАКТОР СПРАВОЧНИКОВ</a>
						</li>
						<li class='nav-item'>
							<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#Section2" role="tab" aria-controls="pills-profile" aria-selected="false">УДАЛЕНИЕ ЗНАЧЕНИЙ</a>
						</li>
					</ul>
				</div>
				<div class="card-body" style='margin: 0px 0px 0px 0px; padding: 0px;'>
					<div class="tab-content">
					
						<div id="Section1" class="tab-pane fade show active" role="tabpanel" aria-labelledby="pills-home-tab" style='margin: 0px; padding: 0px;'>
							<div class="card-body" id="user-information">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Добавление/изменение значений в справочниках</h5></span>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-4 text-right align-middle">
										<label for="fam" class="font-weight-bold" style="font-size: 17px;">Выберите справочник</label>
									</div>
									<div class="col col-sm-8">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="fam"><span class="fa fa-list"></span></label></div>
											<select class="custom-select black-text" id="directory" data-datatype="number">
											<?= $directory_select; ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-4 text-right align-middle">
										<label for="value_directory" class="font-weight-bold" style="font-size: 17px;">Значение справочника</label>
									</div>
									<div class="col col-sm-8">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="imj"><span class="fa fa-list"></span></label></div>
											<select class="custom-select black-text" id="value_directory" data-datatype="number"></select>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-4 text-right align-middle">
										<label for="new_value_directory" class="font-weight-bold" style="font-size: 17px;">Новое значение справочника</label>
									</div>
									<div class="col col-sm-8">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="otch"><span class="fa fa-list"></span></label></div>
											<input type="text" class="form-control black-text" id="new_value_directory" placeholder="Новое значение справочника" maxlength='200'>
										</div>
									</div>
								</div>

							</div>
							
							<div class="card-footer text-center">
								<button type="button" class="btn btn-success" id="btn-save-value-directory" data-type="1" title="Сохранить">Добавить значение</button>
							</div>
						</div>
						
						<div id="Section2" class="tab-pane fade show">
							<div class="card-body" id="user-information-password">
								<div class="form-row text-center mb-3">
									<div class="col">
										<span class="heading font-weight-bold"><h5>Удаление значений в справочниках</h5></span>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-4 text-right align-middle">
										<label for="fam" class="font-weight-bold" style="font-size: 17px;">Выберите справочник</label>
									</div>
									<div class="col col-sm-8">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="fam"><span class="fa fa-list"></span></label></div>
											<select class="custom-select black-text" id="directory2" data-datatype="number">
											<?= $directory_select; ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="form-row mb-2">
									<div class="col col-sm-4 text-right align-middle">
										<label for="value_directory" class="font-weight-bold" style="font-size: 17px;">Значение справочника</label>
									</div>
									<div class="col col-sm-8">
										<div class="input-group">
											<div class="input-group-prepend"><label class="input-group-text black-text" for="imj"><span class="fa fa-list"></span></label></div>
											<select class="custom-select black-text" id="value_directory2" data-datatype="number"></select>
										</div>
									</div>
								</div>

							</div>
							<div class="card-footer text-center">
								<button type="button" class="btn btn-danger" id="btn-remove-value-directory" title="Удалить">Удалить</button>
							</div>
						</div>
						
					</div>
					
					
				</div>
			</div>
		</div>
		<div class='col-sm-2'></div>
	</div>