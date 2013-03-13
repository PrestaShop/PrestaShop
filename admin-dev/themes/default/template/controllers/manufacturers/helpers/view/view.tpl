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

	<h2>{$manufacturer->name}</h2>

	<h3>{l s='Total addresses'} {count($addresses)}</h3>
	<hr />

	{if !count($addresses)}
		{l s='No address has been found for this manufacturer.'}
	{else}
		{foreach $addresses AS $addresse}
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width: 600px;">
				<tr>
					<th><strong>{$addresse.firstname} {$addresse.lastname}</strong></th>
				</tr>
				<tr>
					<td>
						<div style="padding:5px; float:left; width:350px;">
							{$addresse.address1}<br />
							{if $addresse.address2}{$addresse.address2}<br />{/if}
							{$addresse.postcode} {$addresse.city}<br />
							{if $addresse.state}{$addresse.state}<br />{/if}
							<b>{$addresse.country}</b><br />
						</div>
						<div style="padding:5px; float:left;">
							{if $addresse.phone}{$addresse.phone}<br />{/if}
							{if $addresse.phone_mobile}{$addresse.phone_mobile}<br />{/if}
						</div>
						{if $addresse.other}<div style="padding:5px; clear:both;"><br /><i>{$addresse.other}</i></div>{/if}
					</td>
				</tr>
			</table><br />
		{/foreach}
	{/if}

	<h3>{l s='Total products'} {count($products)}</h3>
	{foreach $products AS $product}
		<hr />
		{if !$product->hasAttributes()}
			<div style="float:right;">
				<a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}" class="button">{l s='Edit'}</a>
				<a href="?tab=AdminProducts&id_product={$product->id}&deleteproduct&token={getAdminToken tab='AdminProducts'}" class="button" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">{l s='Delete'}</a>
			</div>
			<br/><br/>
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
				<tr>
					<th height="39">{$product->name}</th>
					{if !empty($product->reference)}<th width="150">{l s='Ref:'} {$product->reference}</th>{/if}
					{if !empty($product->ean13)}<th width="120">{l s='EAN13:'} {$product->ean13}</th>{/if}
					{if !empty($product->upc)}<th width="120">{l s='UPC:'} {$product->upc}</th>{/if}
					{if $stock_management}<th class="right" width="50">{l s='Qty:'} {$product->quantity}</th>{/if}
				</tr>
			</table>
		{else}
			<div style="float:right;">
				<a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}" class="button">{l s='Edit'}</a>
				<a href="?tab=AdminProducts&id_product={$product->id}&deleteproduct&token={getAdminToken tab='AdminProducts'}" class="button" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">{l s='Delete'}</a>
			</div>
			<h3><a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}">{$product->name}</a></h3>
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
				<tr>
					<th height="39">{l s='Attribute name'}</th>
					<th width="80">{l s='Reference'}</th>
					<th width="80">{l s='EAN13'}</th>
					<th width="80">{l s='UPC'}</th>
					{if $stock_management && $shopContext != Shop::CONTEXT_ALL}<th class="right" width="150">{l s='Available Quantity'}</th>{/if}
				</tr>
				{foreach $product->combination AS $id_product_attribute => $product_attribute}
					<tr {if $id_product_attribute %2}class="alt_row"{/if} >
						<td>{$product_attribute.attributes}</td>
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
