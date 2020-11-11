<?php
	$search_result = (empty($search_result)) ? '' : $search_result;
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<h4>Подсистема "Административные правонарушения"</h4>
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
									<label style="font-size: 13px;" for="fam"><strong>Фамилия</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="fam" maxlength="150" placeholder="Фамилия" data-datatype="char" autofocus>
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="imj"><strong>Имя</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="imj" maxlength="150" placeholder="Имя" data-datatype="char">
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label style="font-size: 13px;" for="gos_znak"><strong>Гос. номер</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="gos_znak" maxlength="150" placeholder="Гос. номер" data-datatype="char">
								</div>
							</div>

							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label for="date_dtp1" style="font-size: 13px;"><strong>Дата совершения</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='date_adm1'>с</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_adm1" data-datatype="date" maxlength="10" placeholder="Дата">
									</div>
								</div>
								<div class="col col-sm-1 mb-1"></div>
								<div class="col col-sm-3 mb-1">
									<div class='input-group input-group-sm'>
										<div class='input-group-prepend'><label class='input-group-text' for='date_adm2'>по</label></div>
										<input type="text" class="form-control form-control-sm black-text datepicker-here" id="date_adm2" data-datatype="date" maxlength="10" placeholder="Дата">
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-12 text-center">
									<button type="button" class="btn btn-success mb-2 mt-2 mr-2 btn-search-adm-offense" data-page="1" data-excel="-1" title="Поиск"><span class="fa fa-search">&nbsp;</span>Поиск</button>
									<button type="button" class="btn btn-info mb-2 mt-2 ml-2 btn-search-adm-offense" data-page="1" data-excel="1" title="Выгрузить в Excel"><span class="fa fa-file-excel-o">&nbsp;</span>Выгрузить в Excel</button>
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