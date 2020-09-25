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

<!--  TAX DETAILS -->
{if $tax_exempt}

	{l s='Exempt of VAT according to section 259B of the General Tax Code.' d='Shop.Pdf' pdf='true'}

{elseif (isset($tax_breakdowns) && $tax_breakdowns)}
	<table id="tax-tab" width="100%">
		<thead>
			<tr>
				<th class="header small">{l s='Tax Detail' d='Shop.Pdf' pdf='true'}</th>
				<th class="header small">{l s='Tax Rate' d='Shop.Pdf' pdf='true'}</th>
				{if $display_tax_bases_in_breakdowns}
					<th class="header small">{l s='Base price' d='Shop.Pdf' pdf='true'}</th>
				{/if}
				<th class="header-right small">{l s='Total Tax' d='Shop.Pdf' pdf='true'}</th>
			</tr>
		</thead>
		<tbody>
		{assign var=has_line value=false}

		{foreach $tax_breakdowns as $label => $bd}
			{assign var=label_printed value=false}

			{foreach $bd as $line}
				{if $line.rate == 0}
					{continue}
				{/if}
				{assign var=has_line value=true}
				<tr>
					<td class="white">
						{if !$label_printed}
							{if $label == 'product_tax'}
								{l s='Products' d='Shop.Pdf' pdf='true'}
							{elseif $label == 'shipping_tax'}
								{l s='Shipping' d='Shop.Pdf' pdf='true'}
							{elseif $label == 'ecotax_tax'}
								{l s='Ecotax' d='Shop.Pdf' pdf='true'}
							{elseif $label == 'wrapping_tax'}
								{l s='Wrapping' d='Shop.Pdf' pdf='true'}
							{/if}
							{assign var=label_printed value=true}
						{/if}
					</td>

					<td class="center white">
						{$line.rate} %
					</td>

					{if $display_tax_bases_in_breakdowns}
						<td class="right white">
							{if isset($is_order_slip) && $is_order_slip}- {/if}
							{displayPrice currency=$order->id_currency price=$line.total_tax_excl}
						</td>
					{/if}

					<td class="right white">
						{if isset($is_order_slip) && $is_order_slip}- {/if}
						{displayPrice currency=$order->id_currency price=$line.total_amount}
					</td>
				</tr>
			{/foreach}
		{/foreach}

		{if !$has_line}
		<tr>
			<td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
				{l s='No taxes' d='Shop.Pdf' pdf='true'}
			</td>
		</tr>
		{/if}

		</tbody>
	</table>

{/if}
<!--  / TAX DETAILS -->
