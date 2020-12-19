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
			var date;
			if($(this).data('timepicker')) {
				date = getDateTime(dd);
			} else {
				dd = ( (String(dd)).length == 4 ) ? '01.01.' + dd : dd;	
				date = getDate(dd);
			}
			//$(this).datepicker().data('datepicker').selectDate(getDate(dd));
			$(this).datepicker().data('datepicker').selectDate(date);
			$(this).datepicker().data('datepicker').update({
				autoClose: true,
				clearButton: true,
			});
		}
	});

	$('.btn-cars-old-gos-znak').popover({
		trigger: 'focus'
	});

	$('#btnNoticeEvents').popover({
		trigger: 'focus'
	});

	// Закрытие окошка popover навсегда для сессии
	$('#btnNoticeEvents').on('hidden.bs.popover', function() {
		$(this).remove();
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
		} else if(item == 17) {
			scripts = 'calibration';
			titleForm = 'Список калибровок (экспертиз)';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 18) {
			scripts = 'drivers_card';
			titleForm = 'Список карт водителя';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 19) {
			scripts = 'car_tachograph';
			titleForm = 'Список тахографов';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else if(item == 20) {
			scripts = 'car_glonass';
			titleForm = 'Список ГЛОНАСС';
			query = 'option=get_list&nsyst=' + $('#nsyst').html().trim();
		} else {
			return;
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
			scripts = 'speedometer';
			titleForm = 'Показания спидометра';
			action = 4;
			query = 'option=get_window&nsyst=-1&car=' + $('#nsyst').html();
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
		} else if(item == 17) {
			titleForm = 'Калибровка (экспертиза)';
			scripts = 'calibration';
			action = 17;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 18) {
			titleForm = 'Карта водителя';
			scripts = 'drivers_card';
			action = 18;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 19) {
			titleForm = 'Тахограф';
			scripts = 'car_tachograph';
			action = 19;
			query = 'option=get_window&nsyst=-1';
		} else if(item == 20) {
			titleForm = 'ГЛОНАСС';
			scripts = 'car_glonass';
			action = 20;
			query = 'option=get_window&nsyst=-1';
		} else {
			return;
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
			scripts = 'speedometer';
			titleForm = 'Показания спидометра';
			query = 'option=get_window&nsyst=' + id + '&car=' + $(this).data('car');
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
		} else if(item == 17) {
			scripts = 'calibration';
			titleForm = 'Калибровка (экспертиза)';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 18) {
			scripts = 'drivers_card';
			titleForm = 'Карта водителя';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 19) {
			scripts = 'car_tachograph';
			titleForm = 'Тахограф';
			query = 'option=get_window&nsyst=' + id;
		} else if(item == 20) {
			scripts = 'car_glonass';
			titleForm = 'ГЛОНАСС';
			query = 'option=get_window&nsyst=' + id;
		} else {
			return;
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
		} else if($(this).data('action') == 2) {
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
		} else if($(this).data('action') == 3) {
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
		} else if($(this).data('action') == 4) {
			var resultCollectionsItems = getArrayItemsForms('#formSpeedometer input, #formSpeedometer select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'speedometer';
		} else if($(this).data('action') == 5) {
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
		} else if($(this).data('action') == 6) {
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
		} else if($(this).data('action') == 8) {
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
		} else if($(this).data('action') == 10) {
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
		} else if($(this).data('action') == 12) {
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
		} else if($(this).data('action') == 13) {
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
		} else if($(this).data('action') == 14) {
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
		} else if($(this).data('action') == 15) {
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
		} else if($(this).data('action') == 16) {
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
		} else if($(this).data('action') == 17) {
			var resultCollectionsItems = getArrayItemsForms('#formCalibration input, #formCalibration select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'calibration';
		} else if($(this).data('action') == 18) {
			var resultCollectionsItems = getArrayItemsForms('#formDriversCard input, #formDriversCard select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_driver'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'drivers_card';
		} else if($(this).data('action') == 19) {
			var resultCollectionsItems = getArrayItemsForms('#formCarTachograph input, #formCarTachograph select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'car_tachograph';
		} else if($(this).data('action') == 20) {
			var resultCollectionsItems = getArrayItemsForms('#formGlonass input, #formGlonass select');
			if(resultCollectionsItems[0]) {
				arrayData = resultCollectionsItems[1];
			} else {
				$('#error-message').empty();
				$('#error-message').html(resultCollectionsItems[1]);
				return;
			}
			arrayData['id_car'] = {'value' : $(this).data('id'), 'type' : 'number'};
			query.append('option', 'save');
			script = 'car_glonass';
		} else {
			return;
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
			scripts = 'speedometer';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
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
		} else if(item == 17) {
			scripts = 'calibration';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 18) {
			scripts = 'drivers_card';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 19) {
			scripts = 'car_tachograph';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else if(item == 20) {
			scripts = 'car_glonass';
			query = 'option=remove&nsyst=' + $(this).data('nsyst') + '&object=' + $(this).data('object');
		} else {
			return;
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

	// Добавление спидометра
	$('#btnChangeSpeedometer').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала необходимо сохранить транспортное средство!', 'method' : 'show' });
			return;
		}
			
		showDownloader(true);
		AjaxQuery('POST', 'speedometer', 'option=add&nsyst=' + $('#nsyst').html(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Спидометр успешно добавлен!');
		});
	});

	// Удаление спилометра
	$('#btnRemoveSpeedometer').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала необходимо сохранить транспортное средство!', 'method' : 'show' });
			return;
		}
			
		showDownloader(true);
		AjaxQuery('POST', 'speedometer', 'option=del&nsyst=' + $('#nsyst').html(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Спидометр успешно удален!');
		});
	});

	// Настройки
	$('#btnSpeedometers').click(function() {
		if($('#nsyst').html().trim().length == 0 || $('#nsyst').html() == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала необходимо сохранить транспортное средство!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'speedometer', 'option=settings_speedometer&nsyst=' + $('#nsyst').html().trim(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Начальные показания спидометров', 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});

	$('.modal-ic-komi-view').on('click', '#btnSaveFirstTestimonySpeedometers', function() {
		var query = 'option=save_first_testimony&nsyst=' + $(this).data('nsyst') + '&car=' + $(this).data('idCar') + '&speedometer=' + $(this).data('idSpeedometer');
		query += '&value=' + $(this).closest('tr').find('.inputValueFirstTestimonySpeedometer').val();
		showDownloader(true);
		AjaxQuery('POST', 'speedometer', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'method' : 'hide' });
			});
		});
	});

	// Открытие окошка закрепление водителя за ТС
	$('#btnFixDriverForCar').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала необходимо сохранить транспортное средство!', 'method' : 'show' });
			return;
		}
		
		var query = '';
		if($(this).data('modeShow') == 1)
			query = 'option=driver_fix&nsyst=' + $('#nsyst').html().trim() + '&operation=' + $(this).data('operation');
		else
			query = 'option=car_fix&nsyst=' + $('#nsyst').html().trim() + '&operation=' + $(this).data('operation');

		showDownloader(true);
		AjaxQuery('POST', 'car_for_driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Закрепление водителей за транспортными средствами', 'textBody' : res[1], 'method' : 'show' });
				$("[data-datatype='date']").datepicker({
					autoClose: true,
					clearButton: true
				}).mask('99.99.9999', {placeholder: "ДД.ММ.ГГГГ"});
			});
		});
	});

	$('.modal-ic-komi-view').on('click', '#saveFixCarForDriver', function() {
		var arrSaveItem = {};	// Массив со значениями обязательных параметров
		var itemsArray = [];	// Массив со списком водителей, которых необходимо закрепить
		
		var resultCollectionsItems = getArrayItemsForms('#cardItemsFixDocument select, #cardItemsFixDocument input');
		if(resultCollectionsItems[0]) {
			arrSaveItem = resultCollectionsItems[1];
		} else {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : resultCollectionsItems[1], 'method' : 'show' });
			return;
		}

		if($(this).data('typeSave') == 1)
			arrSaveItem['car_id'] = { 'value' : $(this).data('nsyst'), 'type' : 'number' };
		else
			arrSaveItem['id_driver'] = { 'value' : $(this).data('nsyst'), 'type' : 'number' };

		// Бежим по таблице и собираем что надо закрепить
		$('#ListFixedItem tr').each(function() {
			if($(this).data('check') == '1')
				itemsArray.push($(this).prop('id'));
		});
		
		if(itemsArray.length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Вам необходимо выбрать транспортное средство или водителя для закрепления!', 'method' : 'show' });
			return;
		}
		
		arrSaveItem['dostup'] = { 'value' : 1, 'type' : 'number' };
		arrSaveItem['ibd_arx'] = { 'value' : $(this).data('ibdArx'), 'type' : 'number' };

		var query = new FormData();
		query.append('option', 'save');
		query.append('JSON', JSON.stringify(arrSaveItem));
		query.append('nsyst', $(this).data('fix'));
		query.append('arrayItemFix', JSON.stringify(itemsArray));
		query.append('typeSave', $(this).data('typeSave'));
		
		// Добавляем файл
		$.each(filesList, function(key, value) {
			query.append(key, value);
		});
		
		showDownloader(true)
		AjaxQuery('POST', 'car_for_driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'method' : 'hide' });
			});
		}, true);
		
	});

	// Обработчик выбора строки с водителем для интерфейса добавления водителей закрепленных за ТС
	$('.modal-ic-komi-view').on('click', '#checkboxTextList', function() {
		if($(this).prop('checked') == true) {
			$(this).parent().parent().addClass('table-success');
			$(this).parent().parent().data('check', '1');
		} else {
			$(this).parent().parent().removeClass('table-success');
			$(this).parent().parent().data('check', '0');
		}
	});

	// Обработчик нажатия на кнопку поиск по ФИО для списка водителей
	$('.modal-ic-komi-view').on('click', '#btnSearchListCarForDrivers', function() {
		var searchText = $('#searchFieldTextCarForDrivers').val().trim();
		var tableName = '#ListFixedItem';

		var regExp = new RegExp(searchText, 'i');
	
		$(tableName + ' tr').each(function() {
			var text = $(this).find('#textList').html().trim();
			if(regExp.test(text))
				$(this).css({ 'display' : 'table-row' });
			else
				$(this).css({ 'display' : 'none' });
		});
	});

	// Нажатие ENTER на поле ввода для поиска водителей
	$('.modal-ic-komi-view').on('keydown', '#searchFieldTextCarForDrivers', function(event) {
		if(event.keyCode === 13)
			$('#btnSearchListCarForDrivers').click();
	});

	// Обработчик нажатия на кнопку скорректировать информацию о закреплении
	$('.modal-ic-komi-view').on('click', '#btnEditCarForDrivers', function() {
		var query = '';

		if($(this).data('modeShow') == 1)
			query = 'option=driver_fix&fix=' + $(this).data('fix') + '&operation=2' + '&nsyst=' + $(this).data('nsyst');
		else
			query = 'option=car_fix&fix=' + $(this).data('fix') + '&operation=2' + '&nsyst=' + $(this).data('nsyst');

		showDownloader(true);
		AjaxQuery('POST', 'car_for_driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'textHeader' : 'Закрепление водителей за транспортными средствами', 'textBody' : res[1] });
				$("[data-datatype='date']").datepicker({
					autoClose: true,
					clearButton: true
				}).mask('99.99.9999', {placeholder: "ДД.ММ.ГГГГ"});
			});
		});
	});

	// Обработчик нажатия на кнопку переместить/восстановить из архива сведений о закреплении
	$('.modal-ic-komi-view').on('click', '#btnMoveCarForDriversArchive', function() {
		var item = $(this).closest('tr');
		var operation = $(this).data('operation');
		$(this).data('operation', (operation == 1) ? 2 : 1);
		showDownloader(true);
		AjaxQuery('POST', 'car_for_driver', 'option=move_archive&nsyst=' + $(this).data('nsyst') + '&operation=' + operation, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-view').ModalViewIcKomi({ 'method' : 'hide' });
			});
		});
	});

	// Обработчик нажатия на кнопку удалить сведения о закреплении
	$('.modal-ic-komi-view').on('click', '#btnRemoveCarForDrive', function() {
		var item = $(this).closest('tr');
		showDownloader(true);
		AjaxQuery('POST', 'car_for_driver', 'option=remove&nsyst=' + $(this).data('nsyst'), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$(item).remove();
			});
		});
	});

	// Процедура сохранения водителя
	$('#saveDrivers').click(function() {
		var arrSaveItem = {};
		var resultCollectionsItems = getArrayItemsForms('#mainDriversInformation select, #mainDriversInformation input, #mainDriversInformation textarea');
		if(resultCollectionsItems[0]) {
			arrSaveItem = resultCollectionsItems[1];
		} else {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : resultCollectionsItems[1], 'method' : 'show' });
			return;
		}
		
		var query = 'option=save&JSON=' + JSON.stringify(arrSaveItem);
		if($('#nsyst').html().trim().length == 0)
			query += '&nsyst=-1';
		else
			query += '&nsyst=' + $('#nsyst').html().trim();

		showDownloader(true);
		AjaxQuery('POST', 'driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('#nsyst').empty;
				$('#nsyst').html(res[1]);
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Информация о водителе сохранена!', 'method' : 'show' });
			});
		});
	});

	// Обработчик нажатия на кнопку перевести/восстановить из архива
	$('#btnMoveArchive').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство или водителя!', 'method' : 'show' });
			return;
		}
		
		var type_scripts = $(this).data('type');
		var scripts = (type_scripts == 1) ? 'car' : 'driver';
		var query = 'option=move_archive' + '&nsyst=' + $('#nsyst').html().trim();

		showDownloader(true);
		AjaxQuery('POST', scripts, query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {

				if($('*').is('#badgeDriverArchive')) {
					$('#badgeDriverArchive').remove();
					$('#btnMoveArchive').empty();
					$('#btnMoveArchive').html("<span class='fa fa-folder'>&nbsp;</span>Перевести в архив");
				} else {
					$('#btnMoveArchive').empty();
					$('#btnMoveArchive').html("<span class='fa fa-folder'>&nbsp;</span>Восстановить из архива");
					var htmlBadgeArchive = "<span class='badge badge-pill badge-warning' id='badgeDriverArchive'><span class='fa fa-folder'>&nbsp;</span>В архиве</span>";
					$('#cardDriversHeaderServiceBadge').append(htmlBadgeArchive);
				}

				if($('*').is('#badgeTSArchive')) {
					$('#badgeTSArchive').remove();
					$('#btnMoveArchive').empty();
					$('#btnMoveArchive').html("<span class='fa fa-folder'>&nbsp;</span>Перевести в архив");
				} else {
					$('#btnMoveArchive').empty();
					$('#btnMoveArchive').html("<span class='fa fa-folder'>&nbsp;</span>Восстановить из архива");
					var htmlBadgeArchive = "<span class='badge badge-pill badge-warning' id='badgeTSArchive'><span class='fa fa-folder'>&nbsp;</span>В архиве</span>";
					$('#cardCarsHeaderServiceBadge').append(htmlBadgeArchive);
				}
			});
		});
	});

	// Процедура защиты водителя
	$('#lockDrivers').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните водителя!', 'method' : 'show' });
			return;
		}
		var query = 'option=security' + '&nsyst=' + $('#nsyst').html().trim();
		showDownloader(true);
		AjaxQuery('POST', 'driver', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				if($('*').is('#badgeDriversSecurity')) {
					$('#badgeDriversSecurity').remove();
					$('#lockDrivers').empty();
					$('#lockDrivers').html("<span class='fa fa-lock'></span>&nbsp;Защитить водителя");
				} else {
					var htmlBadgeSecurity = "<span class='badge badge-pill badge-danger' id='badgeDriversSecurity'><span class='fa fa-lock'></span>&nbsp;Доступ к водителю ограничен</span>";
					$('#cardDriversHeaderServiceBadge').append(htmlBadgeSecurity);
					$('#lockDrivers').empty();
					$('#lockDrivers').html("<span class='fa fa-unlock'></span>&nbsp;Снять защиту с водителя");
				}
			});
		});
	});

	// Процедура удаления водителя и связанного с ней информацией
	$('#deleteDrivers').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните водителя!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'driver', 'option=remove&nsyst=' + $('#nsyst').html().trim(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				window.location = 'driver';
			});
		});
	});

	// Процедура блокировки/разблокировки ТС
	$('#lockCars').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство!', 'method' : 'show' });
			return;
		}
		var query = 'option=security' + '&nsyst=' + $('#nsyst').html().trim();
		showDownloader(true);
		AjaxQuery('POST', 'car', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				if($('*').is('#badgeTSSecurity')) {
					$('#badgeTSSecurity').remove();
					$('#lockCars').empty();
					$('#lockCars').html("<span class='fa fa-lock'></span>&nbsp;Защитить ТС");
				} else {
					var htmlBadgeSecurity = "<span class='badge badge-pill badge-danger' id='badgeTSSecurity'><span class='fa fa-lock'></span>&nbsp;Доступ к транспортному средству ограничен</span>";
					$('#cardCarsHeaderServiceBadge').append(htmlBadgeSecurity);
					$('#lockCars').empty();
					$('#lockCars').html("<span class='fa fa-unlock'></span>&nbsp;Снять защиту с ТС");
				}
			});
		});
	});

	// Включить/отключить уведомления на данное ТС
	$('#btnEnableNoticeEvents').click(function() {
		if($('#nsyst').html().trim().length == 0 || $('#nsyst').html() == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'car', 'option=notice_events&nsyst=' + $('#nsyst').html().trim(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				if($('#btnEnableNoticeEvents').find('span').hasClass('fa-bell')) {
					var span = "<span class='fa fa-bell-slash'>&nbsp;</span>Откл. уведомления";
					$('#btnEnableNoticeEvents').html(span);
					$('#cardCarsHeaderServiceBadge').find('#badgeTSNoticeEvents').remove();
				} else {
					var span = "<span class='fa fa-bell'>&nbsp;</span>Вкл. уведомления";
					$('#btnEnableNoticeEvents').html(span);
					var htmlBadgeNoticeEvents = "<span class='badge badge-pill badge-danger' id='badgeTSNoticeEvents'><span class='fa fa-bell'>&nbsp;</span>Уведомления отключены</span>";
					$('#cardCarsHeaderServiceBadge').append(htmlBadgeNoticeEvents);
				}
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Статус уведомлений изменен!', 'method' : 'show' });
			});
		});
	});

	$('#btnCarWriteOff').click(function() {
		if($('#nsyst').html().trim().length == 0 || $('#nsyst').html() == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'car', 'option=write_off&nsyst=' + $('#nsyst').html().trim() + '&operation=' + $(this).data('operation'), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				if($('.car-notice').find('.alert-yellow').length == 0) {
					$('#btnCarWriteOff').html("<span class='fa fa-trash'>&nbsp;</span>Вернуть в строй");
					$('.car-notice').html("<div class='alert alert-yellow text-center'><b>Транспортное средство готовится к списанию!<br>Транспортное средство не подлежит страхованию и проведению тех. осмотра, не выдавать новые запасные части и другие товарно-материальные ценности!</b></div>");
				} else {
					$('#btnCarWriteOff').html("<span class='fa fa-trash'>&nbsp;</span>Готовится к списанию");
					$('.car-notice').empty();
				}
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Статус изменен!', 'method' : 'show' });
			});
		});
	});

	// Функция сохранения данных о транспортном средстве
	$('#saveInfoForCars').click(function() {
		var arrSaveItem = {};
		var resultCollectionsItems = getArrayItemsForms('#mainCarsInformation select, #mainCarsInformation input, #mainCarsInformation textarea');
		if(resultCollectionsItems[0]) {
			arrSaveItem = resultCollectionsItems[1];
		} else {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : resultCollectionsItems[1], 'method' : 'show' });
			return;
		}

		var query = new FormData();
		query.append('option', 'save');
		query.append('JSON', JSON.stringify(arrSaveItem));
		
		if($('#nsyst').html().trim().length == 0)
			query.append('nsyst', -1);
		else
			query.append('nsyst', $('#nsyst').html().trim());

		$.each(filesList, function(key, value) {
			query.append(key, value);
		});
			
		showDownloader(true);
		AjaxQuery('POST', 'car', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('#nsyst').empty;
				$('#nsyst').html(res[1]);
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Информация о транспортном средстве сохранена!', 'method' : 'show' });
			});
		}, true);
	});

	$('#btnFilterNotice').click(function() {
		var query = 'option=search&status=' + $('#selectNoticeStatus').val() + '&subsystem=' + $('#selectNoticeSubsystem').val();
		showDownloader(true);
		AjaxQuery('POST', 'notice_events', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').empty();
				$('.result-list-atx').html(res[1]);
			});
		});
	});

	$('#btnInstallDatabase').click(function() {
		var query = 'action=install&login=' + $('#login_user').val() + '&password=' + $('#password_user').val();
		showDownloader(true);
		AjaxQuery('POST', 'install', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'База данных установлена!');
		});
	});

	$('#deleteCars').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'car', 'option=remove&nsyst=' + $('#nsyst').html().trim(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				window.location = 'car';
			});
		});
	});

	$('#btnGeneratePdf').click(function() {
		if($('#nsyst').html().trim().length == 0) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Сначала сохраните транспортное средство!', 'method' : 'show' });
			return;
		}
		showDownloader(true);
		AjaxQuery('POST', 'car', 'option=pdf&nsyst=' + $('#nsyst').html().trim(), function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-document-view').ModalDocumentViewIcKomi({ 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});
});