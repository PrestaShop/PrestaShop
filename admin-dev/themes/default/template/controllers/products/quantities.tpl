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
*  @version  Release: $Revision: 11069 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($product->id)}

	<h4>{l s='Available quantities for sale'}</h4>
	<div class="separation"></div>
	<div class="hint" style="display:block; position:'auto';">
		<p>{l s='This interface allows you to manage the available quantities for sale of the current product and its combinations on the current shop.'}</p>
		<p>{l s='You can choose to use the advanced stock management system for this product or not.'}</p>
		<p>{l s='You can manually specify the quantities for the product / each product combinations, or choose to automatically determine these quantities based on your stock (if advanced stock management is activated).'}</p>
		<p>{l s='In this case, the quantities correspond to the quantitites of the real stock in the warehouses associated to the current shop or current group of shops.'}</p>
		<br/>
		<p>{l s='For packs, if it has products that use the advanced stock management, you have to specify a common warehouse for these products and the pack.'}</p>
	</div>
	<br />
	<h4>{l s='Available quantities for sale'}</h4>
	<div class="separation"></div>


	{if $show_quantities == true}
		<div class="warn" id="available_quantity_ajax_msg" style="display: none;"></div>
		<div class="error" id="available_quantity_ajax_error_msg" style="display: none;"></div>
		<div class="conf" id="available_quantity_ajax_success_msg" style="display: none;"></div>

		<table cellpadding="5" style="width:100%">
			<tbody>
				<tr {if $product->is_virtual}style="display:none;"{/if} class="stockForVirtualProduct">
					<td valign="top" style="vertical-align:top;">
						<input 
							{if $product->advanced_stock_management == 1 && $stock_management_active == 1}
								value="1" checked="checked"
							{else}
								value="0"
							{/if} 
							{if $stock_management_active == 0 || $product->cache_is_pack}
								disabled="disabled" 
							{/if} 
							type="checkbox" name="advanced_stock_management" class="advanced_stock_management" id="advanced_stock_management" />
						<label style="float:none;font-weight:normal" for="advanced_stock_management">
							{l s='I want to use the advanced stock management system for this product'} 
							{if $stock_management_active == 0 && !$product->cache_is_pack}
							&nbsp;-&nbsp;<b>{l s='This requires to enable the advanced stock management.'}</b>
							{else if $product->cache_is_pack}
							&nbsp;-&nbsp;<b>{l s='This parameter depends on the product(s) in the pack.'}</b>
							{/if}
						</label>
						<br /><br />
					</td>
				</tr>
				<tr {if $product->is_virtual}style="display:none;"{/if} class="stockForVirtualProduct">
					<td valign="top" style="vertical-align:top;">
						<input 
							{if $product->depends_on_stock == 1 && $stock_management_active == 1}
								checked="checked" 
							{/if} 
							{if $stock_management_active == 0 || $product->advanced_stock_management == 0 || $product->cache_is_pack}
								disabled="disabled" 
							{/if} 
							type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_1" value="1"/>
						<label style="float:none;font-weight:normal" for="depends_on_stock_1">
							{l s='Available quantities for current product and its combinations are based on stock in the warehouses'} 
							{if ($stock_management_active == 0 || $product->advanced_stock_management == 0) && !$product->cache_is_pack}
							&nbsp;-&nbsp;<b>{l s='This requires to enable the advanced stock management globaly/for this product.'}</b>
							{else if $product->cache_is_pack}
							&nbsp;-&nbsp;<b>{l s='This parameter depends on the product(s) in the pack.'}</b>
							{/if}
						</label>
						<br /><br />
					</td>
				</tr>
				
				<tr {if $product->is_virtual}style="display:none;"{/if} class="stockForVirtualProduct">
					<td valign="top" style="vertical-align:top;">
						<input 
							{if $product->depends_on_stock == 0 || $stock_management_active == 0}
								checked="checked" 
							{/if} 
							type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_0" value="0"/>
						<label style="float:none;font-weight:normal" for="depends_on_stock_0">
							{l s='I want to specify available quantities manually'}
						</label>
						<br /><br />
					</td>
				</tr>
				{if isset($pack_quantity)}
				<tr>
					<td valign="top" style="text-align:left;vertical-align:top;">
						<p><b>{l s='Given the quantities of the products in this pack, the maximum quantity should be: '} {$pack_quantity}</b></p>
					</td>
				</tr>
				{/if}
				<tr>
					<td valign="top" style="text-align:left;vertical-align:top;">
						<table class="table" cellpadding="0" cellspacing="0" style="width:100%;">
								<colgroup>
									<col width="50"></col>
									<col></col>
								</colgroup>
							<thead>
								<tr>
									<th>{l s='Quantity'}</th>
									<th>{l s='Designation'}</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$attributes item=attribute}
								<tr>
									<td  class="available_quantity" id="qty_{$attribute['id_product_attribute']}">
										<span>{$available_quantity[$attribute['id_product_attribute']]}</span>
										<input type="text" value="{$available_quantity[$attribute['id_product_attribute']]|htmlentities}"/>
									</td>
									<td>{$product_designation[$attribute['id_product_attribute']]}</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</td>
				</tr>
				<tr id="when_out_of_stock">
					<td>
						<table style="margin-top: 15px;">
							<tbody>
								<tr>
									<td class="col-left"><label>{l s='When out of stock:'}</label></td>
									<td style="padding-bottom:5px;">
										<ul class="listForm">
											<li>
										<input {if $product->out_of_stock == 0} checked="checked" {/if} id="out_of_stock_1" type="radio" checked="checked" value="0" class="out_of_stock" name="out_of_stock">
										<label id="label_out_of_stock_1" class="t" for="out_of_stock_1">{l s='Deny orders'}</label>
											</li>
											<li>
										<input {if $product->out_of_stock == 1} checked="checked" {/if} id="out_of_stock_2" type="radio" value="1" class="out_of_stock" name="out_of_stock">
										<label id="label_out_of_stock_2" class="t" for="out_of_stock_2">{l s='Allow orders'}</label>
										</li>
										<li>
										<input {if $product->out_of_stock == 2} checked="checked" {/if} id="out_of_stock_3" type="radio" value="2" class="out_of_stock" name="out_of_stock">
										<label id="label_out_of_stock_3" class="t" for="out_of_stock_3">
											{l s='Default'}:
											<i>{l s='Deny orders'}</i>
											<a class="confirm_leave" href="index.php?tab=AdminPPreferences&token={$token_preferences}">
												{l s='as set in Preferences'}
											</a>
										</label>
										</li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	{else}
		<div class="warn">
			<p>{l s='It is not possible to manage quantities when : '}</p>
			<ul>
				<li>{l s='You are managing all shops.'}</li>
				<li>{l s='You are managing a group of shops where quantities are not shared between all shops of this group.'}</li>
				<li>{l s='You are managing a shop which is in a group where quantities are shared between all shops of this group.'}</li>
			</ul>
		</div>
	{/if}
<div class="separation"></div>
<h4>{l s='Availability settings'}</h4>
<table cellpadding="5">
	{if !$ps_stock_management}
			<tr>
				<td colspan="2">{l s='The stock management is disabled'}</td>
			</tr>
		{/if}
		{if !$has_attribute}
		<tr>
			<td class="col-left"><label>{l s='Minimum quantity:'}</label></td>
			<td style="padding-bottom:5px;">
				<input size="3" maxlength="6" name="minimal_quantity" id="minimal_quantity" type="text" value="{$product->minimal_quantity|default:1}" />
				<p class="preference_description">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)'}</p>
			</td>
		</tr>
	{/if}
	<tr>
		<td class="col-left"><label>{l s='Displayed text when in-stock:'}</label></td>
		<td style="padding-bottom:5px;">
				{include file="controllers/products/input_text_lang.tpl"
					languages=$languages
					input_value=$product->available_now
					input_name='available_now'}
			<span class="hint" name="help_box">{l s='Forbidden characters:'} <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
	</td>
	</tr>
	<tr>
		<td class="col-left"><label>{l s='Displayed text when allowed to be back-ordered:'}</label></td>
		<td style="padding-bottom:5px;">
				{include file="controllers/products/input_text_lang.tpl"
					languages=$languages
					input_value=$product->available_later
					input_name='available_later'}
			<span class="hint" name="help_box">{l s='Forbidden characters:'} <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
		</td>
	</tr>
	{if !$countAttributes}
		<tr>
			<td class="col-left"><label>{l s='Available date:'}</label></td>
			<td style="padding-bottom:5px;">
				<input id="available_date" name="available_date" value="{$product->available_date}" class="datepicker"
					style="text-align: center;" type="text" />
				<p>{l s='The available date when this product is out of stock'}</p>
			</td>
		</tr>
	{/if}
</table>

	<script type="text/javascript">
		$('.datepicker').datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		var showAjaxError = function(msg)
		{
			$('#available_quantity_ajax_error_msg').html(msg);
			$('#available_quantity_ajax_error_msg').show();
			$('#available_quantity_ajax_msg').hide();
			$('#available_quantity_ajax_success_msg').hide();
		};
	
		var showAjaxSuccess = function(msg)
		{
			$('#available_quantity_ajax_success_msg').html(msg);
			$('#available_quantity_ajax_error_msg').hide();
			$('#available_quantity_ajax_msg').hide();
			$('#available_quantity_ajax_success_msg').show();
		};
	
		var showAjaxMsg = function(msg)
		{
			$('#available_quantity_ajax_msg').html(msg);
			$('#available_quantity_ajax_error_msg').hide();
			$('#available_quantity_ajax_msg').show();
			$('#available_quantity_ajax_success_msg').hide();
		};
	
		var ajaxCall = function(data)
		{
			data.ajaxProductQuantity = 1;
			data.id_product = '{$product->id}';
			data.token = "{$token}";
			data.ajax = 1;
			data.controller = "AdminProducts";
			data.action = "productQuantity";
			showAjaxMsg('{l s='Saving data...'}');
			$.ajax({
				type: "POST",
				url: "ajax-tab.php",
				data: data,
				dataType: 'json',
				async : true,
				success: function(msg)
				{
					if (msg.error)
					{
						showAjaxError(msg.error);
						return;
					}
					showAjaxSuccess('{l s='Data saved'}');
				},
				error: function(msg)
				{
					showAjaxError(msg.error);
				}
			});
		};
	
		var refreshQtyAvaibilityForm = function()
		{
			if ($('#depends_on_stock_0').attr('checked'))
			{
				$('.available_quantity').find('input').show();
				$('.available_quantity').find('span').hide();
			}
			else
			{
				$('.available_quantity').find('input').hide();
				$('.available_quantity').find('span').show();
			}
		};
	
		$('.depends_on_stock').click(function(e)
		{
			refreshQtyAvaibilityForm();
			ajaxCall( { actionQty: 'depends_on_stock', value: $(this).val() } );
			if($(this).val() == 0)
				$('.available_quantity input').trigger('change');
		});

		$('.advanced_stock_management').click(function(e)
		{
			var val = 0;
			if ($(this).attr('checked'))
				val = 1;
			
			ajaxCall( { actionQty: 'advanced_stock_management', value: val } );
			if (val == 1)
			{
				$(this).val(1);
				$('#depends_on_stock_1').attr('disabled', false);
			}
			else
			{
				$(this).val(0);
				$('#depends_on_stock_1').attr('disabled', true);
				$('#depends_on_stock_0').attr('checked', true);
				ajaxCall( { actionQty: 'depends_on_stock', value: 0} );
				refreshQtyAvaibilityForm();
			}
			refreshQtyAvaibilityForm();
		});
	
		// bind enter key event on search field
		$('.available_quantity').find('input').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13) { //Enter keycode
				e.stopPropagation();//Stop event propagation
				return false;
			}
		});
	
		$('.available_quantity').find('input').change(function(e, init_val)
		{
			ajaxCall( { actionQty: 'set_qty', id_product_attribute: $(this).parent().attr('id').split('_')[1], value: $(this).val() } );
		});
	
		$('.out_of_stock').click(function(e)
		{
			refreshQtyAvaibilityForm();
			ajaxCall( { actionQty: 'out_of_stock', value: $(this).val() } );
		});
	
		refreshQtyAvaibilityForm();
	</script>

{/if}