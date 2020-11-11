/*
	jQuery плагин для вызова индикатора загрузки

	DownloaderIcKomi - объект, реализующий анимацию с индикатор загрузки

	Объект автоматически инициализируется если блоку установлен класс downloader-ic-komi
	Example:
		<div class='downloader-ic-komi'></div>

	Для работы плагина доступна два формата использования параметров.
	
	1) При помощи конструктора при инициализации компонента:
		method - метод, который будет выполнен: show, hide
		text - текст для отображения процесса индикации
	Example:
		$('.downloader-ic-komi').DownloaderIcKomi({ 'method' : 'show', 'text' : 'example'});
		$('.downloader-ic-komi').DownloaderIcKomi('hide');

	2) При объявлении HTML-кода элемента с использованием data-параметров:
		data-method - метод, который будет выполнен: show, hide
		data-text - текст для отображения процесса индикации
	Example:
		<div class='downloader-ic-komi' data-method='show' data-text='Hello wrold!'></div>

	Copyright: Rostislav Gashin, 2020 Syktyvkar, Komi Republic
*/

/*
	jQuery plugin DownloaderIcKomi
*/
;(function(window) {
	'use strict';
	if(typeof $ === 'undefined')
		throw new TypeError('Plugin\'s JavaScript requires jQuery. jQuery must be included before this Plugin\'s JavaScript.');

	var VERSION = '1.0',
		pluginName = 'DownloaderIcKomi',
		defaults = {
			text : 'Идет выполнение!<br>Пожалуйста подождите..',
			method : '',
		};

	var DownloaderIcKomi = function(el, options) {
		this.el = el;
		this.$el = $(el);
		this.opts = $.extend(true, {}, defaults, options, this.$el.data());
		this.init();
	};

	DownloaderIcKomi.prototype = {
		VERSION: VERSION,

		init: function() {
			if(this.$el.data('text') !== undefined)
				this.opts['text'] = this.$el.data('text');

			if(this.$el.data('method') !== undefined)
				this.opts['method'] = this.$el.data('method');

			this.runMethod();
		},

		runMethod: function() {
			if(this.opts.method === 'show')
				this.show();
			else if(this.opts.method === 'hide')
				this.hide();
		},

		getDownloaderHtmlCode: function() {
			var circularG = $("<div id='circularG'></div>");
			for(var i = 1; i < 9; i++)
				circularG.append($("<div class='circularG circularG_" + String(i) + "'></div>"));
			return circularG;
		},

		getText: function() {
			var text = (this.opts['text'] === undefined) ? '' : this.opts['text'];
			return $("<div class='downloader-ic-komi-text font-weight-bold'>" + text + "</div>");
		},

		show: function() {
			if(this.$el.find('#circularG').length == 0) {
				$('body').append("<div class='downloader-tb-overlay'></div>");
				this.$el.addClass('downloader-ic-komi-center-block');
				this.$el.append(this.getDownloaderHtmlCode());
				this.$el.append(this.getText());
			} else {
				this.$el.find('.downloader-ic-komi-text').html(this.opts['text']);
			}
		},

		hide: function() {
			$('body').find('.downloader-tb-overlay').remove();
			if(this.$el.hasClass('downloader-ic-komi-center-block')) {
				this.$el.empty();
				this.$el.removeClass('downloader-ic-komi-center-block');
			}
		},
	};

	$.fn.DownloaderIcKomi = function(options) {
		return this.each(function() {
			if(!$.data(this, pluginName)) {
				$.data(this, pluginName, new DownloaderIcKomi(this, options));
			} else {
				var _this = $.data(this, pluginName);
				if(typeof(options) == 'object')
					_this.opts = $.extend(true, _this.opts, options);
				else
					_this.opts['method'] = options;
				_this.runMethod();
			}
		});
	};
})(window);

$(document).ready(function() {
	if(typeof $ === 'undefined')
		throw new TypeError('Plugin\'s JavaScript requires jQuery. jQuery must be included before this Plugin\'s JavaScript.');
	$('.downloader-ic-komi').DownloaderIcKomi();
});