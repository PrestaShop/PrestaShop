{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 16965 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='Order'}{/capture}
{include file='./page-title.tpl'}

{if $PS_CATALOG_MODE}
	<p class="warning">{l s='This store does not accept order.'}</p>
{else}

<script type="text/javascript">
	// <![CDATA[
	var imgDir = '{$img_dir}';
	var authenticationUrl = '{$link->getPageLink("authentication", true)}';
	var orderOpcUrl = '{$link->getPageLink("order-opc", true)}';
	var historyUrl = '{$link->getPageLink("history", true)}';
	var guestTrackingUrl = '{$link->getPageLink("guest-tracking", true)}';
	var addressUrl = '{$link->getPageLink("address", true)}';
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
	
	var txtWithTax = "{l s='(tax incl.)'}";
	var txtWithoutTax = "{l s='(tax excl.)'}";
	var txtHasBeenSelected = "{l s='has been selected'}";
	var txtNoCarrierIsSelected = "{l s='No carrier has been selected'}";
	var txtNoCarrierIsNeeded = "{l s='No carrier is needed for this order'}";
	var txtConditionsIsNotNeeded = "{l s='No terms of service must be accepted'}";
	var txtTOSIsAccepted = "{l s='Terms of Service have been accepted'}";
	var txtTOSIsNotAccepted = "{l s='Terms of service have not been accepted'}";
	var txtThereis = "{l s='There is'}";
	var txtErrors = "{l s='error(s)'}";
	var txtDeliveryAddress = "{l s='Delivery address'}";
	var txtInvoiceAddress = "{l s='Invoice address'}";
	var txtModifyMyAddress = "{l s='Modify my address'}";
	var txtInstantCheckout = "{l s='Instant checkout'}";
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

	{* if there is at least one product : checkout process *}
	{if $productNumber}
	
		<!-- Shopping Cart -->
		{include file="./shopping-cart.tpl"}
		<!-- End Shopping Cart -->

		{if $isLogged AND !$isGuest}
			<!--  Address block -->
			{include file="./order-opc-address.tpl"}
			<!--  END Address block -->
			<!-- Carrier -->
			{include file="./order-opc-carrier.tpl"}
			<!-- END Carrier -->
			<!-- Payment -->
			{include file="./order-opc-payment.tpl"}
			<!-- END Payment -->
		{/if}
		
	{* else : warning *}
	{else}
		<p class="warning">{l s='Your shopping cart is empty.'}</p>
	{/if}
	
{/if}
