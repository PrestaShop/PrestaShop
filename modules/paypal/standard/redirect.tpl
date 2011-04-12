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

<html>
	<head>
		<title>PayPal</title>
		<script type="text/javascript" src="{$url}js/jquery/jquery-1.4.4.min.js"></script>
	</head>
	<body>
		<p>{$redirect_text}<br /><a href="javascript:history.go(-1);">{$cancel_text}</a></p>
		<form action="{$paypal_url}" method="post" id="paypal_form" class="hidden">
			<input type="hidden" name="upload" value="1" />
			<input type="hidden" name="address_override" value="0" />
			<input type="hidden" name="first_name" value="{$address->firstname|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="last_name" value="{$address->lastname|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="address1" value="{$address->address1|escape:'htmlall':'UTF-8'}" />
			{if $address->address2 != NULL}
			<input type="hidden" name="address2" value="{$address->address2|escape:'htmlall':'UTF-8'}" />
			{/if}
			<input type="hidden" name="city" value="{$address->city|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="zip" value="{$address->postcode}" />
			<input type="hidden" name="country" value="{$country->iso_code}" />
			{if $state != NULL}
			<input type="hidden" name="state" value="{$state->iso_code}" />
			{/if}
			<input type="hidden" name="amount" value="{$amount}" />
			<input type="hidden" name="email" value="{$customer->email}" />
			{if !$discount}
			{foreach from=$products key=k item=product}
			<input type="hidden" name="item_name_{$k+1}" value="{$product.name|escape:'htmlall':'UTF-8'}{if isset($product.attributes)} - {$product.attributes|escape:'htmlall':'UTF-8'}{/if}" />
			<input type="hidden" name="amount_{$k+1}" value="{$product.price_wt}" />
			<input type="hidden" name="quantity_{$k+1}" value="{$product.cart_quantity}" />
			{/foreach}
			<input type="hidden" name="shipping_1" value="{$shipping}" />
			{else}
			<input type="hidden" name="item_name_1" value="{$cart_text}" />
			<input type="hidden" name="amount_1" value="{$total}" />
			<input type="hidden" name="quantity_1" value="1" />
			{/if}
			<input type="hidden" name="business" value="{$business}" />
			<input type="hidden" name="receiver_email" value="{$business}" />
			<input type="hidden" name="cmd" value="_cart" />
			<input type="hidden" name="charset" value="utf-8" />
			<input type="hidden" name="currency_code" value="{$currency_module->iso_code}" />
			<input type="hidden" name="payer_id" value="{$customer->id}" />
			<input type="hidden" name="payer_email" value="{$customer->email}" />
			<input type="hidden" name="custom" value="{$cart_id}" />
			<input type="hidden" name="return" value="{$url}order-confirmation.php?key={$customer->secure_key}&amp;id_cart={$cart_id}&amp;id_module={$paypal_id}&amp;slowvalidation" />
			<input type="hidden" name="cancel_return" value="{$url}" />
			<input type="hidden" name="notify_url" value="{$url}modules/paypal/validation.php" />
			{if $header != NULL}
			<input type="hidden" name="cpp_header_image" value="{$header}" />
			{/if}
			<input type="hidden" name="rm" value="2" />
			<input type="hidden" name="bn" value="PRESTASHOP_WPS" />
			<input type="hidden" name="cbt" value="{$return_text}" />
			<input type="hidden" name="image_url" value="{$url}img/logo.jpg" /> 
		</form>
		<script type="text/javascript">
		{literal}
		$(document).ready(function() {
			$('#paypal_form').submit();
		});
		{/literal}
		</script>
	</body>
</html>
