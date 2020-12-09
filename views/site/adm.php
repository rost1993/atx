<?php
	use IcKomiApp\widgets\Directory;

	$id = (empty($id)) ? '' : $id;
	$date_adm = (empty($date_adm)) ? '' : IcKomiApp\core\Functions::convertToDate($date_adm);
	$time_adm = (empty($time_adm)) ? '' : $time_adm;
	$place_adm = (empty($place_adm)) ? '' : $place_adm;
	$comment_adm = (empty($comment_adm)) ? '' : $comment_adm;
	$sum_adm = (empty($sum_adm)) ? '' : $sum_adm;

	$oplat_adm = (empty($oplat_adm)) ? '' : $oplat_adm;
	$oplat_adm_checkbox = (empty($oplat_adm)) ? '' : ' checked ';

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

	$st_chast_koap = (empty($st_chast_koap)) ? '' : $st_chast_koap;
	$st_chast_koap_select = IcKomiApp\widgets\Directory::get_directory(26, $st_chast_koap);

	$list_files_doc = empty($list_files_doc) ? '' : $list_files_doc;

	$role = IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardAdmHeader">
					<h4>Информация об административном правонарушении по линии КоАП РФ</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardAdmHeaderServiceBadge"></div>
				</div>
				<div class="card-body" id="cardAdm">

					<div class="col-sm-12 atx-cars-block">
						<div id="mainAdmInformation">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5>1. ОБЩИЕ СВЕДЕНИЯ ОБ АДМИНИСТРАТИВНОМ ПРАВОНАРУШЕНИИ</h5></p>
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
									<label for="date_adm" class="text-muted" style="font-size: 13px;"><strong>Дата совершения</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_adm" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата совершения" data-datatype="date" maxlength="10" placeholder="Дата совершения" value="<?= $date_adm; ?>">
								</div>
								<div class="col col-sm-3 mb-1 text-right" style="vertical-align: center;">
									<label for="time_adm" class="text-muted" style="font-size: 13px;"><strong>Время совершения</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" data-timepicker="true" data-only-timepicker='true' id="time_adm" data-datatype="char" maxlength="5" placeholder="Время совершения" value="<?= $time_adm; ?>">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="st_chast_koap" class="text-muted" style="font-size: 13px;"><strong>Статья и часть КоАП РФ</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="st_chast_koap" data-mandatory="true" data-message-error="Заполните обязательное поле: Статья и часть КоАП РФ" data-datatype="number">
									<?= $st_chast_koap_select; ?>
									</select>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="place_adm" class="text-muted" style="font-size: 13px;"><strong>Место совершения</strong></label>
								</div>
								<div class="col col-sm-8 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="place_adm" data-mandatory="true" data-message-error="Заполните обязательное поле: Место совершения" data-datatype="char" maxlength="500" placeholder="Место совершения" value="<?= $place_adm; ?>">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="comment_adm" class="text-muted" style="font-size: 13px;"><strong>Описание</strong></label>
								</div>
								<div class="col col-sm-8 mb-1">
									<textarea type="text" class="form-control form-control-sm black-text" id="comment_adm" maxlength="1000" rows="5" data-datatype="char" placeholder="Описание адм. правонарушения"><?= $comment_adm; ?></textarea>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="sum_adm" class="text-muted" style="font-size: 13px;"><strong>Сумма штрафа, руб</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="sum_adm" data-mandatory='true' data-message-error="Заполните обязательное поле: Сумма штрафа" data-datatype="number" placeholder="Сумма штрафа" value="<?= $sum_adm; ?>">
								</div>
								<div class="col col-sm-3 mb-1 text-right" style="vertical-align: center;">
									<label class='form-check-label text-muted' for='oplat_adm' style="font-size: 13px;"><strong>Оплата штрафа</strong></label>&nbsp
								</div>
								<div class="col col-sm-3 mb-1 text-left" style="vertical-align: center;">
									<input type='checkbox' id='oplat_adm' name='oplat_adm' <?= $oplat_adm_checkbox; ?> data-datatype="checkbox">
								</div>
							</div>
						</div>
						
						<div class='form-row'>
							<div class='col-2 mb-1 text-right' style='vertical-align: center;'>
								<label for='btnAddFileModalWindow' class='text-muted' style='font-size: 13px;'><strong>Квитанция об оплате штрафа</strong></label>
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

				</div>
				
				<div class='card-footer card-header'><?php
					if($role >= 2) {
						echo "<button type='button' class='btn btn-success mr-1' id='btnSaveAdmOffense' title='Сохранить'><span class='fa fa-check'>&nbsp;</span>Сохранить</button>";
						echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownRemoveAdmOffense' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить''><span class='fa fa-remove'>&nbsp;</span>Удалить</button>";
						echo "<div class='dropdown-menu' aria-labelledby='dropdownRemoveAdmOffense'>";
						echo "<button class='dropdown-item' id='btnRemoveAdmOffense'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button>";
						echo "</div>";
					}
				?></div>

				</div>
			</div>
		</div>
</div>