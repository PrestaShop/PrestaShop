{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	$().ready(function()
	{
		var items_length = $('#ts-list-items').find('input').length;
		//$('#ts-list-items').css('line-height', ((70/items_length) > 12 ? Math.round(70/items_length) : 12 )+'px');

		TS.init();
	});

var TS = (function()
		{
	function updateTsProduct(id_product, type_action)
	{
		$.ajax({
			type: 'POST',
			url: baseDir + 'cart.php',
			async: true,
			cache: false,
			dataType : "json",
			data: type_action+'=1&ajax=true&qty=1&id_product=' + id_product + '&token=' + static_token,
			success: function(jsonData)
			{
				ajaxCart.updateCart(jsonData);
				$('span.price').not('#cart_block_shipping_cost').not('.products .price').html(jsonData.total);
			}
		});
	}
	return {
		init: function ()
		{
			$('#ts-list-items input[type=checkbox]').click(function(e)
			{
				var $t = $(this);
				var id_number = $t.attr('id').split('-')[2];

				if ($t.attr('checked'))	
					updateTsProduct(id_number, 'add');
				else
					updateTsProduct(id_number, 'delete');
			});
		}
	}
})();
</script>

<div style="border:solid 1px #000; width: 537px; border: 1px solid #595A5E; margin-bottom: 10px;">
	<h3 style="padding:0 0 0 5px;"><b>{l s='Trusted Shops Buyer Protection (recommended)' mod='trustedshops'}</b></h3>
	<div style="float:left; width:100px;">
		<img id="logo_trusted" style="margin:2px 0 10px 10px" alt="logo" src="{$module_dir}img/siegel.gif" />
	</div>
	<div id="ts-list-items">
		{foreach from=$buyer_protection_items item=product}
			<p><input id="ts-product-{$product.id_product}" type="checkbox" value="{$product.id_product}" name="item_product"> {l s='Buyer protection from' mod='trustedshops'} {$product.protected_amount_decimal} ({$product.gross_fee|round:2} {l s='GBP incl. VAT' mod='trustedshops'})</p>
		{/foreach}
		<div id="content_checkout" style="margin-left:100px">
		<p>{l s='The Trusted Shops Buyer Protection secures your online purchase. I agree to my email address being transferred and' mod='trustedshops'} <b><a href="http://www.trustedshops.de/info/datenschutz/">{l s='saved' mod='trustedshops'}</a></b> {l s='for the purposes of Buyer Protection processing by Trusted Shops.' mod='trustedshops'}<b><a href="http://www.trustedshops.de/info/garantiebedingungen/">{l s='Conditions' mod='trustedshops'}</a></b>: {l s='for Buyer Protection.' mod='trustedshops'}</p></div>
	</div>
	<div class="clear"/></div>
</div>
