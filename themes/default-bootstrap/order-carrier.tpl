{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !$opc}
	<div id="carrier_area">
		<h1 class="page-heading">{l s='Shipping:'}</h1>
		{include file="$tpl_dir./order-steps.tpl"}
		{include file="$tpl_dir./errors.tpl"}
		<form id="form" action="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:'html':'UTF-8'}" method="post" onsubmit="return acceptCGV();">
{else}
	<div id="carrier_area" class="opc-main-block">
		<h1 class="page-heading step-num"><span>2</span> {l s='Delivery methods'}</h1>
			<div id="opc_delivery_methods" class="opc-main-block">
				<div id="opc_delivery_methods-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
<div class="order_carrier_content box">
	{if isset($virtual_cart) && $virtual_cart}
		<input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
	{else}
		<p class="carrier_title">{l s='Choose your delivery method'}</p>
		<div id="HOOK_BEFORECARRIER">
			{if isset($carriers) && isset($HOOK_BEFORECARRIER)}
				{$HOOK_BEFORECARRIER}
			{/if}
		</div>
		{if isset($isVirtualCart) && $isVirtualCart}
			<p class="alert alert-warning">{l s='No carrier is needed for this order.'}</p>
		{else}
			{if $recyclablePackAllowed}
				<div class="checkbox">
					<label for="recyclable">
						<input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} />
						{l s='I would like to receive my order in recycled packaging.'}.
					</label>
				</div>
			{/if}
			<div class="delivery_options_address">
				{if isset($delivery_option_list)}
					{foreach $delivery_option_list as $id_address => $option_list}
						<p class="carrier_title">
							{if isset($address_collection[$id_address])}
								{l s='Choose a shipping option for this address:'} {$address_collection[$id_address]->alias}
							{else}
								{l s='Choose a shipping option'}
							{/if}
						</p>
						<div class="delivery_options">
							{foreach $option_list as $key => $option}
								<div class="delivery_option {if ($option@index % 2)}alternate_{/if}item">
									<div>
										<table class="resume table table-bordered">
											<tr>
												<td class="delivery_option_radio">
													<input
														class="delivery_option_radio"
														type="radio"
														name="delivery_option[{$id_address}]"
														onchange="{if $opc}updateCarrierSelectionAndGift();{else}updateExtraCarrier('{$key}', {$id_address});{/if}" id="delivery_option_{$id_address}_{$option@index}"
														value="{$key}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
												</td>
												<td class="delivery_option_logo">
													{foreach $option.carrier_list as $carrier}
														{if $carrier.logo}
															<img src="{$carrier.logo}" alt="{$carrier.instance->name}"/>
														{else if !$option.unique_carrier}
															{$carrier.instance->name}
															{if !$carrier@last} - {/if}
														{/if}
													{/foreach}
												</td>
												<td>
													{if $option.unique_carrier}
														{foreach $option.carrier_list as $carrier}
															{$carrier.instance->name}
														{/foreach}
														{if isset($carrier.instance->delay[$cookie->id_lang])}
															{$carrier.instance->delay[$cookie->id_lang]}
														{/if}
													{/if}
													{if count($option_list) > 1}
														{if $option.is_best_grade}
															{if $option.is_best_price}
																{l s='The best price and speed'}
															{else}
																{l s='The fastest'}
															{/if}
														{else}
															{if $option.is_best_price}
																{l s='The best price'}
															{/if}
														{/if}
													{/if}
												</td>
												<td class="delivery_option_price">
													<div class="delivery_option_price">
														{if $option.total_price_with_tax && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
															{if $use_taxes == 1}
																{if $priceDisplay == 1}
																	{convertPrice price=$option.total_price_without_tax} {l s='(tax excl.)'}
																{else}
																	{convertPrice price=$option.total_price_with_tax} {l s='(tax incl.)'}
																{/if}
															{else}
																{convertPrice price=$option.total_price_without_tax}
															{/if}
														{else}
															{l s='Free'}
														{/if}
													</div>
												</td>
											</tr>
										</table>
										<table class="delivery_option_carrier {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}selected{/if} {if $option.unique_carrier}not-displayable{/if}">
											{foreach $option.carrier_list as $carrier}
												<tr>
													{if !$option.unique_carrier}
														<td class="first_item">
															<input type="hidden" value="{$carrier.instance->id}" name="id_carrier" />
															{if $carrier.logo}
																<img src="{$carrier.logo}" alt="{$carrier.instance->name}"/>
															{/if}
														</td>
														<td>
															{$carrier.instance->name}
														</td>
													{/if}
													<td {if $option.unique_carrier}class="first_item" {/if}>
														<input type="hidden" value="{$carrier.instance->id}" name="id_carrier" />
														{if isset($carrier.instance->delay[$cookie->id_lang])}
															<i class="icon-info-sign"></i>{$carrier.instance->delay[$cookie->id_lang]}
															{if count($carrier.product_list) <= 1}
																({l s='Product concerned:'}
															{else}
																({l s='Products concerned:'}
															{/if}
															{* This foreach is on one line, to avoid tabulation in the title attribute of the acronym *}
															{foreach $carrier.product_list as $product}
																{if $product@index == 4}
																	<acronym title="
																{/if}
																{if $product@index >= 4}
																	{$product.name}{if isset($product.attributes) && $product.attributes} {$product.attributes|escape:'htmlall':'UTF-8'}{/if}
																	{if !$product@last}
																		,&nbsp;
																	{else}
																		">&hellip;</acronym>)
																	{/if}
																{else}
																	{$product.name}{if isset($product.attributes) && $product.attributes} {$product.attributes|escape:'htmlall':'UTF-8'}{/if}
																	{if !$product@last}
																		,&nbsp;
																	{else}
																		)
																	{/if}
																{/if}
															{/foreach}
														{/if}
													</td>
												</tr>
											{/foreach}
										</table>
									</div>
								</div> <!-- end delivery_option -->
							{/foreach}
						</div> <!-- end delivery_options -->
						<div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">
							{if isset($HOOK_EXTRACARRIER_ADDR) &&  isset($HOOK_EXTRACARRIER_ADDR.$id_address)}{$HOOK_EXTRACARRIER_ADDR.$id_address}{/if}
						</div>
						{foreachelse}
							<p class="alert alert-warning" id="noCarrierWarning">
								{foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
									{if empty($address->alias)}
										{l s='No carriers available.'}
									{else}
										{l s='No carriers available for the address "%s".' sprintf=$address->alias}
									{/if}
									{if !$address@last}
										<br />
									{/if}
								{foreachelse}
									{l s='No carriers available.'}
								{/foreach}
							</p>
						{/foreach}
					{/if}
				</div> <!-- end delivery_options_address -->
				<div id="extra_carrier" style="display: none;"></div>
					{if $giftAllowed}
						<p class="carrier_title">{l s='Gift'}</p>
						<p class="checkbox gift">
							<input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} />
							<label for="gift">
								{l s='I would like my order to be gift wrapped.'}
								{if $gift_wrapping_price > 0}
									&nbsp;<i>({l s='Additional cost of'}
									<span class="price" id="gift-price">
										{if $priceDisplay == 1}
											{convertPrice price=$total_wrapping_tax_exc_cost}
										{else}
											{convertPrice price=$total_wrapping_cost}
										{/if}
									</span>
									{if $use_taxes}
										{if $priceDisplay == 1}
											{l s='(tax excl.)'}
										{else}
											{l s='(tax incl.)'}
										{/if}
									{/if})
									</i>
								{/if}
							</label>
						</p>
						<p id="gift_div" class="form-group">
							<label for="gift_message">{l s='If you\'d like, you can add a note to the gift:'}</label>
							<textarea rows="5" cols="35" id="gift_message" class="form-control" name="gift_message">{$cart->gift_message|escape:'html':'UTF-8'}</textarea>
						</p>
					{/if}
				{/if}
			{/if}
			{if $conditions AND $cms_id}
				<p class="carrier_title">{l s='Terms of service'}</p>
				<p class="checkbox">
					<input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
					<label for="cgv">{l s='I agree to the terms of service and will adhere to them unconditionally.'}</label>
					<a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)'}</a>
				</p>
			{/if}
		</div> <!-- end delivery_options_address -->
		{if !$opc}
				<div class="clearfix">
					<p><strong class="dark">{l s='Leave a message'}</strong></p>
					<div class="form-group">
						<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
						<textarea class="form-control" cols="120" rows="2" name="message" id="message">{if isset($oldMessage)}{$oldMessage|escape:'html':'UTF-8'}{/if}</textarea>
					</div>
				</div>
				<p class="cart_navigation clearfix">
					<input type="hidden" name="step" value="3" />
					<input type="hidden" name="back" value="{$back}" />
					{if !$is_guest}
						{if $back}
							<a 
								href="{$link->getPageLink('order', true, NULL, "step=1&back={$back}&multi-shipping={$multi_shipping}")|escape:'html':'UTF-8'}"
								title="{l s='Previous'}"
								class="button-exclusive btn btn-default">
								<i class="icon-chevron-left"></i>
								{l s='Continue shopping'}
							</a>
						{else}
							<a
								href="{$link->getPageLink('order', true, NULL, "step=1&multi-shipping={$multi_shipping}")|escape:'html':'UTF-8'}"
								title="{l s='Previous'}"
								class="button-exclusive btn btn-default">
								<i class="icon-chevron-left"></i>
								{l s='Continue shopping'}
							</a>
						{/if}
					{else}
						<a
							href="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:'html':'UTF-8'}"
							title="{l s='Previous'}"
							class="button-exclusive btn btn-default">
							<i class="icon-chevron-left"></i>
							{l s='Continue shopping'}
						</a>
					{/if}
					{if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
						<button type="submit" name="processCarrier" class="button btn btn-default standard-checkout button-medium">
							<span>
								{l s='Proceed to checkout'}
								<i class="icon-chevron-right right"></i>
							</span>
						</button>
					{/if}
				</p>
			</form>
	{else}
			<p><strong class="dark">{l s='Leave a message'}</strong></p>
			<div>
				<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
				<textarea class="form-control" cols="120" rows="2" name="message" id="message">{strip}
					{if isset($oldMessage)}{$oldMessage|escape:'html':'UTF-8'}{/if}
				{/strip}</textarea>
			</div>
		</div> <!-- end opc_delivery_methods -->
	{/if}
</div> <!-- end carrier_area -->
{strip}
{if !$opc}
	{capture name=path}{l s='Shipping:'}{/capture}
	{assign var='current_step' value='shipping'}
	{addJsDef orderProcess='order'}
	{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
	{addJsDef currencyRate=$currencyRate|floatval}
	{addJsDef currencyFormat=$currencyFormat|intval}
	{addJsDef currencyBlank=$currencyBlank|intval}
	{if isset($virtual_cart) && !$virtual_cart && $giftAllowed && $cart->gift == 1}
		{addJsDef cart_gift=true}
	{else}
		{addJsDef cart_gift=false}
	{/if}
	{addJsDef orderUrl=$link->getPageLink("order", true)|addslashes}
	{addJsDefL name=txtProduct}{l s='Product' js=1}{/addJsDefL}
	{addJsDefL name=txtProducts}{l s='Products' js=1}{/addJsDefL}
	{addJsDefL name=msg_order_carrier}{l s='You must agree to the terms of service before continuing.' js=1}{/addJsDefL}
{/if}
{/strip}