<?php
?>

<div class="container-fluid starter-template">
	<div class="row">
		<div class="col-sm-12">

		
			<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>
				<div class='card-header'>
					<h4>Обновление компонентов базы данных</h4>
				</div>
				
				<div class="card-body" id="formUpdater">
					<div class='form-row'>
				
						<div class='col-4 mb-1 text-right'>
							<label for='comment_certificate_reg' class='font-weight-bold fs-13'>Файл обновлений с расширением .sql (пример example.sql)</label>
						</div>
					
						<div class='col-2 mb-1 text-center'>
							<span class='btn btn-sm btn-primary fileinput-button' title='Выберите файл'>
								<span class='fa fa-folder-open'>&nbsp;</span>Выберите файл
								<input id='btnAddFileModalWindow' type='file' name='files' accept='.sql' data-show-error='window'>
							</span>
						</div>

						<div class='col-4 mb-1 text-left'>
							<div id='uploadFileContainer'></div>
						</div>
					</div>

				</div>
				
				<div class="card-header">
					<button class="btn btn-success" id="btnUpdateDatabase"><span class="fa fa-refresh">&nbsp;</span>Обновить</button>
				</div>

			</div>
		</div>
	</div>
</div>