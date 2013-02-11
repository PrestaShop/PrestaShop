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

{$content}
<br /><br />
<h2>{l s='Fees by carrier, geographical zone and ranges'}</h2>
<form action="{$action_fees}" id="fees" name="fees" method="post">
	<fieldset>
		<legend><img src="../img/admin/delivery.gif" />{l s='Fees'}</legend>
		{if empty($carriers)}
			{l s='If you only have free carriers, there\'s no need to configure delivery prices.'}
		{else}
			<b>{l s='Carrier:'} </b>
			<select name="id_carrier2" onchange="$('#fees').attr('action', $('#fees').attr('action')+'&id_carrier='+$(this).attr('value')+'#fees'); $('#fees').submit();">
				{foreach $carriers AS $carrier}
					<option value="{$carrier['id_carrier']|intval}" {if $carrier['id_carrier'] == $id_carrier} selected="selected"{/if}>{$carrier['name']}</option>
				{/foreach}
			</select><br />

			<table class="table space" cellpadding="0" cellspacing="0">
				<tr>
					<th>{l s='Zone / Range'}</th>
					{if !$carrierSelected->is_free}
						{foreach $ranges AS $range}
							<th style="font-size: 11px;">{$range['delimiter1']|floatval}{$suffix} {l s='to'} {$range['delimiter2']|floatval}{$suffix}</th>
						{/foreach}
					{/if}
				</tr>
				{if sizeof($ranges) && !$carrierSelected->is_free}
					{if sizeof($zones) > 1}
						<tr>
							<th style="height: 30px;">{l s='All'}</th>
							{foreach $ranges AS $range}
								<td class="center">
									{$currency->getSign('left')}
									<input type="text" id="fees_all_{$range[$rangeIdentifier]}" onchange="this.value = this.value.replace(/,/g, '.');" onkeyup="if ((event.keyCode||event.which) != 9){ spreadFees({$range[$rangeIdentifier]})}" style="width: 45px;" />
									{$currency->getSign('right')} {l s='(tax excl.)'}
								</td>
							{/foreach}
						</tr>
					{/if}
		
					{foreach $zones AS $zone}
						<tr>
							<th style="height: 30px;">{$zone['name']}</th>
							{foreach $ranges AS $range}
								{if isset($deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]])}
									{$price = $deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]]}
								{else}
									{$price = '0.00'}
								{/if}
								<td class="center">
									{$currency->getSign('left')}
									<input 
										type="text" 
										class="fees_{$range[$rangeIdentifier]}" 
										onchange="this.value = this.value.replace(/,/g, '.');" name="fees_{$zone['id_zone']}_{$range[$rangeIdentifier]}" onkeyup="clearAllFees({$range[$rangeIdentifier]})" 
										value="{$price|string_format:"%.6f"}"
										style="width: 45px;" 
									/>
									{$currency->getSign('right')} {l s='(tax excl.)'}
								</td>
							{/foreach}
						</tr>
					{/foreach}
				{/if}
				<tr>
					<td colspan="{$ranges|sizeof + 1}" class="center" style="border-bottom: none; height: 40px;">
						<input type="hidden" name="submitFees{$table}" value="1" />
					{if sizeof($ranges) && !$carrierSelected->is_free}
						<input type="submit" value="{l s='   Save   '}" class="button" />
					{else if $carrierSelected->is_free}
						{l s='This is a free carrier'}
					{else}
						{l s='No ranges is set for this carrier'}
					{/if}
					</td>
				</tr>
			</table>
		{/if}
		<input type="hidden" name="id_carrier" value="{$id_carrier}" />
	</fieldset>
</form>
