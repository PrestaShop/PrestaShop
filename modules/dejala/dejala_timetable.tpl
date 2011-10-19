<tr style="border-top: 0px solid #bdc2c9;">
	<td colspan="4" style="border-top: 0px solid #bdc2c9;width:100%;padding:0px 2px 0px 2px;">
		
		<div id="djl_shipping_pref" class="{if $djlCarrierChecked < 0}hidden{/if}">
			<div id="shipping_div">
				<div id="shipping_dates" class="{if $isCartOutOfStock}disabled{/if}">
					<label class="title">{l s='Choose your delivery day' mod='dejala'}:</label><br class="clear"/>
					{foreach from=$dates item=dateItem name=dateLabelLoop}
						{assign var='curIdxDate' value=$smarty.foreach.dateLabelLoop.index}
						<div>
							<input type="radio" name="shipping_day" id="shipd{$curIdxDate}" autocomplete="off" value="{$dateItem.value}" onClick="selectDay();" {if ($deliveryDateSelected==$dateItem.value)}checked="checked"{/if} {if $isCartOutOfStock}disabled="disabled"{/if}/>
							<label for="shipd{$curIdxDate}">{$dateItem.label}</label>
						</div>						
					{/foreach}
				</div>
				<div id="shipping_hours" class="{if $isCartOutOfStock}disabled{/if}">
					<label class="title">{l s='Choose you delivery timeslot' mod='dejala'}:</label><br class="clear"/>
					{section name=hour start=0 loop=24 step=1}
						{assign var='curIdxhour' value=$smarty.section.hour.index}
						<div id="divhr{$curIdxhour}">
							<input type="radio" name="shipping_hour" id="shiphr{$curIdxhour}" autocomplete="off" value="{$curIdxhour}" OnClick="selectHour();" {if ($deliveryHourSelected==$curIdxhour)}checked="checked"{/if} {if $isCartOutOfStock}disabled="disabled"{/if}/>
							<label for="shiphr{$curIdxhour}">{$hourLabels.$curIdxhour}</label>
						</div>
					{/section}
				</div>
			</div>
			<br class="clear"/>
		</div>

	</td>
</tr>
