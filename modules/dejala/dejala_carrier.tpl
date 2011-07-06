	<script>
		var djl_calendar = new Array({foreach from=$dates item=dateItem name=datesLoop}{if !$smarty.foreach.datesLoop.first}, {/if}new Array("{$dateItem.label}", "{$dateItem.value}", {$dateItem.start_hour}, {$dateItem.stop_hour}){/foreach}	);
		var deliveryDateSelected = {$deliveryDateIndexSelected};
		var deliveryHourSelected = {$deliveryHourSelected|intval};
	</script>

	<script type="text/javascript" src="{$timetable_js}"></script>
	<link href="{$timetable_css}" rel="stylesheet" type="text/css" media="all" />
	{literal}
		<script>
			$(document).ready(function() {
				$('input[name="id_carrier"]').each(function(idx, elt) {
					if ($(this).parent().hasClass("dejala")) {
						if ($(this).get(0).checked) {
							toggle_visibility('shipping_pref','1');
						}
						$(this).click(function() {
							toggle_visibility('shipping_pref', 1);
						});
					}
					else {
						$(this).click(function() {
							toggle_visibility('shipping_pref', 0);
						});
					}
				}) ;

				var mHour = deliveryHourSelected;
				selectDay(deliveryDateSelected);
				selectHour(mHour);
			});
		</script>
	{/literal}
	
	{*
		switchDay(deliveryDateSelected);
		switchHours(deliveryHourSelected);
		toggleShipping();
	*}

	{foreach from=$carriers item=carrier name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
					<td class="carrier_action radio dejala">
						<input type="hidden" name="dejala_id_carrier" value="{$carrier.id_carrier|intval}"/>
						<input type="hidden" name="dejala_id_product" value="{$product.id|intval}"/>
						<input type="radio" name="id_carrier" value="{$carrier.id_carrier|intval}" id="id_carrier{$carrier.id_carrier|intval}" {if $my_carrier_selected}checked="checked"{/if}"/>
					</td>
					<td class="carrier_name">
						<label for="id_carrier{$carrier.id_carrier|intval}">
							{if $carrier.img}<img src="{$carrier.img|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" />{else}{$carrier.name|escape:'htmlall':'UTF-8'}{/if}
						</label>
					</td>
					<td class="carrier_infos">{$carrier.delay|escape:'htmlall':'UTF-8'}</td>
					<td class="carrier_price">
						{if $carrier.price}
							<span class="price">
								{if $priceDisplay == 1}{convertPrice price=$carrier.price_tax_exc}{else}{convertPrice price=$carrier.price}{/if}
							</span>
							{if $priceDisplay == 1} {l s='(tax excl.)' mod='dejala'}{else} {l s='(tax incl.)' mod='dejala'}{/if}
						{else}
							{l s='Free!' mod='dejala'}
						{/if}
					</td>
				</tr>
	{/foreach}