<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\User;
use IcKomiApp\core\Controller;

class EditController extends Controller {

	// Controller
	public function editAction() {
		if(!empty($_POST) && !empty($_POST['option'])) {
			if($_POST['option'] == 'save') {
				$data = (new User())->save($_POST);
				if($data === true)
					echo json_encode([1]);
				else
					echo json_encode([-1, $data[1]]);
			} else if($_POST['option'] == 'change_password') {
				$data = (new User())->change_password($_POST);
				if($data === true)
					echo json_encode([1]);
				else
					echo json_encode([-1, $data[1]]);
			}
		} else {
			if(($data = (new User())->get_user_info()) === false)
				$this->view->render();
			else
				$this->view->render($data);
		}
	}
}