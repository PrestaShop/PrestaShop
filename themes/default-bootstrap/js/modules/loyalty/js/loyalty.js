/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function() {
	$(document).on('change', '#our_price_display', function(e){
		updateLoyaltyView($('#our_price_display').attr('content'));
	})
	updateLoyaltyView($('#our_price_display').attr('content'));
});

function updateLoyaltyView(new_price) {
	if (typeof(new_price) == 'undefined' || typeof(productPriceWithoutReduction) == 'undefined')
		return;

	var points = Math.floor(new_price / point_rate);
	var total_points = points_in_cart + points;
	var voucher = total_points * point_value;

	if (none_award == 0 && productPriceWithoutReduction != new_price) {
		$('#loyalty').html(loyalty_already);
	}
	else if (!points) {
		$('#loyalty').html(loyalty_nopoints);
	}
	else
	{
		var content = loyalty_willcollect + " <b><span id=\"loyalty_points\">"+points+'</span> ';
		if (points > 1)
			content += loyalty_points + "</b>. ";
		else
			content += loyalty_point + "</b>. ";

		content += loyalty_total + " <b><span id=\"total_loyalty_points\">"+total_points+'</span> ';
		if (total_points > 1)
			content += loyalty_points;
		else
			content += loyalty_point;

		content += '</b> ' + loyalty_converted + ' ';
		content += '<span id="loyalty_price">'+formatCurrency(voucher, currencyFormat, currencySign, currencyBlank)+'</span>.';
		$('#loyalty').html(content);
	}
}
