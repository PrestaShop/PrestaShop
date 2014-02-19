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

{if isset($product->id)}
	<div id="product-quantities" class="panel product-tab">
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
				<div class="form-group" {if $product->is_virtual || $product->cache_is_pack}style="display:none;"{/if} class="row stockForVirtualProduct">
					<div class="col-lg-9 col-lg-offset-3">
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
							</label>
						</p>
							{if $stock_management_active == 0 && !$product->cache_is_pack}
								<p class="help-block"><i class="icon-warning-sign"></i>&nbsp;{l s='This requires you to enable advanced stock management.'}</p>
							{else if $product->cache_is_pack}
								<p class="help-block">{l s='This parameter depends on the product(s) in the pack.'}</p>
							{/if}
					</div>
				</div>

				<div {if $product->is_virtual || $product->cache_is_pack}style="display:none;"{/if} class="form-group stockForVirtualProduct">
					<label class="control-label col-lg-3" for="depends_on_stock_1">{l s='Available quantities'}</label>
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
				<div class="form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<table class="table">
							<thead>
								<tr>
									<th><span class="title_box">{l s='Quantity'}</span></th>
									<th><span class="title_box">{l s='Designation'}</span></th>
								</tr>
							</thead>
							{foreach from=$attributes item=attribute}
								<tr>
									<td class="available_quantity" id="qty_{$attribute['id_product_attribute']}">
										<span>{$available_quantity[$attribute['id_product_attribute']]}</span>
										<input type="text" class="fixed-width-sm" value="{$available_quantity[$attribute['id_product_attribute']]|htmlentities}"/>
									</td>
									<td>{$product_designation[$attribute['id_product_attribute']]}</td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>
				<div id="when_out_of_stock" class="form-group">
					<label class="control-label col-lg-3">{l s='When out of stock'}</label>
					<div class="col-lg-9">
						<p class="radio">
							<label id="label_out_of_stock_1" for="out_of_stock_1">
								<input {if $product->out_of_stock == 0} checked="checked" {/if} id="out_of_stock_1" type="radio" checked="checked" value="0" class="out_of_stock" name="out_of_stock">
								{l s='Deny orders'}
							</label>
						</p>
						<p class="radio">
							<label id="label_out_of_stock_2" for="out_of_stock_2">
								<input {if $product->out_of_stock == 1} checked="checked" {/if} id="out_of_stock_2" type="radio" value="1" class="out_of_stock" name="out_of_stock">
								{l s='Allow orders'}
							</label>
						</p>
						<p class="radio">
							<label id="label_out_of_stock_3" for="out_of_stock_3">
								<input {if $product->out_of_stock == 2} checked="checked" {/if} id="out_of_stock_3" type="radio" value="2" class="out_of_stock" name="out_of_stock">
								{l s='Default'}:
								{if $order_out_of_stock == 1}
								{l s='Allow orders'}
								{else}
								{l s='Deny orders'}
								{/if} 
								<a class="confirm_leave" href="index.php?tab=AdminPPreferences&amp;token={$token_preferences}">
									{l s='as set in Products Preferences page'}
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
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right product_quantities_button"><i class="process-icon-save"></i> {l s='Save'}</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right product_quantities_button"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
		</div>
	</div>
	<div class="panel">
		<h3>{l s='Availability settings'}</h3>

		{if !$has_attribute}
			<div class="form-group">
				<label class="control-label col-lg-3" for="minimal_quantity">{l s='Minimum quantity'}</label>
				<div class="col-lg-9">
					<input class="form-control fixed-width-sm" maxlength="6" name="minimal_quantity" id="minimal_quantity" type="text" value="{$product->minimal_quantity|default:1}" />
					<p class="help-block">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)'}</p>
				</div>
			</div>
		{/if}

		{if $ps_stock_management}			
			<div class="form-group">
				<label class="control-label col-lg-3" for="available_now_{$default_language}">
					{include file="controllers/products/multishop/checkbox.tpl" field="available_now" type="default" multilang="true"}
					<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Forbidden characters:'} &#60;&#62;;&#61;#&#123;&#125;">
						{l s='Displayed text when in-stock'}
					</span>
				</label>
				<div class="col-lg-9">
					{include file="controllers/products/input_text_lang.tpl"
						languages=$languages
						input_value=$product->available_now
						input_name='available_now'}
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="available_later_{$default_language}">
					{include file="controllers/products/multishop/checkbox.tpl" field="available_later" type="default" multilang="true"}
					<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Forbidden characters:'} &#60;&#62;;&#61;#&#123;&#125;">
						{l s='Displayed text when back-ordering is allowed'}
					</span>
					
				</label>
				<div class="col-lg-9">
					{include file="controllers/products/input_text_lang.tpl"
						languages=$languages
						input_value=$product->available_later
						input_name='available_later'}
				</div>
			</div>
			
			{if !$countAttributes}
			<div class="form-group">
				<label class="control-label col-lg-3" for="available_date">{l s='Available date:'}</label>
				<div class="col-lg-9">
					<div class="input-group fixed-width-md">
						<input id="available_date" name="available_date" value="{$product->available_date}" class="datepicker" type="text" />
						<div class="input-group-addon">
							<i class="icon-calendar-empty"></i>
						</div>

					</div>
					<p class="help-block">{l s='The available date when this product is out of stock.'}</p>
				</div>
			</div>
			{/if}
		{/if}
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right product_quantities_button"><i class="process-icon-save"></i> {l s='Save'}</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right product_quantities_button"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
		</div>
		<script type="text/javascript">
			var quantities_ajax_success = '{l s='Data saved' js=1}';
			var quantities_ajax_waiting = '{l s='Saving data...' js=1}';
		</script>
	</div>
{/if}
<script type="text/javascript">
	hideOtherLanguage({$default_form_language});
</script>
