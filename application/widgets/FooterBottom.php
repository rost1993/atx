<?php

namespace IcKomiApp\widgets;

class FooterBottom {

	protected static $footer_bottom_path = '../views/layouts/footer-bottom.php';

	public static function getFooter() {
		if(!file_exists(self::$footer_bottom_path))
			return;
		return require_once(self::$footer_bottom_path);
	}
	
}