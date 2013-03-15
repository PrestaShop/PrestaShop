{if !$opc}
	<script type="text/javascript">
		// <![CDATA[
		var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
		var currencyRate = '{$currencyRate|floatval}';
		var currencyFormat = '{$currencyFormat|intval}';
		var currencyBlank = '{$currencyBlank|intval}';
		var txtProduct = "{l s='product' js=1}";
		var txtProducts = "{l s='products' js=1}";
		// ]]>
	</script>
{/if}

{if isset($empty)}
<p class="warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
<p class="warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<div id="displayMobileShoppingCartTop">
		{hook h="displayMobileShoppingCartTop"}
	</div>
	<div data-role="content" id="content" class="cart">
		{include file="$tpl_dir./errors.tpl"}

		<h2>{l s='Your payment method'}</h2>
		{if !$opc}
			{assign var='current_step' value='payment'}
			{include file="$tpl_dir./errors.tpl"}
		{else}
			<div id="opc_payment_methods" class="opc-main-block">
				<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
		{/if}


		<div class="paiement_block">
		
			<div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT}</div>
			
			{if $HOOK_PAYMENT}
				{if !$opc}
			<div id="order-detail-content" class="table_block">
				<table id="cart_summary" data-role="table" class="ui-body-d ui-shadow ui-responsive table-stroke" data-mode="reflow">
					<thead>
						<tr class="ui-bar-a">
							<th class="cart_product first_item">{l s='Product'}</th>
							<th class="cart_description item">{l s='Description'}</th>
							<th class="cart_availability item">{l s='Avail.'}</th>
							<th class="cart_unit item" style="min-width:100px">{l s='Unit price'}</th>
							<th class="cart_quantity item" style="min-width:30px">{l s='Qty'}</th>
							<th class="cart_total last_item" style="min-width:100px">{l s='Total'}</th>
						</tr>
					</thead>
					<tfoot>
						{if $use_taxes}
							{if $priceDisplay}
								<tr class="cart_total_price">
									<td colspan="5" style="text-align:right;font-weight:bold">{if $display_tax_label}{l s='Total products (tax excl.):'}{else}{l s='Total products:'}{/if}</td>
									<td class="price" id="total_product">{displayPrice price=$total_products}</td>
								</tr>
							{else}
								<tr class="cart_total_price">
									<td colspan="5" style="text-align:right;font-weight:bold">{if $display_tax_label}{l s='Total products (tax incl.):'}{else}{l s='Total products:'}{/if}</td>
									<td class="price" id="total_product">{displayPrice price=$total_products_wt}</td>
								</tr>
							{/if}
						{else}
							<tr class="cart_total_price">
								<td colspan="5" style="text-align:right;font-weight:bold">{l s='Total products:'}</td>
								<td class="price" id="total_product">{displayPrice price=$total_products}</td>
							</tr>
						{/if}
						<tr class="cart_total_voucher" {if $total_wrapping == 0}style="display:none"{/if}>
							<td colspan="5" style="text-align:right;font-weight:bold">
							{if $use_taxes}
								{if $priceDisplay}
									{if $display_tax_label}{l s='Total gift wrapping (tax excl.):'}{else}{l s='Total gift wrapping cost:'}{/if}
								{else}
									{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift wrapping cost:'}{/if}
								{/if}
							{else}
								{l s='Total gift wrapping cost:'}
							{/if}
							</td>
							<td class="price-discount price" id="total_wrapping">
							{if $use_taxes}
								{if $priceDisplay}
									{displayPrice price=$total_wrapping_tax_exc}
								{else}
									{displayPrice price=$total_wrapping}
								{/if}
							{else}
								{displayPrice price=$total_wrapping_tax_exc}
							{/if}
							</td>
						</tr>
						{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
							<tr class="cart_total_delivery">
								<td colspan="5" style="text-align:right;font-weight:bold">{l s='Shipping:'}</td>
								<td class="price" id="total_shipping">{l s='Free Shipping!'}</td>
							</tr>
						{else}
							{if $use_taxes}
								{if $priceDisplay}
									<tr class="cart_total_delivery" {if $shippingCost <= 0} style="display:none"{/if}>
										<td colspan="5" style="text-align:right;font-weight:bold">{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</td>
										<td class="price" id="total_shipping">{displayPrice price=$shippingCostTaxExc}</td>
									</tr>
								{else}
									<tr class="cart_total_delivery"{if $shippingCost <= 0} style="display:none"{/if}>
										<td colspan="5" style="text-align:right;font-weight:bold">{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</td>
										<td class="price" id="total_shipping" >{displayPrice price=$shippingCost}</td>
									</tr>
								{/if}
							{else}
								<tr class="cart_total_delivery"{if $shippingCost <= 0} style="display:none"{/if}>
									<td colspan="5" style="text-align:right;font-weight:bold">{l s='Total shipping:'}</td>
									<td class="price" id="total_shipping" >{displayPrice price=$shippingCostTaxExc}</td>
								</tr>
							{/if}
						{/if}
						<tr class="cart_total_voucher" {if $total_discounts == 0}style="display:none"{/if}>
							<td colspan="5" style="text-align:right;font-weight:bold">
							{if $use_taxes}
								{if $priceDisplay}
									{if $display_tax_label}{l s='Total vouchers (tax excl.):'}{else}{l s='Total vouchers:'}{/if}
								{else}
									{if $display_tax_label}{l s='Total vouchers (tax incl.):'}{else}{l s='Total vouchers:'}{/if}
								{/if}
							{else}
								{l s='Total vouchers:'}
							{/if}
							</td>
							<td class="price-discount price" id="total_discount">
							{if $use_taxes}
								{if $priceDisplay}
									{displayPrice price=$total_discounts_tax_exc*-1}
								{else}
									{displayPrice price=$total_discounts*-1}
								{/if}
							{else}
								{displayPrice price=$total_discounts_tax_exc*-1}
							{/if}
							</td>
						</tr>
						{if $use_taxes}
							{if $priceDisplay && $total_tax != 0}
								<tr class="cart_total_tax">
									<td colspan="5">{l s='Total tax:'}</td>
									<td class="price" id="total_tax" >{displayPrice price=$total_tax}</td>
								</tr>
							{/if}
						<tr class="cart_total_price">
							<td colspan="5" id="cart_voucher" class="cart_voucher" style="text-align:right;font-weight:bold">
							{l s='Total:'}
							{if $voucherAllowed}
								{if isset($errors_discount) && $errors_discount}
									<ul class="error">
									{foreach from=$errors_discount key=k item=error}
										<li>{$error|escape:'htmlall':'UTF-8'}</li>
									{/foreach}
									</ul>
								{/if}
							{/if}
							</td>
							<td class="price total_price_container" id="total_price_container">
								<span>{displayPrice price=$total_price}</span>
							</td>
						</tr>
						{else}
						<tr class="cart_total_price">
							<td colspan="5" id="cart_voucher" class="cart_voucher" style="text-align:right;font-weight:bold">
							{l s='Total:'}
							{if $voucherAllowed}
							<div id="cart_voucher" class="table_block">
								{if isset($errors_discount) && $errors_discount}
									<ul class="error">
									{foreach from=$errors_discount key=k item=error}
										<li>{$error|escape:'htmlall':'UTF-8'}</li>
									{/foreach}
									</ul>
								{/if}
								{if $voucherAllowed}
								<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
									<fieldset>
										<p class="title_block"><label for="discount_name">{l s='Vouchers'}</label></p>
										<p>
											<input type="text" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
										</p>
										<p class="submit"><input type="hidden" name="submitDiscount" /><input type="submit" name="submitAddDiscount" value="{l s='ok'}" class="button" /></p>
									{if $displayVouchers}
										<p id="title" class="title_offers">{l s='Take advantage of our offers:'}</p>
										<div id="display_cart_vouchers">
										{foreach from=$displayVouchers item=voucher}
											<span onclick="$('#discount_name').val('{$voucher.name}');return false;" class="voucher_name">{$voucher.name}</span> - {$voucher.description} <br />
										{/foreach}
										</div>
									{/if}
									</fieldset>
								</form>
								{/if}
							</div>
							{/if}
							</td>
							<td class="price total_price_container" id="total_price_container">
								<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
							</td>
						</tr>
						{/if}
					</tfoot>
					<tbody>
					{foreach from=$products item=product name=productLoop}
						{assign var='mobile_template_dir' value=$smarty.const._PS_THEME_MOBILE_DIR_}
						{assign var='productId' value=$product.id_product}
						{assign var='productAttributeId' value=$product.id_product_attribute}
						{assign var='quantityDisplayed' value=0}
						{assign var='cannotModify' value=1}
						{assign var='odd' value=$product@iteration%2}
						{assign var='noDeleteButton' value=1}
						{* Display the product line *}
						{include file="$mobile_template_dir./shopping-cart-product-line.tpl"}
						{* Then the customized datas ones*}
						{if isset($customizedDatas.$productId.$productAttributeId)}
							{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization'}
								<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
									<td colspan="4">
										{foreach from=$customization.datas key='type' item='datas'}
											{if $type == $CUSTOMIZE_FILE}
												<div class="customizationUploaded">
													<ul class="customizationUploaded">
														{foreach from=$datas item='picture'}
															<li>
																<img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" />
															</li>
														{/foreach}
													</ul>
												</div>
											{elseif $type == $CUSTOMIZE_TEXTFIELD}
												<ul class="typedText">
													{foreach from=$datas item='textField' name='typedText'}
														<li>
															{if $textField.name}
																{l s='%s:' sprintf=$textField.name}
															{else}
																{l s='Text #%s:' sprintf=$smarty.foreach.typedText.index+1}
															{/if}
															{$textField.value}
														</li>
													{/foreach}
												</ul>
											{/if}
										{/foreach}
									</td>
									<td class="cart_quantity">
										{if isset($cannotModify) AND $cannotModify == 1}
											<span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
										{else}
											<div style="float:right">
												<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" title="{l s='Delete this customization'}" width="11" height="13" class="icon" /></a>
											</div>
											<div id="cart_quantity_button" style="float:left">
											<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" /></a><br />
											{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
											<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
												<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
											</a>
											{else}
											<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='Subtract'}">
												<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
											</a>
											{/if}
											</div>
											<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_hidden"/>
											<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"/>
										{/if}
									</td>
									<td class="cart_total"></td>
								</tr>
								{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
							{/foreach}
							{* If it exists also some uncustomized products *}
							{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl"}{/if}
						{/if}
					{/foreach}
					{assign var='last_was_odd' value=$product@iteration%2}
					{foreach $gift_products as $product}
						{assign var='productId' value=$product.id_product}
						{assign var='productAttributeId' value=$product.id_product_attribute}
						{assign var='quantityDisplayed' value=0}
						{assign var='odd' value=($product@iteration+$last_was_odd)%2}
						{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
						{assign var='cannotModify' value=1}
						{* Display the gift product line *}
						{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
					{/foreach}
					</tbody>
				{if count($discounts)}
					<tbody>
					{foreach from=$discounts item=discount name=discountLoop}
						<tr class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
							<td class="cart_discount_name" colspan="2">{$discount.name}</td>
							<td class="cart_discount_description" colspan="3">{$discount.description}</td>
							<td class="cart_discount_price">
								<span class="price-discount">
									{if $discount.value_real > 0}
										{if !$priceDisplay}
											{displayPrice price=$discount.value_real*-1}
										{else}
											{displayPrice price=$discount.value_tax_exc*-1}
										{/if}
									{/if}
								</span>
							</td>
						</tr>
					{/foreach}
					</tbody>
				{/if}
				</table>
			</div>
			{/if}
				{if $opc}<div id="opc_payment_methods-content">{/if}
					<div id="HOOK_PAYMENT">{$HOOK_PAYMENT}</div>
				{if $opc}</div>{/if}
			{else}
				<p class="warning">{l s='No payment modules have been installed.'}</p>
			{/if}
			
			{if !$opc}
				<p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=2{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" data-role="button" data-icon="back" data-ajax="false">&laquo; {l s='Previous'}</a></p>
			{else}
				</div>
			{/if}
		</div>
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		{hook h="displayMobileShoppingCartBottom"}
	</div>
{/if}