<?php

namespace IcKomiApp\widgets;

use IcKomiApp\core\User;
use IcKomiApp\core\Rights;
use IcKomiApp\core\Functions;

class FooterTop {

	protected static $footer_top_path = '../views/layouts/footer-top.php';

	protected static $default_class_dropdown_item = 'dropdown-item';
	protected static $default_class_nav_item = 'nav-item';
	protected static $default_class_nav_link = 'nav-link text-white';

	protected static $array_right_menu = [
		['name' => 'Редактировать данные пользователя', 'href' => '/edit', 'icon' => 'fa fa-vcard-o'],
		['name' => 'Панель администратора', 'href' => '/admin_panel', 'icon' => 'fa fa-clone'],
		['name' => 'Редактор справочников', 'href' => '/edit_directory', 'icon' => 'fa fa-edit'],
		['name' => 'Уведомления', 'href' => '/notice_events', 'icon' => 'fa fa-info-circle'],
		['class' => 'dropdown-divider'],
		['name' => 'Войти на веб-ресурс', 'href' => '/login', 'icon' => 'fa fa-power-off'],
		['name' => 'Выход', 'href' => '/logout', 'icon' => 'fa fa-close'],
	];

	protected static $array_left_menu = [
		['name' => 'Транспортные средства', 'href' => '#',
			'links' => [
				['name' => 'Поиск', 'href' => '/car_search', 'icon' => 'fa fa-search'],
				['name' => 'Добавить/скорректировать', 'href' => '/car', 'icon' => 'fa fa-edit'],
			],
		],
		['name' => 'Водители', 'href' => '#',
			'links' => [
				['name' => 'Поиск', 'href' => '/driver_search', 'icon' => 'fa fa-search'],
				['name' => 'Добавить/скорректировать', 'href' => '/driver', 'icon' => 'fa fa-edit'],
			],
		],
		['name' => 'Ремонт', 'href' => '#',
			'links' => [
				['name' => 'Поиск', 'href' => '/repair_search', 'icon' => 'fa fa-search'],
				['name' => 'Добавить/скорректировать', 'href' => '/repair', 'icon' => 'fa fa-edit'],
			],
		],
		['name' => 'Правонарушения', 'href' => '#',
			'links' => [
				['name' => 'Поиск ДТП', 'href' => '/dtp_search', 'icon' => 'fa fa-search'],
				['name' => 'Добавить/скорректировать ДТП', 'href' => '/dtp', 'icon' => 'fa fa-edit'],
				['class' => 'dropdown-divider'],
				['name' => 'Поиск адм. правонарушений', 'href' => '/adm_search', 'icon' => 'fa fa-search'],
				['name' => 'Добавить/скорректировать адм. правонарушение', 'href' => '/adm', 'icon' => 'fa fa-edit'],
			],
		],
		['name' => 'Контакты', 'href' => '/contacts'],
	];

	public static function getFooter($array_links_left = [], $array_links_right = []) {
		if(!file_exists(self::$footer_top_path))
			return;

		if(!empty($array_links_left))
			self::$array_left_menu = $array_links_left;

		if(!empty($array_links_right))
			self::$array_right_menu = $array_links_right;

		$left_menu = self::get_left_menu();
		$right_menu = self::get_dropdown_menu(self::$array_right_menu);
		$login = self::get_user_label();
		$notice_events_html = self::get_notice_events();
		require_once(self::$footer_top_path);
	}

	private static function get_dropdown_menu($array_menu) {
		if(!is_array($array_menu))
			return '';

		$html = '';
		foreach($array_menu as $key => $value) {
			$class = (empty($value['class'])) ? self::$default_class_dropdown_item : $value['class'];
			$href = (empty($value['href'])) ? '#' : $value['href'];
			$name = (empty($value['name'])) ? '' : $value['name'];
			$icon = (empty($value['icon'])) ? '' : $value['icon'];
			$id = preg_replace('/\//i', '', $href);

			if(preg_match('/divider/i', $class) == 1) {
				$html .= "<div class='dropdown-divider'></div>";
			} else {
				if(Rights::check_access_html_page($href))
					$html .= "<a class='" . $class . "' id='" . $id . "' href='" . $href . "' title='" . $name . "'><span class='" . $icon . "'></span>&nbsp;" . $name . "</a>";
			}
		}
		return $html;
	}

	private static function get_left_menu() {
		$html = '';
		foreach(self::$array_left_menu as $key => $value) {			
			$class = (empty($value['class'])) ? self::$default_class_nav_link : $value['class'];
			$href = (empty($value['href'])) ? '#' : $value['href'];
			$name = (empty($value['name'])) ? '' : $value['name'];
			$icon = (empty($value['icon'])) ? '' : $value['icon'];
			$id = preg_replace('/\//i', '', $href);

			if(!empty($value['links'])) {
				
				$html_dropdown = self::get_dropdown_menu($value['links']);
				$html_dropdown_test = preg_replace("/<div class='dropdown-divider'><\/div>/ui", '', $html_dropdown);
				if(empty($html_dropdown_test) || (mb_strlen($html_dropdown_test) == 0))
					continue;

				$html .= "<li class='" . self::$default_class_nav_item . " dropdown'>";
				$html .= "<a class='" . $class . " dropdown-toggle' href='#' id='" . $id . "' title='" . $name . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" . $name . "</a>";
				$html .= "<div class='dropdown-menu' aria-labelledby='" . $id . "'>";
				$html .= $html_dropdown;
				$html .= "</div></li>";
			} else {
				if(Rights::check_access_html_page($href)) {
					$html .= "<li class='" . self::$default_class_nav_item . "'>";
					$html .= "<a class='" . $class . "' href='" . $href . "' id='" . $id . "' title='" . $name . "'>" . $name . "</a>";
					$html .= "</div></li>";
				}
			}
		}
		return $html;
	}

	public static function get_user_label() {
		$login = User::get('login');

		if(mb_strlen($login) == 0)
			return '';

		$login = '(' . $login . ')';
		return $login;
	}

	/*
		Отрисовываем колокольчик с уведомлением
	*/
	private static function get_notice_events() {
		$role = User::get('role');
		$notice_events = User::get('notice');

		if((mb_strlen($role) == 0) || ($role == 0) || ($role === null))
			return "";

		$notice_events_html = '';

		// Отрисовываем колокольчик для уведомлений
		if(mb_strlen(trim($notice_events)) != 0) {
		
			$array_notice = explode('-', $notice_events);
		
			$all_notice_events = $notice_critical_events = $notice_warning_events = $notice_info_events = 0;
			if(array_key_exists(0, $array_notice)) {
				$notice_critical_events = $array_notice[0];
				$all_notice_events += $array_notice[0];
			}
		
			if(array_key_exists(1, $array_notice)) {
				$notice_warning_events = $array_notice[1];
				$all_notice_events += $array_notice[1];
			}
		
			if(array_key_exists(2, $array_notice)) {
				$notice_info_events =  $array_notice[2];
				$all_notice_events += $array_notice[2];
			}

			$notice_info_class = " class-info-circle ";
			$notice_text = "<span class=\"fa fa-info-circle text-danger\">&nbsp;</span>Просрочен срок: <a href=\"notice_events\" target=\"_blank\">" . $notice_critical_events . "</a><br>"
			. "<span class=\"fa fa-info-circle text-warning\">&nbsp;</span>Предупреждений: <a href=\"notice_events\" target=\"_blank\">" . $notice_warning_events . "</a><br>"
			. "<span class=\"fa fa-info-circle text-primary\">&nbsp;</span>Уведомлений: <a href=\"notice_events\" target=\"_blank\">" . $notice_info_events . "</a>";

			$notice_events_html = "<li class='navbar-brand'>"
				. "<button type='button' class='btn class-bell' id='btnNoticeEvents' data-toggle='popover' title='Уведомления (" . $all_notice_events . ")' data-html='true' data-content='" . $notice_text . "' style='padding: 0px 5px;'>
				<span class='fa fa-bell class-bell'></span>
				<span class='badge badge-pill class-bell' style='padding: 0px;'>" . $all_notice_events . "</span>
				</button>";
		}
		return $notice_events_html;
	}
	
}