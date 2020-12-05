/*
 * Служебный JavaScript файл, предназначенный для внутренней лоигки веб-страниц
 * Содержит в себе практически все служебные вызываемые функции и процедуры,
 * которые необходимы для корректной работы веб-ресурса
 * Copyright: Rostislav Gashin, 2018
*/

var filesList = [];
var MAX_FILE_SIZE = 20971520; // 35 Mb - максимальный разрешенный размер файла
var COUNT_MAX_FILE = 3; 	  // Максимальное количество файлов
var PATH_TO_SCRIPT = '../php-bin/handlers-events/';

$(function () {
	
    'use strict';

	// Функция выбора файла
	$('#btnChangeUploadFile').change(function(){
		if(!$('*').is('#nsyst')) {
			showModal('ModalWindow', 'Функция не поддерживается!');
			return;
		} else {
			if($('#nsyst').html().trim().length == 0) {
				showModal('ModalWindow', 'Сначала сохраните экзамен!');
				return;
			}
		}

		var regExp = /xls|xlsx|pdf/i;

		var files = this.files;
		showDownloader(true);
		for(var i = 0; i < files.length; i++) {
			var fileNameSplit = files[i].name.split('.');
			var fileExtension = fileNameSplit[fileNameSplit.length - 1];
			
			if(!regExp.test(fileExtension)) {
				showModal('ModalWindow', 'Запрещено прикреплять электронный образ документа с таким расширением! Возможна загрузка только PDF документов!');
				filesList = [];
				return;
			}
			
			filesList[0] = files[i];
			
			AjaxDownloadFile('/php-bin/handlers-events/upload-file.php?option=1&nsyst=' + $('#nsyst').html().trim(), 'POST', function(result) {
				showDownloader(false);
				var res = eval(result);
				if(res[0] == -1) {
					showModal('ModalWindow', 'При обработке запроса произошла ошибка! Повторите запрос!');
				} else if(res[0] == -2) {
					showModal('ModalWindow', 'При обработке запроса произошла непредвиденная ошибка! ' + res[1]);
				} else if(res[0] == 1) {
					var html = "<span class='badge badge-pill badge-secondary' style='font-size: 15px;'><span class='fa fa-file-pdf-o file-badge' id='openModalFile' data-href='/" + res[1] + "'></span><button class='btn my_close_button'>&times</button></span>";
					$('#uploadFileContainer').append(html);
				} else {
					showModal('ModalWindow', 'При обработке запроса произошла непредвиденная ошибка!');
				}
			});
		}
		
		
		
	});
	
	// Обработчик выбора файла для модальных окон
	$('.modal-ic-komi-service-interface,#cardDtp,#cardAdm,#cardRepair,#cardDocument,.modal-ic-komi-view').on('change', '#btnAddFileModalWindow', function() {
		// Запрещаем/разрешаем прикрепление более одного документа
		if(!$(this).prop('multiple')) {
			if($(this).closest('.form-row').find('#uploadFileContainer').find('.badge').length > 0) {
				if($(this).data('showError') == 'window') {
					showModal('ModalWindow', 'Запрещено прикреплять более одного документа!');
				} else {
					$('#error-message').empty();
					$('#error-message').html('Запрещено прикреплять более одного документа!');
				}
				return;
			}
		}
		
		// Проверка расширения файла
		var regExp = new RegExp('pdf', 'i');
		if($(this).prop('accept').length > 0)
			regExp = new RegExp($(this).prop('accept').replace(/\./g, '').replace(/\,/g, '|'), 'i');

		var files = this.files;
		
		for(var i = 0; i < files.length; i++) {
			var fileNameSplit = files[i].name.split('.');
			var fileExtension = fileNameSplit[fileNameSplit.length - 1];
			if(!regExp.test(fileExtension)) {
				if($(this).data('showError') == 'window') {
					showModal('ModalWindow', 'Запрещено прикреплять электронный образ документа с таким расширением! Возможна загрузка только PDF документов!');
				} else {
					$('#error-message').empty();
					$('#error-message').html('Запрещено прикреплять электронный образ документа с таким расширением! Возможна загрузка только PDF документов!');
				}
				return;
			}

			filesList.push(files[i]);
			window.URL = window.URL || window.webkitURL;
			var url = window.URL.createObjectURL(files[i]);
	
			var html = '';
			if(getTypeFileForExtension(fileExtension) == 'image') {
				 html = "<span class='badge badge-pill badge-secondary image-preview-block' style='font-size: 15px;'>"
					 + "<img class='image-preview1 rounded' id='openModalFile' data-href='" + url + "' src='" + url + "' data-file-format='" + getTypeFileForExtension(fileExtension) + "'>"
					+ "<button class='btn my_close_button dropdown-toggle' id='btnDropdownDeleteFile' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>&times</button>"
					+ "<div class='dropdown-menu' aria-labelledby='btnDropdownDeleteFile'>"
					+ "<button type='button' class='dropdown-item' id='btnDeleteFile' data-save='-1'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button>"
					+ "</div></span>";
			} else {
				html = "<span class='badge badge-pill badge-secondary image-preview-block' style='font-size: 15px;'>"
					+ "<span class='fa fa-file-pdf-o file-badge' id='openModalFile' data-href='" + url + "' data-file-format='" + getTypeFileForExtension(fileExtension) + "'></span>"
					+ "<button class='btn my_close_button dropdown-toggle' id='btnDropdownDeleteFile' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>&times</button>"
					+ "<div class='dropdown-menu' aria-labelledby='btnDropdownDeleteFile'>"
					+ "<button type='button' class='dropdown-item' id='btnDeleteFile' data-save='-1'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button>"
					+ "</div></span>";
			}
			
			// Ищем ID элемента, который является хранилищем для файлов
			var uploadContainer = ($(this).data('uploadContainer') === undefined) ? 'uploadFileContainer' : $(this).data('uploadContainer');
			$('#' + uploadContainer).append(html);
		}
	});

	// Обработчик удаления файла
	// Либо удаляет саму иконку файла, либо делает запрос в базу и удаляет файл
	$('.modal-ic-komi-service-interface,.form-row,.modal-ic-komi-view').on('click', '#btnDeleteFile', function() {
		var item = $(this);
		if($(this).data('save') == -1) {
			$(item).closest('.badge').remove();
		} else {
			// Определяем скрипт, который необходимо запустить
			var script = '';
			var add_option = '';
			switch($(this).data('item')) {
				case 1:
					script = 'vu';
					break;
				case 2:
					script = 'osago';
					break;
				case 3:
					script = 'technical_inspection';
					break;
				case 5:
					script = 'pts';
					break;
				case 6:
					script = 'certificate_registration';
					break;
				case 8:
					script = 'car_document';
					break;
				case 9:
					script = 'car_for_driver';
					break;
				case 10:
					script = 'tractor_vu';
					break;
				case 11:
					script = 'repair';
					break;
				case 12:
					script = 'dtp';
					break;
				case 13:
					script = 'car';
					break;
				case 14:
					script = 'cranvu';
					break;
				case 15:
					script = 'adm';
					break;
				case 16:
					script = 'cars_dopog';
					break;
				case 17:
					script = 'drivers_dopog';
					break;
			}

			showDownloader(true);
			AjaxQuery('POST', script, 'option=remove_file' + '&nsyst=' + $(this).data('save') + add_option, function(result) {
				showDownloader(false);
				handlerAjaxResult(result, null, function(res) {
					$(item).closest('span').remove();
				});
			});
		}
		filesList = [];
	});

	// Функция возвращает тип файла (PDF, excel, image) в зависимости от расширения файла
	// Данный тип необходим для корректного отображения содержимого файла
	function getTypeFileForExtension(fileExtension) {
		var fileType;
		switch(fileExtension.toLowerCase().replace(/\./g, '').trim()) {
			case 'pdf':
				fileType = 'pdf';
				break;
		
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'tif':
			case 'gif':
				fileType = 'image';
				break;
		
			case 'xlsx':
			case 'xls':
				fileType = 'excel';
				break;
		
			default:
				fileType = 'pdf';
				break;
		}
		return fileType;
	}

	// Обработчик открытия файла при нажатии на него
	$('.starter-template,.modal-ic-komi-view,.modal-ic-komi-service-interface').on('click', '#openModalFile', function() {
		var item = $(this).data('href');	

		$('.modal-ic-komi-document-view').ModalDocumentViewIcKomi({ 'textBody' : item, 'method' : 'show' });

		/*var modal = $('#ModalWindowViewDocument');
		$(modal).find('#textModal').empty();
		$(modal).find('#textModal').html('Просмотр документа');
		$(modal).find('#bodyModal').empty();

		if($(this).data('fileFormat').trim() == 'pdf')
			$(modal).find('#bodyModal').html("<iframe class='file-frame' src='" + item + "' style='width: 100%;'></iframe>");
		else
			$(modal).find('#bodyModal').html("<img src='" + item + "' alt='Фотография' class='img-fluid rounded' style='height: 75vh;'>");

		$(modal).modal('toggle');*/
	});
});