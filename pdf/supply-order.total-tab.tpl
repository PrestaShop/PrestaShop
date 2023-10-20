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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
