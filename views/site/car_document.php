<?php
	$id = (empty($id)) ? '' : $id;
	$date_car_document = (empty($date_car_document)) ? '' : IcKomiApp\core\Functions::convertToDate($date_car_document);
	$number_car_document = (empty($number_car_document)) ? '' : $number_car_document;
	$type_car_document = (empty($type_car_document)) ? '' : $type_car_document;

	$type_car_document_select = IcKomiApp\widgets\Directory::get_directory(23, $type_car_document);

	print_r($type_car_document_select);


	$role = 9;
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
				<div class="card-body" id="card">

					<div class="col-sm-12 atx-cars-block">
						<div id="mainInformation">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5>1. Сведения о документе прикрепляемом к транспортному средству</h5></p>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="type_car_document" class="text-muted" style="font-size: 13px;"><strong>Тип документа</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="type_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Тип документа" data-datatype="number">
									<?= $type_car_document_select; ?>
									</select>
								</div>
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="date_car_document" class="text-muted" style="font-size: 13px;"><strong>Дата документа</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата документа" data-datatype="date" maxlength="20" placeholder="Дата документа" value="<?= $date_car_document; ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="number_car_document" class="text-muted" style="font-size: 13px;"><strong>Номер документа</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="number_car_document" data-mandatory="true" data-message-error="Заполните обязательное поле: Номер документа" data-datatype="char" placeholder="Номер документа" value="<?= $number_car_document; ?>">
								</div>
							</div>

						</div>
						<div class='form-row'>
							<div class='col-2 mb-1 text-right' style='vertical-align: center;'>
								<label for='btnAddFileModalWindow' class='text-muted' style='font-size: 13px;'><strong>Эл. образы</strong></label>
							</div>
							
							<div class='col-6 mb-1 text-left'>
								<div id='uploadFileContainer'></div>
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
					<button type="button" class="btn btn-success" id="btnSaveCarDocument" title="Сохранить информацию о документе"><span class="fa fa-check">&nbsp;</span>Сохранить</button>
					<button type="button" class="btn btn-danger dropdown-toggle" id="dropdownRemoveCarDocument" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Удалить информацию о документе"><span class="fa fa-remove">&nbsp;</span>Удалить</button>
					<div class="dropdown-menu" aria-labelledby="dropdownRemoveCarDocument">
						<button class="dropdown-item" id="btnRemoveCarDocument"><span class="fa fa-check text-success">&nbsp;</span>Подтверждаю удаление</button>
					</div>
				</div>
				
				
				<div class="card-body" id="cardSections">
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5>2. Список транспортных средств</h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-primary" id="btnShowListLink" data-item="document" title="Добавить услугу"><span class="fa fa-cogs">&nbsp;</span>Редактор связей</button>
								<button type="button" class="btn btn-sm btn-outline-info" id="addCarsLinkDocument" data-item="document" title="Добавить услугу"><span class="fa fa-plus">&nbsp;</span>Добавить ТС</button>
							</div>
						</div>
						<div class="form-row">
							<div class="col col-sm-12 mb-1" id="tableCars">
							</div>
						</div>
					</div>
				</div>
				

				</div>
		
		</div>
	</div>
</div>