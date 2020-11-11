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
			$(this).datepicker().data('datepicker').selectDate(getDate(dd));
			$(this).datepicker().data('datepicker').update({
				autoClose: true,
				clearButton: true,
			});
		}
	});

	// Сохранение
	$('.btn-save').click(function() {
		var query = getItemsForm('#adm-ticket input, #adm-ticket select, .btn-save');
		query.append('option', 'save');
		showDownloader(true);
		AjaxQuery('POST', 'ticket', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.modal-basic-ic-komi').ModalBasicIcKomi({ 'textHeader' : 'Сохранено!', 'method' : 'show' });
				$('.btn-save').data('id', res[1]);
			});
		}, true);
	});

	$('.btn-remove').click(function() {
		var query = 'option=remove&id=' + $(this).data('id');
		showDownloader(true);
		AjaxQuery('POST', 'ticket', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				document.location.href = 'ticket';
			});
		});
	});

	$('.btn-search').click(function() {
		var query = getItemsForm('#search-block input, #search-block select');
		query.append('page', $(this).data('page'));
		query.append('excel', $(this).data('excel'));
		showDownloader(true);
		AjaxQuery('POST', 'ticket_search', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.result-block').html(res[1]);
			});
		}, true);
	});


	$('.result-block').on('click', '.btn-search', function() {
		var query = getItemsForm('#search-block input, #search-block select');
		query.append('page', $(this).data('page'));
		query.append('excel', $(this).data('excel'));
		showDownloader(true);
		AjaxQuery('POST', 'ticket_search', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.result-block').html(res[1]);
			});
		}, true);
	});

	// Обработчики выбора элемента SELECT
	$('#ST, #KODRAI, #TYPE_BLANK').change(function() {
		if($(this).prop('id') == 'ST') {
			if(($('#TYPE_BLANK').val() == 0) || ($('#TYPE_BLANK').val() === undefined))
				$('#CBC').val('');
			else if($('#TYPE_BLANK').val() == 5)
				$('#CBC').val($(this).find(':selected').data('param1'));
			else
				$('#CBC').val($(this).find(':selected').data('param2'));
		} else if($(this).prop('id') == 'KODRAI') {
			$('#OKTMO').val($(this).find(':selected').data('param1'));
		} else {
			if(($(this).val() == 4) || ($(this).val() == 5)) {
				$("[for='NUMBER_POST']").html('Номер протокола:');
				$("[for='DATE_POST']").html('Дата протокола:');
				$("#NUMBER_POST").prop('placeholder', 'Номер протокола');
				$("#DATE_POST").prop('placeholder', 'Дата протокола');
			} else {
				$("[for='NUMBER_POST']").html('Номер постановления (протокола):');
				$("[for='DATE_POST']").html('Дата постановления (протокола):');
				$("#NUMBER_POST").prop('placeholder', 'Номер постановления (протокола)');
				$("#DATE_POST").prop('placeholder', 'Дата постановления (протокола)');
			}

			$('#ST').val(0);
			$('#CBC').val('');
		}
	});

	$('.btn-generate-pdf').click(function() {
		showDownloader(true);
		AjaxQuery('POST', 'ticket', 'nsyst=' + $(this).data('id') + '&option=pdf', function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.modal-document-view-ic-komi').ModalDocumentViewIcKomi({ 'textBody' : res[1], 'method' : 'show' });
			});
		});
	});
});