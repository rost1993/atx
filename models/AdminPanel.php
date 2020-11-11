<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\widgets\Directory;
use IcKomiApp\lib\Database\DB;

class AdminPanel extends Model {

	public function get_list_users() {
		$list_users = User::get_list_users();
		$html = $this->render_table_list_users($list_users);
		return ['admin_panel' => $html];
	}

	private function render_table_list_users($list_users) {
		$role = User::get('role');
		$sql = "SELECT category as KOD, text as TEXT FROM role WHERE category < 9 ORDER BY KOD ASC";
		if($role == 9)
			$sql = "SELECT category as KOD, text as TEXT FROM role ORDER BY KOD ASC";

		if(($data = DB::query($sql)) === false)
			return false;

		$role = [];
		$select_slugba_employee = "<option value='0'></option>";
		$select_role_employee = "<option value='0'></option>";
		
		for($i = 0; $i < count($data); $i++) {
			array_push($role, $data[$i]);
			$select_role_employee .= "<option value='" . $data[$i]['KOD'] . "'>" . $data[$i]['TEXT'] . "</option>";
		}

		$html = "<table class='table table-sm table-hover text-center table-bordered' id='adminPanelListUsers' style='font-size: 13px; margin-top: 10px;'>";
		$html .= "<tr class='table-info'>"
					. "<th style='vertical-align: middle;' scope='col'>№ п/п</th>"
					. "<th style='vertical-align: middle;' scope='col'>ФИО</th>"
					. "<th style='vertical-align: middle;' scope='col'>Логин</th>"
					. "<th style='vertical-align: middle;' scope='col'>Роль</th>"
					. "<th style='vertical-align: middle;' scope='col'>Доступ</th>"
					. "<th style='vertical-align: middle;' scope='col'>Сброс<br>пароля</th>"
					. "<th style='vertical-align: middle;' scope='col'>Удалить<br>пользователя</th>"
				. "</tr>";
		
		for($i = 0, $index = 1; $i < count($list_users); $i++, $index++) {
			$html .= "<tr style='cursor: pointer;' id='" . $list_users[$i]['id'] . "' data-hash='" . $list_users[$i]['hash'] . "'>";
			$html .= "<th scope='row' style='vertical-align: middle;'>" . ($i+1) . "</th>";
			$html .= "<td class='text-left employee-fio' style='vertical-align: middle; font-size: 11px;'><strong>" . $list_users[$i]['fam'] . ' ' . $list_users[$i]['imj'] . ' ' . $list_users[$i]['otch'] . "</strong></td>";
			$html .= "<td class='employee-login' style='vertical-align: middle; font-size: 11px;'><strong>" . $list_users[$i]['login'] . "</strong></td>";
			
			// Разбираем роль пользователя
			$html .= "<td><select class='custom-select custom-select-sm black-text employee-role change-role'>";
			for($j = 0; $j < count($role); $j++) {
				if($list_users[$i]['role'] == $role[$j]['KOD'])
					$html .= "<option value='" . $role[$j]['KOD'] . "' selected>" . $role[$j]['TEXT'] . "</option>";
				else
					$html .= "<option value='" . $role[$j]['KOD'] . "'>" . $role[$j]['TEXT'] . "</option>";
			}
			$html .= "</select></td>";
			
			if($list_users[$i]['access'] != 1)
				$html .= "<td><button type='button' class='btn btn-danger btn-sm btn-access-user' title='Доступ пользователю закрыт'><span class='fa fa-thumbs-down'></span></button></td>";
			else
				$html .= "<td><button type='button' class='btn btn-success btn-sm btn-access-user' title='Доступ пользователю открыт'><span class='fa fa-thumbs-up'></span></button></td>";

			$html .= "<td><button type='button' class='btn btn-info btn-sm mr-2 btn-change-default-password' title='Сбросить пароль на пароль по умолчанию'><span class='fa fa-refresh'></span></button></td>";
			$html .= "<td><button type='button' class='btn btn-warning btn-sm mr-2 btn-remove-user' title='Удалить учетную запись пользователя'><span class='fa fa-close'></span></button></td>";
			
			$html .= "</tr>";
		}
		
		$html .= "</table><div id='Result'></div>";

		return $html;
	}

	public function access_user($post) {
		return User::access_user($post);
	}

	public function move_archive($post) {
		return User::move_archive($post);
	}

	public function reset_default_password($post) {
		return User::reset_default_password($post);
	}

	public function change_role($post) {
		return User::change_role($post);
	}

}