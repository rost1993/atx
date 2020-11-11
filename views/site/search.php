<?php 
	use IcKomiApp\widgets\Directory;
	$KODRAI_SELECT = Directory::get_directory_spec(2);
	$ST_SELECT = Directory::get_directory_spec(3);
?>

<div class="row">
	<div class="col">
		<div class="card text-center border-dark" style="margin-top: 70px; background: #E6E6E6; min-width: 520px;">
			<div class='card-header'>
				<h4>Поиск квитанции</h4>
			</div>

			<div class="card-body" id="search-block">
				<div class="col search-block">
					<div class="form-row">
						<div class="col mb-1 text-left">
							<p style="margin: 0px;">
								<h5>
									<a class="black-text-atx show-block" href="#collapseOne" aria-controls="collapseOne" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down"></span>&nbsp;Реквизиты для поиска</a>
								</h5>
							</p>
						</div>
					</div>

					<div class="collapse show" id="collapseOne" aria-labelledby="headingOne">
						<div class="form-row">
							<div class="col text-left">
								<label for="UIN" class="font-weight-bold">УИН:</label>
							</div>
							<div class="col text-left">
								<label for="LASTNAME" class="font-weight-bold">Фамилия:</label>
							</div>
							<div class="col text-left">
								<label for="FIRSTNAME" class="font-weight-bold">Имя:</label>
							</div>
							<div class="col text-left">
								<label for="MIDDLENAME" class="font-weight-bold">Отчество:</label>
							</div>
						</div>

						<div class="form-row mb-4">
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text search-field" id="UIN" placeholder="УИН" maxlength="30">
							</div>
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text search-field" id="LASTNAME" placeholder="Фамилия" maxlength="350">
							</div>
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text search-field" id="FIRSTNAME" placeholder="Имя" maxlength="350">
							</div>
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text search-field" id="MIDDLENAME" placeholder="Отчество" maxlength="350">
							</div>
						</div>

						<div class="form-row">	
							<div class="col text-left">
								<label for="KODRAI" class="font-weight-bold">Район:</label>
							</div>
							<div class="col text-left">
								<label for="ST" class="font-weight-bold">Статья КоАП РФ:</label>
							</div>
							<div class="col text-left">
								<label for="NUMBER_POST" class="font-weight-bold">Номер постановления:</label>
							</div>
							<div class="col text-left">
								<label for="DATE_POST" class="font-weight-bold">Дата постановления:</label>
							</div>
						</div>

						<div class="form-row mb-4">
							<div class="col col-sm">
								<select class="custom-select custom-select-sm black-text search-field" id="KODRAI"><?= $KODRAI_SELECT; ?></select>
							</div>
							<div class="col col-sm">
								<select class="custom-select custom-select-sm black-text search-field" id="ST"><?= $ST_SELECT; ?></select>
							</div>
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text search-field" id="NUMBER_POST" placeholder="Номер постановления" maxlength="6">
							</div>
							<div class="col col-sm">
								<input type="text" class="form-control form-control-sm black-text datepicker-here search-field" id="DATE_POST" placeholder="Дата постановления" maxlength="10">
							</div>
						</div>

						<div class="form-row">
							<div class='col-sm-12 text-center'>
								<button type="button" class="btn btn-success btn-search" data-excel="-1" data-page="1" title="Поиск"><span class="fa fa-search">&nbsp;</span>Поиск</button>
								<button type="button" class="btn btn-primary btn-search" data-excel="1" data-page="1" title="Выгрузить список в формат Excel"><span class="fa fa-file-excel-o">&nbsp;</span>Выгрузить в Excel</button>
							</div>
						</div>

					</div>

				</div>
			</div>

			<div class="card-body result-block"></div>

		</div>
	</div>
</div>