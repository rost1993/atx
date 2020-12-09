<?php
	use IcKomiApp\widgets\Directory;

	$id = (empty($id)) ? '' : $id;
	$date_committing = (empty($date_committing)) ? '' : IcKomiApp\core\Functions::convertToDate($date_committing);
	$time_committing = (empty($time_committing)) ? '' : $time_committing;
	$place_committing = (empty($place_committing)) ? '' : $place_committing;
	$comment_committing = (empty($comment_committing)) ? '' : $comment_committing;
	$sum_committing = (empty($sum_committing)) ? '' : $sum_committing;
	$offender = (empty($offender)) ? '' : $offender;
	$date_recovery_cars = (empty($date_recovery_cars)) ? '' : IcKomiApp\core\Functions::convertToDate($date_recovery_cars);
	$recovery_committing = (empty($recovery_committing)) ? '' : $recovery_committing;

	if(!empty($id_car)) {
		$id_car = (empty($id_car)) ? '' : $id_car;
		$id_car_select = IcKomiApp\widgets\Directory::get_directory_car($id_car);
	} else if(!empty($add_car)) {
		$add_car = (empty($add_car)) ? '' : $add_car;
		$id_car_select = IcKomiApp\widgets\Directory::get_directory_car($add_car);
	} else {
		$id_car_select = IcKomiApp\widgets\Directory::get_directory_car();
	}

	if(!empty($id_driver)) {
		$id_driver = (empty($id_driver)) ? '' : $id_driver;
		$id_driver_select = IcKomiApp\widgets\Directory::get_directory_driver($id_driver);
	} else if(!empty($add_driver)) {
		$add_driver = (empty($add_driver)) ? '' : $add_driver;
		$id_driver_select = IcKomiApp\widgets\Directory::get_directory_driver($add_driver);
	} else {
		$id_driver_select = IcKomiApp\widgets\Directory::get_directory_driver();
	}

	$offender = (empty($offender)) ? '' : $offender;
	$offender_checkbox = (empty($offender)) ? '' : ' checked ';

	$list_files_doc = (empty($list_files_doc)) ? '' : $list_files_doc;
	$list_files_image = (empty($list_files_image)) ? '' : $list_files_image;

	$role = IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardDtpHeader">
					<h4>Информация о дорожно-транспортном происшествии</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardDtpHeaderServiceBadge"></div>
				</div>
				<div class="card-body" id="cardDtp">

					<div class="col-sm-12 atx-cars-block">
						<div id="mainDtpInformation">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5>1. ОБЩИЕ СВЕДЕНИЯ О ДОРОЖНО-ТРАНСПОРТНОМ ПРОИСШЕСТВИИ</h5></p>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="id_car" class="text-muted" style="font-size: 13px;"><strong>Транспортное срество</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="id_car" data-mandatory="true" data-message-error="Заполните обязательное поле: Транспортное средство" data-datatype="number">
									<?= $id_car_select; ?>
									</select>
								</div>
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="id_driver" class="text-muted" style="font-size: 13px;"><strong>Водитель</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="id_driver" data-mandatory="true" data-message-error="Заполните обязательное поле: Водитель" data-datatype="number">
									<?= $id_driver_select; ?>
									</select>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="date_committing" class="text-muted" style="font-size: 13px;"><strong>Дата совершения</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_committing" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата совершения" data-datatype="date" maxlength="10" placeholder="Дата совершения" value="<?= $date_committing; ?>">
								</div>
								<div class="col col-sm-3 mb-1 text-right" style="vertical-align: center;">
									<label for="time_committing" class="text-muted" style="font-size: 13px;"><strong>Время совершения</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text timepicker-here" id="time_committing" data-datatype="char" maxlength="5" placeholder="Время совершения" value="<?= $time_committing; ?>">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="place_committing" class="text-muted" style="font-size: 13px;"><strong>Место совершения</strong></label>
								</div>
								<div class="col col-sm-8 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="place_committing" data-mandatory="true" data-message-error="Заполните обязательное поле: Место совершения" data-datatype="char" maxlength="500" placeholder="Место совершения" value="<?= $place_committing; ?>">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="comment_committing" class="text-muted" style="font-size: 13px;"><strong>Описание ДТП</strong></label>
								</div>
								<div class="col col-sm-8 mb-1">
									<textarea type="text" class="form-control form-control-sm black-text" id="comment_committing" maxlength="4000" rows="5" data-datatype="char" placeholder="Описание ДТП"><?= $comment_committing; ?></textarea>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="sum_committing" class="text-muted" style="font-size: 13px;"><strong>Сумма ущерба, руб</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="sum_committing" data-message-error="Заполните обязательное поле: Сумма ущерба" data-datatype="number" placeholder="Сумма ущерба" value="<?= $sum_committing; ?>">
								</div>
								<div class="col col-sm-3 mb-1 text-right" style="vertical-align: center;">
									<label class='form-check-label text-muted' for='offender' style="font-size: 13px;"><strong>Виновен сотрудник</strong></label>&nbsp
								</div>
								<div class="col col-sm-3 mb-1 text-left" style="vertical-align: center;">
									<input type='checkbox' id='offender' name='offender' <?= $offender_checkbox; ?> data-datatype="checkbox">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="sum_committing" class="text-muted" style="font-size: 13px;"><strong>Дата восстановления ТС</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_recovery_cars" data-datatype="date" placeholder="Дата восстановления ТС" value="<?= $date_recovery_cars; ?>">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="recovery_committing" class="text-muted" style="font-size: 13px;"><strong>Восстановление</strong></label>
								</div>
								<div class="col col-sm-8 mb-1">
									<textarea type="text" class="form-control form-control-sm black-text" id="recovery_committing" maxlength="4000" rows="3" data-datatype="char" placeholder="Восстановление после ДТП"><?= $recovery_committing; ?></textarea>
								</div>
							</div>
						</div>
							
						<div class='form-row'>
							<div class='col-2 mb-1 text-right' style='vertical-align: center;'>
								<label for='btnAddFileModalWindow' class='text-muted' style='font-size: 13px;'><strong>Эл. образы</strong></label>
							</div>
							
							<div class='col-5 mb-1 text-left'>
								<div id='uploadFileContainer'><?= $list_files_doc; ?></div>
							</div>
							<div class='col-3 mb-1 text-right'>
								<span class='btn btn-sm btn-primary fileinput-button'>
									<span class='fa fa-folder-open'>&nbsp;</span>Выберите файл
										<input type='file' id='btnAddFileModalWindow' data-show-error='window' accept='.pdf' multiple>
								</span>
							</div>
						</div>
						
					</div>
					
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-2 mb-1 text-left">
								<p style="margin: 0px;"><h5>2. ФОТОГРАФИИ</h5></p>
							</div>
							<div class="col col-sm-8 mb-1 text-left">
								<span class='btn btn-sm btn-outline-primary fileinput-button'>
									<span class='fa fa-folder-open'>&nbsp;</span>Загрузить фотографии
										<input type='file' id='btnAddFileModalWindow' data-show-error='window' accept='.jpeg,.png,.jpg' data-upload-container='uploadFileContainerImage' multiple>
								</span>
							</div>
						</div>
						<div class='form-row'>
							<div class="col col-sm-2 mb-1 text-right"></div>
							<div class='col-8 mb-1 text-left'>
								<div id='uploadFileContainerImage'><?= $list_files_image; ?></div>
							</div>
						</div>
					</div>

				</div>
				
				<div class='card-footer card-header'><?php
					if($role >= 2) {
						echo "<button type='button' class='btn btn-success mr-1' id='btnSaveDtp' title='Сохранить информацию о ДТП'><span class='fa fa-check'>&nbsp;</span>Сохранить</button>";
						echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownRemoveDtp' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить информацию о ДТП'><span class='fa fa-remove'>&nbsp;</span>Удалить</button>";
						echo "<div class='dropdown-menu' aria-labelledby='dropdownRemoveDtp'>";
							echo "<button class='dropdown-item' id='btnRemoveDtp'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button>";
						echo "</div>";
					}
				?></div>

				</div>
			</div>
		</div>
</div>