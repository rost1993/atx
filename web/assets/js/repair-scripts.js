$(document).ready(function() {
	'use strict';

	// Обработчики поиска и построения списка ремонтов
	$('.starter-template').on('click', '.btn-list-repair,.btn-search-repair', function() {
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
		AjaxQuery('POST', 'repair_search', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').html(res[1]['search_result']);
			});			
		});
	});

	$('#btnSaveRepair').click(function() {
		var arrayData = [];
		var resultCollectionsItems = getArrayItemsForms('#mainRepairsInformation input, #mainRepairsInformation select, #mainRepairsInformation checkbox,#mainRepairsInformation textarea');
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

		AjaxQuery('POST', 'repair', query, function(result) {
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

	$('#btnRemoveRepair').click(function() {
		var query = 'option=remove&nsyst=' + $('#nsyst').html();
		showDownloader(true);
		AjaxQuery('POST', 'repair', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				document.location.href = 'repair';
			});
		});
	});
});