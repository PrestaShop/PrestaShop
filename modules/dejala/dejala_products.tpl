		<form action="{$formAction}" method="post">
			<input type="hidden" name="method" value="products"/>
			<fieldset>
				<h4 class="clear">{l s='Please set you margin for deliveries' mod='dejala'}:</h4>

				<table class="table" width="100%" >
					<tr class='center'>
						<th rowspan="2" class='center bold'>{l s='Name' mod='dejala'}</th>
						<th colspan="2" class='center bold'>{l s='Buy price' mod='dejala'}</th>
						<th rowspan="2" class='center bold'>{l s='Margin TTC' mod='dejala'}</th>						
						<th colspan="2" class='center bold'>{l s='Customer price' mod='dejala'}</th>
						<th rowspan="2" class='center bold'>{l s='Description' mod='dejala'}</th>						
					</tr>
					<tr class='bold'>
						
						<th>{l s='Buy HT' mod='dejala'}</th>
						<th>{l s='Buy TTC' mod='dejala'}</th>
						
						<th>{l s='Sell HT' mod='dejala'}</th>
						<th>{l s='Sell TTC' mod='dejala'}</th>
						
					</tr>

					{foreach from=$products item=product name=productLoop}
						<tr {if ($smarty.foreach.dateLabelLoop.index % 2 == 0)}class='alt_row'{/if}>
							<td>{$product.code}</td>
							<td>{$product.price_notax}</td>
							<td>{$product.price}</td>
							<td><input type="text" name="margin_{$product.id}" value="{if ($product.margin>0)}+{/if}{$product.margin}"/></td>
							<td>{$product.public_price_notax}</td>
							<td>{$product.public_price}</td>
							<td>{$product.description}<br/>{l s='In' mod='dejala'} {$product.timelimit}{l s='h' mod='dejala'}</td>
						</tr>
					{/foreach}
				</table>
				<br/>
				<div><input type="submit" name="btnSubmit" value="{l s='Update settings' mod='dejala'}" class="button" /></div>
			</fieldset>
			<br/>
		</form>