<?php

namespace IcKomiApp\widgets;

class FormBuilder {

	
	public static function build($title_form, $array_items = []) {


		echo "<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>";
		echo self::build_form_header($title_form);



		echo "</div>";

	}

	private static function build_form_header($title_form) {
		return "<div class='card-header'>
				<h4>" . $title_form . "</h4>
				<div id=''></div>
			</div>";
	}

	private static function build_form_body($array_items) {
		$html = "<div class='card-body' id=''>";

		/*foreach($array_items as $row => $items) {

		}*/

		$html .= "</div>";
	}

	private static function build_form_footer() {
		return "";
	}

	
	
}