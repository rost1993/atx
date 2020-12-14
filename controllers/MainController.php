<?php

namespace IcKomiApp\controllers;

use IcKomiApp\models\Install;
use IcKomiApp\core\Functions; 
use IcKomiApp\core\Controller;

class MainController extends Controller {

	public function mainAction() {
		$this->view->render();
	}

	public function editAction() {
		$this->view->render();
	}

	public function editDirectoryAction() {
		$this->view->render();
	}

	public function downloadFileAction() {
		// Скрипт скачивающий файл на компьютер пользователя
		// Имя файла необходимо передать в скрипт с помощью GET параметра: download_file.php?file=FileName
		if(isset($_GET['file'])) {
			$FileName = $_GET['file'];

			$split_array = explode('.', $FileName);

			if($split_array[count($split_array) - 1] != 'xlsx')
				exit();

			// Устанавливаем настройки для скачивания
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Description: File Transfer");
			header("Content-Type: application/vnd.ms-excel");
			//header("Content-Type: application/octet-stream");
			header("Content-Length: " . filesize($FileName));
			header("Content-Disposition: attachment; filename=" . basename($FileName));
			header("Content-Transfer-Encoding: binary");
			header("Pragma: public");
		
			// Скачиваем файл и удаляем его в случае успеха скачивания
			if(@readfile($FileName)) {
				unlink($FileName);
			}
		}
		exit();
	}

	public function installAction() {
		if(empty($_POST))
			$this->view->render();
		else {
			if($_POST['action'] == 'install') {
				echo json_encode((new Install())->install_database($_POST));
			}
		}
	}
}