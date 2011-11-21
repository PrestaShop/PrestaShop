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
			Chaque colis doit au plus faire 20 Kg.<br/>
			{$var.error}
			{if $var.shipping_numbers && $var.sticker}
			{$var.lang_shippingNumber} : 
			<div style="text-align:right">
			{foreach from=$var.shipping_numbers item=v}
			{if $v.shipping_number}
			{$v.shipping_number}<br/>
			{/if}
			{/foreach}
			</div>
			{$var.lang_sticker} : <a style="color:blue" href="{$var.sticker}">{l s="PDF File"}</a><br/>
			{$var.lang_expedition} : {$var.date}<br/>{$var.place}
			{/if}
</fieldset>