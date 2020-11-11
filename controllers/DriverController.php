<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\Driver;

class DriverController extends Controller {

	public function driverAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'save')
				$this->saveCard();
			else if($_POST['option'] == 'remove')
				$this->removeCard();
			else if($_POST['optsio'] == 'archive')
				$this->moveArchiveCard();
		} else {
			$this->view->render();
		}
	}

	/*
		Search card
	*/
	public function Driver_searchAction() {
		if(empty($_POST)){
			$data = (new Driver())->get_list(); 
			$this->view->render($data);
		} else {
			if(empty($_POST['JSON']))
				$data = (new Driver())->get_list($_POST);
			else
				$data = (new Driver())->search($_POST);
			
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
		$data = (new Driver())->get($_GET);

		if(($data === false) || (count($data) == 0))
			$this->view->render();
		else
			$this->view->render($data[0]);
	}

	/*
		Save card
	*/
	public function saveCard() {
		$id = $message_error = '';
		if(!(new Driver())->save($_POST, $id, $message_error)) {
			echo json_encode(array(-2, $message_error));
		} else {
			echo json_encode(array(1, $id));
		}
	}

	/*
		Remove card
	*/
	public function removeCard() {
		echo json_encode(array((new TicketCard())->delete($_POST)));
	}

	/*
		Move archive card
	*/
	public function moveArchiveCard() {
		return true;
	}
}