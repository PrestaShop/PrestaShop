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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

<div id="account_list">
	{foreach from=$account_number_list item=detail key=name}
		<h2>{$detail['title']}</h2>
		{if $detail['list']|count}
			<table class="table" style="width:100%;">
				<thead>
					{if $detail['list']|count}
						{foreach from=$detail['fields'] item=col_name key=sql_name}
							<th>{$col_name}</th>
						{/foreach}
					{/if}
				</thead>
				<tbody>
					{foreach from=$detail['list'] item=row key=row_number}
						<tr>
							{foreach from=$row item=value key=value_num}
								<td>{$value}</td>
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<p>{l s='No defined account number for this list'}</p>
		{/if}
		<div class="separation"></div>
	{/foreach}
</div>
