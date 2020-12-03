<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\CarDocument;

class CarDocumentController extends Controller {

	public function carDocumentAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new CarDocument())->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'get_window') {
				if(($data = (new CarDocument())->rendering_window($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'get_window_edit') {
				if(($data = (new CarDocument())->rendering_window_edit($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'save') {
				$id = '';
				if(($id = (new CarDocument())->save($_POST)) === false) {
					echo json_encode([-1]);
				} else {
					if(!empty($_FILES)) {
						if((new CarDocument())->save_file($_FILES, $id) === false)
							echo json_encode([-2, $id]);
						else
							echo json_encode([1, $id]);
					} else {
						echo json_encode([1, $id]);
					}
				}
			} else if($_POST['option'] == 'save_link') {
				if((new CarDocument())->save_link($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove') {
				if((new CarDocument())->remove($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove_file') {
				if((new CarDocument())->remove_file($_POST) === false)
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
		$data = (new CarDocument())->get($_GET);
		if(($data === false) || (count($data) == 0))
			$this->view->render();
		else
			$this->view->render($data[0]);
	}
}