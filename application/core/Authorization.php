<?php

namespace IcKomiApp\core;

class Authorization extends AuthorizationBasic {
	
	protected $table = 'users';
	protected $name_field_login = 'LOGIN_USER';
	protected $name_field_password = 'PASSWORD_USER';

	protected function mark_session_data($data) {
		$session = new Session();
		$session->start();
		$session->set('login', ((empty($data[0]['login'])) ? '' : $data[0]['login'] ));
		$session->set('fam', ((empty($data[0]['fam'])) ? '' : $data[0]['fam'] ));
		$session->set('imj', ((empty($data[0]['imj'])) ? '' : $data[0]['imj'] ));
		$session->set('otch', ((empty($data[0]['otch'])) ? '' : $data[0]['otch'] ));
		$session->set('role', ((empty($data[0]['role'])) ? '' : $data[0]['role'] ));
		$session->set('hash', ((empty($data[0]['hash'])) ? '' : $data[0]['hash'] ));
		$session->set('id', ((empty($data[0]['id'])) ? '' : $data[0]['id'] ));
		$session->set('notice', ((empty($data[0]['notice_events'])) ? '' : $data[0]['notice_events'] ));
		$session->commit();
	}
}