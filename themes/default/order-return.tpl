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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{include file="./errors.tpl"}
{if isset($orderRet)}
	<h4>{l s='RE#'}<span class="color-myaccount">{$orderRet->id|string_format:"%06d"}</span> {l s='on'} {dateFormat date=$order->date_add full=0}</h4>
	<div>
		<p class="bold">{l s='We have logged your return request.'}</p>
		<p>{l s='Your package must be returned to us within'} {$nbdaysreturn} {l s='days of receiving your order.'}</p>
		<p>{l s='Current status of your merchandise return:'} <span class="bold">{$state_name|escape:'htmlall':'UTF-8'}</span></p>
		<p>{l s='List of items to be returned:'}</p>
	</div>
	<div id="order-detail-content" class="table_block">
		<table class="std">
			<thead>
				<tr>
					<th class="first_item">{l s='Reference'}</th>
					<th class="item">{l s='Product'}</th>
					<th class="last_item">{l s='Quantity'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$products item=product name=products}

				{assign var='quantityDisplayed' value=0}
				{foreach from=$returnedCustomizations item='customization' name=products}
					{if $customization.product_id == $product.product_id}
						<tr class="{if $smarty.foreach.products.first}first_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if}">
							<td>{if $customization.reference}{$customization.reference|escape:'htmlall':'UTF-8'}{else}--{/if}</td>
							<td class="bold">{$customization.name|escape:'htmlall':'UTF-8'}</td>
							<td><span class="order_qte_span editable">{$customization.product_quantity|intval}</span></td>
						</tr>
						{assign var='productId' value=$customization.product_id}
						{assign var='productAttributeId' value=$customization.product_attribute_id}
						{assign var='customizationId' value=$customization.id_customization}
						{foreach from=$customizedDatas.$productId.$productAttributeId.$customizationId.datas key='type' item='datas'}
							<tr class="alternate_item">
								<td colspan="3">
									{if $type == $smarty.const._CUSTOMIZE_FILE_}
									<ul class="customizationUploaded">
										{foreach from=$datas item='data'}
											<li>WAHOU<img src="{$pic_dir}{$data.value}_small" alt="" class="customizationUploaded" /></li>
										{/foreach}
									</ul>
									{elseif $type == $smarty.const._CUSTOMIZE_TEXTFIELD_}
									<ul class="typedText">{counter start=0 print=false}
										{foreach from=$datas item='data'}
											{assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
											<li>{l s='%s:' sprintf=$data.name|default:$customizationFieldName} {$data.value}</li>
										{/foreach}
									</ul>
									{/if}
								</td>
							</tr>
						{/foreach}
						{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.product_quantity}
					{/if}
				{/foreach}

				{if $product.product_quantity > $quantityDisplayed}
					<tr class="{if $smarty.foreach.products.first}first_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if}">
						<td>{if $product.product_reference}{$product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</td>
						<td class="bold">{$product.product_name|escape:'htmlall':'UTF-8'}</td>
						<td><span class="order_qte_span editable">{$product.product_quantity|intval}</span></td>
					</tr>
				{/if}
			{/foreach}
			</tbody>
		</table>
	</div>

	{if $orderRet->state == 2}
	<p class="bold">{l s='REMINDER:'}</p>
	<div>
		- {l s='All products must be returned in their original packaging without damage or wear.'}
		<br />- {l s='Please print out the'} <a href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$orderRet->id|intval}")}">{l s='PDF return slip'}</a> {l s='and include it with your package.'}
		<br />- {l s='Please see PDF return slip'} (<a href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$orderRet->id|intval}")}">{l s='for the correct address'}</a>)
		<br /><br />
		{l s='Upon receiving your package, we will notify you by e-mail. We will then begin processing the reimbursement of your order total.'}
		<br /><br /><a href="{$link->getPageLink('contact', true)}">{l s='Please let us know if you have any questions.'}</a>
		<br />
		<p class="bold">{l s='If the conditions of return listed above are not respected, we reserve the right to refuse your package and/or reimbursement.'}</p>
	</div>
	{elseif $orderRet->state == 1}
		<p class="bold">{l s='You must wait for our confirmation before returning any merchandise.'}</p>
	{/if}
{/if}

