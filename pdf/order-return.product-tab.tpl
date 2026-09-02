{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
