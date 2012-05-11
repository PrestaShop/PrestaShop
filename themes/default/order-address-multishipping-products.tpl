{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<p>{l s='Choose the delivery addresses:'}</p>
<script type="text/javascript">
	CloseTxt = '{l s='Submit' js=1}';
	QtyChanged = '{l s='Some product quantities have changed, please check them.' js=1}';
	ShipToAnOtherAddress = '{l s='Ship to multiple addresses' js=1}';
</script>
<div id="order-detail-content" class="table_block">
	<table id="cart_summary" class="std">
		<thead>
			<tr>
				<th class="cart_product first_item">{l s='Product'}</th>
				<th class="cart_description item">{l s='Description'}</th>
				<th class="cart_ref item">{l s='Ref.'}</th>
				<th class="cart_quantity item">{l s='Qty'}</th>
				<th class="shipping_address last_item">{l s='Shipping address'}</th>
				<th class="delete">{l s=''}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$products item=product name=productLoop}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{* Display the product line *}
			{include file="$tpl_dir./order-address-product-line.tpl" productLast=$smarty.foreach.productLoop.last productFirst=$smarty.foreach.productLoop.first}
			{* Then the customized datas ones*}
			{if isset($customizedDatas.$productId.$productAttributeId)}
				{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization'}
					<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" class="product_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval} alternate_item cart_item">
						<td colspan="4">
							{foreach from=$customization.datas key='type' item='datas'}
								{if $type == $CUSTOMIZE_FILE}
									<div class="customizationUploaded">
										<ul class="customizationUploaded">
											{foreach from=$datas item='picture'}<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>{/foreach}
										</ul>
									</div>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
									<ul class="typedText">
										{foreach from=$datas item='textField' name='typedText'}<li>{if $textField.name}{$textField.name}{l s=':'}{else}{l s='Text #%s:' sprintf=$smarty.foreach.typedText.index+1}{/if} {$textField.value}</li>{/foreach}
									</ul>
								{/if}
							{/foreach}
						</td>
						<td class="cart_quantity">
							{if isset($cannotModify) AND $cannotModify == 1}
								<span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
							{else}
								<div style="float:right">
									<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" title="{l s='Delete this customization'}" width="11" height="13" class="icon" /></a>
								</div>
								<div id="cart_quantity_button" class="cart_quantity_button" style="float:left">
								<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" /></a><br />
								{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
								<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
									<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
								</a>
								{else}
								<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="#" title="{l s='Subtract'}">
									<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
								</a>
								{/if}
								</div>
								<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}_hidden"/>
								<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"/>
							{/if}
						</td>
						<td class="cart_total"></td>
					</tr>
					{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
				{/foreach}
				{* If it exists also some uncustomized products *}
				{if $product.quantity-$quantityDisplayed > 0}
					{include file="$tpl_dir./order-address-product-line.tpl" productLast=$smarty.foreach.productLoop.last productFirst=$smarty.foreach.productLoop.first}
				{/if}
				
			{/if}
		{/foreach}
		</tbody>
	</table>
</div>
