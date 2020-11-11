/*
	jQuery плагин для взаимодействия с модальными окнами Twitter Bootstrap
	Реализует два интерфейса для взаимодействия с модальными окнами: ModalWindowBasicIcKomi, ModalWindowDocumentViewIcKomi

	ModalWindowBasicIcKomi - базовый компонент для отображения стандартного модального окна: есть header, body и footer.
	ModalWindowDocumentViewIcKomi - модальное окно для отображения PDF документов

	Разрешена инициализация только в блоках DIV.
	Для ModalWindowBasicIcKomi класс (modal-window-basic)
	Для ModalWindowDocumentViewIcKomi класс (modal-window-document-view)
	Example:
		$('.modal-window-basic').ModalWindowBasicIcKomi();
		$('.modal-window-document-view').ModalWindowDocumentViewIcKomi();

	Параметры:
		method - метод, который будет выполнен: show, hide
		textHeader - текст для заголовка модального окна
		textBody - текст для тела модального окна
	Example:
		$('.modal-window-basic').ModalWindowBasicIcKomi({ 'method' : 'show', 'textHeader' : 'example'});
		$('.modal-window-document-view').ModalWindowDocumentViewIcKomi({ 'method' : 'show', 'textBody' : 'example'});

	Copyright: Rostislav Gashin, 2020 Syktyvkar, Komi Republic
*/

/*
	jQuery plugin ModalWindowBasicIcKomi
*/
;(function(window) {
	'use strict';
	if(typeof $ === 'undefined')
		throw new TypeError('Plugin\'s JavaScript requires jQuery. jQuery must be included before this Plugin\'s JavaScript.');

	var VERSION = '1.0',
		pluginName = 'ModalWindowBasicIcKomi',
		defaults = {
			textHeader : '',
			textBody : '',
			method : '',
		};

	var ModalWindowBasicIcKomi = function(el, options) {
		this.el = el;
		this.$el = $(el);
		this.opts = $.extend(true, {}, defaults, options, this.$el.data());
		this.init();
	};

	ModalWindowBasicIcKomi.prototype = {
		VERSION: VERSION,

		init: function() {
			if(this.$el.prop('tagName').toUpperCase() == 'DIV')
				this.paintingModalWindow();
		},

		paintingModalWindow: function() {
			this.$el.addClass('modal').addClass('fade');
			this.$el.css({ 'z-index' : '2001'});
			this.$el.data('backdrop', 'static');

			var modalDialog = $("<div class='modal-dialog modal-lg' role='document'></div>");
			var modalContent = $("<div class='modal-content'></div>");
			
			modalContent.append(this.setContextHeader());
			modalContent.append(this.setContextBody());
			
			var modalFooter = $("<div class='modal-footer'></div>");
			var btnClose = $("<button id='closeButton' class='btn btn-warning' type='button' aria-label='Close'>Закрыть</button>");
			btnClose.unbind();
			btnClose.on('click', this.hide);
			modalFooter.append(btnClose);

			modalContent.append(modalFooter);
			modalDialog.append(modalContent);
			modalDialog.appendTo(this.$el);

			this.runMethod();
		},

		runMethod: function() {
			if((this.opts.method !== undefined) && (String(this.opts.method).trim().length != 0)) {
				if(this.opts.method == 'show')
					this.show();
				else if(this.opts.method == 'hide')
					this.hide();
			}
		},

		setContextHeader: function() {
			this.$el.find('.modal-header').remove();
			if((this.opts['textHeader'] === undefined) || (String(this.opts['textHeader']).trim().length == 0))
				return $();

			var p = $("<div class='col text-center'></div>");
			var h4 = $("<h4>" + this.opts['textHeader'] + "</h4>");
			p.append(h4);

			var header = $("<div class='modal-header'></div>");
			header.append(p);

			return header;
		},

		setContextBody: function() {
			this.$el.find('.modal-body').remove();
			if((this.opts['textBody'] === undefined) || (String(this.opts['textBody']).trim().length == 0))
				return $();

			var p = $("<div class='col'></div>");
			var h5 = $("<h5>" + this.opts['textBody'] + "</h5>");
			p.append(h5);

			var body = $("<div class='modal-body'></div>");
			body.append(p);

			return body;
		},

		show: function() {
			$(this.$el).modal('toggle');
		},

		hide: function() {
			var modal;

			if(event === undefined)
				modal = this.$el;
			else
				modal = $(this).closest('.modal-window-basic');

			$(modal).find('.modal-header').remove();
			$(modal).find('.modal-body').remove();
			$(modal).modal('hide');
		},

		update: function() {
			var modalContent = this.$el.find('.modal-content');
			modalContent.prepend(this.setContextBody());
			modalContent.prepend(this.setContextHeader());
			this.runMethod();
		},

		clearOptions: function() {
			this.opts['textHeader'] = '';
			this.opts['textBody'] = '';
		},
	};

	$.fn.ModalWindowBasicIcKomi = function(options) {
		return this.each(function() {
			if(!$.data(this, pluginName)) {
				$.data(this, pluginName, new ModalWindowBasicIcKomi(this, options));
			} else {
				var _this = $.data(this, pluginName);
				_this.clearOptions();
				_this.opts = $.extend(true, _this.opts, options);
				_this.update();
			}
		});
	};
})(window);

/*
	jQuery plugin ModalWindowDocumentViewIcKomi
*/
;(function(window) {
	'use strict';
	if(typeof $ === 'undefined')
		throw new TypeError('Plugin\'s JavaScript requires jQuery. jQuery must be included before this Plugin\'s JavaScript.');

	var VERSION = '1.0',
		pluginName = 'ModalWindowDocumentViewIcKomi',
		defaults = {
			textBody : '',
			method : '',
		};

	var ModalWindowDocumentViewIcKomi = function(el, options) {
		this.el = el;
		this.$el = $(el);
		this.opts = $.extend(true, {}, defaults, options, this.$el.data());
		this.init();
	};

	ModalWindowDocumentViewIcKomi.prototype = {
		VERSION: VERSION,

		init: function() {
			if(this.$el.prop('tagName').toUpperCase() == 'DIV')
				this.paintingModalWindow();
		},

		paintingModalWindow: function() {
			this.$el.addClass('modal').addClass('fade');
			this.$el.css({ 'z-index' : '2001'});
			this.$el.data('backdrop', 'static');

			var modalDialog = $("<div class='modal-dialog modal-xl'  role='document' style='max-width: 95%;'></div>");
			var modalContent = $("<div class='modal-content' style='height: 90vh; width: 100%;'></div>");
			
			modalContent.append(this.setContextHeader());
			modalContent.append(this.setContextBody());
		
			modalDialog.append(modalContent);
			modalDialog.appendTo(this.$el);

			this.runMethod();
		},

		setContextHeader: function() {
			this.$el.find('.modal-header').remove();
			var btnOpen = $("<button type='button' class='btn btn-outline-secondary btn-open-file-new-page mr-1' title='Открыть в новом окне'><span class='fa fa-folder-open-o'></span></button>");
			var btnClose = $("<button type='button' class='btn btn-outline-danger' title='Закрыть просмотр' aria-label='Close'><span class='fa fa-close'></span></button>");
			btnOpen.unbind();
			btnClose.unbind();
			btnClose.on('click', this.hide);
			btnOpen.on('click', this.open);
			var blockHeaderRight = $("<div class='text-right'></div>");
			blockHeaderRight.append(btnOpen);
			blockHeaderRight.append(btnClose);

			var h4 = $("<h4>Просмотр документа</h4>");

			var header = $("<div class='modal-header'></div>");
			header.append(h4);
			header.append(blockHeaderRight);

			return header;
		},

		setContextBody: function() {
			this.$el.find('.modal-body').remove();
			if((this.opts.textBody === undefined) || (String(this.opts.textBody).trim().length == 0))
				return $();
			
			var body = $("<div class='modal-body' style='height: 100%; position: relative;'></div>");
			var iframe = $("<iframe class='file-frame' src='data:application/pdf;base64," + this.opts.textBody + "' style='width: 100%;'></iframe>");
			body.append(iframe);
			return body;
		},

		show: function() {
			$(this.$el).modal('toggle');
		},

		hide: function() {
			var modal;

			if(event === undefined)
				modal = this.$el;
			else
				modal = $(this).closest('.modal-window-document-view');

			$(modal).find('.modal-header').remove();
			$(modal).find('.modal-body').remove();
			$(modal).modal('hide');
		},

		open: function() {
			var item = $(this).closest('.modal-window-document-view').find('iframe');
			window.open($(item).prop('src'));
		},

		runMethod: function() {
			if((this.opts.method !== undefined) && (String(this.opts.method).trim().length != 0)) {
				if(this.opts.method == 'show')
					this.show();
				else if(this.opts.method == 'hide')
					this.hide();
			}
		},

		update: function() {
			var modalContent = this.$el.find('.modal-content');
			modalContent.append(this.setContextHeader());
			modalContent.append(this.setContextBody());
			this.runMethod();
		},

		clearOptions: function() {
			this.opts['textBody'] = '';
		},
	};

	$.fn.ModalWindowDocumentViewIcKomi = function(options) {
		return this.each(function() {
			if(!$.data(this, pluginName)) {
				$.data(this, pluginName, new ModalWindowDocumentViewIcKomi(this, options));
			} else {
				var _this = $.data(this, pluginName);
				_this.clearOptions();
				_this.opts = $.extend(true, _this.opts, options);
				_this.update();
			}
		});
	};
})(window);

$(document).ready(function() {
	if(typeof $ === 'undefined')
		throw new TypeError('Plugin\'s JavaScript requires jQuery. jQuery must be included before this Plugin\'s JavaScript.');
	$('.modal-window-basic').ModalWindowBasicIcKomi();
	$('.modal-window-document-view').ModalWindowDocumentViewIcKomi();
});