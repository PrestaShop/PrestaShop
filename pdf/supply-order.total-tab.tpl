{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{l s='Summary:' d='Shop.Pdf' pdf='true'}<br/>

<table id="total-tab" width="100%">

	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TE' d='Shop.Pdf' pdf='true'} <br /> {l s='(Before discount)' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Order Discount' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->discount_value_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TE' d='Shop.Pdf' pdf='true'} <br /> {l s='(After discount)' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_with_discount_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Tax value' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_tax} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TI' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total to pay' d='Shop.Pdf' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}
		</td>
	</tr>

</table>
