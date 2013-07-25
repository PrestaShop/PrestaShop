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

	<legend>{$manufacturer->name}</legend>

	<span>{l s='Total addresses'} {count($addresses)}</span>
	<hr />

	{if !count($addresses)}
		{l s='No address has been found for this manufacturer.'}
	{else}
		{foreach $addresses AS $addresse}
			<table class="table">
				<thead>
					<tr>
						<th>{$addresse.firstname} {$addresse.lastname}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
								{$addresse.address1}<br />
								{if $addresse.address2}{$addresse.address2}<br />{/if}
								{$addresse.postcode} {$addresse.city}<br />
								{if $addresse.state}{$addresse.state}<br />{/if}
								<b>{$addresse.country}</b><br />
								{if $addresse.phone}{$addresse.phone}<br />{/if}
								{if $addresse.phone_mobile}{$addresse.phone_mobile}<br />{/if}
								{if $addresse.other}<div ><br />
									<i>{$addresse.other}</i></div>
								{/if}
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td><a class="btn btn-default pull-right" href="{$link->getAdminLink('AdminManufacturers')}&id_address={$addresse.id_address}&editaddresses=1">
							<i class="icon-edit"></i> {l s='Edit'}</a></td>
					</tr>
				</tfoot>
			</table>
		{/foreach}
	{/if}

	<span>{l s='Total products'} {count($products)}</span>

	{foreach $products AS $product}
		<hr />
		{if !$product->hasAttributes()}
			<div>
				<a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}" class="btn btn-default"><i class="icon-edit"></i> {l s='Edit'}</a>
				<a href="?tab=AdminProducts&id_product={$product->id}&deleteproduct&token={getAdminToken tab='AdminProducts'}" class="btn btn-default" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');"><i class="icon-trash"></i> {l s='Delete'}</a>
			</div>

			<table class="table">
				<thead>
					<tr>
						<th >{$product->name}</th>
						{if !empty($product->reference)}<th>{l s='Ref:'} {$product->reference}</th>{/if}
						{if !empty($product->ean13)}<th>{l s='EAN13:'} {$product->ean13}</th>{/if}
						{if !empty($product->upc)}<th>{l s='UPC:'} {$product->upc}</th>{/if}
						{if $stock_management}<th>{l s='Qty:'} {$product->quantity}</th>{/if}
					</tr>
				</thead>
			</table>
		{else}
			<div>
				<a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}" class="btn btn-default"><i class="icon-edit"></i> {l s='Edit'}</a>
				<a href="?tab=AdminProducts&id_product={$product->id}&deleteproduct&token={getAdminToken tab='AdminProducts'}" class="btn btn-default" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');"><i class="icon-trash"></i> {l s='Delete'}</a>
			</div>

			<span><a href="?tab=AdminProducts&id_product={$product->id}&updateproduct&token={getAdminToken tab='AdminProducts'}">{$product->name}</a></span>

			<table class="table">
				<thead>
					<tr>
						<th>{l s='Attribute name'}</th>
						<th>{l s='Reference'}</th>
						<th>{l s='EAN13'}</th>
						<th>{l s='UPC'}</th>
						{if $stock_management && $shopContext != Shop::CONTEXT_ALL}
							<th>{l s='Available Quantity'}</th>
						{/if}
					</tr>
				</thead>
				<tbody>
				{foreach $product->combination AS $id_product_attribute => $product_attribute}
					<tr {if $id_product_attribute %2}class="alt_row"{/if} >
						<td>{$product_attribute.attributes}</td>
						<td>{$product_attribute.reference}</td>
						<td>{$product_attribute.ean13}</td>
						<td>{$product_attribute.upc}</td>
						{if $stock_management && $shopContext != Shop::CONTEXT_ALL}
							<td class="right">{$product_attribute.quantity}</td>
						{/if}
					</tr>
				{/foreach}
				</tbody>
			</table>
		{/if}
	{/foreach}
{/block}
