<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\CarForDriver;

class CarForDriverController extends Controller {

	public function carForDriverAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'get_list') {
				if(($data = (new CarForDriver())->rendering_list($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'car_fix') {
				if(($data = (new CarForDriver())->rendering_window_drivers_for_car($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			} else if($_POST['option'] == 'driver_fix') {
				if(($data = (new CarForDriver())->rendering_list($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			}
		} else {
			$this->view->render();
		}
	}
}