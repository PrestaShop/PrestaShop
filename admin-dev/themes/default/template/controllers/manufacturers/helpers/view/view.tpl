{**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="panel">
	<h3>{l s='Addresses' d='Admin.Global'} <span class="badge">{count($addresses)}</span></h3>
	{if !count($addresses)}
		{l s='No address has been found for this brand.' d='Admin.Catalog.Notification'}
	{else}
		{foreach $addresses AS $addresse}
		<div class="panel">
			<div class="panel-heading">
				{$addresse.firstname} {$addresse.lastname}
				<div class="pull-right">
					<a class="btn btn-default" href="{$link->getAdminLink('AdminManufacturers', true, [], ['id_address' => $addresse.id_address, 'editaddresses' => 1])|escape:'html':'UTF-8'}">
						<i class="icon-edit"></i>
						{l s='Edit' d='Admin.Actions'}</a>
				</div>
			</div>

			<table class="table">
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
								<i>{$addresse.other|nl2br}</i></div>
							{/if}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		{/foreach}
	{/if}
</div>
<div class="panel">
	<h3>{l s='Products' d='Admin.Global'} <span class="badge">{count($products)}</span></h3>

	{foreach $products AS $product}
		{if !$product->hasAttributes()}
			<div class="panel">
				<div class="panel-heading">
					{$product->name}
					<div class="pull-right">
						<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product->id|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}" class="btn btn-default btn-sm">
							<i class="icon-edit"></i> {l s='Edit' d='Admin.Actions'}
						</a>
						<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product->id|intval, 'deleteproduct' => '1'])|escape:'html':'UTF-8'}" class="btn btn-default btn-sm" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">
							<i class="icon-trash"></i> {l s='Delete' d='Admin.Actions'}
						</a>
					</div>
				</div>

				<table class="table">
					<thead>
						<tr>
							{if !empty($product->reference)}<th><span class="title_box">{l s='Ref:' d='Admin.Catalog.Feature'}</span> {$product->reference}</th>{/if}
							{if !empty($product->ean13)}<th><span class="title_box">{l s='EAN13:' d='Admin.Catalog.Feature'}</span> {$product->ean13}</th>{/if}
							{if !empty($product->upc)}<th><span class="title_box">{l s='UPC:' d='Admin.Catalog.Feature'}</span> {$product->upc}</th>{/if}
							{if $stock_management}<th><span class="title_box">{l s='Qty:' d='Admin.Catalog.Feature'}</span> {$product->quantity}</th>{/if}
						</tr>
					</thead>
				</table>
			</div>
		{else}
			<div class="panel">
				<div class="panel-heading">

					<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product->id|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}">
						{$product->name}
					</a>
					<div class="pull-right">
						<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product->id|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}" class="btn btn-default btn-sm">
							<i class="icon-edit"></i>
							{l s='Edit' d='Admin.Actions'}
						</a>
						<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product->id|intval, 'deleteproduct' => '1'])|escape:'html':'UTF-8'}" class="btn btn-default btn-sm" onclick="return confirm('{l s='Delete item #'}{$product->id} ?');">
							<i class="icon-trash"></i>
							{l s='Delete' d='Admin.Actions'}
						</a>
					</div>

				</div>

				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Attribute name' d='Admin.Catalog.Feature'}</span></th>
							<th><span class="title_box">{l s='Reference' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='EAN13' d='Admin.Catalog.Feature'}</span></th>
							<th><span class="title_box">{l s='UPC' d='Admin.Catalog.Feature'}</span></th>
							{if $stock_management && $shopContext != Shop::CONTEXT_ALL}
								<th><span class="title_box">{l s='Available quantity' d='Admin.Catalog.Feature'}</span></th>
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
			</div>
		{/if}
	{/foreach}
</div>
{/block}
