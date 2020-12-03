$(document).ready(function() {
	'use strict';

	// Показатеть все связи
	$('#btnShowListLink').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните сведения о документе!', 'method' : 'show' });
			return false;
		}
		var query = 'option=get_list&nsyst=' + $('#nsyst').html().trim() + '&item=' + $(this).data('item');
		showDownloader(true);
		AjaxQuery('POST', 'car_document', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Список', 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});

	// Обработчик нажатия на кнопку открыть окно с добавлением услуги
	$('#addCarsLinkDocument').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните сведения о документе!', 'method' : 'show' });
			return false;
		}
		//var query = ($(this).data('item') == 'document') ? 'option=4' : 'option=5';
		var query = 'option=get_window&nsyst=' + $('#nsyst').html().trim() + '&item=' + $(this).data('item');
		showDownloader(true);
		AjaxQuery('POST', 'car_document', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Информационная карточка со списком связей', 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});

	$('.modal-ic-komi-view').on('change', "[type='checkbox']", function() {
		if($(this).prop('checked')) {
			$(this).closest('tr').addClass('table-success');
			$(this).closest('tr').data('save', 1);
		} else {
			$(this).closest('tr').removeClass('table-success');
			$(this).closest('tr').data('save', 0);
		}
	});

	// Обработчик нажатия на Enter
	//$('#ContextOutputInterface').on('keydown', '#searchText', function(event) {
	$('.modal-ic-komi-view').on('keydown', '#searchText', function(event) {
		if(event.keyCode == 13) {
			$(this).closest('.card').find('#btnSearchListText').click();
		}
	});
	
	// Кнопка поиска на интерфейса доп поиска
	//$('#ContextOutputInterface').on('click', '#btnSearchListText', function() {
	$('.modal-ic-komi-view').on('click', '#btnSearchListText', function() {
		var search_text = $(this).closest('.card').find('#searchText').val();
		var table = $(this).closest('.card').find('table');
		// Регулярное выражение для поиска ФИО
		var regExp = new RegExp(search_text, 'i');
		
		$('tr', table).each(function() {
			if(regExp.test($(this).find('#kodrai').html()))
				$(this).css({ 'display' : 'table-row' });
			else
				$(this).css({ 'display' : 'none' });
		});
	});

	// Edit link car <-> document
	$('.modal-ic-komi-view').on('click', '.btnEditLinkCarDocument', function() {
		showDownloader(true);
		AjaxQuery('POST', 'car_document', 'option=get_window_edit&nsyst=' + $(this).data('id') + '&item=' + $(this).data('item'), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Информационная карточка со списком связей', 'textBody' : res[1], 'method' : 'none' });
			});
		});
	});

	$('#btnSaveCarDocument').click(function() {
		var arrayData = [];
		var resultCollectionsItems = getArrayItemsForms('#mainInformationCarDocument input,#mainInformationCarDocument select,#mainInformationCarDocument textarea');
		if(resultCollectionsItems[0]) {
			arrayData = resultCollectionsItems[1];
		} else {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : resultCollectionsItems[1], 'method' : 'show' });
			return;
		}

		var query = new FormData();
		query.append('option', 'save');
		query.append('JSON', JSON.stringify(arrayData));
		if($('#nsyst').html().trim().length == 0)
			query.append('nsyst', -1);
		else
			query.append('nsyst', $('#nsyst').html().trim());

		$.each(filesList, function(key, value) {
			query.append(key, value);
		});
		filesList = [];

		AjaxQuery('POST', 'car_document', query, function(result) {
			showDownloader(false);
			try {
				var res = eval(result);
				if(res[0] == -1) {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла ошибка!', 'method' : 'show' });
				} else if(res[0] == -2) {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сведния сохранены, но при сохранении файла произошла ошибка!', 'method' : 'show' });
					$('#nsyst').html(res[1]);
				} else if(res[0] == 1) {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сохранено!', 'method' : 'show' });
					$('#nsyst').html(res[1]);
				} else {
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла непредвиденная ошибка!', 'method' : 'show' });
				}
			} catch(error) {

			}
		}, true);
	});

	$('#btnRemoveCarDocument').click(function() {
		var query = 'option=remove&nsyst=' + $('#nsyst').html();
		showDownloader(true);
		AjaxQuery('POST', 'car_document', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				document.location.href = 'car_document';
			});
		});
	});

	$('.modal-ic-komi-view').on('click', '#btnSaveLinkCarDocument', function() {
		var comment_doc = $(this).closest('.modal-ic-komi-view').find('.card-deck').find('#comment').val();
		var title_doc = $(this).closest('.modal-ic-komi-view').find('.card-deck').find('#title_document').val();
		var arrSaveItem = {};
		$('#list-object tr').each(function() {
			if($(this).data('save') == 1) {
				var arr = [comment_doc, title_doc];
				arrSaveItem[$(this).prop('id')] = arr;
			}
		});
		var query = 'option=save_link&JSON=' + JSON.stringify(arrSaveItem) + '&nsyst=' + $(this).data('id') + '&save=' + $(this).data('typeSave');
		query += ($(this).data('nsyst') == -1) ? '&action=insert' : '&action=update';
		showDownloader(true);
		AjaxQuery('POST', 'car_document', query, function(result) {
			showDownloader(false);
			try {
				var res = eval(result);
				if(res[0] == -1)
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла ошибка!', 'method' : 'show' });
				else if(res[0] == 1)
					$('.modal-ic-komi-view').ModalViewIcKomi({ 'method' : 'hide' });
				else
					$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'При обработке запроса произошла непредвиденная ошибка!', 'method' : 'show' });
			} catch(error) {

			}

			
		});
	});
});