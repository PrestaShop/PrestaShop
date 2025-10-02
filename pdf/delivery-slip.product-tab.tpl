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
<table class="product" width="100%" cellpadding="4" cellspacing="0">

	<thead>
		<tr>
			<th class="product header small" width="25%">{l s='Reference' d='Shop.Pdf' pdf='true'}</th>
			<th class="product header small" width="65%">{l s='Product' d='Shop.Pdf' pdf='true'}</th>
			<th class="product header small" width="10%">{l s='Qty' d='Shop.Pdf' pdf='true'}</th>
		</tr>
	</thead>

	<tbody>
		<!-- PRODUCTS -->
		{foreach $order_details as $order_detail}
			{if $order_detail.product_quantity-$order_detail.product_quantity_refunded > 0}
				{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
				<tr class="product {$bgcolor_class}">

					<td class="product left">
						{if empty($order_detail.product_reference)}
							---
						{else}
							{$order_detail.product_reference}
						{/if}
					</td>
					<td class="product left">
						{if $display_product_images}
							<table width="100%">
								<tr>
									<td width="15%">
										{if isset($order_detail.image) && $order_detail.image->id}
											{$order_detail.image_tag}
										{/if}
									</td>
									<td width="5%">&nbsp;</td>
									<td width="80%">
										{$order_detail.product_name}
									</td>
								</tr>
							</table>
						{else}
							{$order_detail.product_name}
						{/if}
					</td>
					<td class="product center">
						{$order_detail.product_quantity-$order_detail.product_quantity_refunded}
					</td>

				</tr>

				{foreach $order_detail.customizedDatas as $customizationPerAddress}
					{foreach $customizationPerAddress as $customizationId => $customization}
						<tr class="customization_data {$bgcolor_class}">
							<td class="center"> &nbsp;</td>

							<td>
								{if isset($customization.datas[Product::CUSTOMIZE_TEXTFIELD]) && count($customization.datas[Product::CUSTOMIZE_TEXTFIELD]) > 0}
									<table style="width: 100%;">
										{foreach $customization.datas[Product::CUSTOMIZE_TEXTFIELD] as $customization_infos}
											<tr>
												<td style="width: 30%;">
													{$customization_infos.name|string_format:{l s='%s:' d='Shop.Pdf' pdf='true'}}
												</td>
												<td>{$customization_infos.value}</td>
											</tr>
										{/foreach}
									</table>
								{/if}

								{if isset($customization.datas[Product::CUSTOMIZE_FILE]) && count($customization.datas[Product::CUSTOMIZE_FILE]) > 0}
									<table style="width: 100%;">
										<tr>
											<td style="width: 30%;">{l s='image(s):' d='Shop.Pdf' pdf='true'}</td>
											<td>{count($customization.datas[Product::CUSTOMIZE_FILE])}</td>
										</tr>
									</table>
								{/if}
							</td>

							<td class="center">
								({if $customization.quantity == 0}1{else}{$customization.quantity-$order_detail.product_quantity_refunded}{/if})
							</td>

						</tr>
					{/foreach}
				{/foreach}
			{/if}



		{/foreach}
		<!-- END PRODUCTS -->
	</tbody>

</table>
