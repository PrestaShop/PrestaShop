{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table id="total-tab" width="100%">

	{if $order_slip->shipping_cost_amount > 0}
		<tr>
			{if $tax_excluded_display}
				<td class="grey" width="70%">{l s='Shipping (Tax Excl.)' d='Shop.Pdf' pdf='true'}</td>
			{else}
				<td class="grey" width="70%">{l s='Shipping (Tax Incl.)' d='Shop.Pdf' pdf='true'}</td>
			{/if}
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}
			</td>
		</tr>
	{/if}

	{if isset($order_details) && count($order_details) > 0}
		{if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
			{if $tax_excluded_display}
				<tr>
					<td class="grey" width="70%">
						{l s='Product Total (Tax Excl.)' d='Shop.Pdf' pdf='true'}
					</td>
					<td class="white" width="30%">
						- {displayPrice currency=$order->id_currency price=$order->total_products}
					</td>
				</tr>
			{else}
				<tr>
					<td class="grey" width="70%">
						{l s='Product Total (Tax Incl.)' d='Shop.Pdf' pdf='true'}
					</td>
					<td class="white" width="30%">
						- {displayPrice currency=$order->id_currency price=$order->total_products_wt}
					</td>
				</tr>
			{/if}
		{else}
			<tr>
				<td class="grey" width="70%">
					{l s='Product Total' d='Shop.Pdf' pdf='true'}
				</td>
				<td class="white" width="30%">
					- {displayPrice currency=$order->id_currency price=$order->total_products}
				</td>
			</tr>
		{/if}
	{/if}

	{if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
		<tr>
			<td class="grey" width="70%">
				{l s='Total Tax' d='Shop.Pdf' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}
			</td>
		</tr>
	{/if}

  {if $tax_excluded_display}
    <tr class="bold">
      <td class="grey" width="70%">
        {l s='Total (Tax Excl.)' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white" width="30%">
        {if $total_cart_rule}
          {assign var=total_paid value=0}
          {$total_paid = $order->total_paid_tax_excl - $total_cart_rule}
          - {displayPrice currency=$order->id_currency price=$total_paid}
        {else}
          - {displayPrice currency=$order->id_currency price=$order->total_paid_tax_excl}
        {/if}
      </td>
    </tr>
  {/if}

  <tr class="bold">
    <td class="grey" width="70%">
      {l s='Total (Tax Incl.)' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="30%">
      {if $total_cart_rule}
        {assign var=total_paid value=0}
        {$total_paid = $order->total_paid_tax_incl - $total_cart_rule}
        - {displayPrice currency=$order->id_currency price=$total_paid}
      {else}
        - {displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}
      {/if}
    </td>
  </tr>

</table>
