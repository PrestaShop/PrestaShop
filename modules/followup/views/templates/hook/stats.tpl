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

<div class="panel" id="fieldset_4">
    <h3><i class="icon-bar-chart"></i> {l s="Statistics"}</h3>
	<p>{l s="Detailed statistics for last 30 days:"}</p>
	<p style="font-size: 10px; font-weight: bold;">
		{l s="S = Number of sent e-mails"}<br />
		{l s="U = Number of discounts used (valid orders only)"}<br />
		{l s="% = Conversion rate"}
	</p>
	<table class="table">
		<tr>
			<th rowspan="2" style="width: 75px;">{l s="Date"}</th>
			<th colspan="3">{l s="Cancelled carts"}</th>
			<th colspan="3">{l s="Re-order"}</th>
			<th colspan="3">{l s="Best cust."}</th>
			<th colspan="3">{l s="Bad cust."}</th>
		</tr>
		<tr>
			<td class="center">{l s="S"}</td>
			<td class="center">{l s="U"}</td>
			<td class="center">%</td>
			<td class="center">{l s="S"}</td>
			<td class="center">{l s="U"}</td>
			<td class="center">%</td>
			<td class="center">{l s="S"}</td>
			<td class="center">{l s="U"}</td>
			<td class="center">%</td>
			<td class="center">{l s="S"}</td>
			<td class="center">{l s="U"}</td>
			<td class="center">%</td>
		</tr>
		{foreach from=$stats_array key='date' item='stats'}
		<tr>
			<td class="center">{$date}</td>
			{foreach from=$stats key='key' item='val'}
				<td class="center">{$val.nb}</td>
				<td class="center">{$val.nb_used}</td>
				<td class="center">{$val.rate}</td>
			{/foreach}	
		</tr>
		{foreachelse}
			<tr>
				<td colspan="13" style="font-weight: bold; text-align: center;">{l s="No statistics at this time."}</td>
			</tr>
		{/foreach}
	</table>
</div>