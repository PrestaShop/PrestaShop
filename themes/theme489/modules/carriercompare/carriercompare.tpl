{if !$opc}
<script type="text/javascript">
	// <![CDATA[
	var taxEnabled = "{$use_taxes}";
	var displayPrice = "{$priceDisplay}";
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var id_carrier = '{$id_carrier|intval}';
	var id_state = '{$id_state|intval}';
	var txtFree = "{l s='Free!' mod='carriercompare'}";
	//]]>
</script>
<form class="std" id="compare_shipping_form" method="POST" action="#" >
	<fieldset id="compare_shipping">
		<h3>{l s='Estimate your shipping & taxes' mod='carriercompare'}</h3>
		<div id="carriercompare_errors" style="display:none;">
			<ul id="carriercompare_errors_list">
			</ul>
		</div>
		<p class="select">
			<label for="id_country">{l s='Country' mod='carriercompare'}</label>
			<select name="id_country" id="id_country">
				{foreach from=$countries item=country}
					<option value="{$country.id_country}" {if $id_country == $country.id_country}selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</p>
		<p class="select" id="states" style="display:none;">
			<label for="id_state">{l s='State' mod='carriercompare'}</label>
			<select name="id_state" id="id_state">
			</select>
		</p>
		<p class="text">
			<label for="zipcode">{l s='Zipcode' mod='carriercompare'}</label>
			<input type="text" name="zipcode" id="zipcode" value="{$zipcode|escape:'htmlall':'UTF-8'}"/>
		</p>
		<div id="availableCarriers" style="display: none;">
			<table cellspacing="0" cellpadding="0" id="availableCarriers_table" class="std">
				<thead>
					<tr>
						<th class="carrier_action first_item"></th>
						<th class="carrier_name item">{l s='Carrier' mod='carriercompare'}</th>
						<th class="carrier_infos item">{l s='Information' mod='carriercompare'}</th>
						<th class="carrier_price last_item">{l s='Price' mod='carriercompare'}</th>
					</tr>
				</thead>
				<tbody id="carriers_list">
				</tbody>
			</table>
		</div>
		<p class="warning" id="noCarrier" style="display:none;">{l s='No carrier is available for this selection' mod='carriercompare'}</p>
		<p class="center">
			<input class="exclusive_large" id="carriercompare_submit" type="submit" name="carriercompare_submit" value="{l s='Update my shipping option' mod='carriercompare'}"/>
		</p>
	</fieldset>
</form>
{/if}