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
*  @version  Release: $Revision: 8088 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<br/>
<fieldset style="width:400px">
			<legend><img src="../img/admin/delivery.gif" />{l s='Shipping information'}</legend>
			<!--{$var.error}--><br/>
			<form action="{$var.currentIndex}&view{$var.table}&token={$var.token}" method="post" style="margin-top:10px;">
			{if $var.weight}
			{l s='The package weight must be between 0.1 and 30.0 kg or call your TNT commercial' mod='tntcarrier'}<br/><br/>
			{l s='Weight' mod='tntcarrier'} : <input type="text" name="weightErrorOrder" /><br/><br/>
			{/if}
			{if $var.weightHidden}<input type="hidden" value="{$var.weightHidden}" name="weightErrorOrder" />{/if}
			{if $var.date}
			{l s='You must change the expedition date' mod='tntcarrier'}<br/><br/>
			{l s='Date' mod='tntcarrier'} : <input type="text" value="{$var.date}" name="dateErrorOrder" /><br/><br/>
			{/if}
			{if $var.dateHidden}<input type="hidden" value="{$var.dateHidden}" name="dateErrorOrder" />{/if}
			{if !$var.dateHidden || !$var.weightHidden}<input type="submit" value="{l s='Modify' mod='tntcarrier'}" class="button" />{/if}
</fieldset>