$(document).ready(function() {
	'use strict';

	// Определяем на какой странице запустился JavaScript
	var page = location.pathname.match(/[^/]*$/).toString().replace(".php", "");
	if(page.length == 0)
		page = 'index';
	$('#' + page).addClass('active');

	// Работаем с календарем
	$('.datepicker-here').each(function() {
		if(($(this).val() === undefined) || (String($(this).val()).trim().length == 0)) {
			$(this).datepicker().data('datepicker').update({
				autoClose: true,
				clearButton: true,
			});
		} else {
			var dd = $(this).val();
			dd = ( (String(dd)).length == 4 ) ? '01.01.' + dd : dd;
			$(this).datepicker().data('datepicker').selectDate(getDate(dd));
			$(this).datepicker().data('datepicker').update({
				autoClose: true,
				clearButton: true,
			});
		}
	});

	$('.btn-cars-old-gos-znak').popover({
		trigger: 'focus'
	});

	// Сохранение
	$('.btn-save').click(function() {
		var query = getItemsForm('#atx-form input, #atx-form select, .btn-save');
		query.append('option', 'save');
		showDownloader(true);
		AjaxQuery('POST', 'driver', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сохранено!', 'method' : 'show' });
				$('.btn-save').data('id', res[1]);
			});
		}, true);
	});

	$('.starter-template').on('click', '.btn-list-cars,.btn-search-cars', function() {
		var query;
		if($(this).hasClass('btn-list-cars')) {
			query = 'page=' + $(this).data('page');
		} else {
			var arrSaveItem = {};
			var resultArray = {};
			var resultCollectionsItems = getArrayItemsForms('.search-block select, .search-block input');
			if(resultCollectionsItems[0]) {
				arrSaveItem = resultCollectionsItems[1];
			} else {
				showModal('ModalWindow', resultCollectionsItems[1]);
				return;
			}
			query = 'JSON=' + JSON.stringify(arrSaveItem) + '&page=' + $(this).data('page') + '&excel=' + $(this).data('excel');
		}

		showDownloader(true);
		AjaxQuery('POST', 'car_search', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').html(res[1]['search_result']);
			});
		});
	});

	// Поиск водителей
	$('.starter-template').on('click', '.btn-search-drivers,.btn-list-drivers', function() {
		var query;
		if($(this).hasClass('btn-list-drivers')) {
			query = 'archive=2&page=' + $(this).data('page');
		} else {
			var arrSaveItem = {};
			var resultArray = {};
			var resultCollectionsItems = getArrayItemsForms('.search-block select, .search-block input');
			if(resultCollectionsItems[0]) {
				arrSaveItem = resultCollectionsItems[1];
			} else {
				showModal('ModalWindow', resultCollectionsItems[1]);
				return;
			}
			query = 'JSON=' + JSON.stringify(arrSaveItem) + '&page=' + $(this).data('page') + '&excel=' + $(this).data('excel');
		}

		showDownloader(true);
		AjaxQuery('POST', 'driver_search', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').html(res[1]['search_result']);
			});
		});
	});

	// Функция открытия окна с историей ПТС, ОСАГО, Техосмотра, показателей спидометра
	$('.btnShowList').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство или водителя!', 'method' : 'show' });
			return;
		}
		
		var item = $(this).data('item');
		var scripts, titleForm, query;
		if(item == 1) {
			scripts = 'vu';
			titleForm = 'Информационная карточка со списком водительских удостоверений';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim() + '&class=' + $(this).data('class');
		} else if(item == 2) {
			scripts = 'osago';
			titleForm = 'Информационная карточка со списком полисов ОСАГО';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 3) {
			scripts = 'technical_inspection';
			titleForm = 'Информационная карточка со списком технических осмотров транспортного средства';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 4) {
			scripts = 'speedometer';
			titleForm = 'Информационная карточка со списком показаний спидометра';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 5){
			scripts = 'pts';
			titleForm = 'Информационная карточка со списком ПТС';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 6){
			scripts = 'certificate_registration';
			titleForm = 'Информационная карточка со списком свидетельств о регистрации ТС';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 7){
			scripts = 'repair';
			titleForm = 'Информационная карточка со списком ремонтов ТС';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 8){
			scripts = 'car-documents-events.php';
			titleForm = 'Информационная карточка со списком документов на ТС';
			query = 'option=3&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 9) {
			scripts = 'dtp';
			titleForm = 'Информационная карточка со списком ДТП';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim() + '&type=' + $(this).data('type');
		} else if(item == 10) {
			scripts = 'tractor_vu';
			titleForm = 'Информационная карточка со списком удостоверений тракториста-машиниста';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 11) {
			scripts = 'adm';
			titleForm = 'Информационная карточка со списком административных правонарушений';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim() + '&type=' + $(this).data('type');
		} else if(item == 12) {
			scripts = 'accessories';
			titleForm = $(this).data('titleForm');
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim() + '&object=' + $(this).data('object');
		} else if(item == 13) {
			scripts = 'wheel';
			titleForm = 'Список шин';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 14) {
			scripts = 'cranvu';
			titleForm = 'Список удостоверений';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 15) {
			scripts = 'drivers_dopog';
			titleForm = 'Список свидетельство ДОПОГ';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 16) {
			scripts = 'cars_dopog';
			titleForm = 'Список свидетельство ДОПОГ';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		}

		showDownloader(true);
		AjaxQuery('POST', scripts, query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : titleForm, 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});

	// Обработчик нажатия на кнопку открыть историю закреплений водителей за данным ТС
	$('#btnShowListFixDriverForCar').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			showModal('ModalWindow', 'Сначала необходимо сохранить транспортное средство или водителя!');
			return;
		}
		var query = 'option=get_list&nsyst=' + $('#nsyst').html() + '&type=' + $(this).data('modeShow');
		showDownloader(true);
		AjaxQuery('POST', 'car_for_driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Сведения о закреплении водителей за ТС', 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});

	// Функция открытия окна добавления ПТС, ОСАГО, Техосмотра, показателей спидометра
	$('.btnAddItem').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство или водителя!', 'method' : 'show' });
			return;
		}

		//filesList = [];	// Обнуляем массив с файлами
		
		var object;
		var item = $(this).data('item');
		var scripts, titleForm, action, query;
		if(item == 1) {
			scripts = 'vu';
			titleForm = 'Водительское удостоверение';
			action = 1;
			//object = $(this).data('class');
			query = 'option=get_window&nsyst=-1&class=' + object;
		} else if(item == 2) {
			scripts = 'osago';
			titleForm = 'Полис ОСАГО';
			action = 2;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 3) {
			scripts = 'technical_inspection';
			titleForm = 'Технический осмотр транспортного средства';
			action = 3;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 4) {
			scripts = 'speedometer-events.php';
			titleForm = 'Показания спидометра';
			action = 4;
			query = 'option=1&nsyst=-1&car=' + $('#nsyst').html();
		} else if(item == 5){
			scripts = 'pts';
			titleForm = 'Паспорт технического средства';
			action = 5;
			query = 'option=get_window&nsyst=-1';
		} else if (item == 6){
			scripts = 'certificate_registration';
			titleForm = 'Свидетельство о регистрации';
			action = 6;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 8){
			scripts = 'car-documents-events.php';
			titleForm = 'Документ на ТС';
			action = 8;
			query = 'option=1&nsyst=-1';
		} else if(item == 10) {
			scripts = 'tractor_vu';
			titleForm = 'Удостоверение тракториста-машиниста';
			action = 10;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 12) {
			scripts = 'accessories';
			titleForm = $(this).data('titleForm');
			action = 12;
			object = $(this).data('object');
			query = 'option=get_window&nsyst=-1&object=' + object;
		} else if(item == 13) {
			titleForm = 'Учет шин';
			scripts = 'wheel';
			action = 13;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 14) {
			titleForm = 'Удостоверение';
			scripts = 'cranvu';
			action = 14;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 15) {
			titleForm = 'Свидетельство ДОПОГ';
			scripts = 'drivers_dopog';
			action = 15;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 16) {
			titleForm = 'Свидетельство ДОПОГ';
			scripts = 'cars_dopog';
			action = 16;
			query = 'option=get_window&nsyst=-1';
		}

		showDownloader(true);
		AjaxQuery('POST', scripts, query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-service-interface').ModalViewServiceInterfaceIcKomi({ 'textHeader' : titleForm, 'textBody' : res[1], 'method' : 'show' });
				$('#saveModalWindowButton').data('action', action);
				$('#saveModalWindowButton').data('nsyst', 0);
				$('#saveModalWindowButton').data('id', ($('#nsyst').html().trim().length == 0) ? 0 : $('#nsyst').html().trim());
				if(item == 1)
					$('#saveModalWindowButton').data('object', object);

				if(item == 12)
					$('#saveModalWindowButton').data('object', object);
				$("[data-datatype='date']").datepicker({
					autoClose: true,
					clearButton: true
				}).mask('99.99.9999', {placeholder: "ДД.ММ.ГГГГ"});
			});
		});
	});

	// Общий обработчик нажатия на кнопку скорректировать объект
	$('.modal-ic-komi-view').on('click', '#btnEditItem', function() {
		var item = $(this).data('item');
		var id = $(this).data('nsyst');
		var scripts, titleForm, query, object;
		scripts = titleForm = query = '';
		if(item == 1) {
			scripts = 'vu';
			titleForm = 'Водительское удостоверение';
			object = $(this).data('class');
			query = 'option=get_window&nsyst=' + id + "&class=" + object;
		} else if(item == 2) {
			scripts = 'osago';
			titleForm = 'Полис ОСАГО';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 3) {
			scripts = 'technical_inspection';
			titleForm = 'Технический осмотр транспортного средства';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 4) {
			scripts = 'speedometer-events.php';
			titleForm = 'Показания спидометра';
			query = 'option=1&nsyst=' + id + '&car=' + $(this).data('car');
		} else if(item == 5){
			scripts = 'pts';
			titleForm = 'Паспорт технического средства';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 6){
			scripts = 'certificate_registration';
			titleForm = 'Свидетельство о регистрации';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 8) {
			scripts = 'car-documents-events.php';
			titleForm = 'Документ на ТС';
			query = 'option=1&nsyst=' + id;
		} else if(item == 10) {
			scripts = 'tractor_vu';
			titleForm = 'Удостоверение тракториста-машиниста';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 12) {
			scripts = 'accessories';
			titleForm = $(this).data('titleForm');
			object = $(this).data('object');
			query = 'option=get_window&nsyst=' + id + '&object=' + $(this).data('object');
		} else if(item == 13) {
			scripts = 'wheel';
			titleForm = 'Учет шин';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 14) {
			scripts = 'cranvu';
			titleForm = 'Удостоверение';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 15) {
			scripts = 'drivers_dopog';
			titleForm = 'Свидетельство ДОПОГ';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 16) {
			scripts = 'cars_dopog';
			titleForm = 'Свидетельство ДОПОГ';
			query = 'option=get_window&nsyst=' + id;
		}
		
		showDownloader(true);
		AjaxQuery('POST', scripts, query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-service-interface').ModalViewServiceInterfaceIcKomi({ 'textHeader' : titleForm, 'textBody' : res[1], 'method' : 'show' });
				$('#saveModalWindowButton').data('action', item);
				$('#saveModalWindowButton').data('nsyst', id);
				$('#saveModalWindowButton').data('id', ($('#nsyst').html().trim().length == 0) ? 0 : $('#nsyst').html().trim());

				if(item == 1)
					$('#saveModalWindowButton').data('object', object);
					
				if(item == 12)
					$('#saveModalWindowButton').data('object', object);
				$("[data-datatype='date']").datepicker({
					autoClose: true,
					clearButton: true
				}).mask('99.99.9999', {placeholder: "ДД.ММ.ГГГГ"});
			});
		});
	});

	// Обработчик нажатия на кнопку сохранить объект
	//$('.modal-ic-komi-service-interface').click(function(result) {
	$('.modal-ic-komi-service-interface').on('click', '#saveModalWindowButton',function() {
		if($(this).data('id') == 0 || $(this).data('id') === undefined) {
			$('#error-message').empty();
			if($(this).data('action') == 1)
				$('#error-message').html('Вам необходимо сначала сохранить водителя!');
			else
				$('#error-message').html('Вам необходимо сначала сохранить транспортное средство!');
			return;
		}
		var PATH_TO_SCRIPT;
		var arrayData = {};
		//var query = '';
		var query = new FormData();
		var script = '';
		
		var tempAction = $(this).data('action');
		
		// По коду операции определяем необходимое действие
		if($(this).data('action') == 1) {
			var resultCollectionsItems = getArrayItemsForms('#VU input');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_driver'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('class', $(this).data('object'));
			query.append('option', 'save');
			script = 'vu';
		}
		
		if($(this).data('action') == 2) {
			var resultCollectionsItems = getArrayItemsForms('#formOsago input, #formOsago select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'osago';
		}
		
		if($(this).data('action') == 3) {
			var resultCollectionsItems = getArrayItemsForms('#formTechnicalInspection input, #formTechnicalInspection select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'technical_inspection';
		}
		
		if($(this).data('action') == 4) {
			var resultCollectionsItems = getArrayItemsForms('#formSpeedometer input, #formSpeedometer select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};

			query.append('option', 2);
			script = PATH_TO_SCRIPT + 'speedometer-events.php';
		}

		if($(this).data('action') == 5) {
			var resultCollectionsItems = getArrayItemsForms('#formPts input, #formPts select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'pts';
		}
		
		if($(this).data('action') == 6) {
			var resultCollectionsItems = getArrayItemsForms('#formCertificate input, #formCertificate select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};

			query.append('option', 'save');
			script = 'certificate_registration';			
		}

		if($(this).data('action') == 8) {
			var resultCollectionsItems = getArrayItemsForms('#formCarDocument input, #formCarDocument select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 2);
			script = PATH_TO_SCRIPT + 'car-documents-events.php';
		}
		
		if($(this).data('action') == 10) {
			var resultCollectionsItems = getArrayItemsForms('#TractorVU input, #TractorVU select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_driver'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'tractor_vu';
		}
		
		if($(this).data('action') == 12) {
			var resultCollectionsItems = getArrayItemsForms('#formAccessories input');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			query.append('object', $(this).data('object'));
			
			script = 'accessories';
		}

		if($(this).data('action') == 13) {
			var resultCollectionsItems = getArrayItemsForms('#formWheel input, #formWheel select,#formWheel textarea');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'wheel';
		}

		if($(this).data('action') == 14) {
			var resultCollectionsItems = getArrayItemsForms('#formCranVu input, #formCranVu select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_driver'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'cranvu';
		}

		if($(this).data('action') == 15) {
			var resultCollectionsItems = getArrayItemsForms('#formDriversDopog input, #formDriversDopog select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_driver'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'drivers_dopog';
		}

		if($(this).data('action') == 16) {
			var resultCollectionsItems = getArrayItemsForms('#formCarsDopog input, #formCarsDopog select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'cars_dopog';
		}
		
		$.each(filesList, function(key, value) {
			query.append(key, value);
		});

		query.append('idobject', $(this).data('id'));
		filesList = [];

		query.append('JSON', JSON.stringify(arrayData));
		if($(this).data('nsyst') == 0)
			query.append('nsyst', -1);
		else
			query.append('nsyst', $(this).data('nsyst'));

		showDownloader(true);
		AjaxQuery('POST', script, query, function(result) {
			showDownloader(false);
			try {
				var res = eval(result);
				if(res[0] == 1) {
					$('.modal-ic-komi-service-interface').ModalViewServiceInterfaceIcKomi({'method' : 'hide'});
				} else {
					$('#error-message').empty();
					$('#error-message').html('При сохранении произошла ошибка!');
				}
			} catch(error) {
				$('#error-message').empty();
				$('#error-message').html('При сохранении произошла неопределенная ошибка!');
			}
		}, true);
	});

	// Общий обработчик нажатия на кнопку удалить объект
	//$('#ContextOutputInterface').on('click', '#btnRemoveItem', function() {
	$('.modal-ic-komi-view').on('click', '#btnRemoveItem', function() {
		var obj = $(this).closest('tr');
		var item = $(this).data('item');
		var scripts = '', query = '';
		if(item == 1) {
			scripts = 'vu';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 2) {
			scripts = 'osago';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 3) {
			scripts = 'technical_inspection';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 4) {
			scripts = 'speedometer-events.php';
			query = 'option=4&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 5){
			scripts = 'pts';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 6){
			scripts = 'certificate_registration';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 8) {
			scripts = 'car-documents-events.php';
			query = 'option=4&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 10) {
			scripts = 'tractor_vu';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 12) {
			scripts = 'accessories';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object') + '&id=' + $(this).data('idObject');
		} else if(item == 13) {
			scripts = 'wheel';
			query = 'option=remove&nsyst=' + $(this).data('nsyst');
		} else if(item == 14) {
			scripts = 'cranvu';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 15) {
			scripts = 'drivers_dopog';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 16) {
			scripts = 'cars_dopog';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		}
		
		AjaxQuery('POST', scripts, query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$(obj).remove();
			});
		});
	});

	// Обработчик нажатия на модальном окне 
	$('.modal-ic-komi-service-interface').on('change', "[type='number']", function() {
		var item = $(this).closest('.modal-ic-komi-service-interface').find('#start_date').val();
		if(item === undefined || item.length == 0)
			return;
		var dd = new Date(Number(item.substr(6, 4)) + Number($(this).val()), item.substr(3, 2) - 1, item.substr(0, 2));
		var datepicker = $(this).closest('.modal-ic-komi-service-interface').find('#end_date').datepicker().data('datepicker');
		datepicker.selectDate(dd);
	});
});