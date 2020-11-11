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
});