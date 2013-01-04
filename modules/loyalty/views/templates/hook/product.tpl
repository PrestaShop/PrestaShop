{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
var point_rate = {$point_rate};
var point_value = {$point_value};
var points_in_cart = {$points_in_cart};
var none_award = {$none_award};

$(document).ready(function() {
	// Force color "button" to fire event change
	$('#color_to_pick_list').click(function() {
		$('#color_pick_hidden').triggerHandler('change');
	});

	// Catch all attribute changeent of the product
	$('.product_attributes input, .product_attributes select').change(function() {
		if (typeof(productPrice) == 'undefined' || typeof(productPriceWithoutReduction) == 'undefined')
			return;
		
		var points = Math.round(productPrice / point_rate);
		var total_points = points_in_cart + points;
		var voucher = total_points * point_value;
		if (!none_award && productPriceWithoutReduction != productPrice) {
			$('#loyalty').html("{l s='No reward points for this product because there\'s already a discount.' mod='loyalty'}");
		} else if (!points) {
			$('#loyalty').html("{l s='No reward points for this product.' mod='loyalty'}");
		} else {
			var content = "{l s='By buying this product you can collect up to' mod='loyalty'} <b><span id=\"loyalty_points\">"+points+'</span> ';
			if (points > 1)
				content += "{l s='loyalty points' mod='loyalty'}</b>. ";
			else
				content += "{l s='loyalty point' mod='loyalty'}</b>. ";
			
			content += "{l s='Your cart will total' mod='loyalty'} <b><span id=\"total_loyalty_points\">"+total_points+'</span> ';
			if (total_points > 1)
				content += "{l s='points' mod='loyalty'}";
			else
				content += "{l s='point' mod='loyalty'}";
			
			content += "</b> {l s='that can be converted into a voucher of' mod='loyalty'} ";
			content += '<span id="loyalty_price">'+formatCurrency(voucher, currencyFormat, currencySign, currencyBlank)+'</span>.';
			$('#loyalty').html(content);
		}
	});
});
</script>
<p id="loyalty" class="align_justify">
	{if $points}
		{l s='By buying this product you can collect up to' mod='loyalty'} <b><span id="loyalty_points">{$points}</span> 
		{if $points > 1}{l s='loyalty points' mod='loyalty'}{else}{l s='loyalty point' mod='loyalty'}{/if}</b>. 
		{l s='Your cart will total' mod='loyalty'} <b><span id="total_loyalty_points">{$total_points}</span> 
		{if $total_points > 1}{l s='points' mod='loyalty'}{else}{l s='point' mod='loyalty'}{/if}</b> {l s='that can be converted into a voucher of' mod='loyalty'} 
		<span id="loyalty_price">{convertPrice price=$voucher}</span>.
	{else}
		{if isset($no_pts_discounted) && $no_pts_discounted == 1}
			{l s='No reward points for this product because there\'s already a discount.' mod='loyalty'}
		{else}
			{l s='No reward points for this product.' mod='loyalty'}
		{/if}
	{/if}
</p>
<br class="clear" />