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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2013 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}
{block name="script"}
	var string_price = '{l s="Will be applied when the price will be:" js=1}';
	var string_weight = '{l s="Will be applied when the weight will be:" js=1}';
{/block}

{block name="field"}
	{if $input.name == 'zones'}
		{include file='controllers/carrier_wizard/helpers/form/form_ranges.tpl'}
		<div class="new_range">
			<a href="#" onclick="add_new_range();return false;" class="button" id="add_new_range">{l s="Add new range"}<img src="../img/admin/add.gif"/></a>
		</div>
	{/if}
	{$smarty.block.parent}
{/block}
