<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\models\Repair;
use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;

class RepairController extends Controller {

	public function repairAction() {
		if(!empty($_GET)) {
			if(!empty($_GET['add_car']))
				$this->view->render([ 'add_car' => addslashes($_GET['add_car']) ]);
			else if(!empty($_GET['add_driver']))
				$this->view->render([ 'add_driver' => addslashes($_GET['add_driver']) ]);
			else
				$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'save')
				$this->saveCard();
			else if($_POST['option'] == 'remove')
				$this->removeCard();
			else if($_POST['option'] == 'get_list') {
				if(($data = (new Repair())->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'remove_file') {
				if((new Repair())->remove_file($_POST) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1]);
			}
		} else {
			$this->view->render();
		}
	}

	/*
		Search card
	*/
	public function Repair_searchAction() {
		if(empty($_POST)) {
			$data = (new Repair())->get_list();
			$this->view->render($data);
		} else {
			if(empty($_POST['JSON']))
				$data = (new Repair())->get_list($_POST);
			else
				$data = (new Repair())->search($_POST);
			
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
		$data = (new Repair())->get($_GET);
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
		if(($id = (new Repair())->save($_POST)) === false) {
			echo json_encode([-1]);
		} else {
			if(!empty($_FILES)) {
				if((new Repair())->save_file($_FILES, $id) === false)
					echo json_encode([-2, $id]);
				else
					echo json_encode([1, $id]);
			} else {
					echo json_encode([1, $id]);
			}
		}
	}

	/*
		Remove card
	*/
	public function removeCard() {
		if((new Repair())->remove($_POST) === false)
			echo json_encode([-1]);
		else
			echo json_encode([1]);
	}
}