<?php
	$id = (empty($id)) ? '' : $id;
	$car_mileage = (empty($car_mileage)) ? '' : $car_mileage;
	$org_repair = (empty($org_repair)) ? '' : $org_repair;
	$date_start_repair = (empty($date_start_repair)) ? '' : IcKomiApp\core\Functions::convertToDate($date_start_repair);
	$date_end_repair = (empty($date_end_repair)) ? '' : IcKomiApp\core\Functions::convertToDate($date_end_repair);
	$prim_repair = (empty($prim_repair)) ? '' : $prim_repair;
	$change_oil = (empty($change_oil)) ? '' : $change_oil;
	$price_repair = (empty($price_repair)) ? '' : $price_repair;

	$change_oil = (empty($change_oil)) ? '0' : $change_oil;
	$change_oil_checkbox = ($change_oil == 0) ? '' : ' checked ';

	$org_repair = (empty($org_repair)) ? '' : $org_repair;

	$org_repair_select = IcKomiApp\widgets\Directory::get_directory(18, $org_repair);

	if(!empty($id_car)) {
		$id_car = (empty($id_car)) ? '' : $id_car;
		$car_select = IcKomiApp\widgets\Directory::get_directory_car($id_car);
	} else if(!empty($add_car)) {
		$add_car = (empty($add_car)) ? '' : $add_car;
		$car_select = IcKomiApp\widgets\Directory::get_directory_car($add_car);
	} else {
		$car_select = IcKomiApp\widgets\Directory::get_directory_car();
	}

	$list_files_doc = (empty($list_files_doc)) ? '' : $list_files_doc;

	$role = IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardRepairHeader">
					<h4>Информация о ремонте</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardRepairHeaderServiceBadge"></div>
				</div>
				<div class="card-body" id="cardRepair">

					<div class="col-sm-12 atx-cars-block">
						<div id="mainRepairsInformation">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5>1. ОБЩИЕ СВЕДЕНИЯ О РЕМОНТЕ ТРАНСПОРТНОГО СРЕДСТВА</h5></p>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="id_car" class="text-muted font-weight-bold fs-13">Транспортное срество</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="id_car"  data-mandatory="true" data-message-error="Заполните обязательное поле: Транспортное средство" data-datatype="number">
									<?= $car_select; ?>
									</select>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label for="car_mileage" class="text-muted font-weight-bold fs-13">Пробег</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="car_mileage" data-mandatory="true" data-message-error="Заполните обязательное поле: Пробег" data-datatype="char" maxlength="150" placeholder="Пробег" value="<?= $car_mileage; ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="org_repair" class="text-muted font-weight-bold fs-13">Станция ремонта</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="org_repair" data-mandatory="true" data-message-error="Заполните обязательное поле: Станция где произведен ремонт" data-datatype="number"><?= $org_repair_select; ?></select>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label for="date_start_repair" class="text-muted font-weight-bold fs-13">Дата ремонта</label>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text black-text' for='date_start_repair'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_start_repair" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата начала ремонта" data-datatype="date" maxlength="10" placeholder="Дата ремонта" value="<?= $date_start_repair; ?>">
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text black-text' for='date_end_repair'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_end_repair" data-datatype="date" maxlength="10" placeholder="Дата ремонта" value="<?= $date_end_repair; ?>">
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="prim_repair" class="text-muted font-weight-bold fs-13">Примечание</label>
								</div>
								<div class="col col-sm-9 mb-1">
									<textarea type="text" class="form-control form-control-sm black-text" id="prim_repair" maxlength="4000" rows="3" data-datatype="char" placeholder="Примечание"><?= $prim_repair; ?></textarea>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="price_repair" class="text-muted font-weight-bold fs-13">Стоимость ремонта</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="price_repair" data-mandatory="true" data-message-error="Заполните обязательное поле: Стоимость ремонта" data-datatype="number" placeholder="Стоимость ремонта" value="<?= $price_repair; ?>">
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label for="change_oil" class="text-muted font-weight-bold fs-13">Замена масла в ДВС</label>
								</div>
								<div class="col col-sm-3 mb-1 text-left">
									<input type='checkbox' id='change_oil' name='change_oil' <?= $change_oil_checkbox; ?> data-datatype="checkbox">
								</div>
							</div>
						</div>
						<div class='form-row'>
							<div class='col-2 mb-1 text-right'>
								<label for='btnAddFileModalWindow' class='text-muted font-weight-bold fs-13'>Эл. образы</label>
							</div>
							
							<div class='col-6 mb-1 text-left'>
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
						echo "<button type='button' class='btn btn-success mr-1' id='btnSaveRepair' title='Сохранить информацию о ремонте'><span class='fa fa-check'></span>&nbsp;Сохранить</button>";
						echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownRemoveRepair' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить информацию о ремонте''><span class='fa fa-remove'></span>&nbsp;Удалить</button>";
						echo "<div class='dropdown-menu' aria-labelledby='dropdownRemoveRepair'>";
							echo "<button class='dropdown-item' id='btnRemoveRepair'><span class='fa fa-check text-success'></span>&nbsp;Подтверждаю удаление</button>";
						echo "</div>";
					}
				?></div>
				</div>
			</div>
		</div>
	</div>
</div>