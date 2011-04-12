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

<p class="payment_module">
	<a href="javascript:document.ogone_form.submit();" title="{l s='Pay with Ogone' mod='ogone'}" style="height:48px">
		<span style="height:40px;width:86px;float:left"><img src="{$module_dir}ogone.gif" alt="{l s='Ogone logo' mod='ogone'}" /></span>
		<span style="width:350px;float:left;margin-left:10px">{l s='Pay with Ogone' mod='ogone'}<br />{l s='Pay safely and quickly on the next page with IDEAL / Mastercard / Visa / Paypal / Mister Cash / Bancontact.' mod='ogone'}</span>
		<div style="clear:both;height:0;line-height:0">&nbsp;</div>
	</a>
	<div style="clear:both;height:0;line-height:0">&nbsp;</div>
</p>
<form name="ogone_form" action="https://secure.ogone.com/ncol/{if $OGONE_MODE}prod{else}test{/if}/orderstandard_utf8.asp" method="post">
{foreach from=$ogone_params key=ogone_key item=ogone_value}
	<input type="hidden" name="{$ogone_key}" value="{$ogone_value}" />
{/foreach}
</form>




