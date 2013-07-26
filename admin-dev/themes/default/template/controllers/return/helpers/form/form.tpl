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

{extends file='helpers/form/form.tpl'}

{block name="input"}
	{if $input.type == 'text_customer'}
		<span>{$customer->firstname} {$customer->lastname}</span>
		<p>
			<a class="text-muted" href="{$url_customer}">{l s='View details on the customer page'}</a>
		</p>
	{elseif $input.type == 'text_order'}
		<span>{$text_order}</span>
		<p>
			<a class="text-muted" href="{$url_order}">{l s='View details on the order page'}</a>
		</p>
	{elseif $input.type == 'list_products'}
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Reference'}</th>
					<th>{l s='Product name'}</th>
					<th class="text-center">{l s='Quantity'}</th>
					<th class="text-center">{l s='Action'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $returnedCustomizations as $returnedCustomization}
					<tr>
						<td>{$returnedCustomization['reference']}</td>
						<td>{$returnedCustomization['name']}</td>
						<td class="text-center">{$returnedCustomization['product_quantity']|intval}</td>
						<td class="text-center">
							<a class="btn btn-default" href="{$current}&deleteorder_return_detail&id_order_detail={$returnedCustomization['id_order_detail']}&id_order_return={$id_order_return}&id_customization={$returnedCustomization['id_customization']}&token={$token}">
								<i class="icon-remove"></i>
								{l s='Delete'}
							</a>
						</td>
					</tr>
					{foreach $customizationDatas as $type => $datas}
						<tr>
							<td colspan="4">
							{if $type == 'type_file'}
								<ul>
								{foreach $datas a $data name='loop'}
									<li>
										<a href="displayImage.php?img={$data['value']}&name={$order->id|intval}-file{$loop.iteration}" target="_blank"><img src="{$picture_folder}{$data['value']}_small" alt="" /></a>
									</li>
								{/foreach}
								</ul>
							{elseif $type == 'type_textfield'}
								<ul>
									{foreach $datas as $data name='loop'}
										<li>{if $data['name']}$data['name']{else}{l s='Text #%d' sprintf=$loop.iteration}{/if}{l s=':'} {$data['value']}</li>
									{/foreach}
								</ul>
							{/if}
							</td>
						</tr>
					{/foreach}
				{/foreach}

				{* Classic products *}
				{foreach $products as $k => $product}
					{if !isset($quantityDisplayed[$product['id_order_detail']]) || $product['product_quantity']|intval > $quantityDisplayed[$product['id_order_detail']]|intval}
						<tr>
							<td>{$product['product_reference']}</td>
							<td class="text-center">{$product['product_name']}</td>
							<td class="text-center">{$product['product_quantity']}</td>
							<td class="text-center">
								<a class="btn btn-default"  href="{$current}&deleteorder_return_detail&id_order_detail={$product['id_order_detail']}&id_order_return={$id_order_return}&token={$token}">
									<i class="icon-remove"></i>
									{l s='Delete'}
								</a>
							</td>
						</tr>
					{/if}
				{/foreach}
			</tbody>
		</table>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}