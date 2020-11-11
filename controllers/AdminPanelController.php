<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\User;
use IcKomiApp\core\Controller;
use IcKomiApp\models\AdminPanel;

class AdminPanelController extends Controller {

	// Controller
	public function adminPanelAction() {
		if(!empty($_POST) && !empty($_POST['option'])) {
			if($_POST['option'] == 'access') {
				if((new AdminPanel())->access_user($_POST))
					echo json_encode([1]);
				else
					echo json_encode([-1]);
			} else if($_POST['option'] == 'archive') {
				if((new AdminPanel())->move_archive($_POST))
					echo json_encode([1]);
				else
					echo json_encode([-1]);
			} else if($_POST['option'] == 'reset') {
				if((new AdminPanel())->reset_default_password($_POST))
					echo json_encode([1]);
				else
					echo json_encode([-1]);
			} else if($_POST['option'] == 'change_role') {
				if((new AdminPanel())->change_role($_POST))
					echo json_encode([1]);
				else
					echo json_encode([-1]);
			}
		} else {
			if(($data = (new AdminPanel())->get_list_users()) === false)
				$this->view->render();
			else
				$this->view->render($data);
		}
	}
}