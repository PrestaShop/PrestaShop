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

		<h2>{l s='List of products'}</h2>
		{if isset($products)}
		<ul data-role="listview" data-inset="true" data-split-theme="d" data-split-icon="delete">
			{foreach $products as $product}
				<li id="element_product_{$product.id_product}">
					<a>
						<input type="hidden" name="cart_product_id[]" value="{$product.id_product}"/>
						<input type="hidden" id="cart_product_attribute_id_{$product.id_product}" value="{$product.id_product_attribute|intval}"/>
						<input type="hidden" id="cart_product_address_delivery_id_{$product.id_product}" value="{$product.id_address_delivery}"/>

						<div class="fl width-20">
							<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" class="img_product_cart" />
						</div>
						<div class="fl width-60 padding-left-5px">
							<h3>{$product.name}</h3>
							{if $product.reference}<p>{l s='Ref:'} {$product.reference}</p>{/if}
							<p>{$product.description_short}</p>
						</div>
						<div class="clear"></div>

						<table class="width-100">
							<thead>
								<tr>
									<td class="width-40">{l s='Unit price'}</td>
									<td>{l s='Qty'}</td>
									<td class="width-40">{l s='Total'}</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{displayPrice price=$product.price_wt}</td>
									<td>
										<input
											type="number"
											class="qty-field cart_quantity_input"
											name="product_cart_quantity_{$product.id_product}"
											value="{$product.cart_quantity}"
											min="0"
											max="{$product.quantity_available}"
											data-mini="true"
											data-initial-quantity="{$product.cart_quantity}"
											data-id-product="{$product.id_product}"
											data-id-product-attribute="{$product.id_product_attribute}" />
									</td>
									<td class="right">{displayPrice price=$product.total_wt}</td>
								</tr>
							</tbody>
						</table>
					</a>
					<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}" data-ajax="false">{l s='Delete'}</a>
				</li>
			{/foreach}
			{assign var='last_was_odd' value=$product@iteration%2}
			{foreach $gift_products as $product}
				<li id="element_product_{$product.id_product}">
					<a style="padding-right:10px">
						{assign var='productId' value=$product.id_product}
						{assign var='productAttributeId' value=$product.id_product_attribute}
						{assign var='quantityDisplayed' value=0}
						{assign var='odd' value=($product@iteration+$last_was_odd)%2}
						{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
						{assign var='cannotModify' value=1}
						{* Display the gift product line *}
						{include file="./shopping-cart-gift-line.tpl" productLast=$product@last productFirst=$product@first}
					</a>
					<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}" data-ajax="false" style="display:none">{l s='Delete'}</a>
				</li>
			{/foreach}
		</ul>
		{/if}
		{if sizeof($discounts)}
		<h2>{l s='List of vouchers'}</h2>
		<ul data-role="listview" data-inset="true" data-split-theme="d" data-split-icon="delete">
			{foreach $discounts as $discount}
			<li>
				<a>
					<table class="width-100">
						<tr>
							<td>{$discount.name}</td>
							<td class="right">
								{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
							</td>
						</tr>
					</table>
				</a>
				{if strlen($discount.code)}<a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" class="price_discount_delete" title="{l s='Delete'}" data-ajax="false">{l s='Delete'}</a>{/if}
			</li>
			{/foreach}
		</ul>
		{/if}
		<br />
		<div class="ui-grid-a cart_total_bar same-height">
			<div class="ui-block-a">
				<div class="ui-bar ui-bar-c">
					<h3>{l s='Voucher:'}</h3>
					<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" data-ajax="false">
						<input type="text" name="discount_name" id="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"  placeholder="{l s='Voucher code:'}" />
						<div class='btn-row'>
							<input type="hidden" name="submitDiscount" />
							<button type="submit" data-theme="a" name="submitAddDiscount" value="submit-value">{l s='Send'}</button>
						</div><!-- .btn-row -->
					</form>
				</div>
			</div>
			<div class="ui-block-b total_price">
				<div class="ui-bar ui-bar-c">
					{if $use_taxes}
						{if $priceDisplay}
							<h3>{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</h3>
							<p><span class="price" id="total_product">{displayPrice price=$total_products}</span></p>
						{else}
							<h3>{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</h3>
							<p><span class="price" id="total_product">{displayPrice price=$total_products_wt}</span></p>
						{/if}
					{else}
						<h3>{l s='Total products'}</h3>
						<p><span class="price" id="total_product">{displayPrice price=$total_products}</span></p>
					{/if}

					<div {if $total_discounts == 0}class="hide"{/if}>
						{if $use_taxes && $display_tax_label}
							<h3>{l s='Total vouchers (tax excl.)'}</h3>
						{else}
							<h3>{l s='Total vouchers'}</h3>
						{/if}

						{if $use_taxes && !$priceDisplay}
							{assign var='total_discounts_negative' value=$total_discounts * -1}
						{else}
							{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
						{/if}
							<p><span class="price" id="total_discount">{displayPrice price=$total_discounts_negative}</span></p>
					</div>

					<div {if $total_wrapping == 0}class="hide"{/if}>
						<h3>
						{if $use_taxes}
							{if $display_tax_label}{l s='Total gift wrapping (tax incl.):'}{else}{l s='Total gift-wrapping cost:'}{/if}
						{else}
							{l s='Total gift-wrapping cost:'}
						{/if}
						</h3>
						<p><span class="price" id="total_wrapping">
						{if $use_taxes}
							{if $priceDisplay}
								{displayPrice price=$total_wrapping_tax_exc}
							{else}
								{displayPrice price=$total_wrapping}
							{/if}
						{else}
							{displayPrice price=$total_wrapping_tax_exc}
						{/if}
						</span></p>
					</div>

					{if $use_taxes}
						{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
							<h3>{l s='Shipping'}</h3>
							<p><span class="price" id="total_shipping">{l s='Free Shipping!'}</span></p>
						{else}
							{if $priceDisplay}
								<div {if $total_shipping_tax_exc <= 0}class="hide"{/if}>
									<h3>{if $display_tax_label}{l s='Total shipping (tax excl.)'}{else}{l s='Total shipping'}{/if}</h3>
									<p><span class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</span></p>
								</div>
							{else}
								<div {if $total_shipping <= 0}class="hide"{/if}>
									<h3>{if $display_tax_label}{l s='Total shipping (tax incl.)'}{else}{l s='Total shipping'}{/if}</h3>
									<p><span class="price" id="total_shipping">{displayPrice price=$total_shipping}</span></p>
								</div>
							{/if}
						{/if}
					{else}
						<div {if $total_shipping_tax_exc <= 0}class="hide"{/if}>
							<h3>{l s='Total shipping'}</h3>
							<p><span class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</span></p>
						</div>
					{/if}
					{if $use_taxes}
						<h3>{l s='Total (tax excl.)'}</h3>
						<p><span class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</span></p>
	
						<h3>{l s='Total tax'}</h3>
						<p><span class="price" id="total_tax">{displayPrice price=$total_tax}</span></p>
					{/if}
					<h3>{l s='Total'}</h3>
					{if $use_taxes}
						<p><span class="price" id="total_price">{displayPrice price=$total_price}</span></p>
					{else}
						<p><span class="price" id="total_price">{displayPrice price=$total_price_without_tax}</span></p>
					{/if}
				</div>
			</div>
		</div><!-- /grid-a -->
		<br />
		{if $opc && $isLogged && !$isGuest}
			<a href="{$link->getPageLink('index', true)}" data-role="button" data-theme="a" data-icon="back" data-ajax="false">{l s='Continue shopping'}</a>
		{else}
			<ul data-role="listview" data-inset="true" id="list_myaccount">
				<li data-theme="a" data-icon="back">
					<a href="{$link->getPageLink('index', true)}" data-ajax="false">{l s='Continue shopping'}</a>
				</li>
				<li data-theme="b" data-icon="check">
					<a href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}" data-ajax="false">{l s='Confirm order'}</a>
				</li>
				{hook h="displayMobileShoppingCartButton"}
			</ul>
		{/if}
		<br />
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		{hook h="displayMobileShoppingCartBottom"}
	</div>
{/if}