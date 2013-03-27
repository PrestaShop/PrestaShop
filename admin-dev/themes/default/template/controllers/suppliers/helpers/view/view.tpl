{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

<h2>{$supplier->name}</h2>

<h3>{l s='Number of products:'} {count($products)}</h3>
{foreach $products AS $product}
	<hr />
	{if !$product->hasAttributes()}
		<table border="0" cellpadding="0" cellspacing="0" class="table" style="">
			<tr>
				<th  width="450">{l s='Name'} {$product->name}</th>
				{if !empty($product->product_supplier_reference)}<th width="190">{l s='Supplier Reference:'} {$product->product_supplier_reference}</th>{/if}
				{if !empty($product->product_supplier_price_te)}<th width="190">{l s='Wholesale price:'} {$product->product_supplier_price_te}</th>{/if}
				{if !empty($product->reference)}<th width="150">{l s='Reference:'} {$product->reference}</th>{/if}
				{if !empty($product->ean13)}<th width="120">{l s='EAN13:'} {$product->ean13}</th>{/if}
				{if !empty($product->upc)}<th width="120">{l s='UPC:'} {$product->upc}</th>{/if}
				{if $stock_management}<th class="right" width="150">{l s='Available Quantity:'} {$product->quantity}</th>{/if}
			</tr>
		</table>
	{else}
		<h3><a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}">{$product->name}</a></h3>
		<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
			<colgroup>
				<col>
				<col width="190">
				<col width="190">
				<col width="80">
				<col width="80">
				<col width="80">
				<col width="80">
			</colgroup>
			<tr>
				<th style="height:40px;">{l s='Attribute name'}</th>
				<th>{l s='Supplier Reference'}</th>
				<th >{l s='Wholesale price'}</th>
				<th>{l s='Reference'}</th>
				<th>{l s='EAN13'}</th>
				<th>{l s='UPC'}</th>
				{if $stock_management && $shopContext != Shop::CONTEXT_ALL}<th class="right">{l s='Available Quantity'}</th>{/if}
			</tr>
			{foreach $product->combination AS $id_product_attribute => $product_attribute}
				<tr {if $id_product_attribute %2}class="alt_row"{/if} >
					<td>{$product_attribute.attributes}</td>
					<td>{$product_attribute.product_supplier_reference}</td>
					<td>{$product_attribute.product_supplier_price_te}</td>
					<td>{$product_attribute.reference}</td>
					<td>{$product_attribute.ean13}</td>
					<td>{$product_attribute.upc}</td>
					{if $stock_management && $shopContext != Shop::CONTEXT_ALL}<td class="right">{$product_attribute.quantity}</td>{/if}
				</tr>
			{/foreach}
		</table>
	{/if}
{/foreach}

{/block}

