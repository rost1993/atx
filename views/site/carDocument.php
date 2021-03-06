<?php
	$id = (empty($id)) ? '' : $id;
	$date_car_document = (empty($date_car_document)) ? '' : IcKomiApp\core\Functions::convertToDate($date_car_document);
	$number_car_document = (empty($number_car_document)) ? '' : $number_car_document;
	$type_car_document = (empty($type_car_document)) ? '' : $type_car_document;
	$type_car_document_select = IcKomiApp\widgets\Directory::get_directory(23, $type_car_document);
	$list_add_car = (empty($list_add_car)) ? '' : $list_add_car;
	$list_files_doc = (empty($list_files_doc)) ? '' : $list_files_doc;

	$role = IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardHeader">
					<h4>Информация о документе</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardHeaderServiceBadge"></div>
				</div>
				<div class="card-body" id="cardDocument">

					<div class="col-sm-12 atx-cars-block">
						<div id="mainInformationCarDocument">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5>1. Сведения о документе прикрепляемом к транспортному средству</h5></p>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="type_car_document" class="text-muted font-weight-bold fs-13">Тип документа</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="type_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Тип документа" data-datatype="number">
									<?= $type_car_document_select; ?>
									</select>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label for="date_car_document" class="text-muted font-weight-bold fs-13">Дата документа</label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата документа" data-datatype="date" maxlength="20" placeholder="Дата документа" value="<?= $date_car_document; ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="number_car_document" class="text-muted font-weight-bold fs-13">Номер документа</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="number_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Номер документа" data-datatype="char" placeholder="Номер документа" value="<?= $number_car_document; ?>">
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
										<input type='file' id='btnAddFileModalWindow' data-show-error='window' accept='.pdf'>
								</span>
							</div>
						</div>

					</div>
				</div>
				
				<div class='card-footer card-header'>
					<?php
					if($role >= 2) {
						echo "<button type='button' class='btn btn-success' id='btnSaveCarDocument' title='Сохранить информацию о документе'><span class='fa fa-check'>&nbsp;</span>Сохранить</button>";
						echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownRemoveCarDocument' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить информацию о документе'><span class='fa fa-remove'>&nbsp;</span>Удалить</button>";
						echo "<div class='dropdown-menu' aria-labelledby='dropdownRemoveCarDocument'>"
							. "<button class='dropdown-item' id='btnRemoveCarDocument'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button>"
						. "</div>";
					}
					?>
				</div>
				
				
				<div class="card-body" id="cardSections">
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5>2. Список транспортных средств</h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<?php
								if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-primary' id='btnShowListLink' data-item='document' title='Добавить услугу'><span class='fa fa-cogs'>&nbsp;</span>Редактор связей</button>";
									echo "<button type='button' class='btn btn-sm btn-outline-info' id='addCarsLinkDocument' data-item='document' title='Добавить услугу'><span class='fa fa-plus'>&nbsp;</span>Добавить ТС</button>";
								}
								?>
							</div>
						</div>
						<div class="form-row">
							<div class="col col-sm-12 mb-1" id="tableCars"><?= $list_add_car; ?></div>
						</div>
					</div>
				</div>
				

				</div>
		
		</div>
	</div>
</div>