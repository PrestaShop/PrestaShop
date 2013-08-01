		<script>var zones_nbr = {$zones|count +3} ; /*corresponds to the third input text (max, min and all)*/</script>
		<div style="float:left" id="zone_ranges">
			<table cellpadding="5" cellspacing="0" id="zones_table">
				<tr class="range_inf">
					<td class="range_type"></td>
					<td class="border_left border_bottom">>=</td>
					{foreach from=$ranges key=r item=range}
						<td class="border_bottom center"><input name="range_inf[{$range.id_range|intval}]" type="text" value="{$range.delimiter1|string_format:"%.6f"}" /><sup>*</sup><span class="weight_unit">&nbsp; {$PS_WEIGHT_UNIT}</span><span class="price_unit">&nbsp; {$currency_sign}</span></td>
					{foreachelse}
						<td class="border_bottom center"><input name="range_inf[{$range.id_range|intval}]" type="text" /><sup>*</sup><span class="weight_unit">&nbsp; {$PS_WEIGHT_UNIT}</span><span class="price_unit">&nbsp; {$currency_sign}</span></td>
					{/foreach}
				</tr>
				<tr class="range_sup">
					<td class="center range_type"></td>
					<td class="border_left "><</td>
					{foreach from=$ranges key=r item=range}
						<td class="center"><input name="range_sup[{$range.id_range|intval}]" type="text" {if isset($form_id) && !$form_id} value="" {else} value="{$range.delimiter2|string_format:"%.6f"}" {/if}/><sup>*</sup><span class="weight_unit">&nbsp; {$PS_WEIGHT_UNIT}</span><span class="price_unit">&nbsp; {$currency_sign}</span></td>
					{foreachelse}
						<td class="center"><input name="range_sup[{$range.id_range|intval}]" type="text" /><sup>*</sup><span class="weight_unit">&nbsp; {$PS_WEIGHT_UNIT}</span><span class="price_unit">&nbsp; {$currency_sign}</span></td>
					{/foreach}
				</tr>
				<tr class="fees_all">
					<td class="border_top border_bottom border_bold"><span class="fees_all" {if $ranges|count == 0}style="display:none" {/if}>All</span></td>
					<td></td>
					{foreach from=$ranges key=r item=range}
						<td class="center border_top border_bottom {if $range.id_range != -1} validated {/if}"  >
							<input type="text" {if isset($form_id) &&  !$form_id} disabled="disabled"{/if} {if $range.id_range == -1} style="display:none"{/if} /><span class="currency_sign">&nbsp; {$currency_sign}</span>
							{if $range.id_range == -1} <button class="button">{l s="Validate"}</button> {/if}
						</td>
					{foreachelse}
						<td class="center border_top border_bottom">
							<input style="display:none" type="text"  /><span class="currency_sign" style="display:none">&nbsp; {$currency_sign}</span>
							<button class="button">{l s="Validate"}</button>
						</td>
					{/foreach}
				</tr>
				{foreach from=$zones key=i item=zone}
				<tr class="fees {if $i is odd}alt_row{/if}" data-zoneid="{$zone.id_zone}">
					<td>{$zone.name}</td>
					<td class="zone">
						<input class="input_zone" name="zone_{$zone.id_zone}" value="1" type="checkbox" {if isset($fields_value[$input.name][$zone.id_zone]) && $fields_value[$input.name][$zone.id_zone]} checked="checked"{/if}/>
					</td>
					{foreach from=$ranges key=r item=range}
						<td class="center">
							<input name="fees[{$zone.id_zone|intval}][{$range.id_range|intval}]" {if (isset($form_id) &&  !$form_id) || isset($change_ranges) || (isset($fields_value[$input.name][$zone.id_zone]) && !$fields_value[$input.name][$zone.id_zone])} disabled="disabled"{/if} type="text" value="{if isset($price_by_range[$range.id_range][$zone.id_zone]) && $fields_value[$input.name][$zone.id_zone]} {$price_by_range[$range.id_range][$zone.id_zone]|string_format:"%.6f"} {/if}" />&nbsp; {$currency_sign}
						</td>
					{/foreach}
				</tr>
				{/foreach}
				<tr class="delete_range">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					{foreach from=$ranges name=ranges key=r item=range}
						{if $smarty.foreach.ranges.first}
							<td class="center">&nbsp;</td>
						{else}
							<td class="center"><button class="button">{l s='Delete'}</button</td>
						{/if}
					{/foreach}
				</tr>
			</table>
		</div>
