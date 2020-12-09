<?php
	$search_result = (empty($search_result)) ? '' : $search_result;
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col col-sm-12">
		
			<div class='card text-center border-dark' style='margin-top: 80px; background: #f5f5f5; min-width: 620px;'>
				<div class='card-header'>
					<h4>Подсистема "Водители"</h4>
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
								<div class="col col-sm-1 mb-1 text-right">
									<label class="font-weight-bold fs-13" for="fam">Фамилия</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="fam" maxlength="150" placeholder="Фамилия" data-datatype="char" autofocus>
								</div>
								<div class="col col-sm-1 mb-1 text-right">
									<label class="font-weight-bold fs-13" for="imj">Имя</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="imj" maxlength="150" placeholder="Имя" data-datatype="char">
								</div>
								<div class="col col-sm-1 mb-1 text-right">
									<label class="font-weight-bold fs-13" for="otch">Отчество</label>
								</div>
								<div class="col col-sm-3 mb-1">
									<input type="text" class="form-control form-control-sm black-text" id="otch" maxlength="150" placeholder="Отчество" data-datatype="char">
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-12 mb-1 text-center">
									<label class="mr-3 fs-13">
										<input type='radio' name='ibd_arx' value="2" data-datatype="radio" checked>
										<strong>Актуальные</strong>
									</label>
									
									<label class="mr-3 fs-13">
										<input type='radio' name='ibd_arx' value="1" data-datatype="radio">
										<strong>Архив</strong>
									</label>
									
									<label class="fs-13">
										<input type='radio' name='ibd_arx' value="3" data-datatype="radio">
										<strong>Все</strong>
									</label>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col col-sm-12 text-center">
									<button type="button" class="btn btn-success btn-search-item mb-2 mt-2 mr-2 btn-search-drivers" data-page="1" data-excel="-1" title="Поиск"><span class="fa fa-search">&nbsp;</span>Поиск водителя</button>
									<button type="button" class="btn btn-info mb-2 mt-2 ml-2 btn-search" data-page="1" data-excel="1" title="Выгрузить в Excel"><span class="fa fa-file-excel-o">&nbsp;</span>Выгрузить в Excel</button>
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