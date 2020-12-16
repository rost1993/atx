<?php

	$directories = IcKomiApp\widgets\Directory::get_multiple_directory([35, 36]);
	$select_notice_status = $directories[35];
	$select_notice_subsystems = $directories[36];
	$list_notice_events = (empty($list_notice_events)) ? '' : $list_notice_events;
	$role = 9;
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header' id="cardCarsHeader">
					<h4>Уведомления</h4>
					<h6>(сбор и анализ сведений осуществляется в автоматическом режиме каждые 2 часа)</h6>
				</div>
				<div class='card-header' id="cradFilterNotice">
					<div class="col-sm-12 atx-cars-block">
						<div class="form-row">
							<div class="col col-sm-12 mb-1 text-left">
								<p style="margin: 0px;"><h5><a class="black-text-atx show-block" href="#collapseOne" aria-controls="collapseOne" data-toggle="collapse" title="Скрыть/раскрыть блок"><span class="fa fa-caret-down">&nbsp;</span>Фильтр</a></h5></p>
							</div>
						</div>
						<div class="collapse show" id="collapseOne" aria-labelledby="headingOne">
							<div class="form-row">
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label for="selectNoticeStatus" class="text-muted" style="font-size: 13px;"><strong>Важность</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="selectNoticeStatus" data-datatype="char">
									<?= $select_notice_status; ?>
									</select>
								</div>
								<div class="col col-sm-1 mb-1 text-right" style="vertical-align: center;">
									<label for="selectNoticeSubsystem" class="text-muted" style="font-size: 13px;"><strong>Подсистема</strong></label>
								</div>
								<div class="col col-sm-3 mb-1">
									<select class="custom-select custom-select-sm black-text" id="selectNoticeSubsystem" data-datatype="char">
										<?= $select_notice_subsystems; ?>
									</select>
								</div>
								<div class="col col-sm-4 mb-1">
									<button type="button" class="btn btn-sm btn-outline-success" id="btnFilterNotice" title="Применить фильтр"><span class="fa fa-search">&nbsp;</span>Применить</button>
									<!--<button type="button" class="btn btn-sm btn-outline-info" id="btnNoticeExcel" title="Выгрузить в Excel"><span class="fa fa-file-excel-o">&nbsp;</span>Выгрузить в Excel</button>-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body text-left" id="notice-list"></div>
				<div class="result-list-atx"><?= $list_notice_events; ?></div>
			</div>
		</div>
	</div>
</div>