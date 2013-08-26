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

{if isset($product->id)}
	<fieldset>
		<input type="hidden" name="submitted_tabs[]" value="Quantities" />
		<h3>{l s='Available quantities for sale'}</h3>
		{if !$ps_stock_management}
			<div class="alert alert-info">{l s='The stock management is disabled'}</div>
		{else}
			{include file="controllers/products/multishop/check_fields.tpl" product_tab="Quantities"}
			<div class="alert alert-info">
				{l s='This interface allows you to manage available quantities for sale for products. It also allows you to manage product combinations in the current shop.'}<br/>
				{l s='You can choose whether or not to use the advanced stock management system for this product.'}<br/>
				{l s='You can manually specify the quantities for the product/each product combination, or you can choose to automatically determine these quantities based on your stock (if advanced stock management is activated).'}<br/>
				{l s='In this case, quantities correspond to the real-stock quantities in the warehouses connected with the current shop, or current group of shops.'}<br/>
				{l s='For packs: If it has products that use advanced stock management, you have to specify a common warehouse for these products in the pack.'}<br/>
				{l s='Also, please note that when a product has combinations, its default combination will be used in stock movements.'}
			</div>

			{if $show_quantities == true}
				<div class="alert alert-warning" id="available_quantity_ajax_msg" style="display: none;"></div>
				<div class="error" id="available_quantity_ajax_error_msg" style="display: none;"></div>
				<div class="alert" id="available_quantity_ajax_success_msg" style="display: none;"></div>

				
					
				<div {if $product->is_virtual || $product->cache_is_pack}style="display:none;"{/if} class="row stockForVirtualProduct">
					<div class="col-lg-12">
						<p class="checkbox">
							<label for="advanced_stock_management">
								<input type="checkbox" name="advanced_stock_management" class="advanced_stock_management" id="advanced_stock_management" 
									{if $product->advanced_stock_management == 1 && $stock_management_active == 1}
										value="1" checked="checked"
									{else}
										value="0"
									{/if} 
									{if $stock_management_active == 0 || $product->cache_is_pack}
										disabled="disabled" 
									{/if} 
								/>
								{l s='I want to use the advanced stock management system for this product.'} 
								{if $stock_management_active == 0 && !$product->cache_is_pack}&nbsp;-&nbsp;<strong>{l s='This requires you to enable advanced stock management.'}</strong>
								{else if $product->cache_is_pack}&nbsp;-&nbsp;{l s='This parameter depends on the product(s) in the pack.'}
								{/if}
							</label>
						</p>
					</div>
				</div>

				<div {if $product->is_virtual || $product->cache_is_pack}style="display:none;"{/if} class="row stockForVirtualProduct">
					<label class="control-label col-lg-3" for="depends_on_stock_1">{l s='Available quantities:'}</label>
					<div class="col-lg-9">
						<p class="radio">
							<label for="depends_on_stock_1">
								<input type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_1" value="1"
									{if $product->depends_on_stock == 1 && $stock_management_active == 1}
										checked="checked" 
									{/if} 
									{if $stock_management_active == 0 || $product->advanced_stock_management == 0 || $product->cache_is_pack}
										disabled="disabled" 
									{/if} 
								/>
								{l s='Available quantities for current product and its combinations are based on warehouse stock. '} 
								{if ($stock_management_active == 0 || $product->advanced_stock_management == 0) && !$product->cache_is_pack} &nbsp;-&nbsp;{l s='This requires you to enable advanced stock management globally or for this product.'}
								{else if $product->cache_is_pack} &nbsp;-&nbsp;{l s='This parameter depends on the product(s) in the pack.'}
								{/if}
							</label>
						</p>
						<p class="radio">
							<label for="depends_on_stock_0" for="depends_on_stock_0">
								<input type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_0" value="0"
									{if $product->depends_on_stock == 0 || $stock_management_active == 0}
										checked="checked" 
									{/if} 
								/>
								{l s='I want to specify available quantities manually.'}
							</label>
						</p>
					</div>
				</div>

				{if isset($pack_quantity)}
					<div class="alert alert-info">
						<p>{l s='When a product has combinations, quantities will be based on the default combination.'}</p>
						<p>{l s='Given the quantities of the products in this pack, the maximum quantity should be:'} {$pack_quantity}</p>
					</div>	
				{/if}

				<table class="table">
					<colgroup>
						<col width="50">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th>{l s='Quantity'}</th>
							<th>{l s='Designation'}</th>
						</tr>
					</thead>
					{foreach from=$attributes item=attribute}
						<tr>
							<td class="available_quantity" id="qty_{$attribute['id_product_attribute']}">
								<span>{$available_quantity[$attribute['id_product_attribute']]}</span>
								<input type="text" value="{$available_quantity[$attribute['id_product_attribute']]|htmlentities}"/>
							</td>
							<td>{$product_designation[$attribute['id_product_attribute']]}</td>
						</tr>
					{/foreach}
				</table>

				<div id="when_out_of_stock" class="row">
					<label class="control-label col-lg-3">{l s='When out of stock:'}</label>
					<div class="col-lg-9">
						<p class="checkbox">
							<label id="label_out_of_stock_1" for="out_of_stock_1">
								<input {if $product->out_of_stock == 0} checked="checked" {/if} id="out_of_stock_1" type="radio" checked="checked" value="0" class="out_of_stock" name="out_of_stock">
								{l s='Deny orders'}
							</label>
						</p>
						<p class="checkbox">
							<label id="label_out_of_stock_2" for="out_of_stock_2">
								<input {if $product->out_of_stock == 1} checked="checked" {/if} id="out_of_stock_2" type="radio" value="1" class="out_of_stock" name="out_of_stock">
								{l s='Allow orders'}
							</label>
						</p>
						<p class="checkbox">
							<label id="label_out_of_stock_3" for="out_of_stock_3">
								<input {if $product->out_of_stock == 2} checked="checked" {/if} id="out_of_stock_3" type="radio" value="2" class="out_of_stock" name="out_of_stock">
								{l s='Default'}:
								{if $order_out_of_stock == 1}
								{l s='Allow orders'}
								{else}
								{l s='Deny orders'}
								{/if} 
								<a class="confirm_leave" href="index.php?tab=AdminPPreferences&token={$token_preferences}">
									{l s='as set in Preferences'}
								</a>
							</label>
						</p>
					</div>
				</div>

			{else}
				<div class="alert alert-warning">
					<p>{l s='It is not possible to manage quantities when:'}</p>
					<ul>
						<li>{l s='You are currently managing all of your shops.'}</li>
						<li>{l s='You are currently managing a group of shops where quantities are not shared between every shop in this group.'}</li>
						<li>{l s='You are currently managing a shop that is in a group where quantities are shared between every shop in this group.'}</li>
					</ul>
				</div>
			{/if}
		{/if}
	</fieldset>
	<fieldset>
		<h3>{l s='Availability settings'}</h3>

		{if !$has_attribute}
			<div class="row">
				<label class="control-label col-lg-3" for="minimal_quantity">{l s='Minimum quantity:'}</label>
				<div class="col-lg-1">
					<input maxlength="6" name="minimal_quantity" id="minimal_quantity" type="text" value="{$product->minimal_quantity|default:1}" />
				</div>
				<p class="help-block">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)'}</p>
			</div>
		{/if}

		{if $ps_stock_management}
			{include file="controllers/products/multishop/checkbox.tpl" field="available_now" type="default" multilang="true"}
			<div class="row">
				<label class="control-label col-lg-5" for="available_now_{$id_lang}">
					<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Forbidden characters:'} &#60;&#62;;&#61;#&#123;&#125;">
						{l s='Displayed text when in-stock:'}
					</span>
				</label>
				<div class="col-lg-5">
					<div class="row">
						{include file="controllers/products/input_text_lang.tpl"
							languages=$languages
							input_value=$product->available_now
							input_name='available_now'}
					</div>
				</div>
			</div>
			<div class="row">
				{include file="controllers/products/multishop/checkbox.tpl" field="available_later" type="default" multilang="true"}
				<label class="control-label col-lg-5" for="available_later_{$id_lang}">
					<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Forbidden characters:'} &#60;&#62;;&#61;#&#123;&#125;">
						{l s='Displayed text when back-ordereding is allowed:'}
					</span>
					
				</label>
				<div class="col-lg-5">
					<div class="row">
						{include file="controllers/products/input_text_lang.tpl"
							languages=$languages
							input_value=$product->available_later
							input_name='available_later'}
					</div>
				</div>
			</div>
			
			{if !$countAttributes}
				<label for="available_date">{l s='Available date:'}</label>
				<input id="available_date" name="available_date" value="{$product->available_date}" class="datepicker" type="text" />
				<p>{l s='The available date when this product is out of stock.'}</p>
			{/if}
		{/if}

		<script type="text/javascript">
			var quantities_ajax_success = '{l s='Data saved'}';
			var quantities_ajax_waiting = '{l s='Saving data...'}';
		</script>
	</fieldset>
{/if}
{*$default_language*}