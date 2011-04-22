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
<style>
{literal}
input.myButtonTricksTS
{
	font-size:9px !important;
	font-weight:bolder;
	cursor:pointer;
	padding:3px;
}
{/literal}
</style>
<div class="trustedshops-form" style="text-align:center;border:1px solid #ccc;padding:10px;">
	<div style="float:left;width:72px;">
		<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php" target="_blank">
			<input type="image" style="border:0;margin:10px 0 0 0;" border="0" src="{$module_dir}img/siegel.gif" title="{l s='Trusted Shops Seal of Approval - Click to verify.' mod='trustedshops'}">
			<input name="shop_id" type="hidden" value="{$buyer_protection.shop_id}">
		</form>
	</div>
	<form id="formTShops" name="formTShops" method="post" action="https://www.trustedshops.com/shop/protection.php" target="_blank">
	<div style="float:left;text-align:left;padding:30px 0 0 10px;width:80%;">
			<input name="_charset_" type="hidden" value="{$buyer_protection.charset}">
			<input name="shop_id" type="hidden" value="{$buyer_protection.shop_id}">
			<input name="email" type="hidden" value="{$buyer_protection.buyer_email}">
			<input name="amount" type="hidden" value="{$buyer_protection.amount}">
			<input name="curr" type="hidden" value="{$buyer_protection.currency}">
			<input name="paymentType" type="hidden" value="{$buyer_protection.payment_type}">
			<input name="kdnr" type="hidden" value="{$buyer_protection.customer_id}">
			<input name="ordernr" type="hidden" value="{$buyer_protection.order_id}">
			{l s='We offer you the Trusted Shops Buyer Protection as an additional service. We cover all costs for this guarantee. All you have to do is register!' mod='trustedshops'}
			<br><br>
	</div>
	<div class="clear"></div>
	<div style="text-align:right;width:100%;">
		<input class="myButtonTricksTS" type="submit" id="btnProtect" style="display:inline-block;" name="btnProtect" value="{l s='Register for Trusted Shops Buyer Protection' mod='trustedshops'}"/>
	</div>
	</form>
</div>

