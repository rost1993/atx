<?php

namespace IcKomiApp\controllers;

use IcKomiApp\models\Dtp; 
use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;

class DtpController extends Controller {

	public function dtpAction() {
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
				if(($data = (new Dtp())->rendering_list($_POST)) === false)
					echo json_encode(array(-1));
				else
					echo json_encode(array(1, $data));
			} else if($_POST['option'] == 'remove_file') {
				if((new Dtp())->remove_file($_POST) === false)
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
	public function Dtp_searchAction() {
		if(empty($_POST)) {
			$data = (new Dtp())->get_list();
			$this->view->render($data);
		} else {
			if(empty($_POST['JSON']))
				$data = (new Dtp())->get_list($_POST);
			else
				$data = (new Dtp())->search($_POST);
			
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
		$data = (new Dtp())->get($_GET);

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
		if(($id = (new Dtp())->save($_POST)) === false) {
			echo json_encode([-1]);
		} else {
			if(!empty($_FILES)) {
				if((new Dtp())->save_file($_FILES, $id) === false)
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
		if((new Dtp())->remove($_POST) === false)
			echo json_encode([-1]);
		else
			echo json_encode([1]);
	}

	/*
		Move archive card
	*/
	public function moveArchiveCard() {
		return true;
	}
}