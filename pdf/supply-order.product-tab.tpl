{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{l s='Products ordered:' d='Shop.Pdf' pdf='true'}<br/>

<table class="product small" width="100%" cellpadding="4" cellspacing="0">

	<thead>
	<tr>
		<th class="product header small" width="14%">{l s='Reference' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="21%">{l s='Designation' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="5%">{l s='Qty' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="10%">{l s='Unit Price TE' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="11%">{l s='Total TE' d='Shop.Pdf' pdf='true'} <br /> {l s='Before discount' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="9%">{l s='Discount Rate' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="11%">{l s='Total TE' d='Shop.Pdf' pdf='true'} <br /> {l s='After discount' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="9%">{l s='Tax rate' d='Shop.Pdf' pdf='true'}</th>
		<th class="product header small" width="10%">{l s='Total TI' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	</thead>

	<tbody>

	{foreach $supply_order_details as $supply_order_detail}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
			<td class="product left">
				{$supply_order_detail->supplier_reference}
			</td>
			<td class="product left">
				{$supply_order_detail->name}
			</td>
			<td  class="product right">
				{$supply_order_detail->quantity_expected}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->unit_price_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$supply_order_detail->discount_rate}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_with_discount_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$supply_order_detail->tax_rate}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_ti} {$currency->suffix}
			</td>
		</tr>
	{/foreach}

	</tbody>

</table>
