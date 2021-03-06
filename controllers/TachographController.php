<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;
use IcKomiApp\models\Tachograph;

class TachographController extends Controller {

	public function tachographAction() {
		if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new Tachograph())->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'get_window') {
				if(($data = (new Tachograph())->rendering_window($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'save') {
				if(($id = (new Tachograph())->save($_POST)) === false) {
					echo json_encode([-1]);
				} else {
					if(!empty($_FILES)) {
						if((new Tachograph())->save_file($_FILES, $id, Functions::get_id_from_json($_POST['JSON'], 'id_car')) === false)
							echo json_encode([-1]);
						else
							echo json_encode([1]);
					} else {
						echo json_encode([1]);
					}
				}
			} else if($_POST['option'] == 'remove') {
				if((new Tachograph())->remove($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove_file') {
				if((new Tachograph())->remove_file($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			}
		} else {
			$this->view->render();
		}
	}
}