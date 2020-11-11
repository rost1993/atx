<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Registration;
use IcKomiApp\core\Authorization;

abstract class UserBasic {

	protected static $table = "";

	/*
		$post - 
		$settings - 
	*/
	public static function registration($post, $settings = []) {
		$registration = new Registration($settings);
		if(!$registration->registration($post))
			return [false, $registration->message_error];
		return [true];
	}

	/*
		$post - 
		$settings - 
	*/
	public static function authorization($post, $settings = []) {
		$authorization = new Authorization($settings);
		if(!$authorization->login($post))
			return [false, $authorization->message_error];
		return [true];
	}

	/*
		Logout
	*/
	public static function logout() {
		(new Authorization())->logout();
		return [true];
	}

	public static function get($param) {
		if(!is_string($param) || (mb_strlen($param) == 0) || empty($param))
			return '';

		$session = new Session;
		$session->start();
		$user_param = $session->get($param);
		$session->commit();

		if(($user_param == null) || (mb_strlen($user_param) == 0))
			$user_param = '';

		return $user_param;
	}

}