<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Controller;
use IcKomiApp\core\User;

//use IcKomiApp\core\Auth;

class LoginController extends Controller {

	// Controller login for web-resource
	public function loginAction() {
		if(empty($_GET)) {
			$this->view->render();
			return;
		}

		if(empty($_GET['action'])) {
			$this->view->render();
			return;
		}

		if($_GET['action'] == 'auth')
			$this->authorizationAction();
		else if($_GET['action'] == 'registration')
			$this->registrationAction();
		else
			$this->view->render();
	}

	// Controller logout for web-resource
	public function logoutAction() {
		User::logout();
		$this->redirect('/');
	}

	// Private controller registration for web-resource
	private function registrationAction() {
		$user = User::registration($_POST);
		if(!$user[0])
			echo json_encode(array(-1, $user[1]));
		else
			echo json_encode(array(1));
	}

	private function authorizationAction() {
		$user = User::authorization($_POST);
		if(!$user[0])
			echo json_encode(array(-1, $user[1]));
		else
			echo json_encode(array(1));
	}

}