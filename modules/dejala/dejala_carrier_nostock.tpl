	{foreach from=$carriers item=carrier name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
					<td class="carrier_action radio dejala">
						<input type="hidden" name="dejala_id_carrier" value="{$carrier.id_carrier|intval}"/>
						<input type="hidden" name="dejala_id_product" value="{$product.id|intval}"/>
						<input type="radio" name="id_carrier" value="{$carrier.id_carrier|intval}" id="id_carrier{$carrier.id_carrier|intval}" {if $carrier.id_carrier == $checked}checked="checked"{/if} />
					</td>
					<td class="carrier_name">
						<label for="id_carrier{$carrier.id_carrier|intval}">
							{if $carrier.img}<img src="{$carrier.img|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" />{else}{$carrier.name|escape:'htmlall':'UTF-8'}{/if}
						</label>
					</td>
					<td class="carrier_infos">{$nostock_info}</td>
					<td class="carrier_price">{if $carrier.price}
						<span class="price">
								{if $priceDisplay == 1}{convertPrice price=$carrier.price_tax_exc}{else}{convertPrice price=$carrier.price}{/if}
							</span>
							{if $priceDisplay == 1} {l s='(tax excl.)' mod='dejala'}{else} {l s='(tax incl.)' mod='dejala'}{/if}
						{else}{l s='Free!'}
						{/if}
					</td>
				</tr>
	{/foreach}