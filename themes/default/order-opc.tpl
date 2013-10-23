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

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
	{else}
	{assign var="back_order_page" value="order.php"}
{/if}

{if $PS_CATALOG_MODE}
	{capture name=path}{l s='Your shopping cart'}{/capture}
	<h2 id="cart_title">{l s='Your shopping cart'}</h2>
	<p class="alert alert-warning">{l s='Your new order was not accepted.'}</p>
{else}
<script type="text/javascript">
	// <![CDATA[
	var imgDir = '{$img_dir}';
	var authenticationUrl = '{$link->getPageLink("authentication", true)|addslashes}';
	var orderOpcUrl = '{$link->getPageLink("order-opc", true)|addslashes}';
	var historyUrl = '{$link->getPageLink("history", true)|addslashes}';
	var guestTrackingUrl = '{$link->getPageLink("guest-tracking", true)|addslashes}';
	var addressUrl = '{$link->getPageLink("address", true, NULL, "back={$back_order_page}")|addslashes}';
	var orderProcess = 'order-opc';
	var guestCheckoutEnabled = {$PS_GUEST_CHECKOUT_ENABLED|intval};
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var displayPrice = {$priceDisplay};
	var taxEnabled = {$use_taxes};
	var conditionEnabled = {$conditions|intval};
	var countries = new Array();
	var countriesNeedIDNumber = new Array();
	var countriesNeedZipCode = new Array();
	var vat_management = {$vat_management|intval};
	
	var txtWithTax = "{l s='(tax incl.)' js=1}";
	var txtWithoutTax = "{l s='(tax excl.)' js=1}";
	var txtHasBeenSelected = "{l s='has been selected' js=1}";
	var txtNoCarrierIsSelected = "{l s='No carrier has been selected' js=1}";
	var txtNoCarrierIsNeeded = "{l s='No carrier is needed for this order' js=1}";
	var txtConditionsIsNotNeeded = "{l s='You do not need to accept the Terms of Service for this order.' js=1}";
	var txtTOSIsAccepted = "{l s='The service terms have been accepted' js=1}";
	var txtTOSIsNotAccepted = "{l s='The service terms have not been accepted' js=1}";
	var txtThereis = "{l s='There is' js=1}";
	var txtErrors = "{l s='Error(s)' js=1}";
	var txtDeliveryAddress = "{l s='Delivery address' js=1}";
	var txtInvoiceAddress = "{l s='Invoice address' js=1}";
	var txtModifyMyAddress = "{l s='Modify my address' js=1}";
	var txtInstantCheckout = "{l s='Instant checkout' js=1}";
	var txtSelectAnAddressFirst = "{l s='Please start by selecting an address.' js=1}";
	var errorCarrier = "{$errorCarrier}";
	var errorTOS = "{$errorTOS}";
	var checkedCarrier = "{if isset($checked)}{$checked}{else}0{/if}";

	var addresses = new Array();
	var isLogged = {$isLogged|intval};
	var isGuest = {$isGuest|intval};
	var isVirtualCart = {$isVirtualCart|intval};
	var isPaymentStep = {$isPaymentStep|intval};
	//]]>
</script>
	{if $productNumber}
		<!-- Shopping Cart -->
		{include file="$tpl_dir./shopping-cart.tpl"}
		<!-- End Shopping Cart -->
		{if $isLogged AND !$isGuest}
			{include file="$tpl_dir./order-address.tpl"}
		{else}
			<!-- Create account / Guest account / Login block -->
			{include file="$tpl_dir./order-opc-new-account.tpl"}
			<!-- END Create account / Guest account / Login block -->
		{/if}
		<!-- Carrier -->
		{include file="$tpl_dir./order-carrier.tpl"}
		<!-- END Carrier -->
	
		<!-- Payment -->
		{include file="$tpl_dir./order-payment.tpl"}
		<!-- END Payment -->
	{else}
		{capture name=path}{l s='Your shopping cart'}{/capture}
		<h2>{l s='Your shopping cart'}</h2>
		<p class="alert alert-warning">{l s='Your shopping cart is empty.'}</p>
	{/if}
{/if}
