{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
	</table>
</div>
<div class="row">
	<div class="col-lg-8">
		{if $bulk_actions}
		<div class="btn-group bulk-actions">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions' d='Admin.Global'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$table}Box[]', true);return false;">
						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' d='Admin.Actions'}
					</a>
				</li>
				<li>
					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), '{$table}Box[]', false);return false;">
						<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' d='Admin.Actions'}
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
	</div>
	{if !$simple_header && $list_total > $pagination[0]}
	<div class="col-lg-4">
		{* Choose number of results per page *}
		<div class="pagination">
			{l s='Display' d='Admin.Actions'}
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{$selected_pagination}
				<i class="icon-caret-down"></i>
			</button>
			<ul class="dropdown-menu">
			{foreach $pagination AS $value}
				<li>
					<a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id}">{$value}</a>
				</li>
			{/foreach}
			</ul>
			/ {$list_total} {l s='result(s)' d='Admin.Global'}
			<input type="hidden" id="{$list_id}-pagination-items-page" name="{$list_id}_pagination" value="{$selected_pagination|intval}" />
		</div>
		<script type="text/javascript">
			$('.pagination-items-page').on('click',function(e){
				e.preventDefault();
				$('#'+$(this).data("list-id")+'-pagination-items-page').val($(this).data("items")).closest("form").submit();
			});
		</script>
		<ul class="pagination pull-right">
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id}">
					<i class="icon-double-angle-left"></i>
				</a>
			</li>
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page - 1}" data-list-id="{$list_id}">
					<i class="icon-angle-left"></i>
				</a>
			</li>
			{assign p 0}
			{while $p++ < $total_pages}
				{if $p < $page-2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $page-3}
				{elseif $p > $page+2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $total_pages}
				{else}
					<li {if $p == $page}class="active"{/if}>
						<a href="javascript:void(0);" class="pagination-link" data-page="{$p}" data-list-id="{$list_id}">{$p}</a>
					</li>
				{/if}
			{/while}
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page + 1}" data-list-id="{$list_id}">
					<i class="icon-angle-right"></i>
				</a>
			</li>
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages}" data-list-id="{$list_id}">
					<i class="icon-double-angle-right"></i>
				</a>
			</li>
		</ul>
		<script type="text/javascript">
			$('.pagination-link').on('click',function(e){
				e.preventDefault();

				if (!$(this).parent().hasClass('disabled'))
					$('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
			});
		</script>
	</div>
	{/if}
</div>
{if !$simple_header}
		<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
	</div>
	{hook h='displayAdminListAfter'}
</form>
{/if}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($controller_name)}
    {capture name=hookName assign=hookName}display{$controller_name|ucfirst}ListAfter{/capture}
    {hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListAfter{/capture}
	{hook h=$hookName}
{/if}


{block name="after"}{/block}
