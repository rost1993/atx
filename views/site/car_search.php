<?php
	use IcKomiApp\widgets\Directory;

	$array_directory = Directory::get_multiple_directory([3, 4, 5, 9]);

	$marka_select = (empty($array_directory[3])) ? '' : $array_directory[3];
	$model_select = (empty($array_directory[4])) ? '' : $array_directory[4];

	$kateg_ts_select = (empty($array_directory[5])) ? '' : $array_directory[5];
	$kateg_gost_select = (empty($array_directory[9])) ? '' : $array_directory[9];

	$search_result = (empty($search_result)) ? '' : $search_result;
?>

<div class="container-fluid starter-template">
	<div class="row">
	
		<div class="col col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<h4>Подсистема "Транспортные средства"</h4>
				</div>
				
				<div class="card-body search-block">
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-12 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseOne" aria-controls="collapseOne" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down"></span>&nbsp;Реквизиты для поиска</a></h5></p>
							</div>
						</div>
						
						<div class="collapse show" id="collapseOne" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="gos_znak"><strong>Гос. номер</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="gos_znak" data-datatype="char" maxlength="11" placeholder="Гос. рег. номер" autofocus>
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="marka"><strong>Марка</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="marka" data-datatype="number">
										<?= $marka_select; ?>
									</select>
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="model"><strong>Модель</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="model" data-datatype="number">
									<?= $model_select; ?>
									</select>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="god_car"><strong>Год выпуска</strong></label>
								</div>
								<div class="col col-sm-2 mb-1">
									<input type="text" class="form-control form-control-sm black-text datepicker-here" id="god_car" data-datatype="number" data-min-view="years" data-view="years" data-date-format="yyyy" maxlength="10" placeholder="Год выпуска">
								</div>
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="vin"><strong>VIN</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="vin" data-datatype="char" maxlength="100" placeholder="VIN / зав. № машины (рамы)">
								</div>
								<div class="col col-sm-4 mb-1">
									<label class="mr-3" style="font-size: 13px;">
										<input type='radio' name='ibd_arx' value="2" data-datatype="radio" checked>
										<strong>Актуальные</strong>
									</label>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="kateg_ts"><strong>Категория ТС</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="kateg_ts" data-datatype="number">
									<?= $kateg_ts_select; ?>
									</select>
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="kateg_gost"><strong>Категория ГОСТ</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="kateg_gost" data-datatype="number">
									<?= $kateg_gost_select; ?>
									</select>
								</div>
								<div class="col col-sm-4 mb-1">
									<label class="mr-3" style="font-size: 13px;">
										<input type='radio' name='ibd_arx' value="4" data-datatype="radio">
										<strong>Готовится к списанию</strong>
									</label>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="kateg_ts"><strong>Дата окончания ОСАГО</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='end_dt_osago1'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="end_dt_osago1" data-datatype="date" maxlength="10" placeholder="Дата с">
									</div>
								</div>
								<div class="col col-sm-1 mb-1"></div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='end_dt_osago2'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="end_dt_osago2" data-datatype="date" maxlength="10" placeholder="Дата по">
									</div>
								</div>
								<div class="col col-sm-4 mb-1">
									<label class="mr-3" style="font-size: 13px;">
										<input type='radio' name='ibd_arx' value="1" data-datatype="radio">
										<strong>Архив</strong>
									</label>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="kateg_ts"><strong>Дата окончания тех. осмотра</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='end_date_certificate1'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="end_date_certificate1" data-datatype="date" maxlength="10" placeholder="Дата с">
									</div>
								</div>
								<div class="col col-sm-1 mb-1"></div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='end_date_certificate2'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="end_date_certificate2" data-datatype="date" maxlength="10" placeholder="Дата по">
									</div>
								</div>
								<div class="col col-sm-4 mb-1">
									<label class="mr-3" style="font-size: 13px;">
										<input type='radio' name='ibd_arx' value="3" data-datatype="radio">
										<strong>Все</strong>
									</label>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-12 text-center">
									<button type="button" class="btn btn-success btn-search-item mb-2 mt-2 mr-2 btn-search-cars" data-page="1" data-excel="-1" title="Поиск транспортных средств"><span class="fa fa-search">&nbsp;</span>Поиск ТС</button>
									<button type="button" class="btn btn-info mb-2 mt-2 ml-2 btn-search-cars" data-page="1" data-excel="1" title="Поиск транспортных средств"><span class="fa fa-file-excel-o">&nbsp;</span>Выгрузить в Excel</button>
								</div>
							</div>
							
						</div>
						
					</div>
				</div>
				
				<div class="card-body result-list-atx"><?= $search_result; ?></div>
				
			</div>
		</div>

	</div>
</div>
