<?php
?>

<div class='modal fade' id='ModalWindowViewDocument' role='dialog' data-backdrop='static'>
	<div class='modal-dialog modal-dialog-centered modal-xl' role='document' style='max-width: 90%'>
		<div class='modal-content text-center' style='height: 90vh; width: 100%;'>
			<div class='modal-header'>
				<h4 class='modal-tittle' id='textModal'></h4>
				<div class='text-right'>
					<button type='button' class='btn btn-outline-secondary btn-open-file-new-page' title='Открыть в новом окне'><span class='fa fa-folder-open-o'></span></button>
					<button type='button' class='btn btn-outline-danger' title='Закрыть просмотр' data-dismiss='modal' aria-label='Close'><span class='fa fa-close'></span></button>
				</div>
			</div>
			<div class='modal-body' id='bodyModal' style='height: 100%; position: relative;'>
			</div>
			<div id='TextModalWindowViewDocument'></div>
		</div>
	</div>
</div>