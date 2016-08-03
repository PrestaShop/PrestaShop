import $ from 'jquery';
import prestashop from 'prestashop';

prestashop.responsive = prestashop.responsive || {};

prestashop.responsive.current_width = $(window).width();
prestashop.responsive.min_width = 768;
prestashop.responsive.mobile = prestashop.responsive.current_width < prestashop.responsive.min_width;

function toggleMobileStyles()
{
	$('.responsive').each(function(idx, el) {
		if (prestashop.responsive.mobile) {
			$(el).addClass('mobile');
		} else {
			$(el).removeClass('mobile');
		}
	});
	if (prestashop.responsive.mobile) {
		$("*[id^='_desktop_']").each(function(idx, el) {
			var target = $('#' + el.id.replace('_desktop_', '_mobile_'));
			if (target) {
				console.info(target);
				var html = $(el).html();
				$(el).html('');
				target.html(html);
			}
		});
	} else {
		$("*[id^='_mobile_']").each(function(idx, el) {
			var target = $('#' + el.id.replace('_mobile_', '_desktop_'));
			if (target) {
				var html = $(el).html();
				$(el).html('');
				target.html(html);
			}
		});
	}
}

$(window).on('resize', function() {
	var _cw = prestashop.responsive.current_width;
	var _mw = prestashop.responsive.min_width;
	var _w = $(window).width();
	var _toggle = (_cw >= _mw && _w < _mw) || (_cw < _mw && _w >= _mw);
	prestashop.responsive.mobile = _cw >= _mw;
	prestashop.responsive.current_width = _w;
	if (_toggle) {
		toggleMobileStyles();
	}
});

$(document).ready(function() {
	if (prestashop.responsive.mobile) {
		toggleMobileStyles();
	}
});

