<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\User;
use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;
use IcKomiApp\models\EditDirectory;

class EditDirectoryController extends Controller {

	// Controller
	public function editDirectoryAction() {
		if(!empty($_POST) && !empty($_POST['option'])) {
			if($_POST['option'] == 'save') {
				if(($data = (new EditDirectory())->save_value_directory($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove') {
				if(($data = (new EditDirectory())->remove_value_directory($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'get_value_directory') {
				if(($data = (new EditDirectory())->get_value_directory($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			}
		} else {
			$this->view->render();
		}
	}
}