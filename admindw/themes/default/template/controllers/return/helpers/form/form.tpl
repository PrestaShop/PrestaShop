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
*  @version  Release: $Revision: 16259 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file='helpers/form/form.tpl'}

{block name="input"}
	{if $input.type == 'text_customer'}
		<span class="normal-text">{$customer->firstname} {$customer->lastname}</span>
		<p style="clear: both">
			<a href="{$url_customer}">{l s='View details on customer page'}</a>
		</p>
	{elseif $input.type == 'text_order'}
		<span class="normal-text">{$text_order}</span>
		<p style="clear: both">
			<a href="{$url_order}">{l s='View details on order page'}</a>
		</p>
	{elseif $input.type == 'list_products'}
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="col-left">&nbsp;</td>
				<td>
					<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th style="width: 100px;">{l s='Reference'}</th>
						<th>{l s='Product name'}</th>
						<th>{l s='Quantity'}</th>
						<th>{l s='Action'}</th>
					</tr>

					{foreach $returnedCustomizations as $returnedCustomization}
						<tr>
							<td>{$returnedCustomization['reference']}</td>
							<td class="center">{$returnedCustomization['name']}</td>
							<td class="center">{$returnedCustomization['product_quantity']|intval}</td>
							<td class="center">
								<a href="{$current}&deleteorder_return_detail&id_order_detail={$returnedCustomization['id_order_detail']}&id_order_return={$id_order_return}&id_customization={$returnedCustomization['id_customization']}&token={$token}">
									<img src="../img/admin/delete.gif">
								</a>
							</td>
						</tr>
						{foreach $customizationDatas as $type => $datas}
							<tr>
								<td colspan="4">
								{if $type == 'type_file'}
									<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
									{foreach $datas a $data name='loop'}
										<li style="display: inline; margin: 2px;">
											<a href="displayImage.php?img={$data['value']}&name={$order->id|intval}-file{$loop.iteration}" target="_blank"><img src="{$picture_folder}{$data['value']}_small" alt="" /></a>
										</li>
									{/foreach}
									</ul>
								{elseif $type == 'type_textfield'}
									<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
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
								<td class="center">{$product['product_name']}</td>
								<td class="center">{$product['product_quantity']}</td>
								<td class="center">
									<a href="{$current}&deleteorder_return_detail&id_order_detail={$product['id_order_detail']}&id_order_return={$id_order_return}&token={$token}">
										<img src="../img/admin/delete.gif">
									</a>
								</td>
							</tr>
						{/if}
					{/foreach}
					</table>
				</td>
			</tr>
		</table>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}