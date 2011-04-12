{* MFR081028 - information de shipping *}
<tr style="border-top: 0px solid #bdc2c9;">
	<td colspan="4" style="border-top: 0px solid #bdc2c9;width:100%;padding:0px 2px 0px 2px;">
		
		<div id="shipping_pref" {if !$my_carrier_selected}style="display:none;"{/if}>
			<div id="shipping_div">
				<div id="shipping_dates">
					<label class="title">{l s='Choose your delivery day' mod='dejala'}:</label><br class="clear"/>
					{foreach from=$dates item=dateItem name=dateLabelLoop}
						{assign var='curIdxDate' value=$smarty.foreach.dateLabelLoop.index}
						<div><label><input type="radio" name="shipping_day" id="shipd{$curIdxDate}" value="{$dateItem.value}" onClick="selectDay({$curIdxDate});" {if ($deliveryDateSelected==$dateItem.value)}checked="checked" {/if}/> {$dateItem.label}</label></div>
					{/foreach}
				</div>
				<div id="shipping_hours">
					<label class="title">{l s='Choose you delivery timeslot' mod='dejala'}:</label><br class="clear"/>
					{section name=hour start=0 loop=24 step=1}
						{assign var='curIdxhour' value=$smarty.section.hour.index}
						<div id="divhr{$curIdxhour}"><label><input type="radio" name="shipping_hour" id="shiphr{$curIdxhour}" value="{$curIdxhour}" OnClick="selectHour({$curIdxhour});" {if ($deliveryHourSelected==$curIdxhour)}checked="checked"{/if}/> {$hourLabels.$curIdxhour}</label></div>
					{/section}
				</div>
			</div>
			<br class="clear"/>
		</div>

	</td>
</tr>
{* MFR081028 - information de shipping *}
