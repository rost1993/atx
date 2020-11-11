<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\Functions;
use IcKomiApp\models\Adm;

class AdmController extends Controller {

	public function AdmAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'save')
				$this->saveCard();
			else if($_POST['option'] == 'remove')
				$this->removeCard();
			else if($_POST['option'] == 'archive')
				$this->moveArchiveCard();
			else if($_POST['option'] == 'get_list') {
				if(($data = (new Adm())->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			}
		} else {
			$this->view->render();
		}
	}

	/*
		Search card
	*/
	public function Adm_searchAction() {
		if(empty($_POST)) {
			$data = (new Adm())->get_list();
			$this->view->render($data);
		} else {
			if(empty($_POST['JSON']))
				$data = (new Adm())->get_list($_POST);
			else
				$data = (new Adm())->search($_POST);
			
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
		$data = (new Adm())->get($_GET);

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
		if(!(new TicketCard())->save($_POST, $id, $message_error)) {
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