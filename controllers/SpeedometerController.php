<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\Speedometer;

class SpeedometerController extends Controller {

	public function SpeedometerAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new Speedometer())->rendering_list($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'get_window') {
				if(($data = (new Speedometer())->rendering_window($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'save') {
				if((new Speedometer())->save($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'remove') {
				if((new Speedometer())->remove($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'add') {
				if((new Speedometer())->add_speedometer($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'del') {
				if((new Speedometer())->remove_speedometer($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'settings_speedometer') {
				if(($data = (new Speedometer())->settings_speedometer($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'save_first_testimony') {
				if((new Speedometer())->save_first_testimony($_POST) === false)
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