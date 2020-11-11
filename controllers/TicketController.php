<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Session;
use IcKomiApp\core\Cookie;
use IcKomiApp\models\TicketCard;


class TicketController extends Controller {

	public function ticketAction() {
		if(!empty($_GET)) {
			$this->getCard();
		} else if(!empty($_POST)) {
			if($_POST['option'] == 'save')
				$this->saveCard();
			else if($_POST['option'] == 'remove')
				$this->removeCard();
			else if($_POST['option'] == 'pdf')
				$this->generatePDF();
		} else {
			$this->view->render();
		}
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
		Get card
	*/
	public function getCard() {
		$data = (new TicketCard())->get($_GET);

		if(($data === false) || (count($data) == 0))
			$this->view->render();
		else
			$this->view->render($data[0]);
	}

	/*
		Remove card
	*/
	public function removeCard() {
		echo json_encode(array((new TicketCard())->delete($_POST)));
	}

	/*
		Generate PDF document
	*/
	public function generatePDF() {
		$result = (new TicketCard())->get_pdf_document($_POST);
		echo json_encode(array(1, $result));
	}

	/*
		Search card
	*/
	public function searchAction() {
		if(empty($_POST)) {
			$this->view->render();
		}
		else {
			$data = (new TicketCard())->search($_POST);
			echo json_encode(array(1, $data));
		}
	}
}