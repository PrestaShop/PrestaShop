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
{extends file="helpers/form/form.tpl"}

{block name="other_fieldsets"}
	{if isset($show_product_management_form)}
	<p>&nbsp;</p>

	<input type="hidden" id="product_ids" name="product_ids" value="{$product_ids}" />
	<input type="hidden" id="product_ids_to_delete" name="product_ids_to_delete" value="{$product_ids_to_delete}" />
	<input type="hidden" name="updatesupply_order" value="1" />

	<fieldset>
		<legend>
			<img alt="Supply Order Management" src="../img/admin/edit.gif">
			{l s='Manage the products you want to order from the supplier.'}
		</legend>

		<p class="clear">{l s='To add a product to the order, type the first letters of the product name, then select it from the drop-down list.'}</p>
		<input type="text" size="100" id="cur_product_name" />
		<span onclick="addProduct();" style="cursor: pointer;"><img src="../img/admin/add.gif" alt="{l s='Add a product to the supply order'}" title="{l s='Add a product to the supply order'}" /></span>

		<p>&nbsp;</p>

		<table class="table_grid">
			<tr>
				<td>
					<table
					id="products_in_supply_order"
					class="table"
					cellpadding="0" cellspacing="0"
					style="width: 100%; margin-bottom:10px;"
					>
						<thead>
							<tr class="nodrag nodrop">
								<th style="width: 150px">{l s='Reference'}</th>
								<th style="width: 50px">{l s='EAN13'}</th>
								<th style="width: 50px">{l s='UPC'}</th>
								<th style="width: 150px">{l s='Supplier Reference'}</th>
								<th>{l s='Name'}</th>
								<th style="width: 100px">{l s='Unit Price (tax excl.)'}</th>
								<th style="width: 100px">{l s='Quantity'}</th>
								<th style="width: 100px">{l s='Discount rate'}</th>
								<th style="width: 100px">{l s='Tax rate'}</th>
								<th style="width: 40px">{l s='Delete'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $products_list AS $product}
								<tr style="height:50px;">
									<td>
										{$product.reference}
										<input type="hidden" name="input_check_{$product.id_product}_{$product.id_product_attribute}" value="{$product.checksum}" />
										<input type="hidden" name="input_reference_{$product.id_product}_{$product.id_product_attribute}" value="{$product.reference}" />
										<input type="hidden" name="input_id_{$product.id_product}_{$product.id_product_attribute}" value="{if isset($product.id_supply_order_detail)}{$product.id_supply_order_detail}{/if}" />
									</td>
									<td>
										{$product.ean13}
										<input type="hidden" name="input_ean13_{$product.id_product}_{$product.id_product_attribute}" value="{$product.ean13}" />
									</td>
									<td>
										{$product.upc}
										<input type="hidden" name="input_upc_{$product.id_product}_{$product.id_product_attribute}" value="{$product.upc}" />
									</td>
									<td>
										{$product.supplier_reference}
										<input type="hidden" name="input_supplier_reference_{$product.id_product}_{$product.id_product_attribute}" value="{$product.supplier_reference}" />
									</td>
									<td>
										{$product.name}
										<input type="hidden" name="input_name_{$product.id_product}_{$product.id_product_attribute}" value="{$product.name}" />
									</td>
									<td class="center">
										{$currency->prefix}&nbsp;<input type="text" name="input_unit_price_te_{$product.id_product}_{$product.id_product_attribute}" value="{$product.unit_price_te|htmlentities}" size="8" />&nbsp;{$currency->suffix}
									</td>
									<td class="center">
										<input type="text" name="input_quantity_expected_{$product.id_product}_{$product.id_product_attribute}" value="{$product.quantity_expected|htmlentities}" size="5" />
									</td>
									<td class="center">
										<input type="text" name="input_discount_rate_{$product.id_product}_{$product.id_product_attribute}" value="{round($product.discount_rate, 4)}" size="5" />%
									</td>
									<td class="center">
										<input type="text" name="input_tax_rate_{$product.id_product}_{$product.id_product_attribute}" value="{round($product.tax_rate, 4)}" size="5" />%
									</td>
									<td class="center">
										<a href="#" id="deletelink|{$product.id_product}_{$product.id_product_attribute}" class="removeProductFromSupplyOrderLink">
											<img src="../img/admin/delete.gif" alt="{l s='Remove this product from the order.'}" title="{l s='Remove this product from the order.'}" />
										</a>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</td>
			</tr>
		</table>

	</fieldset>

	<script type="text/javascript">
		product_infos = null;
		debug = null;
		if ($('#product_ids').val() == '')
			product_ids = [];
		else
			product_ids = $('#product_ids').val().split('|');

		if ($('#product_ids_to_delete').val() == '')
			product_ids_to_delete = [];
		else
			product_ids_to_delete = $('#product_ids_to_delete').val().split('|');


		function addProduct()
		{
			// check if it's possible to add the product
			if (product_infos == null || $('#cur_product_name').val() == '')
			{
				jAlert('{l s='Please select at least one product.'}');
				return false;
			}

			if (!product_infos.unit_price_te)
				product_infos.unit_price_te = 0;

			// add a new line in the products table
			$('#products_in_supply_order > tbody:last').append(
				'<tr style="height:50px;">'+
				'<td>'+product_infos.reference+'<input type="hidden" name="input_check_'+product_infos.id+'" value="'+product_infos.checksum+'" /><input type="hidden" name="input_reference_'+product_infos.id+'" value="'+product_infos.reference+'" /></td>'+
				'<td>'+product_infos.ean13+'<input type="hidden" name="input_ean13_'+product_infos.id+'" value="'+product_infos.ean13+'" /></td>'+
				'<td>'+product_infos.upc+'<input type="hidden" name="input_upc_'+product_infos.id+'" value="'+product_infos.upc+'" /></td>'+
				'<td>'+product_infos.supplier_reference+'<input type="hidden" name="input_supplier_reference_'+product_infos.id+'" value="'+product_infos.supplier_reference+'" /></td>'+
				'<td>'+product_infos.name+'<input type="hidden" name="input_name_displayed_'+product_infos.id+'" value="'+product_infos.name+'" /></td>'+
				'<td class="center">{$currency->prefix}&nbsp;<input type="text" name="input_unit_price_te_'+product_infos.id+'" value="'+product_infos.unit_price_te+'" size="8" />&nbsp;{$currency->suffix}</td>'+
				'<td class="center"><input type="text" name="input_quantity_expected_'+product_infos.id+'" value="0" size="5" /></td>'+
				'<td class="center"><input type="text" name="input_discount_rate_'+product_infos.id+'" value="0" size="5" />%</td>'+
				'<td class="center"><input type="text" name="input_tax_rate_'+product_infos.id+'" value="0" size="5" />%</td>'+
				'<td class="center"><a href="#" class="removeProductFromSupplyOrderLink" id="deletelink|'+product_infos.id+'">'+
				'<img src="../img/admin/delete.gif" alt="{l s='Remove this product from the order.'}" title="{l s='Remove this product from the order.'}" />'+
				'</a></td></tr>'
			);

			// add the current product id to the product_id array - used for not show another time the product in the list
			product_ids.push(product_infos.id);

			// update the product_ids hidden field
			$('#product_ids').val(product_ids.join('|'));

			// clear the cur_product_name field
			$('#cur_product_name').val("");

			// clear the product_infos var
			product_infos = null;
		}

		/* function autocomplete */
		$(function() {
			// add click event on just created delete item link
			$('a.removeProductFromSupplyOrderLink').live('click', function() {

				var id = $(this).attr('id');
				var product_id = id.split('|')[1];


				//find the position of the product id in product_id array
				var position = jQuery.inArray(product_id, product_ids);
				if (position != -1)
				{
					//remove the id from the array
					product_ids.splice(position, 1);

					var input_id = $('input[name~="input_id_'+product_id+'"]');
					if (input_id != 'undefined')
						if (input_id.length > 0)
							product_ids_to_delete.push(product_id);

					// update the product_ids hidden field
					$('#product_ids').val(product_ids.join('|'));
					$('#product_ids_to_delete').val(product_ids_to_delete.join('|'));

					//remove the table row
					$(this).parents('tr:eq(0)').remove();
				}

				return false;
			});

			btn_save = $('span[class~="process-icon-save"]').parent();

			btn_save.click(function() {
				$('#supply_order_form').submit();
			});

			// bind enter key event on search field
			$('#cur_product_name').bind('keypress', function(e) {
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code == 13) { //Enter keycode
					e.stopPropagation();//Stop event propagation
					return false;
				}
			});

			// set autocomplete on search field
			$('#cur_product_name').autocomplete("ajax-tab.php", {
				delay: 100,
				minChars: 3,
				autoFill: true,
				max:100,
				matchContains: true,
				mustMatch:false,
				scroll:false,
				cacheLength:0,
	            dataType: 'json',
	            extraParams: {
	                id_supplier: '{$supplier_id}',
	                id_currency: '{$currency->id}',
					ajax : '1',
					controller : 'AdminSupplyOrders',
					token : '{$token}',
					action : 'searchProduct'
	            },
	            parse: function(data) {
		            if (data == null || data == 'undefined')
			        	return [];
	            	var res = $.map(data, function(row) {
		            	// filter the data to chaeck if the product is already added to the order
	            		if (jQuery.inArray(row.id, product_ids) == -1)
		    				return {
		    					data: row,
		    					result: row.supplier_reference + ' - ' + row.name,
		    					value: row.id
		    				}
	    			});
	    			return res;
	            },
	    		formatItem: function(item) {
	    			return item.supplier_reference + ' - ' + item.name;
	    		}
	        }).result(function(event, item){
				product_infos = item;
	            if (typeof(ajax_running_timeout) !== 'undefined')
	            	clearTimeout(ajax_running_timeout);
			});
		});
	</script>
	{/if}
{/block}
