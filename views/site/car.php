<?php
	$id = (empty($id)) ? '' : $id;
	$gos_znak = (empty($gos_znak)) ? '' : $gos_znak;
	$n_reg = (empty($n_reg)) ? '' : $n_reg;
	$god_car = (empty($god_car)) ? '' : $god_car;
	$vin = (empty($vin)) ? '' : $vin;
	$shassi = (empty($shassi)) ? '' : $shassi;
	$mass_max = (empty($mass_max)) ? '' : $mass_max;
	$car_vat = (empty($car_vat)) ? '' : $car_vat;
	$car_v = (empty($car_v)) ? '' : $car_v;
	$mileage_oil = (empty($mileage_oil)) ? '' : $mileage_oil;
	$n_dvig = (empty($n_dvig)) ? '' : $n_dvig;
	$kuzov = (empty($kuzov)) ? '' : $kuzov;
	$mass_min = (empty($mass_min)) ? '' : $mass_min;
	$basic_fuel = (empty($basic_fuel)) ? '' : $basic_fuel;
	$summer_fuel = (empty($summer_fuel)) ? '' : $summer_fuel;
	$winter_fuel = (empty($winter_fuel)) ? '' : $winter_fuel;
	$inventory_n = (empty($inventory_n)) ? '' : $inventory_n;
	$balance_price = (empty($balance_price)) ? '' : $balance_price;
	$prim = (empty($prim)) ? '' : $prim;

	$marka = (empty($marka)) ? '' : $marka;
	$model = (empty($model)) ? '' : $model;
	$color = (empty($color)) ? '' : $color;
	$kateg_ts = (empty($kateg_ts)) ? '' : $kateg_ts;
	$tip_strah = (empty($tip_strah)) ? '' : $tip_strah;
	$kateg_gost = (empty($kateg_gost)) ? '' : $kateg_gost;
	$owner_car = (empty($owner_car)) ? '' : $owner_car;

	$array_directory = IcKomiApp\widgets\Directory::get_multiple_directory([3, 4, 12, 5, 7, 9, 40], ['3' => $marka, '4' => $model, '12' => $color, '5' => $kateg_ts, '7' => $tip_strah, '9' => $kateg_gost, '40' => $owner_car]);

	$marka_select = (empty($array_directory[3])) ? '' : $array_directory[3];
	$model_select = (empty($array_directory[4])) ? '' : $array_directory[4];
	$color_select = (empty($array_directory[12])) ? '' : $array_directory[12];
	$owner_car_select = (empty($array_directory[40])) ? '' : $array_directory[40];

	$kateg_ts_select = (empty($array_directory[5])) ? '' : $array_directory[5];
	$tip_strah_select = (empty($array_directory[7])) ? '' : $array_directory[7];
	$kateg_gost_select = (empty($array_directory[9])) ? '' : $array_directory[9];

	$s_certificate_reg = (empty($s_certificate_reg)) ? '' : $s_certificate_reg;
	$n_certificate_reg = (empty($n_certificate_reg)) ? '' : $n_certificate_reg;
	$date_certificate_reg = (empty($date_certificate_reg)) ? '' : $date_certificate_reg;
	$org_certificate_reg = (empty($org_certificate_reg)) ? '' : $org_certificate_reg;
	$comment_certificate_reg = (empty($comment_certificate_reg)) ? '' : $comment_certificate_reg;
	$s_pts = (empty($s_pts)) ? '' : $s_pts;
	$n_pts = (empty($n_pts)) ? '' : $n_pts;
	$date_pts = (empty($date_pts)) ? '' : $date_pts;
	$type_ts_pts = (empty($type_ts_pts)) ? '' : $type_ts_pts;
	$firma_pts = (empty($firma_pts)) ? '' : $firma_pts;
	$n_osago = (empty($n_osago)) ? '' : $n_osago;
	$end_dt_osago = (empty($end_dt_osago)) ? '' : $end_dt_osago;
	$firma_osago = (empty($firma_osago)) ? '' : $firma_osago;
	$number_certificate = (empty($number_certificate)) ? '' : $number_certificate;
	$date_certificate = (empty($date_certificate)) ? '' : $date_certificate;
	$end_date_certificate = (empty($end_date_certificate)) ? '' : $end_date_certificate;
	$firma_technical_inspection = (empty($firma_technical_inspection)) ? '' : $firma_technical_inspection;
	$address_technical_inspection = (empty($address_technical_inspection)) ? '' : $address_technical_inspection;
	$mileage = (empty($mileage)) ? '' : $mileage;

	$issued_date_fire_extinguisher = (empty($issued_date_fire_extinguisher)) ? '' : $issued_date_fire_extinguisher;
	$start_date_fire_extinguisher = (empty($start_date_fire_extinguisher)) ? '' : $start_date_fire_extinguisher;
	$end_date_fire_extinguisher = (empty($end_date_fire_extinguisher)) ? '' : $end_date_fire_extinguisher;
	$issued_date_first_aid_kid = (empty($issued_date_first_aid_kid)) ? '' : $issued_date_first_aid_kid;
	$start_date_first_aid_kid = (empty($start_date_first_aid_kid)) ? '' : $start_date_first_aid_kid;
	$end_date_first_aid_kid = (empty($end_date_first_aid_kid)) ? '' : $end_date_first_aid_kid;
	$issued_date_warning_triangle = (empty($issued_date_warning_triangle)) ? '' : $issued_date_warning_triangle;
	$start_date_car_battery = (empty($start_date_car_battery)) ? '' : $start_date_car_battery;
	$type_battery = (empty($type_battery)) ? '' : $type_battery;
	$firma_battery = (empty($firma_battery)) ? '' : $firma_battery;

	$list_driver = (empty($list_driver)) ? '' : $list_driver;
	$list_repair = (empty($list_repair)) ? '' : $list_repair;
	$list_car_doc = (empty($list_car_doc)) ? '' : $list_car_doc;
	$list_images = (empty($list_images)) ? '' : $list_images;
	$list_dtp = (empty($list_dtp)) ? '' : $list_dtp;
	$list_adm = (empty($list_adm)) ? '' : $list_adm;
	$list_old_gos_znak = (empty($list_old_gos_znak)) ? '' : $list_old_gos_znak;
	$list_wheels = (empty($list_wheels)) ? '' : $list_wheels;

	$number_dopog = (empty($number_dopog)) ? '' : $number_dopog;
	$date_start_dopog = (empty($date_start_dopog)) ? '' : $date_start_dopog;
	$date_end_dopog = (empty($date_end_dopog)) ? '' : $date_end_dopog;
	$firma_dopog_text = (empty($firma_dopog_text)) ? '' : $firma_dopog_text;

	$file_pts = (empty($file_pts)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_pts, $ext_file_pts);
	$file_osago = (empty($file_osago)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_osago, $ext_file_osago);
	$file_cert_reg = (empty($file_cert_reg)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_cert_reg, $ext_file_cert_reg);
	$file_tech_inspection = (empty($file_tech_inspection)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_tech_inspection, $ext_file_tech_inspection);
	$file_dopog = (empty($file_dopog)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_dopog, $ext_file_dopog);

	$date_calibration = (empty($date_calibration)) ? '' : $date_calibration;
	$date_next_calibration = (empty($date_next_calibration)) ? '' : $date_next_calibration;
	$firma_calibration_text = (empty($firma_calibration_text)) ? '' : $firma_calibration_text;
	$file_calibration = (empty($file_calibration)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_calibration, $ext_file_calibration);

	$number_tachograph = (empty($number_tachograph)) ? '' : $number_tachograph;
	$model_tachograph_text = (empty($model_tachograph_text)) ? '' : $model_tachograph_text;
	$date_start_skzi = (empty($date_start_skzi)) ? '' : $date_start_skzi;
	$date_end_skzi = (empty($date_end_skzi)) ? '' : $date_end_skzi;
	$file_tachograph = (empty($file_tachograph)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_tachograph, $ext_file_tachograph);

	$number_dvr = (empty($number_dvr)) ? '' : $number_dvr;
	$marka_dvr = (empty($marka_dvr)) ? '' : $marka_dvr;
	$model_dvr = (empty($model_dvr)) ? '' : $model_dvr;

	$number_glonass = (empty($number_glonass)) ? '' : $number_glonass;
	$date_glonass = (empty($date_glonass)) ? '' : $date_glonass;
	$number_dut_glonass_1 = (empty($number_dut_glonass_1)) ? '' : $number_dut_glonass_1;
	$number_dut_glonass_2 = (empty($number_dut_glonass_2)) ? '' : $number_dut_glonass_2;
	$file_glonass = (empty($file_glonass)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_glonass, $ext_file_glonass);

	$date_maintenance = (empty($date_maintenance)) ? '' : $date_maintenance;
	$mileage_maintenance = (empty($mileage_maintenance)) ? '' : $mileage_maintenance;
	$file_maintenance = (empty($file_maintenance)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_maintenance, $ext_file_maintenance);

	$ibd_arx = (empty($ibd_arx)) ? 1 : $ibd_arx;
	$text_btn_archive = $badge_archive = '';
	if($ibd_arx == 1) {
		$text_btn_archive = 'Перевести в архив';
		$badge_archive = '';
	} else if($ibd_arx == 2) {
		$text_btn_archive = 'Восстановить из архива';
		$badge_archive = "<span class='badge badge-pill badge-warning' id='badgeTSArchive'><span class='fa fa-folder'>&nbsp;</span>В архиве</span>";
	}

	$dostup = (empty($dostup)) ? 1 : $dostup;
	$text_btn_dostup = $span_btn_dostup = $badge_dostup = '';
	if($dostup == 1) {
		$text_btn_dostup = 'Защитить ТС';
		$span_btn_dostup = 'fa fa-lock';
		$badge_dostup = '';
	} else if($dostup == 2) {
		$text_btn_dostup = 'Снять защиту с ТС';
		$span_btn_dostup = 'fa fa-unlock';
		$badge_dostup = "<span class='badge badge-pill badge-danger' id='badgeTSSecurity'><span class='fa fa-lock'></span>&nbsp;Доступ к транспортному средству ограничен</span>";
	}

	$exception_notice_events = (empty($exception_notice_events)) ? 0 : $exception_notice_events;
	$text_btn_exc = $span_btn_exc = $badge_exc = '';
	if($exception_notice_events == 0) {
		$text_btn_exc = 'Откл. уведомления';
		$span_btn_exc = 'fa fa-bell-slash';
		$badge_exc = '';
	} else {
		$text_btn_exc = 'Вкл. уведомления';
		$span_btn_exc = 'fa fa-bell';
		$badge_exc = "<span class='badge badge-pill badge-danger' id='badgeTSNoticeEvents'><span class='fa fa-bell'>&nbsp;</span>Уведомления отключены</span>";
	}

	$write_off = (empty($write_off)) ? 0 : $write_off;
	$text_btn_write_off = $badge_write_off = '';
	if($write_off == 0) {
		$text_btn_write_off = 'Готовится к списанию';
		$badge_write_off = '';
	} else {
		$text_btn_write_off = 'Вернуть в строй';
		$badge_write_off = "<div class='alert alert-yellow text-center'><b>Транспортное средство готовится к списанию!<br>Транспортное средство не подлежит страхованию и проведению тех. осмотра, не выдавать новые запасные части и другие товарно-материальные ценности!</b></div>";
	}

	$role = (int)IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardCarsHeader">
					<h4>Информация о транспортном средстве</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardCarsHeaderServiceBadge"><?= $badge_archive; ?><?= $badge_dostup; ?><?= $badge_exc; ?></div>
				</div>
				<div class="card-body" id="cardCars">
					<div class='car-notice'><?= $badge_write_off; ?></div>

					<?php
						if($role >= 2) {

						echo "<div class='col atx-cars-block'>"
						. "<div class='form-row'>"
							. "<div class='col mb-1 text-left'>"
									. "<p style='margin: 0px;'><h6><a class='black-text-atx show-block' href='#collapseSetting' aria-controls='collapseSetting' data-toggle='collapse' title='Скрыть/раскрыть блок'><span class='fa fa-caret-down'>&nbsp;</span>Дополнительные операции с карточкой</a></h6></p>"
							. "</div>"
						. "</div>"

						."<div class='collapse show' id='collapseSetting' aria-labelledby='headingOne'>"
							. "<div class='form-row'>"
								. "<div class='col mb-1 text-left'>"
									. "<button class='btn btn-sm btn-info' id='btnEnableNoticeEvents' title='Включить/отключить уведомления'><span class='" . $span_btn_exc . "'>&nbsp;</span>" . $text_btn_exc . "</button>"
									. "<button class='btn btn-sm btn-danger ml-2' id='btnCarWriteOff' title='ТС готовится к списанию' data-operation='disable'><span class='fa fa-trash'>&nbsp;</span>" . $text_btn_write_off . "</button>"
								. "</div>"
							. "</div>"
						. "</div>"
						. "</div>";
					}
					?>

					<div id="mainCarsInformation">
					
						<div class="col-sm-12 atx-cars-block">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseOne" aria-controls="collapseOne" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>1. ОБЩИЕ СВЕДЕНИЯ О ТРАНСПОРТНОМ СРЕДСТВЕ</a></h5></p>
								</div>
							</div>
							
							<div class="collapse show" id="collapseOne" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label for="marka" class="text-muted font-weight-bold fs-13">Марка</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="marka" data-mandatory="true" data-message-error="Заполните обязательное поле: Марка" data-datatype="number">
										<?= $marka_select; ?>
										</select>
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label for="model" class="text-muted font-weight-bold fs-13">Модель</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="model" data-mandatory="true" data-message-error="Заполните обязательное поле: Модель" data-datatype="number">
										<?= $model_select; ?>
										</select>
									</div>
								</div>
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="gos_znak">Гос. знак</label>
									</div>
									<div class="col col-sm-2 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="gos_znak" data-mandatory="true" data-message-error="Заполните обязательное поле: Гос. знак" maxlength="11" placeholder="Гос. регистр. знак" data-datatype="char" value="<?= $gos_znak; ?>">
									</div>
									<div class="col col-sm-1 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="n_reg" maxlength="3" placeholder="Регион" data-datatype="number" value="<?= $n_reg; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="color">Цвет</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="color" data-datatype="number">
										<?= $color_select; ?>
										</select>
									</div>
								</div>
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="god_car">Год выпуска</label>
									</div>
									<div class="col col-sm-1 mb-1">
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="god_car" data-min-view="years" data-view="years" data-date-format="yyyy" data-mandatory="true" data-message-error="Заполните обязательное поле: Год выпуска" maxlength="4" placeholder="Год" data-datatype="number" value="<?= $god_car; ?>">
									</div>
									<div class="col col-sm-2 mb-1 cars-old-gos-znak"><?= $list_old_gos_znak; ?></div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="owner_car">Собственник</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="owner_car" data-datatype="number">
										<?= $owner_car_select; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					
						<div class="col-sm-12 atx-cars-block">
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwo" aria-controls="collapseTwo" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>2. КАТЕГОРИИ ТРАНСПОРТНОГО СРЕДСТВА</a></h5></p>
								</div>
							</div>
							
							<div class="collapse show" id="collapseTwo" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="kateg_ts">Категория ТС</label>
									</div>
									<div class="col col-sm-3 mb-1 text-right">
										<select class="custom-select custom-select-sm black-text" id="kateg_ts" data-datatype="number">
										<?= $kateg_ts_select; ?>
										</select>
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="tip_strah">Тип для страховой</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="tip_strah" data-datatype="number">
										<?= $tip_strah_select; ?>
										</select>
									</div>
								</div>
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label for="kateg_gost" class="text-muted font-weight-bold fs-13">Категория ГОСТ</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<select class="custom-select custom-select-sm black-text" id="kateg_gost" data-datatype="number">
										<?= $kateg_gost_select; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					
						<div class="col-sm-12 atx-cars-block">	
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseThree" aria-controls="collapseThree" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>3. ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ ТРАНСПОРТНОГО СРЕДСТВА</a></h5></p>
								</div>
							</div>
							
							<div class="collapse show" id="collapseThree" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="vin">VIN / зав. № машины (рамы)</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="vin" maxlength="100" placeholder="VIN / зав. № машины (рамы)" data-datatype="char" value="<?= $vin; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="n_dvig">Двигатель</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="n_dvig" maxlength="100" placeholder="Двигатель" data-datatype="char" value="<?= $n_dvig; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="shassi">Шасси / коробка передач</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="shassi" maxlength="100" placeholder="Шасси / коробка передач" data-datatype="char" value="<?= $shassi; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="kuzov">Кузов / осн. ведущий мост (мосты)</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="kuzov" maxlength="100" placeholder="Кузов / осн. ведущий мост (мосты)" data-datatype="char" value="<?= $kuzov; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="mass_max">Разр. макс. масса</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="mass_max" maxlength="10" placeholder="Макс. масса" data-datatype="number" value="<?= $mass_max; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="mass_min">Масса без нагрузки</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="mass_min" maxlength="100" placeholder="Мин. масса" data-datatype="number" value="<?= $mass_min; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="car_vat">Мощность л.с</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="car_vat" maxlength="10" placeholder="Мощность" data-datatype="number" value="<?= $car_vat; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="car_v">Раб. объем двигателя куб.см</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="car_v" maxlength="10" placeholder="Объем" data-datatype="number" value="<?= $car_v; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="mileage_oil">ТО по замене масла, км</label>
									</div>
									<div class="col col-sm-2 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="mileage_oil" maxlength="10" placeholder="" data-datatype="number" value="<?= $mileage_oil; ?>">
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12 atx-cars-block">	
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFour" aria-controls="collapseFour" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>4. НОРМЫ РАСХОДА ТОПЛИВА</a></h5></p>
								</div>
							</div>
							<div class="collapse show" id="collapseFour" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="basic_fuel">Базовая норма</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="basic_fuel" maxlength="10" placeholder="Базовая норма" data-datatype="number" value="<?= $basic_fuel; ?>">
									</div>
								</div>
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="summer_fuel">Эксплуатационная летняя норма</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="summer_fuel" maxlength="10" placeholder="Летняя норма" data-datatype="number" value="<?= $summer_fuel; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="winter_fuel">Эксплуатационная зимняя норма</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="winter_fuel" maxlength="10" placeholder="Зимняя норма" data-datatype="number" value="<?= $winter_fuel; ?>">
									</div>
								</div>
							</div>
						</div>
					
						<div class="col-sm-12 atx-cars-block">	
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFive" aria-controls="collapseFive" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>5. ПРОЧИЕ СВЕДЕНИЯ</a></h5></p>
								</div>
							</div>
							<div class="collapse show" id="collapseFive" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="inventory_n">Инвентарный номер</label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="inventory_n" maxlength="50" placeholder="Инвентарный номер" data-datatype="char" value="<?= $inventory_n; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="balance_price">Первоначальная (балансовая) стоимость</label>
									</div>
									<div class="col col-sm-8 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="balance_price" maxlength="15" placeholder="Первоначальная (балансовая) стоимость" data-datatype="number" value="<?= $balance_price; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right">
										<label class="text-muted font-weight-bold fs-13" for="prim">Примечание</label>
									</div>
									<div class="col col-sm-8 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="prim" maxlength="3000" placeholder="Примечание" data-datatype="char" value="<?= $prim; ?>">
									</div>
								</div>
							</div>
						</div>
					</div>
						
					<div class="col-sm-12 atx-cars-block" id="CarBlockImages">	
						<div class="form-row">
							<div class="col col-sm-2 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSix" aria-controls="collapseSix" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>6. ФОТОГРАФИИ</a></h5></p>
							</div>
							<div class="col col-sm-8 mb-1 text-left">
								<span class='btn btn-sm btn-outline-primary fileinput-button'>
									<span class='fa fa-folder-open'>&nbsp;</span>Загрузить фотографии
										<input type='file' id='btnAddFileModalWindow' data-show-error='window' accept='.jpeg,.png,.jpg' data-upload-container='uploadFileContainerCarsImages' multiple>
								</span>
							</div>
						</div>
						<div class="collapse show" id="collapseSix" aria-labelledby="headingOne">
							<div class='form-row'>
								<div class="col col-sm-2 mb-1 text-right"></div>
								<div class='col-8 mb-1 text-left'>
									<div id='uploadFileContainerCarsImages'><?= $list_images; ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
					
									
				<div class='card-footer card-header'>
					<div class="test"></div>

					<?php
						if($role >= 2) {
							echo "<button type='button' class='btn btn-success mr-1' id='saveInfoForCars' title='Сохранить информацию о транспортном средстве'><span class='fa fa-check'>&nbsp;</span>Сохранить ТС</button>";

							if($role > 2)
								echo "<button type='button' class='btn btn-primary mr-1' id='lockCars' title='Изменить уровень видимости транспортного средства'><span class='fa fa-lock'>&nbsp;</span>" . $text_btn_dostup . "</button>";

							echo "<button type='button' class='btn btn-warning mr-1' id='btnMoveArchive' title='Перевести в архив/восстановить из архива' data-type='1'><span class='fa fa-folder'>&nbsp;</span>" . $text_btn_archive . "</button>";

							echo "<button class='btn btn-info mr-1' id='btnGeneratePdf' title='Сформировать справку'><span class='fa fa-file-pdf-o'>&nbsp;</span>Справка</button>";

							echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownDeleteCars' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить транспортное средство'><span class='fa fa-remove'>&nbsp;</span>Удалить ТС</button>
							<div class='dropdown-menu' aria-labelledby='dropdownDeleteCars'>
							<button class='dropdown-item' id='deleteCars'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button>
							</div>";
						}
					?></div>

				<div class='card-header' id="cardCarsHeader">
					<h4>Дополнительные сведения</h4>
				</div>
				
				<div class="card-body" id="cardCars">
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSeven" aria-controls="collapseSeven" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>7. ДОКУМЕНТЫ НА ТС</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-primary" id="btnShowListLink" data-item="car" title="Развернуть список всех свидетельств о регистрации"><span class="fa fa-cogs">&nbsp;</span>Редактор связей</button>
								<?php
								if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info mr-1' id='addCarsLinkDocument' data-item='car' title='Добавить свидетельство о регистрации'><span class='fa fa-plus'>&nbsp;</span>Добавить документ</button>";
									echo "<a href='car_document' role='button' target='_blank' class='btn btn-sm btn-outline-info' title='Добавить свидетельство о регистрации'><span class='fa fa-plus'>&nbsp;</span>Создать документ</a>";
							}?></div>
						</div>
						
						<div class="collapse show" id="collapseSeven" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-document-for-car"><?= $list_car_doc; ?></div>
							</div>
						</div>
					</div>
				
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseEight" aria-controls="collapseEight" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>8. СВЕДЕНИЯ О РЕГИСТРАЦИИ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonSix'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='6' title="Развернуть список всех свидетельств о регистрации"><span class="fa fa-search">&nbsp;</span>История регистрации</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='6' title='Добавить свидетельство о регистрации'><span class='fa fa-plus'>&nbsp;</span>Добавить свидетельство</button>" : ""; ?>
								<?= $file_cert_reg; ?>
							</div>
						</div>
						
						<div class="collapse show" id="collapseEight" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13" for="s_certificate_reg">Серия свидетельства</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="s_certificate_reg" placeholder="Серия" value="<?= $s_certificate_reg; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13" for="n_certificate_reg">Номер свидетельства</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="n_certificate_reg" placeholder="Номер" value="<?= $n_certificate_reg; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13" for="date_certificate_reg">Дата выдачи</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="date_certificate_reg" placeholder="Дата выдачи" value="<?= $date_certificate_reg; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13" for="org_certificate_reg">Кем выдано</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="org_certificate_reg" placeholder="Кем выдано" value="<?= $org_certificate_reg; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13" for="comment_certificate_reg">Примечание</label>
								</div>
								<div class="col col-sm-8 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="comment_certificate_reg" placeholder="Примечание" value="<?= $comment_certificate_reg; ?>" disabled>
								</div>
							</div>
						</div>
					</div>
				
				
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseNine" aria-controls="collapseNine" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>9. СВЕДЕНИЯ О ПТС</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonSeven'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='5' title="Развернуть список всех ПТС"><span class="fa fa-search">&nbsp</span>История ПТС</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='5' title='Добавить полис ПТС'><span class='fa fa-plus'>&nbsp</span>Добавить ПТС</button>" : ""; ?>
								<?= $file_pts; ?>
							</div>
						</div>
						
						<div class="collapse show" id="collapseNine" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Серия ПТС</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="s_pts" placeholder="Серия" value="<?= $s_pts; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Номер ПТС</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="n_pts" placeholder="Номер" value="<?= $n_pts; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата выдачи</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="date_pts" placeholder="Дата выдачи ПТС" value="<?= $date_pts; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Тип ТС по ПТС</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="type_ts_pts" placeholder="Тип ТС по ПТС" value="<?= $type_ts_pts; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Орган, выдавший ПТС</label>
								</div>
								<div class="col col-sm-8 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="firma_pts" placeholder="Орган, выдавший ПТС" value="<?= $firma_pts; ?>" disabled>
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTen" aria-controls="collapseTen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>10. ПОЛИС ОСАГО</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonEight'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='2' title="Развернуть список всех полисов ОСАГО"><span class="fa fa-search"></span>&nbspИстория ОСАГО</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='2' title='Добавить полис ОСАГО'><span class='fa fa-plus'></span>&nbspДобавить ОСАГО</button>" : ""; ?>
								<?= $file_osago; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseTen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Серия и номер</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $n_osago; ?>" disabled>
								</div>
								
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата окончания полиса</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $end_dt_osago; ?>" disabled>
									</div>
								</div>
								
								
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Страховая компания</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $firma_osago; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseEleven" aria-controls="collapseEleven" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>11. ТЕХНИЧЕСКИЙ ОСМОТР</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='3' title="Развернуть список всех технических осмотров"><span class="fa fa-search"></span>&nbspИстория Техосмотров</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='3' title='Добавить полис технический осмотр'><span class='fa fa-plus'></span>&nbspДобавить Техосмотр</button>" : "";?>
								<?= $file_tech_inspection; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseEleven" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Номер сертификата</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="number_certificate" value="<?= $number_certificate; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Срок действия</label>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $date_certificate; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $end_date_certificate; ?>" disabled>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Организация</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="firma_technical_inspection" value="<?= $firma_technical_inspection; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Адрес прохождения</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="address_technical_inspection" value="<?= $address_technical_inspection; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwelve" aria-controls="collapseTwelve" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>12. СВИДЕТЕЛЬСТВО ДОПОГ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='16' title="Список всех ДОПОГ"><span class="fa fa-search"></span>&nbsp;История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='16' title='Добавить свидетельство ДОПОГ'><span class='fa fa-plus'></span>&nbsp;Добавить</button>" : "";?>
								<?= $file_dopog; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseTwelve" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Номер свидетельства</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $number_dopog; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Срок действия</label>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $date_start_dopog; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $date_end_dopog; ?>" disabled>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Организация</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $firma_dopog_text; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseThirteen" aria-controls="collapseThirteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>13. КАЛИБРОВКА (ЭКСПЕРТИЗА)</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='17' title="Список всех экспертиз"><span class="fa fa-search"></span>&nbsp;История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='17' title='Добавить экспертизу'><span class='fa fa-plus'></span>&nbsp;Добавить</button>" : "";?>
								<?= $file_calibration; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseThirteen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата клибровки</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $date_calibration; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата следующей килбровки</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $date_next_calibration; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Организация</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $firma_calibration_text; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFourteen" aria-controls="collapseFourteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>14. ТАХОГРАФ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='19' title="Список всех экспертиз"><span class="fa fa-search"></span>&nbsp;История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='19' title='Добавить экспертизу'><span class='fa fa-plus'></span>&nbsp;Добавить</button>" : "";?>
								<?= $file_tachograph; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseFourteen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Номер</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $number_tachograph; ?>" disabled>
								</div>
								<div class="col col-sm-1 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Модель</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $model_tachograph_text; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Срок действия блока СКЗИ</label>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $date_start_skzi; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $date_end_skzi; ?>" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFiveteen" aria-controls="collapseFiveteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>15. ГЛОНАСС</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='20' title="Список всех ГЛОНАСС"><span class="fa fa-search"></span>&nbsp;История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='20' title='Добавить ГЛОНАСС'><span class='fa fa-plus'></span>&nbsp;Добавить</button>" : "";?>
								<?= $file_glonass; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseFiveteen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Номер модулятора</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $number_glonass; ?>" disabled>
								</div>
								<div class="col col-sm-1 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата установки</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $date_glonass; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Датчик уровня топлива 1</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $number_dut_glonass_1; ?>" disabled>
								</div>
							</div>
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Датчик уровня топлива 2</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $number_dut_glonass_2; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSixteen" aria-controls="collapseSixteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>16. ТЕХНИЧЕСКОЕ ОБСЛУЖИВАНИЕ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonNine'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='21' title="Список технических обслуживаний"><span class="fa fa-search"></span>&nbsp;История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='21' title='Добавить техническое обслуживание'><span class='fa fa-plus'></span>&nbsp;Добавить</button>" : "";?>
								<?= $file_maintenance; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseSixteen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Дата</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $date_maintenance; ?>" disabled>
								</div>
								<div class="col col-sm-1 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Пробег</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" value="<?= $mileage_maintenance; ?>" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSeventeen" aria-controls="collapseSeventeen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>17. ПРОБЕГ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='4' title="Развернуть список всех показаний спидометра"><span class="fa fa-search">&nbsp;</span>История спидометра</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='4' title='Передать показания спидометра'><span class='fa fa-plus'>&nbsp;</span>Показания спидометра</button>" : ""; ?>
								
								<?php
									if($role >= 2) {
									echo "<div style='display: inline-block;'><button type='button' class='btn btn-sm btn-outline-primary' id='dropdownChangeSpeedometer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Добавить новый спидометр'><span class='fa fa-cogs'>&nbsp;</span>Замена спидометра</button>"
											. "<div class='dropdown-menu' aria-labelledby='dropdownChangeSpeedometer'>"
												. "<button class='dropdown-item' id='btnChangeSpeedometer'><span class='fa fa-check text-success'>&nbsp;</span>Добавить новый спидометр</button>"
									. "</div></div>";
									}
									if($role >= 2) {
									echo "<div style='display: inline-block;'><button type='button' class='btn btn-sm btn-outline-danger' id='dropdownRemoveSpeedometer' style='margin-left: 4px;' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить спидометр'><span class='fa fa-trash'>&nbsp;</span>Удаление спидометра</button>"
											. "<div class='dropdown-menu' aria-labelledby='dropdownRemoveSpeedometer'>"
												. "<button class='dropdown-item' id='btnRemoveSpeedometer'><span class='fa fa-check text-success'>&nbsp;</span>Удалить спидометр</button>"
									. "</div></div>";
									echo "<div style='display: inline-block;'><button type='button' class='btn btn-sm btn-outline-primary' id='btnSpeedometers' style='margin-left: 4px;' title='Начальные показания спидометра'><span class='fa fa-cog'>&nbsp;</span>Спидометры</button>"
									. "</div>";
									}
									?>
							</div>
						</div>
						<div class="collapse show" id="collapseSeventeen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted font-weight-bold fs-13">Пробег, км</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="mileage" value="<?= $mileage; ?>" disabled>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseEighteen" aria-controls="collapseEighteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>18. ВОДИТЕЛИ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success" id="btnShowListFixDriverForCar" data-mode-show="2" title="Открыть историю закреплений"><span class="fa fa-search"></span>&nbsp;История закреплений</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info' id='btnFixDriverForCar' data-mode-show='2' data-operation='1' title='Закрепить нового водителя за транспортным средством'><span class='fa fa-plus'></span>&nbspДобавить водителя</button>" : "";?>
							</div>
						</div>
						<div class="collapse show" id="collapseEighteen" aria-labelledby="headingOne">
							<div class="form-row" id="listDrivers">
								<div class="col col-sm-12 mb-1" id="list-drivers-for-car"><?= $list_driver; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseNineteen" aria-controls="collapseNineteen" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>19. РЕМОНТ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='7' title="Открыть список всех ремонтов"><span class="fa fa-search">&nbsp;</span>Список ремонтов</button>

								<?php
								if($role >= 2)
									echo "<a class='btn btn-sm btn-outline-info' href='repair?add_car=" . $id . "' target='_blank' title='Добавить ремонт к транспортному средству'><span class='fa fa-plus'>&nbsp;</span>Добавить ремонт</a>";
								?>
							</div>
						</div>
						<div class="collapse show" id="collapseNineteen" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-repairs-for-car"><?= $list_repair; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwenty" aria-controls="collapseTwenty" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>20. ДТП</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='9' data-type='1' title="Открыть список всех ДТП"><span class="fa fa-search">&nbsp;</span>Список ДТП</button>

								<?php
								if($role >= 2)
								echo "<a class='btn btn-sm btn-outline-info' href='dtp?add_car=" . $id . "' target='_blank' title='Добавить ДТП к транспортному средству'><span class='fa fa-plus'>&nbsp;</span>Добавить ДТП</a>";
								?>
							</div>
						</div>
						<div class="collapse show" id="collapseTwenty" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-dtp-for-car"><?= $list_dtp; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwentyone" aria-controls="collapseTwentyone" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp</span>21. АДМИНИСТРАТИВНЫЕ ПРАВОНАРУШЕНИЯ</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='11' data-type='1' title=""><span class="fa fa-search">&nbsp;</span>Список адм. правонарушений</button>

								<?php
								if($role >= 2)
								echo "<a class='btn btn-sm btn-outline-info' href='adm?add_car=" . $id . "' target='_blank' title='Добавить адм. правонарушение'><span class='fa fa-plus'>&nbsp;</span>Добавить адм. правонарушение</a>";
								?>
							</div>
						</div>
						<div class="collapse show" id="collapseTwentyone" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-adm"><?= $list_adm; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwentytwo" aria-controls="collapseTwentytwo" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>22. ТОВАРНО-МАТЕРИАЛЬНЫЕ ЦЕННОСТИ</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left">
							</div>
						</div>
						<div class="collapse show" id="collapseTwentytwo" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-2 text-right">
									<label class="blockquote font-weight-bold fs-13">Огнетушитель</label>
								</div>
								<div class="col col-sm-4">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">выдан&nbsp;&nbsp;</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="issued_date_fire_extinguisher" value="<?= $issued_date_fire_extinguisher; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-6 mb-1 text-left">
									<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="12" data-object="car_fire_extinguisher" data-title-form="Огнетушитель" title="Отобразить список огнетушителей"><span class="fa fa-search">&nbsp;</span>Список</button>
									<?php
									if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem mr-1' title='Добавить огнетушитель' data-item='12' data-object='car_fire_extinguisher' data-title-form='Огнетушитель'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>";
									}
									?>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right"></div>
								<div class="col col-sm-2 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">годен с</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="start_date_fire_extinguisher" date-format="date" value="<?= $start_date_fire_extinguisher; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">по</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="end_date_fire_extinguisher" value="<?= $end_date_fire_extinguisher; ?>" disabled>
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 text-right">
									<label class="blockquote font-weight-bold fs-13">Аптечка</label>
								</div>
								<div class="col col-sm-4">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">выдан&nbsp;&nbsp;</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="issued_date_first_aid_kid" value="<?= $issued_date_first_aid_kid; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-6 text-left">
									<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="12" title="Отобразить список аптечек" data-object="car_first_aid_kid" data-title-form="Аптечка"><span class="fa fa-search">&nbsp;</span>Список</button>
									<?php
									if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem mr-1' title='Добавить аптечку' data-item='12' data-object='car_first_aid_kid' data-title-form='Аптечка'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>";
								}?>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right"></div>
								<div class="col col-sm-2 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">годен с</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="start_date_first_aid_kid" value="<?= $start_date_first_aid_kid; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-2 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">по</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="end_date_first_aid_kid" value="<?= $end_date_first_aid_kid; ?>" disabled>
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="blockquote font-weight-bold fs-13">Знак аварийной остановки</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">выдан&nbsp;&nbsp;</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="issued_date_warning_triangle" value="<?= $issued_date_warning_triangle; ?>" disabled>
									</div>
								</div>
								
								<div class="col col-sm-6 mb-1 text-left">
									<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="12" data-object="car_warning_triangle" title="Отобразить список знаков аварийной остановки" data-title-form="Знак аварийной остановки"><span class="fa fa-search">&nbsp;</span>Список</button>
									<?php
									if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem mr-1' title='Добавить знак аварийной остановки' data-item='12' data-object='car_warning_triangle' data-title-form='Знак аварийной остановки'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>";
								}?></div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="blockquote font-weight-bold fs-13">Аккумуляторная батарея</label>
								</div>
								<div class="col col-sm-4 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">установлена</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="start_date_car_battery" value="<?= $start_date_car_battery; ?>" disabled>
									</div>
								</div>
								
								<div class="col col-sm-6 mb-1 text-left">
									<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="12" data-object="car_battery" title="Отобразить список аккумуляторных батарей" data-title-form="Аккумуляторная батарея"><span class="fa fa-search">&nbsp;</span>Список</button>
									<?php
									if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem mr-1' title='Добавить аккумуляторную батарею' data-item='12' data-object='car_battery' data-title-form='Аккумуляторная батарея'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>";
								}?></div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right"></div>
								<div class="col col-sm-2 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">тип</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="type_battery" value="<?= $type_battery; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-5 mb-1">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">изготовитель</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="firma_battery" value="<?= $firma_battery; ?>" disabled>
									</div>
								</div>
							</div>

							<div class="form-row mt-2">
								<div class="col col-sm-2 text-right">
									<label class="blockquote font-weight-bold fs-13">Видеорегистратор</label>
								</div>
								<div class="col col-sm-4">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">номер</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $number_dvr; ?>" disabled>
									</div>
								</div>
								
								<div class="col col-sm-6 text-left">
									<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="12" data-object="car_dvr" title="Отобразить список видеорегистраторов" data-title-form="Видеорегистратор"><span class="fa fa-search">&nbsp;</span>Список</button>
									<?php
									if($role >= 2) {
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem mr-1' title='Добавить видеорегистратор' data-item='12' data-object='car_dvr' data-title-form='Видеорегистратор'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>";
								}?></div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-2 text-right"></div>
								<div class="col col-sm-4">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">марка</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $marka_dvr; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-4">
									<div class="input-group input-group-sm">
										<div class="input-group-prepend"><label class="input-group-text black-text">модель</label></div>
										<input type="text" class="form-control form-control-sm black-text" value="<?= $model_dvr; ?>" disabled>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">	
						<div class="form-row">
							<div class="col col-sm-2 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwentythree" aria-controls="collapseTwentythree" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>23. УЧЕТ ШИН</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='13' title="Развернуть список всех шин"><span class="fa fa-search"></span>&nbsp;Список шин</button>
								<?php
									if($role >= 2)
									echo "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='13' title='Добавить шину'><span class='fa fa-plus'></span>&nbsp;Добавить шину</button>";
								?>
							</div>
						</div>
						<div class="collapse show" id="collapseTwentythree" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-wheels"><?= $list_wheels; ?></div>
							</div>
						</div>
					</div>

				</div>
				
			</div>
		</div>
	</div>
</div>