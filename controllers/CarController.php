<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\Car;

class CarController extends Controller {

	public function carAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'save') {
				$this->saveCard();
			} else if($_POST['option'] == 'remove') {
				$this->removeCard();
			} else if($_POST['option'] == 'move_archive') {
				$this->moveArchiveCard();
			} else if($_POST['option'] == 'security') {
				$this->securityCard();
			} else if($_POST['option'] == 'notice_events') {
				$this->carNoticeEvents();
			} else if($_POST['option'] == 'write_off') {
				$this->carWriteOff();
			} else if($_POST['option'] == 'remove_file') {
				if((new Car())->remove_file($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			} else if($_POST['option'] == 'pdf') {
				$this->generate_pdf();
			}
		} else {
			$this->view->render();
		}
	}

	/*
		Search card
	*/
	public function Car_searchAction() {
		if(empty($_POST)){
			$data = (new Car())->get_list(); 
			$this->view->render($data);
		} else {
			if(empty($_POST['JSON']))
				$data = (new Car())->get_list($_POST);
			else
				$data = (new Car())->search($_POST);
			
			if($data === false)
				echo json_encode(array(-1));
			else
				echo json_encode(array(1, $data)); 
		}
	}

	/*
		Get card
	*/
	public function getCard() {
		$data = (new Car())->get($_GET);
		if(($data === false) || (count($data) == 0))
			$this->view->render();
		else
			$this->view->render($data[0]);
	}

	/*
		Save card
	*/
	public function saveCard() {
		$id = '';
		if(($id = (new Car())->save($_POST)) === false) {
			echo json_encode([-1]);
		} else {
			if(!empty($_FILES)) {
				if((new Car())->save_file($_FILES, $id) === false)
					echo json_encode([-2, 'Информация о ТС сохранена, но файл не сохранен!']);
				else
					echo json_encode([1]);
			} else {
				echo json_encode([1]);
			}
		}
	}

	/*
		Remove card
	*/
	public function removeCard() {
		if((new Car())->remove($_POST) === false)
			echo json_encode([-1]);
		else
			echo json_encode([1]);
	}

	/*
		Move archive card
	*/
	public function moveArchiveCard() {
		if((new Car())->move_to_archive($_POST) === false) {
			echo json_encode([-1]);
		} else {
			echo json_encode([1]);
		}
	}

	public function securityCard() {
		if((new Car())->lock_unlock_car($_POST) === false) {
			echo json_encode([-1]);
		} else {
			echo json_encode([1]);
		}
	}

	public function carNoticeEvents() {
		if((new Car())->car_enable_disable_notice_events($_POST) === false) {
			echo json_encode([-1]);
		} else {
			echo json_encode([1]);
		}
	}

	public function carWriteOff() {
		if((new Car())->car_write_off($_POST) === false) {
			echo json_encode([-1]);
		} else {
			echo json_encode([1]);
		}
	}

	public function generate_pdf() {
		$data = [];
		if(($data = (new Car())->generate_reference_car($_POST)) === false) {
			echo json_encode([-1]);
		} else {
			echo json_encode([1, $data]);
		}
	}
}