<?php
	$admin_panel = (empty($admin_panel)) ? '' : $admin_panel;
	$role = 9;
?>

<div class="row">
		<div class="col-sm-12 admin-panel-list-users">
			<div class='card text-center border-dark' style='margin-top: 70px; background: #f5f5f5; min-width: 920px;'>
				<div class='card-header'>
					<h4>Список пользователей</h4>
				</div>
				<div class='card-body admin-panel' id="admin-panel" style="padding: 7px;">
					<?= $admin_panel; ?>
				</div>
			</div>
		</div>
	</div>