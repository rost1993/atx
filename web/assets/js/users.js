$(document).ready(function() {
	'use strict';

	$('.eye-password').click(function() {
		$(this).closest('.form-row').find('input').prop('type', (($(this).closest('.form-row').find('input').prop('type') == 'text') ? 'password' : 'text'));
		if($(this).find('span').hasClass('fa-eye'))
			$(this).find('span').removeClass('fa-eye').addClass('fa-eye-slash');
		else
			$(this).find('span').removeClass('fa-eye-slash').addClass('fa-eye');
	});

	// Auth web-resource
	$('.btn-login').click(function() {
		var query = getItemsForm('#user-auth input');
		AjaxQuery('POST', '/login?action=auth', query, function(result) {
			handlerAjaxResult(result, null, function() {
				window.location = '/';
			});
		}, true);
	});

	// Registration a new user
	$('.btn-registration').click(function() {
		var query = getItemsForm('#user-registration input, #user-registration select');
		AjaxQuery('POST', '/login?action=registration', query, function(result) {
			handlerAjaxResult(result, 'Пользователь зарегистрирован!');
		}, true);
	});

	// Keydown 'Enter'
	//$("input[type='password']").keydown(function(event) {
	$("#Section1 input[type='password']").keydown(function(event) {
		if(event.keyCode === 13)
			$('.btn-login').click();
	});

	$('#admin-panel').on('click', '.btn-access-user', function() {
		var id = $(this).closest('tr').prop('id');
		var hash = $(this).closest('tr').data('hash');

		if(id === undefined || hash === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Ошибка при захвате параметров', 'method' : 'show' });
			return;
		}
		var item = $(this);
		var query = 'option=access&id=' + id + '&hash=' + hash;
		showDownloader(true);
		AjaxQuery('POST', 'admin_panel', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Настройки сохранены!', 'method' : 'show' });
				if($(item).hasClass('btn-success')) {
					$(item).removeClass('btn-success');
					$(item).addClass('btn-danger');
					$(item).find('span').removeClass('fa-thumbs-up');
					$(item).find('span').addClass('fa-thumbs-down');
				} else {
					$(item).removeClass('btn-danger');
					$(item).addClass('btn-success');
					$(item).find('span').removeClass('fa-thumbs-down');
					$(item).find('span').addClass('fa-thumbs-up');
				}
			});
		});
	});

	$('#admin-panel').on('click', '.btn-remove-user', function() {
		var id = $(this).closest('tr').prop('id');
		var hash = $(this).closest('tr').data('hash');

		if(id === undefined || hash === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Ошибка при захвате параметров', 'method' : 'show' });
			return;
		}
		var item = $(this).closest('tr');
		var query = 'option=archive&id=' + id + '&hash=' + hash;
		showDownloader(true);
		AjaxQuery('POST', 'admin_panel', query, function(result) {
			handlerAjaxResult(result, null, function(res) {
				$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Настройки сохранены!', 'method' : 'show' });
				$(item).remove();
			});
		});
	});

	$('#admin-panel').on('click', '.btn-change-default-password', function() {
		var id = $(this).closest('tr').prop('id');
		var hash = $(this).closest('tr').data('hash');

		if(id === undefined || hash === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Ошибка при захвате параметров', 'method' : 'show' });
			return;
		}
		var query = 'option=reset&id=' + id + '&hash=' + hash;
		showDownloader(true);
		AjaxQuery('POST', 'admin_panel', query, function(result) {
			handlerAjaxResult(result, 'Пароль сброшен на пароль по умолчанию');
		});
	});

	$('#admin-panel').on('change', '.change-role', function() {
		var id = $(this).closest('tr').prop('id');
		var hash = $(this).closest('tr').data('hash');
		var role = $(this).val();

		if(id === undefined || hash === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Ошибка при захвате параметров', 'method' : 'show' });
			return;
		}
		var query = 'option=change_role&id=' + id + '&hash=' + hash + '&role=' + role;
		showDownloader(true);
		AjaxQuery('POST', 'admin_panel', query, function(result) {
			handlerAjaxResult(result, 'Роль пользователя изменена');
		});
	});

	$('#btn-save-user-data').click(function() {
		var query = getItemsForm('#user-information input');
		query.append('option', 'save');
		showDownloader(true);
		AjaxQuery('POST', 'edit', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Настройки изменены');
		}, true);
	});

	$('#btn-change-user-password').click(function() {
		var query = getItemsForm('#user-information-password input');
		query.append('option', 'change_password');
		showDownloader(true);
		AjaxQuery('POST', 'edit', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Настройки изменены');
		}, true);
	});

	$('#directory').change(function() {
		$('#btn-save-value-directory').html('Добавить значение');
		$('#btn-save-value-directory').data('type', 1);
		$('#new_value_directory').val('');
		if(($(this).val() == 0) || ($(this).val() === undefined)) {
			$('#value_directory').html('');
			return;
		}
		showDownloader(true);
		var query = 'option=get_value_directory&directory=' + $(this).val();
		AjaxQuery('POST', 'edit_directory', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('#value_directory').html(res[1]);
			});
		});
	});

	$('#value_directory').change(function() {
		if($(this).val() === undefined || $(this).val() == 0) {
			$('#new_value_directory').val('');
			$('#btn-save-value-directory').html('Добавить значение');
			$('#btn-save-value-directory').data('type', 1);
		} else {
			$('#new_value_directory').val($('#value_directory option:selected').text());
			$('#btn-save-value-directory').html('Скорректировать значение');
			$('#btn-save-value-directory').data('type', 2);
		}
	});

	$('#directory2').change(function() {
		if(($(this).val() == 0) || ($(this).val() === undefined)) {
			$('#value_directory2').html('');
			return;
		}
		showDownloader(true);
		var query = 'option=get_value_directory&directory=' + $(this).val();
		AjaxQuery('POST', 'edit_directory', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, null, function(res) {
				$('#value_directory2').html(res[1]);
			});
		});
	});

	$('#btn-remove-value-directory').click(function() {
		if($('#directory2').val() == 0 || $('#directory2').val() === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Выберите справочник!', 'method' : 'show' });
			return;
		}

		if($('#value_directory2').val() == 0 || $('#value_directory2').val() === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Выберите значение справочника!', 'method' : 'show' });
			return;
		}
		var query = 'option=remove&directory=' + $('#directory2').val() + '&value=' + $('#value_directory2').val();
		AjaxQuery('POST', 'edit_directory', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Значение справочника удалено!');
		});
	});

	$('#btn-save-value-directory').click(function() {
		if($('#directory').val() == 0 || $('#directory').val() === undefined) {
			$('.modal-ic-komi-basic').ModalBasicIcKomi({ 'textHeader' : 'Выберите справочник!', 'method' : 'show' });
			return;
		}
		var query = 'option=save&directory=' + $('#directory').val() + '&value=' + $('#value_directory').val() + '&new_value=' + $('#new_value_directory').val();
		AjaxQuery('POST', 'edit_directory', query, function(result) {
			showDownloader(false);
			handlerAjaxResult(result, 'Изменения сохранены!');
		});
	});

});