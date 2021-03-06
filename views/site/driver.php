<?php
	$id = (empty($id)) ? '' : $id;
	$fam = (empty($fam)) ? '' : $fam;
	$imj = (empty($imj)) ? '' : $imj;
	$otch = (empty($otch)) ? '' : $otch;
	$dt_rojd = (empty($dt_rojd)) ? '' : IcKomiApp\core\Functions::convertToDate($dt_rojd);
	$mob_phone = (empty($mob_phone)) ? '' : $mob_phone;

	$doc_s = (empty($doc_s)) ? '' : $doc_s;
	$doc_n = (empty($doc_n)) ? '' : $doc_n;
	$doc_date = (empty($doc_date)) ? '' : $doc_date;
	$doc_end_date = (empty($doc_end_date)) ? '' : $doc_end_date;

	$doc_s_tractor = (empty($doc_s_tractor)) ? '' : $doc_s_tractor;
	$doc_n_tractor = (empty($doc_n_tractor)) ? '' : $doc_n_tractor;
	$doc_date_tractor = (empty($doc_date_tractor)) ? '' : $doc_date_tractor;
	$doc_end_date_tractor = (empty($doc_end_date_tractor)) ? '' : $doc_end_date_tractor;
	
	$doc_s_boat = (empty($doc_s_boat)) ? '' : $doc_s_boat;
	$doc_n_boat = (empty($doc_n_boat)) ? '' : $doc_n_boat;
	$doc_date_boat = (empty($doc_date_boat)) ? '' : $doc_date_boat;
	$doc_end_date_boat = (empty($doc_end_date_boat)) ? '' : $doc_end_date_boat;

	$list_car_for_driver = (empty($list_car_for_driver)) ? '' : $list_car_for_driver;
	$list_dtp = (empty($list_dtp)) ? '' : $list_dtp;
	$list_adm = (empty($list_adm)) ? '' : $list_adm;
	$list_tractor_vu = (empty($list_tractor_vu)) ? '' : $list_tractor_vu;

	$number_dopog = (empty($number_dopog)) ? '' : $number_dopog;
	$date_start_dopog = (empty($date_start_dopog)) ? '' : $date_start_dopog;
	$date_end_dopog = (empty($date_end_dopog)) ? '' : $date_end_dopog;

	$file_vu = (empty($file_vu)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_vu, $ext_file_vu);
	$file_vu_tractor = (empty($file_vu_tractor)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_vu_tractor, $ext_file_vu_tractor);
	$file_dopog = (empty($file_dopog)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_dopog, $ext_file_dopog);

	$dostup = (empty($dostup)) ? 1 : $dostup;
	$text_btn_dostup = $span_btn_dostup = $badge_dostup = '';
	if($dostup == 1) {
		$text_btn_dostup = 'Защитить водителя';
		$span_btn_dostup = 'fa fa-lock';
		$badge_dostup = '';
	} else if($dostup == 2){
		$text_btn_dostup = 'Снять защиту с водителя';
		$span_btn_dostup = 'fa fa-unlock';
		$badge_dostup = "<span class='badge badge-pill badge-danger' id='badgeDriversSecurity'><span class='fa fa-lock'></span>&nbsp;Доступ к водителю ограничен</span>";
	}

	$ibd_arx = (empty($ibd_arx)) ? 1 : $ibd_arx;
	$text_btn_archive = $badge_archive = '';
	if($ibd_arx == 1) {
		$text_btn_archive = 'Перевести в архив';
		$badge_archive = '';
	} else if($ibd_arx == 2) {
		$text_btn_archive = 'Восстановить из архива';
		$badge_archive = "<span class='badge badge-pill badge-warning' id='badgeDriverArchive'><span class='fa fa-folder'>&nbsp;</span>В архиве</span>";
	}

	$number_card = (empty($number_card)) ? '' : $number_card;
	$firma_card_text = (empty($firma_card_text)) ? '' : $firma_card_text;
	$date_start_card = (empty($date_start_card)) ? '' : $date_start_card;
	$date_end_card = (empty($date_end_card)) ? '' : $date_end_card;
	$file_card = (empty($file_card)) ? '' : IcKomiApp\core\Functions::rendering_icon_file($file_card, $ext_file_card);

	// Обработка категорий ВУ
	$strKategVU = $strKategVUTractor = $strKategVUBoat = '';
			
	if(!empty($c_a) && $c_a > 0)
		$strKategVU .= "<span class='style-kateg-vu'>A</span>";
	if(!empty($c_a1) && $c_a1 > 0)
		$strKategVU .= "<span class='style-kateg-vu'>A1</span>";
	if(!empty($c_b) && $c_b > 0)
		$strKategVU .= "<span class='style-kateg-vu'>B</span>";
	if(!empty($c_b1) && $c_b1 > 0)
		$strKategVU .= "<span class='style-kateg-vu'>B1</span>";
	if(!empty($c_c) && $c_c > 0)
		$strKategVU .= "<span class='style-kateg-vu'>C</span>";
	if(!empty($c_c1) && $c_c1 > 0)
		$strKategVU .= "<span class='style-kateg-vu'>C1</span>";
	if(!empty($c_d) && $c_d > 0)
		$strKategVU .= "<span class='style-kateg-vu'>D</span>";
	if(!empty($c_d1) && $c_d1 > 0)
		$strKategVU .= "<span class='style-kateg-vu'>D1</span>";
	if(!empty($c_be) && $c_be > 0)
		$strKategVU .= "<span class='style-kateg-vu'>BE</span>";
	if(!empty($c_ce) && $c_ce > 0)
		$strKategVU .= "<span class='style-kateg-vu'>CE</span>";
	if(!empty($c_c1e) && $c_c1e > 0)
		$strKategVU .= "<span class='style-kateg-vu'>C1E</span>";
	if(!empty($c_de) && $c_de > 0)
		$strKategVU .= "<span class='style-kateg-vu'>DE</span>";
	if(!empty($c_d1e) && $c_d1e > 0)
		$strKategVU .= "<span class='style-kateg-vu'>D1E</span>";
	if(!empty($c_m) && $c_m > 0)
		$strKategVU .= "<span class='style-kateg-vu'>M</span>";
	if(!empty($c_tm) && $c_tm > 0)
		$strKategVU .= "<span class='style-kateg-vu'>TM</span>";
	if(!empty($c_tb) && $c_tb > 0)
		$strKategVU .= "<span class='style-kateg-vu'>TB</span>";
	
	if(!empty($c_a1_tr) && $c_a1_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>A1</span>";
	if(!empty($c_a2_tr) && $c_a2_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>A2</span>";
	if(!empty($c_a3_tr) && $c_a3_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>A3</span>";
	if(!empty($c_a4_tr) && $c_a4_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>A4</span>";
	if(!empty($c_b_tr) && $c_b_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>B</span>";
	if(!empty($c_c_tr) && $c_c_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>C</span>";
	if(!empty($c_d_tr) && $c_d_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>D</span>";
	if(!empty($c_e_tr) && $c_e_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>E</span>";
	if(!empty($c_f_tr) && $c_f_tr > 0)
		$strKategVUTractor .= "<span class='style-kateg-vu'>F</span>";
	
	if(!empty($c_gydro) && $c_gydro > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Гидроцикл</span>";
	if(!empty($c_moto) && $c_moto > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Мотолодка</span>";
	if(!empty($c_cater) && $c_cater > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Катер</span>";
	if(!empty($c_parus_12) && $c_parus_12 > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Парусное до 12 кв.м</span>";
	if(!empty($c_parus_22) && $c_parus_22 >0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Парусное до 22 кв.м</span>";
	if(!empty($c_parus_60) && $c_parus_60 > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Парусное до 60 кв.м</span>";
	if(!empty($c_parus_more_60) && $c_parus_more_60 > 0)
		$strKategVUBoat .= "<span class='style-kateg-vu'>Парусное более 60 кв.м</span>";

	$role = IcKomiApp\core\User::get('role');
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">

		
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardCarsHeader">
					<h4>Информация о водителе</h4>
					<div id="nsyst" style="display: none;"><?= $id; ?></div>
					<div id="cardDriversHeaderServiceBadge"><?= $badge_dostup; ?><?= $badge_archive; ?></div>
				</div>
				
				<div class="card-body atx-form">
					<div id="mainDriversInformation" class='mainDriversInformation'>
						<div class="col-sm-12 atx-cars-block">
						
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-left">
									<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseOne" aria-controls="collapseOne" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>1. ОБЩИЕ СВЕДЕНИЯ О ВОДИТЕЛЕ</a></h5></p>
								</div>
							</div>
							
							<div class="collapse show" id="collapseOne" aria-labelledby="headingOne">
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
										<label for="fam" class="text-muted" style="font-size: 13px;"><strong>Фамилия</strong></label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="fam" data-mandatory="true" data-message-error="Заполните обязательное поле: Фамилия" data-datatype="char" maxlength="150" placeholder="Фамилия" value="<?= $fam; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
										<label for="imj" class="text-muted" style="font-size: 13px;"><strong>Имя</strong></label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="imj" data-mandatory="true" data-message-error="Заполните обязательное поле: Имя" data-datatype="char" maxlength="150" placeholder="Имя" value="<?= $imj; ?>">
									</div>
								</div>
							
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
										<label for="otch" class="text-muted" style="font-size: 13px;"><strong>Отчество</strong></label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="otch" data-mandatory="true" data-message-error="Заполните обязательное поле: Отчество" data-datatype="char" maxlength="150" placeholder="Отчество" value="<?= $otch; ?>">
									</div>
									<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
										<label for="dt_rojd" class="text-muted" style="font-size: 13px;"><strong>Дата рождения</strong></label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="dt_rojd" data-mandatory="true" data-message-error="Заполните обязательное поле: Дата рождения" data-datatype="date" maxlength="10" placeholder="Дата рождения" value="<?= $dt_rojd; ?>">
									</div>
								</div>
								
								<div class="form-row">
									<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
										<label for="mob_phone" class="text-muted" style="font-size: 13px;"><strong>Контактный телефон</strong></label>
									</div>
									<div class="col col-sm-3 mb-1">
										<input type="text" class="form-control form-control-sm black-text" id="mob_phone" maxlength="20" data-datatype="char" placeholder="+7(999)-999-99-99" value="<?= $mob_phone; ?>">
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
				
				<div class='card-footer card-header'><?php
					if($role >= 2) {
						echo "<button type='button' class='btn btn-success' id='saveDrivers' title='Сохранить информацию о водителе' style='margin: 3px;'><span class='fa fa-check'></span>&nbsp;Сохранить водителя</button>";
						echo "<button type='button' class='btn btn-primary' id='lockDrivers' title='Изменить уровень видимости водителя' style='margin: 3px;'><span class='" . $span_btn_dostup . "'></span>&nbsp;" . $text_btn_dostup . "</button>";

						echo "<button type='button' class='btn btn-warning' id='btnMoveArchive' title='Перевести в архив/восстановить из архива' style='margin: 2px;' data-type='2'><span class='fa fa-folder'>&nbsp;</span>" . $text_btn_archive ."</button>";
						
						echo "<button type='button' class='btn btn-danger dropdown-toggle' id='dropdownDeleteDrivers' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить водителя' style='margin: 3px;'><span class='fa fa-remove'></span>&nbsp;Удалить водителя</button>
						<div class='dropdown-menu' aria-labelledby='dropdownDeleteDrivers'>
							<button class='dropdown-item' id='deleteDrivers'><span class='fa fa-check text-success'></span>&nbsp;Подтверждаю удаление</button>
						</div>";
					}
				?></div>
				
				<div class="card-header">
					<h4>Дополнительные сведения</h4>
				</div>
				
				<div class="card-body">
				
					<div class="col-sm-12 atx-cars-block" id="SECTION1-VU">

						<div class="form-row">
							<div class="col col-sm-3 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseTwo" aria-controls="collapseTwo" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>2. ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left" id='groupButtonVU'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='1' data-class="car" title="Показать историю водительских удостоверений"><span class="fa fa-search">&nbsp;</span>История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='1' data-class='car' title='Добавить водительское удостоверение'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>" : "";?>
								<?= $file_vu; ?>
							</div>
						</div>

						<div class="collapse show" id="collapseTwo" aria-labelledby="headingTwo">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_s" class="text-muted" style="font-size: 13px;"><strong>Водительское удостоверение</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="doc_s" maxlength="4" placeholder="Серия" value="<?= $doc_s; ?>" disabled>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="doc_n" maxlength="6" placeholder="Номер" value="<?= $doc_n; ?>" disabled>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_date" class="text-muted" style="font-size: 13px;"><strong>Срок действия ВУ</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='doc_date'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="doc_date" maxlength="10" placeholder="Дата выдачи ВУ" value="<?= $doc_date; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='doc_end_date'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" maxlength="10" id="doc_end_date" placeholder="Дата окончания ВУ" value="<?= $doc_end_date; ?>" disabled>
									</div>
								</div>
							</div>
						

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_s" class="text-muted" style="font-size: 13px;"><strong>Категории ВУ</strong></label>
								</div>
								<div class="col col-sm-8 text-left" id='kategVU'><?= $strKategVU; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block" id="SECTION2-VU">

						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseThree" aria-controls="collapseThree" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>3. УДОСТОВЕРЕНИЕ ТРАКТОРИСТА-МАШИНИСТА</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left" id='groupButtonVUTractor'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='10' data-class="tractor" title="Показать историю водительских удостоверений"><span class="fa fa-search">&nbsp;</span>История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='10' data-class='tractor' title='Добавить водительское удостоверение'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>" : "";?>
								<?= $file_vu_tractor; ?>
							</div>
						</div>

						<div class="collapse show" id="collapseThree" aria-labelledby="headingTwo">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_s_tractor" class="text-muted" style="font-size: 13px;"><strong>Удостоверение</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="doc_s_tractor" maxlength="4" placeholder="Серия" value="<?= $doc_s_tractor; ?>" disabled>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="doc_n_tractor" maxlength="6" placeholder="Номер" value="<?= $doc_n_tractor; ?>" disabled>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_date_tractor" class="text-muted" style="font-size: 13px;"><strong>Срок действия</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='doc_date_tractor'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="doc_date_tractor" maxlength="10" placeholder="Дата выдачи" value="<?= $doc_date_tractor; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='doc_end_date_tractor'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" maxlength="10" id="doc_end_date_tractor" placeholder="Дата окончания" value="<?= $doc_end_date_tractor; ?>" disabled>
									</div>
								</div>
							</div>
						

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="doc_s" class="text-muted" style="font-size: 13px;"><strong>Категории</strong></label>
								</div>
								<div class="col col-sm-8 text-left" id='kategVUTractor'><?= $strKategVUTractor; ?></div>
							</div>
						</div>
					</div>
					
					
					
					<div class="col-sm-12 atx-cars-block">

						<div class="form-row">
							<div class="col col-sm-5 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFour" aria-controls="collapseFour" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>4. УДОСТОВЕРЕНИЕ МАШИНИСТА КРАНА ИЛИ КРАНА МАНИПУЛЯТОРА</a></h5></p>
							</div>
							<div class="col col-sm-6 mb-1 text-left" id='groupButtonVUBoat'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='14' data-class="boat" title="Показать список удостоверений"><span class="fa fa-search">&nbsp;</span>Список</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='14' data-class='boat' title='Добавить удостоверение'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>" : "";?>
							</div>
						</div>

						<div class="collapse show" id="collapseFour" aria-labelledby="headingTwo">
							<?= $list_tractor_vu; ?>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">

						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseFive" aria-controls="collapseFive" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>5. СВИДЕТЕЛЬСТВО ДОПОГ</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left" id='groupButtonVUTractor'>
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='15' title="Показать историю водительских удостоверений"><span class="fa fa-search">&nbsp;</span>История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='15' title='Добавить водительское удостоверение'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>" : "";?>
								<?= $file_dopog; ?>
							</div>
						</div>

						<div class="collapse show" id="collapseFive" aria-labelledby="headingTwo">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="number_dopog" class="text-muted" style="font-size: 13px;"><strong>Свидетельство</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="number_dopog" maxlength="4" placeholder="Номер" value="<?= $number_dopog; ?>" disabled>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right" style="vertical-align: center;">
									<label for="date_start_dopog" class="text-muted" style="font-size: 13px;"><strong>Срок действия</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='date_start_dopog'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" id="date_start_dopog" maxlength="10" placeholder="Дата выдачи" value="<?= $date_start_dopog; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='date_end_dopog'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" maxlength="10" id="date_end_dopog" placeholder="Дата окончания" value="<?= $date_end_dopog; ?>" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 atx-cars-block">

						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSix" aria-controls="collapseSix" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>6. КАРТА ВОДИТЕЛЯ</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='18' title="Показать историю карт водителя"><span class="fa fa-search">&nbsp;</span>История</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info btnAddItem' data-item='18' title='Добавить карту водителя'><span class='fa fa-plus'>&nbsp;</span>Добавить</button>" : "";?>
								<?= $file_card; ?>
							</div>
						</div>

						<div class="collapse show" id="collapseSix" aria-labelledby="headingTwo">
							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label for="number_dopog" class="text-muted font-weight-bold fs-13">Номер</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" placeholder="Номер" value="<?= $number_card; ?>" disabled>
								</div>
								<div class="col col-sm-2 mb-1 text-right">
									<label for="number_dopog" class="text-muted font-weight-bold fs-13">Кем выдана</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" placeholder="Кем выдана" value="<?= $firma_card_text; ?>" disabled>
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-2 mb-1 text-right">
									<label class="text-muted">Срок действия</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text" maxlength="10" placeholder="Дата выдачи" value="<?= $date_start_card; ?>" disabled>
									</div>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text" maxlength="10" placeholder="Дата окончания" value="<?= $date_end_card; ?>" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-2 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseSeven" aria-controls="collapseSeven" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>7. ЗАКРЕПЛЕНИЕ ЗА ТС</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success" id="btnShowListFixDriverForCar" data-mode-show="1" title="Открыть историю закреплений"><span class="fa fa-search">&nbsp;</span>История закреплений</button>
								<?php echo ($role >= 2) ? "<button type='button' class='btn btn-sm btn-outline-info' id='btnFixDriverForCar' data-mode-show='1' data-operation='1' title='Закрепить новое транспортное средство за водителем'><span class='fa fa-plus'>&nbsp;</span>Добавить ТС</button>" : ""; ?>
							</div>
						</div>
						<div class="collapse show" id="collapseSeven" aria-labelledby="headingFour">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-car-for-driver"><?= $list_car_for_driver; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-2 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseEight" aria-controls="collapseEight" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>8. ДТП</a></h5></p>
							</div>
							<div class="col col-sm-9 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item='9' data-type='2' title="Открыть список всех ДТП"><span class="fa fa-search">&nbsp;</span>Список ДТП</button>
								
								<?php
								if($role >= 2)
									echo "<a class='btn btn-sm btn-outline-info' href='dtp?add_driver=<?= $id; ?>' target='_blank' title='Добавить ДТП'><span class='fa fa-plus'>&nbsp;</span>Добавить ДТП</a>";
								?>
							</div>
						</div>

						<div class="collapse show" id="collapseEight" aria-labelledby="headingFive">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-dtp-for-driver"><?= $list_dtp; ?></div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-4 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseNine" aria-controls="collapseNine" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>9. АДМИНИСТРАТИВНЫЕ ПРАВОНАРУШЕНИЯ</a></h5></p>
							</div>
							<div class="col col-sm-7 mb-1 text-left">
								<button type="button" class="btn btn-sm btn-outline-success btnShowList" data-item="11" data-type='2' title="Открыть список всех адм. правонарушений"><span class="fa fa-search">&nbsp;</span>Список адм. правонарушений</button>
								<?php
								if($role >= 2)
								echo "<a class='btn btn-sm btn-outline-info' href='adm?add_driver=<?= $id; ?>' target='_blank' title='Добавить адм. правонарушение'><span class='fa fa-plus'>&nbsp;</span>Добавить адм. правонарушение</a>";
								?>
							</div>
						</div>

						<div class="collapse show" id="collapseNine" aria-labelledby="headingSix">
							<div class="form-row">
								<div class="col col-sm-12 mb-1" id="list-adm"><?= $list_adm; ?></div>
							</div>
						</div>
					</div>
					
				</div>

			</div>

		</div>
	</div>
</div>