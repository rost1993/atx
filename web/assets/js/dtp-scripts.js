$(document).ready(function() {
	'use strict';

	// Обработчики нажатия на кнопки
	$('.starter-template').on('click', '.btn-list-dtp,.btn-search-dtp', function() {
		var query;
		if($(this).hasClass('btn-list-dtp')) {
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
		AjaxQuery('POST', 'dtp_search', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').html(res[1]['search_result']);
			});
		});
	});

	// Обработчики нажатия на кнопки
	$('.starter-template').on('click', '.btn-list-adm-offense,.btn-search-adm-offense', function() {
		var query;
		if($(this).hasClass('btn-list-adm-offense')) {
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
		AjaxQuery('POST', 'adm_search', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('.result-list-atx').html(res[1]['search_result']);
			});
		});
	});
});