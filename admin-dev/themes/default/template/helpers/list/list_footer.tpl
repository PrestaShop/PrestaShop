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
	</table>

	{if $bulk_actions}
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			<li>
				<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$table}Box[]', true);return false;">
					<i class="icon-check-sign"></i>&nbsp;{l s='Select all'}
				</a>
			</li>
			<li>
				<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$table}Box[]', false);return false;">
					<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all'}
				</a>
			</li>
			<li class="divider"></li>
			{foreach $bulk_actions as $key => $params}
				<li{if $params.text == 'divider'} class="divider"{/if}>
					{if $params.text != 'divider'}
					<a href="#" onclick="{if isset($params.confirm)}if (confirm('{$params.confirm}')){/if}sendBulkAction($(this).closest('form').get(0), 'submitBulk{$key}{$table}');">
						{if isset($params.icon)}<i class="{$params.icon}"></i>{/if}&nbsp;{$params.text}
					</a>
					{/if}
				</li>
			{/foreach}
			</ul>
		</div>
	{/if}

	{if !$simple_header && $list_total > 20}
	<div class="table-pagination pull-right">
		<span>
		{if $page > 1}
			<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter{$table}').value=1"/>&nbsp;
			<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter{$table}').value={$page - 1}"/>
		{/if}
			{l s='Page'} <b>{$page}</b> / {$total_pages}
		{if $page < $total_pages}
			<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter{$table}').value={$page + 1}"/>&nbsp;
			<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter{$table}').value={$total_pages}"/>
		{/if}
		</span>

		<span>
			&nbsp;|&nbsp;
			<label >{l s='Display'}</label>
			<select class="filter fixed-width-S" name="pagination" onchange="submit()">
				{* Choose number of results per page *}
				{foreach $pagination AS $value}
					<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
				{/foreach}
			</select>
		</span>
		<span> / {$list_total} {l s='result(s)'}</span>
	</div>
	{/if}



{if !$simple_header}
		<input type="hidden" name="token" value="{$token}" />
	</fieldset>
</form>
{/if}

{hook h='displayAdminListAfter'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListAfter{/capture}
	{hook h=$hookName}
{/if}


{block name="after"}{/block}
