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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{l Considering the coverage period choosen and the quantity of products/combinations that you sold, this array gives you an idea of when one product will run out of stock}

{if count($stock_cover_periods) > 1}
	<form type="get" onchange="submit();">
		<label for="coverage_period">{l s="Select a period to cover:"}</label>
		<input type="hidden" name="controller" value="AdminStockCover" />
		<input type="hidden" name="token" value="{$token}" />
		<select name="coverage_period">
			{foreach from=$stock_cover_periods key=k item=i}
				<option {if $i == $stock_cover_cur_period} selected="selected"{/if} value="{$i}">{$k}</option>
			{/foreach}
		</select>
	</form>
{/if}

{$content}