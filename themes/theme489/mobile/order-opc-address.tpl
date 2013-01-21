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
*  @version  Release: $Revision: 16764 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='Address'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="address-section">
	<div class="ui-grid-a margin-bottom-10px">
		<div class="ui-block-a">
			<h3 class="bg">{l s='Delivery address:'}</h3>
			{if isset($delivery)}
				<ul class="adress">
					<li class="address_name">{$delivery->firstname|escape:'htmlall':'UTF-8'} {$delivery->lastname|escape:'htmlall':'UTF-8'}</li>
					{if $delivery->company}
						<li class="address_company">{$delivery->company|escape:'htmlall':'UTF-8'}</li>
					{/if}
					<li class="address_address1">{$delivery->address1|escape:'htmlall':'UTF-8'}</li>
					{if $delivery->address2}
						<li class="address_address2">{$delivery->address2|escape:'htmlall':'UTF-8'}</li>
					{/if}
					<li class="address_city">{$delivery->postcode|escape:'htmlall':'UTF-8'} {$delivery->city|escape:'htmlall':'UTF-8'}</li>
					<li class="address_country">{$delivery->country|escape:'htmlall':'UTF-8'} {if $delivery_state}({$delivery_state|escape:'htmlall':'UTF-8'}){/if}</li>
				</ul>
			{/if}
			<label for="delivery-address-choice" class="select">{l s='Change address:'}</label>
			<select
				name="delivery-address-choice"
				id="delivery-address-choice"
				class="address-field"
				data-mini="true"
				data-address-type="delivery"
			>
				{foreach from=$addresses item=address}
					<option value="{$address.id_address}"{if ($address.id_address == $delivery->id)} selected="selected"{/if}>{$address.alias}</option>
				{/foreach}
			</select>
		</div>
		<div class="ui-block-b">
			<h3 class="bg">{l s='Invoice address:'}</h3>
			{if isset($invoice)}
				<ul class="adress">
					<li class="address_name">{$invoice->firstname|escape:'htmlall':'UTF-8'} {$invoice->lastname|escape:'htmlall':'UTF-8'}</li>
					{if $invoice->company}
						<li class="address_company">{$invoice->company|escape:'htmlall':'UTF-8'}</li>
					{/if}
					<li class="address_address1">{$invoice->address1|escape:'htmlall':'UTF-8'}</li>
					{if $invoice->address2}
						<li class="address_address2">{$invoice->address2|escape:'htmlall':'UTF-8'}</li>
					{/if}
					<li class="address_city">{$invoice->postcode|escape:'htmlall':'UTF-8'} {$invoice->city|escape:'htmlall':'UTF-8'}</li>
					<li class="address_country">{$invoice->country|escape:'htmlall':'UTF-8'} {if $invoice_state}({$invoice_state|escape:'htmlall':'UTF-8'}){/if}</li>
				</ul>
			{else}
				<p class="warning">{l s='You have to specify your delivery and invoice address.'}</p>
			{/if}
			<label for="invoice-address-choice" class="select">{l s='Change address:'}</label>
			<select
				name="invoice-address-choice"
				id="invoice-address-choice"
				class="address-field"
				data-mini="true"
				data-address-type="invoice"
			>
				{foreach from=$addresses item=address}
					<option value="{$address.id_address}"{if ($address.id_address == $invoice->id)} selected="selected"{/if}>{$address.alias}</option>
				{/foreach}
			</select>
		</div>
	</div>

	{if $opc}
		{assign var="back_order_page" value="order-opc.php"}
		{else}
		{assign var="back_order_page" value="order.php"}
	{/if}

	<p><a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")}" title="{l s='Add a new address'}" data-role="button" data-theme="e" data-icon="plus" data-ajax="false">{l s='Add a new address'}</a><br /></p>

</div>
