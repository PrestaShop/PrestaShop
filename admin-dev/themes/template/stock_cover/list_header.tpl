{extends file="helper/list/list_header.tpl"}
{block name=leadin}
	<form id="stock_cover" type="get">
		<input type="hidden" name="controller" value="AdminStockCover" />
		<input type="hidden" name="token" value="{$token}" />
	{if count($stock_cover_periods) > 1}
		<div id="stock_cover_form_period">
			<label for="coverage_period">{l s="Select a period:"}</label>
			<select name="coverage_period" onChange="$(this).parent().parent().submit();">
				{foreach from=$stock_cover_periods key=k item=i}
					<option {if $i == $stock_cover_cur_period} selected="selected"{/if} value="{$i}">{$k}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	{if count($stock_cover_warehouses) > 1}
		<div id="stock_cover_form_warehouse">
			<label for="coverage_warehouse">{l s="Select a warehouse:"}</label>
			<select name="coverage_warehouse" onChange="$(this).parent().parent().submit();">
				{foreach from=$stock_cover_warehouses key=k item=i}
					<option {if $i.id_warehouse == $stock_cover_cur_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	</form>
{/block}
