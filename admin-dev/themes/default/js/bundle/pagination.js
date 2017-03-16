/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {

	/*
	 * Link action on the select list in the navigator toolbar. When change occurs, the page is refreshed (location.href redirection)
	 */
	$('select[name="paginator_select_page_limit"]').change(function() {
		var url = $(this).attr('psurl').replace(/_limit/, $('option:selected', this).val());
		window.location.href = url;
		return false;
	});

	/*
	 * Input field changes management
	 */
	function checkInputPage(eventOrigin) {
		var e = eventOrigin || event;
		var char = e.type === 'keypress' ? String.fromCharCode(e.keyCode || e.which) : (e.clipboardData || window.clipboardData).getData('Text');
		if (/[^\d]/gi.test(char)) {
			return false;
		}
	}
	$('input[name="paginator_jump_page"]').each(function() {
		this.onkeypress = checkInputPage;
		this.onpaste = checkInputPage;

		$(this).on('keyup', function(e) {
			var val = parseInt($(e.target).val());
			if (e.which === 13) { // ENTER
				e.preventDefault();
				if (parseInt(val) > 0) {
					var limit = $(e.target).attr('pslimit');
					var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
					window.location.href = url;
					return false;
				}
			}
			var max = parseInt($(e.target).attr('psmax'));
			if (val > max) {
				$(this).val(max);
				return false;
			}
		});
		$(this).on('blur', function(e) {
			var val = parseInt($(e.target).val());
			if (parseInt(val) > 0) {
				var limit = $(e.target).attr('pslimit');
				var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
				window.location.href = url;
				return false;
			}
		});
	});
});
