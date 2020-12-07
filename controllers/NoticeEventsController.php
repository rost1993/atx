<?php

namespace IcKomiApp\controllers;
 
use IcKomiApp\core\Functions;
use IcKomiApp\core\Controller;
use IcKomiApp\models\NoticeEvents;

class NoticeEventsController extends Controller {

	public function noticeEventsAction() {
		if(!empty($_POST)) {
			if($_POST['option'] == 'search') {
				if(($data = (new NoticeEvents())->search($_POST)) === false)
					echo json_encode([-1]);
				else
					echo json_encode([1, $data]);
			}
		} else {
			$data = [];
			if(($data = (new NoticeEvents())->get_list_notice(true)) === false)
				$this->view->render();
			else
				$this->view->render($data);
		}
	}
}