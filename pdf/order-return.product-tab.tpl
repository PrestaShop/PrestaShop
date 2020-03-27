{**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<table class="product" width="100%" cellpadding="4" cellspacing="0">

	<thead>
		<tr>
			<th class="product header small" width="60%">{l s='Items to be returned' d='Shop.Pdf' pdf='true'}</th>
			<th class="product header small" width="20%">{l s='Reference' d='Shop.Pdf' pdf='true'}</th>
			<th class="product header small" width="20%">{l s='Qty' d='Shop.Pdf' pdf='true'}</th>
		</tr>
	</thead>

	<tbody>
		<!-- PRODUCTS -->
		{foreach $products as $product}
			{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
			<tr class="product {$bgcolor_class}">
				<td class="product left">
					{$product.product_name}
				</td>
				<td class="product left">
					{if empty($product.product_reference)}
						---
					{else}
						{$product.product_reference}
					{/if}
				</td>
				<td class="product center">
					{$product.product_quantity}
				</td>
			</tr>
		{/foreach}
		<!-- END PRODUCTS -->
	</tbody>

</table>
