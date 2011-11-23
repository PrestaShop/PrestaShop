{extends file="helper/list/list_header.tpl"}
{block name=override_header}
<div class="filter-stock">
	<form id="stock_cover" type="get">
		<input type="hidden" name="controller" value="AdminStockCover" />
		<input type="hidden" name="token" value="{$token}" />
	{if count($stock_cover_periods) > 1}
			<label for="coverage_period">{l s="Select a period and a warehouse:"}</label>
			<select name="coverage_period" onChange="$(this).parent().submit();">
				{foreach from=$stock_cover_periods key=k item=i}
					<option {if $i == $stock_cover_cur_period} selected="selected"{/if} value="{$i}">{$k}</option>
				{/foreach}
			</select>
	{/if}
	{if count($stock_cover_warehouses) > 1}
			<select name="id_warehouse" onChange="$(this).parent().submit();">
				{foreach from=$stock_cover_warehouses key=k item=i}
					<option {if $i.id_warehouse == $stock_cover_cur_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
				{/foreach}
			</select>
	{/if}
	</form>
</div>
{/block}
