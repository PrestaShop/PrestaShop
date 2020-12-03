{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
