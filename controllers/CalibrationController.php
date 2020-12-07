<?php

namespace IcKomiApp\controllers;

use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;
use IcKomiApp\models\Calibration;

class CalibrationController extends Controller {

	public function calibrationAction() {
		if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new Calibration())->rendering_list($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'get_window') {
				if(($data = (new Calibration())->rendering_window($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'save') {
				$id = '';
				if(($id = (new Calibration())->save($_POST)) === false) {
					echo json_encode([-1]);
				} else {
					if(!empty($_FILES)) {
						if((new Calibration())->save_file($_FILES, $id, Functions::get_id_from_json($_POST['JSON'], 'id_car')) === false)
							echo json_encode([-1]);
						else
							echo json_encode([1]);
					} else {
						echo json_encode([1]);
					}
				}
			} else if($_POST['option'] == 'remove') {
				if((new Calibration())->remove($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove_file') {
				if((new Calibration())->remove_file($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			}
		} else {
			$this->view->render();
		}
	}
}