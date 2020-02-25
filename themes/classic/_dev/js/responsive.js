/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import prestashop from 'prestashop';

prestashop.responsive = prestashop.responsive || {};

prestashop.responsive.current_width = window.innerWidth;
prestashop.responsive.min_width = 768;
prestashop.responsive.mobile = prestashop.responsive.current_width < prestashop.responsive.min_width;

function swapChildren(obj1, obj2)
{
	var temp = obj2.children().detach();
	obj2.empty().append(obj1.children().detach());
	obj1.append(temp);
}

function toggleMobileStyles()
{
	if (prestashop.responsive.mobile) {
		$("*[id^='_desktop_']").each(function(idx, el) {
			var target = $('#' + el.id.replace('_desktop_', '_mobile_'));
			if (target.length) {
				swapChildren($(el), target);
			}
		});
	} else {
		$("*[id^='_mobile_']").each(function(idx, el) {
			var target = $('#' + el.id.replace('_mobile_', '_desktop_'));
			if (target.length) {
				swapChildren($(el), target);
			}
		});
	}
	prestashop.emit('responsive update', {
		mobile: prestashop.responsive.mobile
	});
}

$(window).on('resize', function() {
	var _cw = prestashop.responsive.current_width;
	var _mw = prestashop.responsive.min_width;
	var _w = window.innerWidth;
	var _toggle = (_cw >= _mw && _w < _mw) || (_cw < _mw && _w >= _mw);
	prestashop.responsive.current_width = _w;
  prestashop.responsive.mobile = prestashop.responsive.current_width < prestashop.responsive.min_width;
	if (_toggle) {
		toggleMobileStyles();
	}
});

$(document).ready(function() {
	if (prestashop.responsive.mobile) {
		toggleMobileStyles();
	}
});
