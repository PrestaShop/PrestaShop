{**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{assign var="currencySymbolBeforeAmount" value=$currency->format[0] === '¤'}
{if $product['customizedDatas']}
{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}
	<tr class="customized customized-{$product['id_order_detail']|intval} product-line-row">
		<td>
			<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
			{if isset($product['image']) && $product['image']->id|intval}{$product['image_tag']}{else}--{/if}
		</td>
		<td>
			<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product['product_id']|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}">
			<span class="productName">{$product['product_name']} - {l s='Customized' d='Admin.Orderscustomers.Feature'}</span><br />
			{if ($product['product_reference'])}{l s='Reference number:' d='Admin.Orderscustomers.Feature'} {$product['product_reference']}<br />{/if}
			{if ($product['product_supplier_reference'])}{l s='Supplier reference:' d='Admin.Orderscustomers.Feature'} {$product['product_supplier_reference']}{/if}
			</a>
		</td>
		<td>
			<span class="product_price_show">{displayPrice price=$product_price currency=$currency->id|intval}</span>
			{if $can_edit}
			<div class="product_price_edit" style="display:none;">
				<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
				<div class="form-group">
					<div class="fixed-width-xl">
						<div class="input-group">
							{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
							<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)}" size="5" />
							{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
						</div>
					</div>
					<br/>
					<div class="fixed-width-xl">
						<div class="input-group">
							{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
							<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)}" size="5" />
							{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
						</div>
					</div>
				</div>
			</div>
			{/if}
		</td>
		<td class="productQuantity text-center">{$product['customizationQuantityTotal']}</td>
		{if $display_warehouse}<td>&nbsp;</td>{/if}
		{if ($order->hasBeenPaid())}<td class="productQuantity text-center">{$product['customizationQuantityRefunded']}</td>{/if}
		{if ($order->hasBeenDelivered() || $order->hasProductReturned())}<td class="productQuantity text-center">{$product['customizationQuantityReturned']}</td>{/if}
		{if $stock_management}<td class="text-center">{$product['current_stock']}</td>{/if}
		<td class="total_product">
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{displayPrice price=Tools::ps_round($product['product_price'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{else}
			{displayPrice price=Tools::ps_round($product['product_price_wt'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{/if}
		</td>
		<td class="cancelQuantity standard_refund_fields current-edit" style="display:none" colspan="2">
			&nbsp;
		</td>
		<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
		<td class="partial_refund_fields current-edit" style="text-align:left;display:none;"></td>
		{if ($can_edit && !$order->hasBeenDelivered())}
			<td class="product_action text-right">
				{* edit/delete controls *}
				<div class="btn-group">
					<button type="button" class="btn btn-default edit_product_change_link">
						<i class="icon-pencil"></i>
						{l s='Edit' d='Admin.Actions'}
					</button>
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="#" class="delete_product_line">
								<i class="icon-trash"></i>
								{l s='Delete' d='Admin.Actions'}
							</a>
						</li>
					</ul>
				</div>
				{* Update controls *}
				<button type="button" class="btn btn-default submitProductChange" style="display: none;">
					<i class="icon-ok"></i>
					{l s='Update' d='Admin.Actions'}
				</button>
				<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
					<i class="icon-remove"></i>
					{l s='Cancel' d='Admin.Actions'}
				</button>
			</td>
		{/if}
	</tr>
	{foreach $product['customizedDatas'] as $customizationPerAddress}
		{foreach $customizationPerAddress as $customizationId => $customization}
			<tr class="customized customized-{$product['id_order_detail']|intval}">
				<td colspan="2">
				<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
					<div class="form-horizontal">
						{foreach $customization.datas as $type => $datas}
							{if ($type == Product::CUSTOMIZE_FILE)}
								{foreach from=$datas item=data}
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong>{if $data['name']}{$data['name']}{else}{l s='Picture #' d='Admin.Orderscustomers.Feature'}{$data@iteration}{/if}</strong></span>
										<div class="col-lg-8">
											<a href="displayImage.php?img={$data['value']}&amp;name={$order->id|intval}-file{$data@iteration}" class="_blank">
												<img class="img-thumbnail" src="{$smarty.const._THEME_PROD_PIC_DIR_}{$data['value']}_small" alt=""/>
											</a>
										</div>
									</div>
								{/foreach}
							{elseif ($type == Product::CUSTOMIZE_TEXTFIELD)}
								{foreach from=$datas item=data}
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong>{if $data['name']}{$data['name']}{else}{l s='Text #%s' sprintf=[$data@iteration] d='Admin.Orderscustomers.Feature'}{/if}</strong></span>
										<div class="col-lg-8">
											<p class="form-control-static">{$data['value']}</p>
										</div>
									</div>
								{/foreach}
							{/if}
						{/foreach}
					</div>
				</td>
				<td>-</td>
				<td class="productQuantity text-center">
					<span class="product_quantity_show{if (int)$customization['quantity'] > 1} red bold{/if}">{$customization['quantity']}</span>
					{if $can_edit}
					<span class="product_quantity_edit" style="display:none;">
						<input type="text" name="product_quantity[{$customizationId|intval}]" class="edit_product_quantity" value="{$customization['quantity']|htmlentities}" size="2" />
					</span>
					{/if}
				</td>
				{if $display_warehouse}<td>&nbsp;</td>{/if}
				{if ($order->hasBeenPaid())}
				<td class="text-center">
					{if !empty($product['amount_refund'])}
					{l s='%quantity_refunded% (%amount_refunded% refund)' sprintf=['%quantity_refunded%' => $customization['quantity_refunded'], '%amount_refunded%' => $product['amount_refund']] d='Admin.Orderscustomers.Feature'}
					{/if}
					<input type="hidden" value="{$product['quantity_refundable']}" class="partialRefundProductQuantity" />
					<input type="hidden" value="{(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal']))}" class="partialRefundProductAmount" />
				</td>
				{/if}
				{if ($order->hasBeenDelivered())}<td class="text-center">{$customization['quantity_returned']}</td>{/if}
				<td class="text-center">-</td>
				<td class="total_product">
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{displayPrice price=Tools::ps_round($product['product_price'] * $customization['quantity'], 2) currency=$currency->id|intval}
					{else}
						{displayPrice price=Tools::ps_round($product['product_price_wt'] * $customization['quantity'], 2) currency=$currency->id|intval}
					{/if}
				</td>
				<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
					<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$customization['quantity_returned']|intval}" />
					<input type="hidden" name="totalQty" id="totalQty" value="{$customization['quantity']|intval}" />
					<input type="hidden" name="productName" id="productName" value="{$product['product_name']}" />
					{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($customization['quantity_returned']) < (int)($customization['quantity']))}
						<input type="checkbox" name="id_customization[{$customizationId|intval}]" id="id_customization[{$customizationId|intval}]" value="{$product['id_order_detail']|intval}" onchange="setCancelQuantity(this, {$customizationId|intval}, {$customization['quantity'] - $product['customizationQuantityTotal'] - $product['product_quantity_reinjected']})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
					{else}
					--
				{/if}
				</td>
				<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
				{if ($customization['quantity_returned'] + $customization['quantity_refunded'] >= $customization['quantity'])}
					<input type="hidden" name="cancelCustomizationQuantity[{$customizationId|intval}]" value="0" />
				{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
					<input type="text" id="cancelQuantity_{$customizationId|intval}" name="cancelCustomizationQuantity[{$customizationId|intval}]" size="2" onclick="selectCheckbox(this);" value="" />0/{$customization['quantity']-$customization['quantity_refunded']}
				{/if}
				</td>
				<td class="partial_refund_fields current-edit" colspan="2" style="display:none; width: 250px;">
					{if $product['quantity_refundable'] > 0}
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{assign var='amount_refundable' value=$product['amount_refundable']}
					{else}
						{assign var='amount_refundable' value=$product['amount_refundable_tax_incl']}
					{/if}
					<div class="form-group">
						<div class="{if $product['amount_refundable'] > 0}col-lg-4{else}col-lg-12{/if}">
							<label class="control-label">
								{l s='Quantity:' d='Admin.Orderscustomers.Feature'}
							</label>
							<div class="input-group">
								<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="partialRefundProductQuantity[{$product['id_order_detail']|intval}]" value="{if ($customization['quantity']-$customization['quantity_refunded']) >0}1{else}0{/if}" />
								<div class="input-group-addon">/ {$product['quantity_refundable']}</div>
							</div>
						</div>
						<div class="{if $product['quantity_refundable'] > 0}col-lg-8{else}col-lg-12{/if}">
							<label class="control-label">
								<span class="title_box ">{l s='Amount:' d='Admin.Orderscustomers.Feature'}</span>
								<small class="text-muted">({$smarty.capture.TaxMethod})</small>
							</label>
							<div class="input-group">
								{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign}</div>{/if}
								<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[{$product['id_order_detail']|intval}]" />
								{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign}</div>{/if}
							</div>
							<p class="help-block"><i class="icon-warning-sign"></i> {l s='(Max %amount_refundable% %tax_method%)' sprintf=[ '%amount_refundable%' => Tools::displayPrice(Tools::ps_round($amount_refundable, 2), $currency->id), '%tax_method%' => $smarty.capture.TaxMethod] d='Admin.Orderscustomers.Help'}</p>
						</div>
					</div>
					{/if}
				</td>
				{if ($can_edit && !$order->hasBeenDelivered())}
					<td class="edit_product_fields" colspan="2" style="display:none"></td>
					<td class="product_action" style="text-align:right"></td>
				{/if}
			</tr>
		{/foreach}
	{/foreach}
{/if}
