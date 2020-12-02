<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\Vu;

class VuController extends Controller {

	public function vuAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new Vu($_POST['class']))->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'get_window') {
				if(($data = (new Vu($_POST['class']))->rendering_window($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'save') {
				if(($id = (new Vu())->save($_POST)) === false) {
					echo json_encode([-1]);
				} else {
					if(!empty($_FILES)) {
						if((new Vu())->save_file($_FILES, $id, Functions::get_id_from_json($_POST['JSON'], 'id_driver')) === false)
							echo json_encode([-1]);
						else
							echo json_encode([1]);
					} else {
						echo json_encode([1]);
					}
				}
			} else if($_POST['option'] == 'remove') {
				if((new Vu($_POST['class']))->remove($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove_file') {
				if((new Vu())->remove_file($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			}
		} else {
			$this->view->render();
		}
	}

	/*
		Get card
	*/
	public function getCard() {
	}
}