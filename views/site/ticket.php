<?php
	//use vendor\widgets\forms\FormBuilder;

	//FormBuilder::build('Контрактование', $arr_items, $vars);

	use IcKomiApp\widgets\Directory;
	use IcKomiApp\widgets\Modal;

	$LASTNAME = (empty($LASTNAME)) ? '' : $LASTNAME;
	$FIRSTNAME = (empty($FIRSTNAME)) ? '' : $FIRSTNAME;
	$MIDDLENAME = (empty($MIDDLENAME)) ? '' : $MIDDLENAME;
	$BIRTHDATE = (empty($BIRTHDATE)) ? '' : $BIRTHDATE;
	$PAYERINN = (empty($PAYERINN)) ? '' : $PAYERINN;
	$PAYER_ADDRESS_KODRAI = (empty($PAYER_ADDRESS_KODRAI)) ? '' : $PAYER_ADDRESS_KODRAI;
	$PAYER_ADDRESS_CITY = (empty($PAYER_ADDRESS_CITY)) ? '' : $PAYER_ADDRESS_CITY;
	$PAYER_ADDRESS_STREET = (empty($PAYER_ADDRESS_STREET)) ? '' : $PAYER_ADDRESS_STREET;
	$PAYER_ADDRESS_HOUSE = (empty($PAYER_ADDRESS_HOUSE)) ? '' : $PAYER_ADDRESS_HOUSE;
	$PAYER_ADDRESS_KORP = (empty($PAYER_ADDRESS_KORP)) ? '' : $PAYER_ADDRESS_KORP;
	$PAYER_ADDRESS_FLAT = (empty($PAYER_ADDRESS_FLAT)) ? '' : $PAYER_ADDRESS_FLAT;

	$KODRAI = (empty($KODRAI)) ? '' : $KODRAI;
	$ST = (empty($ST)) ? '' : $ST;
	$NUMBER_POST = (empty($NUMBER_POST)) ? '' : $NUMBER_POST;
	$DATE_POST = (empty($DATE_POST)) ? '' : $DATE_POST;
	$UIN = (empty($UIN)) ? '' : $UIN;
	$CBC = (empty($CBC)) ? '' : $CBC;
	$OKTMO = (empty($OKTMO)) ? '' : $OKTMO;
	$TYPE_BLANK = (empty($TYPE_BLANK)) ? '' : $TYPE_BLANK;
	$ID = (empty($ID)) ? '' : $ID;
	$SUM = (empty($SUM)) ? '' : $SUM;

	$TYPE_BLANK_SELECT = Directory::get_directory(1, $TYPE_BLANK);
	$KODRAI_SELECT = Directory::get_directory_spec(2, $KODRAI);
	$PAYER_ADDRESS_KODRAI_SELECT = Directory::get_directory_spec(2, $PAYER_ADDRESS_KODRAI);
	$ST_SELECT = Directory::get_directory_spec(3, $ST);

	$label_number_post = $label_date_post = '';
	if(($TYPE_BLANK == 4) || ($TYPE_BLANK == 5)) {
		$label_number_post = 'Номер протокола:';
		$label_date_post = 'Дата протокола:';
	} else {
		$label_number_post = 'Номер постановления (протокола):';
		$label_date_post = 'Дата постановления (протокола):';
	}
?>

<div class="row">
	<div class="col">

		<div class="card text-center border-dark" style="margin-top: 70px; background: #E6E6E6; min-width: 620px;">
			<div class="card-header" id="cardCarsHeader">
				<h4>Квитанция на штраф</h4>
			</div>

			<div class="card-body" id="adm-ticket">
				<div class="col">

					<div class="form-row mb-0">
						<div class="col col-sm text-center align-middle mb-0">
							<label class="mb-0"><h5 class="mb-0">Информация о нарушителе</h5></label>
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="LASTNAME" class="text-muted font-weight-bold" style="font-size: 13px;">Фамилия:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="FIRSTNAME" class="text-muted font-weight-bold" style="font-size: 13px;">Имя:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="MIDDLENAME" class="text-muted font-weight-bold" style="font-size: 13px;">Отчество:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="BIRTHDATE" class="text-muted font-weight-bold" style="font-size: 13px;">Дата рождения:</label>
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="LASTNAME" placeholder="Фамилия" value="<?= $LASTNAME; ?>" maxlength="350">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="FIRSTNAME" placeholder="Имя" value="<?= $FIRSTNAME; ?>" maxlength="350">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="MIDDLENAME" placeholder="Отчество" value="<?= $MIDDLENAME; ?>" maxlength="350">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm datepicker-here black-text" id="BIRTHDATE" placeholder="Дата рождения" value="<?= $BIRTHDATE; ?>" maxlength="10">
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="PAYER_ADDRESS_KODRAI" class="text-muted font-weight-bold" style="font-size: 13px;">Адрес плательщика (район):</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="PAYER_ADDRESS_CITY" class="text-muted font-weight-bold" style="font-size: 13px;">Населенный пункт:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="PAYER_ADDRESS_STREET" class="text-muted font-weight-bold" style="font-size: 13px;">Улица:</label>
						</div>
						<div class="col-1 col-sm-1 text-left align-middle">
							<label for="PAYER_ADDRESS_HOUSE" class="text-muted font-weight-bold" style="font-size: 13px;">Дом:</label>
						</div>
						<div class="col-1 col-sm-1 text-left align-middle">
							<label for="PAYER_ADDRESS_KORP" class="text-muted font-weight-bold" style="font-size: 13px;">Корпус:</label>
						</div>
						<div class="col-1 col-sm-1 text-left align-middle">
							<label for="PAYER_ADDRESS_FLAT" class="text-muted font-weight-bold" style="font-size: 13px;">Квартира:</label>
						</div>
					</div>

					<div class="form-row mb-1">
						<div class="col col-sm">
							<select class="custom-select custom-select-sm black-text" id="PAYER_ADDRESS_KODRAI"><?= $PAYER_ADDRESS_KODRAI_SELECT; ?></select>
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="PAYER_ADDRESS_CITY" placeholder="Населенный пункт" value="<?= $PAYER_ADDRESS_CITY; ?>" maxlength="50">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="PAYER_ADDRESS_STREET" placeholder="Улица" value="<?= $PAYER_ADDRESS_STREET; ?>" maxlength="50">
						</div>
						<div class="col col-sm-1">
							<input type="text" class="form-control form-control-sm black-text" id="PAYER_ADDRESS_HOUSE" placeholder="Дом" value="<?= $PAYER_ADDRESS_HOUSE; ?>" maxlength="10">
						</div>
						<div class="col-1 col-sm-1">
							<input type="text" class="form-control form-control-sm black-text" id="PAYER_ADDRESS_KORP" placeholder="Корпус" value="<?= $PAYER_ADDRESS_KORP; ?>" maxlength="10">
						</div>
						<div class="col-1 col-sm-1">
							<input type="text" class="form-control form-control-sm black-text" id="PAYER_ADDRESS_FLAT" placeholder="Квартира" value="<?= $PAYER_ADDRESS_FLAT; ?>" maxlength="10">
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="PAYERINN" class="text-muted font-weight-bold" style="font-size: 13px;">ИНН плательщика (при наличии):</label>
						</div>
					</div>

					<div class="form-row mb-1">
						<div class="col col-sm-3">
							<input type="text" class="form-control form-control-sm black-text" id="PAYERINN" placeholder="ИНН плательщика" value="<?= $PAYERINN; ?>" maxlength="12">
						</div>
					</div>

					<div class="form-row mt-3 mb-0">
						<div class="col col-sm text-center align-middle">
							<label class="mb-0"><h5 class="mb-0">Постановление</h5></label>
						</div>
					</div>

					<div class="form-row mt-0 mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="TYPE_BLANK" class="text-muted font-weight-bold" style="font-size: 13px;">Тип бланка:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="KODRAI" class="text-muted font-weight-bold" style="font-size: 13px;">Район:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="ST" class="text-muted font-weight-bold" style="font-size: 13px;">Статья КоАП РФ:</label>
						</div>
					</div>

					<div class="form-row mb-1">
						<div class="col col-sm">
							<select class="custom-select custom-select-sm black-text" id="TYPE_BLANK"><?= $TYPE_BLANK_SELECT; ?></select>
						</div>
						<div class="col col-sm">
							<select class="custom-select custom-select-sm black-text" id="KODRAI"><?= $KODRAI_SELECT; ?></select>
						</div>
						<div class="col col-sm">
							<select class="custom-select custom-select-sm black-text" id="ST"><?= $ST_SELECT; ?></select>
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="UIN" class="text-muted font-weight-bold" style="font-size: 13px;">УИН:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="OKTMO" class="text-muted font-weight-bold" style="font-size: 13px;">ОКТМО:</label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="CBC" class="text-muted font-weight-bold" style="font-size: 13px;">КБК:</label>
						</div>
					</div>

					<div class="form-row mb-1">
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="UIN" placeholder="УИН" value="<?= $UIN; ?>" maxlength="30">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="OKTMO" placeholder="ОКТМО" value="<?= $OKTMO; ?>" readonly>
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="CBC" placeholder="КБК" value="<?= $CBC; ?>" readonly>
						</div>
					</div>

					<div class="form-row mb-0">
						<div class="col col-sm text-left align-middle">
							<label for="NUMBER_POST" class="text-muted font-weight-bold" style="font-size: 13px;"><?= $label_number_post; ?></label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="DATE_POST" class="text-muted font-weight-bold" style="font-size: 13px;"><?= $label_date_post; ?></label>
						</div>
						<div class="col col-sm text-left align-middle">
							<label for="UIN" class="text-muted font-weight-bold" style="font-size: 13px;">Сумма:</label>
						</div>
					</div>

					<div class="form-row mb-1">
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="NUMBER_POST" placeholder="<?= $label_number_post; ?>" value="<?= $NUMBER_POST; ?>" maxlength="6">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text datepicker-here" id="DATE_POST" placeholder="<?= $label_date_post; ?>" value="<?= $DATE_POST; ?>" maxlength="10">
						</div>
						<div class="col col-sm">
							<input type="text" class="form-control form-control-sm black-text" id="SUM" placeholder="Сумма" value="<?= $SUM; ?>" maxlength="30">
						</div>
					</div>

					<div class='result'>
						
					</div>

				</div>
			</div>

			<div class='card-footer card-header'>
				<button type="button" class="btn btn-success btn-save" title="Сохранить" data-id="<?= $ID; ?>"><span class="fa fa-check">&nbsp;</span>Сохранить</button>
				<button type="button" class="btn btn-primary btn-generate-pdf" title="Печать" data-id="<?= $ID; ?>"><span class="fa fa-file-pdf-o">&nbsp;</span>Распечатать</button>
				<button type="button" class="btn btn-danger btn-remove" title="Удалить" data-id="<?= $ID; ?>"><span class='fa fa-trash'>&nbsp;</span>Удалить</button>
			</div>

		</div>
	</div>
</div>

<?= Modal::getModalDocumentView(); ?>
