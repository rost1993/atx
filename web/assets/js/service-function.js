'use strict';

function AjaxQuery(method, url, query, callback, fileDownload, statusError)
{
	var result;
	var cacheVal, processDataVal, contentTypeVal;
	
	fileDownload = (typeof fileDownload !== undefined) ? fileDownload : false;
	
	if(fileDownload === undefined || fileDownload == false) {
		cacheVal = true;
		processDataVal = true;
		contentTypeVal = 'application/x-www-form-urlencoded';
	} else {
		cacheVal = false;
		processDataVal = false;
		contentTypeVal = false;
	}
	
	$.ajax({
		type: method,
		url: url,
		data: query,
		cache: cacheVal,
		processData: processDataVal,
		contentType: contentTypeVal,
		
		success: function(data) {
			if(callback != null)
				callback(data);
		},
		
		complete: function() {
			//callback(result);
		},
		
		error: function(data, status, xhr) {
			/*if(statusError === undefined)
				showModal('ModalWindow', 'Ошибка при выполнении запроса! Повторите запрос или обратитесь к администратору!');
			return;*/
		},
		
		statusCode: {
			404: function() {
				document.location.href = '/not-found';
			}
		}
	});
}

function getItemsForm(searchItems) {
	var form = new FormData();

	$(searchItems).each(function() {
		var id = (($(this).prop('id') === undefined) || ($(this).prop('id') == null) || (String($(this).prop('id')).length == 0)) ? $(this).prop('name') : $(this).prop('id');

		if(id !== undefined) {
			if($(this).prop('tagName').toUpperCase() == 'SELECT') {
				form.append(id, (($(this).val() == null) || ($(this).val() === undefined)) ? 0 : $(this).val());
			} else if($(this).prop('tagName').toUpperCase() == 'CHECKBOX') {
				form.append(id, $(this).prop('checked'));
			} else if($(this).prop('tagName').toUpperCase() == 'BUTTON') {
				if(($(this).data('id') !== undefined) && ($(this).data('id') != -1))
					form.append('id', $(this).data('id'));
			} else {
				form.append(id, $(this).val().trim());
			}
		}
	});
	return form;
}

/* 
 * Функция всплытия модального окна.
 * message - сообщение, которое необходимо вывести
 * modalName - ID модального окна, которое необходимо вызвать
 * modalOpen - параметр, если установлен в TRUE, то добавляет класс modal-open и убирает полосу прокрутки (по умолчанию FALSE)
 */
function showModal(modalName, message, modalOpen)
{
	// Всегда скрываем оконо загрузчика
	//showDownloader(false);
	
	document.getElementById(modalName).getElementsByTagName('h4')[0].innerHTML = message;	
	$('#' + modalName).modal('toggle');

	// Вызываем глобальную потерю фокуса с кнопок и устанавливаем фокус на кнопке закрытия
	$('.btn').blur();
	$('#' + modalName).on('shown.bs.modal', function() {
		$('#' + modalName).find('#closeButton').focus();
	});
}

/* 
	Функция всплытия модального окна.
 	message - сообщение, которое необходимо вывести
 	modalName - ID модального окна, которое необходимо вызвать
 	modalOpen - параметр, если установлен в TRUE, то добавляет класс modal-open и убирает полосу прокрутки (по умолчанию FALSE)
 */
function showModalError(modalName, msgHeader, msgBody, modalOpen)
{
	// Всегда скрываем оконо загрузчика
	//showDownloader(false);
	
	$('#' + modalName).find('.modal-header').find('h4').html(msgHeader);
	$('#' + modalName).find('.modal-body').html(msgBody);
	$('#' + modalName).modal('toggle');

	// Вызываем глобальную потерю фокуса с кнопок и устанавливаем фокус на кнопке закрытия
	$('.btn').blur();
	$('#' + modalName).on('shown.bs.modal', function() {
		$('#' + modalName).find('#closeButton').focus();
	});
}

// Функция обработки результатов Ajax-запроса
function handlerAjaxResult(result, success_text, callback) {
	try {
		showDownloader(false);
		var res = eval(result);
		if(res[0] == -1) {
			try {
				if(res[1] === undefined || String(res[1]).length == 0) {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла ошибка!', 'method' : 'show' });
				} else {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : res[1], 'method' : 'show' });
				}
			} catch(error) {
				$('.modal-basic-ic-komi').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла ошибка!', 'method' : 'show' });
			}
		} else if(res[0] == -2) {
			try {
				var msg_error = eval(res[1]);
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Логическое условие&nbsp;№&nbsp;' + msg_error[0], 'textBody' : msg_error[1], 'method' : 'show' });
			} catch(error) {
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Ошибка!', 'textBody' : res[1], 'method' : 'show' });
			}
			
		} else if(res[0] == 1) {
			if((callback === null) || (callback === undefined)) {
				var text = ((String(success_text).length == 0) || (success_text == null) || (success_text === 'undefined')) ? 'Выполнено!' : success_text;
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : text, 'method' : 'show' });
			} else {
				callback(res);
			}
		} else {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла непредвиденная ошибка!', 'method' : 'show' });
		}
	} catch(error) {
		$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Неопределенная ошибка!', 'method' : 'show' });
	}
}

// Преобразователь из строки в дату
function getDate(str) {
	if(String(str).length != 10)
		return null;

	// Пытаемся определить delemiter даты
	var temp = String(str).split('.');
	if(temp.length != 3)
		temp = String(str).split('-');
	
	if(temp.length != 3)
		temp = String(str).split('/');

	if(temp.length != 3)
		return null;

	return new Date(temp[2], (Number(temp[1]) - 1), temp[0]);
}

/*
	Function show or hide indicator downloader
	mode - true or false
*/
function showDownloader(mode) {
	try {
		if(mode)
			$('.downloader-ic-komi').DownloaderIcKomi('show');
		else
			$('.downloader-ic-komi').DownloaderIcKomi('hide');
	} catch(error) {
	}
}

function getArrayItemsForms(searchItems) {
	var flgCheck = true;
	var messageError = '';
	var arrSaveItem = {};
	
	$(searchItems).each(function() {
		if($(this).data('mandatory')) {
			if($(this).prop('tagName').toUpperCase() == 'SELECT') {
				if($(this).val() == 0 || $(this).val() === undefined) {
					messageError = $(this).data('messageError');
					flgCheck = false;
					return false;
				}
			} else {
				if($(this).val().trim().length == 0) {
					messageError = $(this).data('messageError');
					flgCheck = false;
					return false;
				}
			}
			
			var nameItem = $(this).prop('id');
			var arrayTemp = {}
			
			if($(this).prop('type') == 'CHECKBOX')
				arrayTemp['value'] = encodeURIComponent($(this).prop('checked'));
			else
				arrayTemp['value'] = encodeURIComponent($(this).val().trim().toUpperCase());
			
			arrayTemp['type'] = $(this).data('datatype');
			arrSaveItem[nameItem] = arrayTemp;
		} else {
			flgCheck = true;
			var nameItem = $(this).prop('id');
			var arrayTemp = {};
			
			arrayTemp['type'] = $(this).data('datatype');
			
			if($(this).prop('tagName').toUpperCase() == 'SELECT') {
				arrayTemp['value'] = encodeURIComponent($(this).val());
			} else if($(this).prop('type').toUpperCase() == 'CHECKBOX') {
				arrayTemp['value'] = encodeURIComponent($(this).prop('checked'));
			} else if($(this).prop('type').toUpperCase() == 'RADIO') {
				if($(this).prop('checked')) {
					nameItem = $(this).prop('name');
					arrayTemp['value'] = encodeURIComponent($(this).val());
				} else {
					flgCheck = false;
				}
			} else {
				arrayTemp['value'] = encodeURIComponent($(this).val().trim().toUpperCase());
			}

			if(flgCheck)
				arrSaveItem[nameItem] = arrayTemp;
			flgCheck = true;
		}
	});
	
	var arrayResult = {};
	
	if(!flgCheck) {
		arrayResult[0] = false;
		arrayResult[1] = messageError;
	} else {
		arrayResult[0] = true;
		arrayResult[1] = arrSaveItem;
	}

	return arrayResult;
}