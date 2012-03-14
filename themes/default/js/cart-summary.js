/*
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
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function()
{
	// If block cart isn't used, we don't bind the handle actions
	if (window.ajaxCart !== undefined)
	{
		$('.cart_quantity_up').unbind('click').live('click', function(){ upQuantity($(this).attr('id').replace('cart_quantity_up_', '')); return false;	});
		$('.cart_quantity_down').unbind('click').live('click', function(){ downQuantity($(this).attr('id').replace('cart_quantity_down_', '')); return false; });
		$('.cart_quantity_delete' ).unbind('click').live('click', function(){ deleteProductFromSummary($(this).attr('id')); return false; });
		$('.cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: updateQty });
	}
	
	$('.cart_address_delivery').live('change', function(){ changeAddressDelivery($(this)); });
	
	cleanSelectAddressDelivery();
});

function cleanSelectAddressDelivery()
{
	if (window.ajaxCart !== undefined)
	{
		//Removing "Ship to an other address" from the address delivery select option if there is not enought address
		$.each($('.cart_address_delivery'), function(it, item)
		{
			var options = $(item).find('option');
			var address_count = 0;
			
			var ids = $(item).attr('id').split('_');
			var id_product = ids[3];
			var id_product_attribute = ids[4];
			var id_address_delivery = ids[5];
			
			$.each(options, function(i) {
				if ($(options[i]).val() > 0
					&& ($('#product_' + id_product + '_' + id_product_attribute + '_0_' + $(options[i]).val()).length == 0 // Check the address is not already used for a similare products
						|| id_address_delivery == $(options[i]).val()
					)
				)
					address_count++;
			});
			
			if (address_count < 2) // Need at least two address to allow skipping products to multiple address
				$($(item).find('option[value=-2]')).remove();
			else if($($(item).find('option[value=-2]')).length == 0)
				$(item).append($('<option value="-2">' + ShipToAnOtherAddress + '</option>'));
		});
	}
}

function changeAddressDelivery(obj)
{
	var ids = obj.attr('id').split('_');
	var id_product = ids[3];
	var id_product_attribute = ids[4];
	var old_id_address_delivery = ids[5];
	var new_id_address_delivery = obj.val();
	
	if (new_id_address_delivery == old_id_address_delivery)
		return;
	
	if (new_id_address_delivery > 0) // Change the delivery address
	{
		$.ajax({
			type: 'GET',
			url: baseDir,
			async: true,
			cache: false,
			dataType: 'json',
			data: 'controller=cart&ajax=true&changeAddressDelivery&summary&id_product='+id_product
				+'&id_product_attribute='+id_product_attribute
				+'&old_id_address_delivery='+old_id_address_delivery
				+'&new_id_address_delivery='+new_id_address_delivery
				+'&token='+static_token
				+'&allow_refresh=1',
			success: function(jsonData)
			{
				// The product exist
				if ($('#product_'+id_product+'_'+id_product_attribute+'_0_'+new_id_address_delivery).length)
				{
					updateCustomizedDatas(jsonData.customizedDatas);
					updateCartSummary(jsonData.summary);
					updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
					updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
					if (typeof(getCarrierListAndUpdate) != 'undefined')
						getCarrierListAndUpdate();

					// @todo reverse the remove order
					// This effect remove the current line, but it's better to remove the other one, and refresshing this one
					$('#product_'+id_product+'_'+id_product_attribute+'_0_'+old_id_address_delivery).remove();
					
					// @todo improve customization upgrading
					$('.product_'+id_product+'_'+id_product_attribute+'_0_'+old_id_address_delivery).remove();
				}
				
				if (window.ajaxCart !== undefined)
					ajaxCart.refresh();
				updateAddressId(id_product, id_product_attribute, old_id_address_delivery, new_id_address_delivery);
				cleanSelectAddressDelivery();
			}
		});
	}
	else if (new_id_address_delivery == -1) // Adding a new address
			window.location = $($('.address_add a')[0]).attr('href');
	else if (new_id_address_delivery == -2) // Add a new line for this product
	{
		// This test is will not usefull in the future
		if (old_id_address_delivery == 0)
		{
			alert('Please select first an address'); // @todo translate
			return false;
		}
		
		// Get new address to deliver
		var id_address_delivery = 0;
		var options = $('#select_address_delivery_'+id_product+'_'+id_product_attribute+'_'+old_id_address_delivery+' option');
		$.each(options, function(i) {
			if ($(options[i]).val() > 0 && $(options[i]).val() != old_id_address_delivery
				&& $('#product_' + id_product + '_' + id_product_attribute + '_0_' + $(options[i]).val()).length == 0 // Check the address is not already used for a similare products
			)
			{
				id_address_delivery = $(options[i]).val();
				return false;
			}
		});
		
		$.ajax({
			type: 'GET',
			url: baseDir,
			async: true,
			cache: false,
			dataType: 'json',
			context: obj,
			data: 'controller=cart'
				+'&ajax=true&duplicate&summary'
				+'&id_product='+id_product
				+'&id_product_attribute='+id_product_attribute
				+'&id_address_delivery='+old_id_address_delivery
				+'&new_id_address_delivery='+id_address_delivery
				+'&token='+static_token
				+'&allow_refresh=1',
			success: function(jsonData)
			{
				if (jsonData.error)
				{
					alert(jsonData.reason);
					return;
				}
				
				var line = $('#product_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery);
				var new_line = line.clone();
				updateAddressId(id_product, id_product_attribute, old_id_address_delivery, id_address_delivery, new_line);
				line.after(new_line);
				new_line.find('input[name=quantity_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery + '_hidden]')
					.val(1);
				new_line.find('.cart_quantity_input')
					.val(1);
				$('#select_address_delivery_' + id_product+'_' + id_product_attribute + '_' + old_id_address_delivery).val(old_id_address_delivery);
				$('#select_address_delivery_' + id_product+'_' + id_product_attribute + '_' + id_address_delivery).val(id_address_delivery);
				
				
				cleanSelectAddressDelivery();
				
				updateCartSummary(jsonData.summary);
				
				if (window.ajaxCart !== undefined)
					ajaxCart.refresh();
			}
		});
	}
}

function updateAddressId(id_product, id_product_attribute, old_id_address_delivery, id_address_delivery, line)
{
	if (typeof(line) == 'undefined')
		var line = $('#product_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery);
	
	line.attr('id', 'product_' + id_product+'_' + id_product_attribute + '_0_' + id_address_delivery);
	line.find('.cart_quantity_input')
		.attr('name', 'quantity_' + id_product+'_' + id_product_attribute + '_0_' + id_address_delivery);
	line.find('input[name=quantity_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery + '_hidden]')
		.attr('name', 'quantity_' + id_product+'_' + id_product_attribute + '_0_' + id_address_delivery + '_hidden');
	line.find('#cart_quantity_down_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery)
		.attr('id', 'cart_quantity_down_' + id_product+'_' + id_product_attribute + '_0_' + id_address_delivery);
	line.find('#cart_quantity_up_' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery)
		.attr('id', 'cart_quantity_up_' + id_product+'_' + id_product_attribute + '_0_' + id_address_delivery);
	line.find('#' + id_product+'_' + id_product_attribute + '_0_' + old_id_address_delivery)
		.attr('id', id_product+'_' + id_product_attribute + '_0_' + id_address_delivery);
	line.find('#select_address_delivery_' + id_product+'_' + id_product_attribute + '_' + old_id_address_delivery)
		.attr('id', 'select_address_delivery_' + id_product+'_' + id_product_attribute + '_' + id_address_delivery);
}

function updateQty(val)
{
	var id = $(this.el).attr('name');
	var exp = new RegExp("^[0-9]+$");

	if (exp.test(val) == true)
	{
		var hidden = $('input[name='+ id +'_hidden]').val();
		var input = $('input[name='+ id +']').val();
		var QtyToUp = parseInt(input) - parseInt(hidden);

		if (parseInt(QtyToUp) > 0)
			upQuantity(id.replace('quantity_', ''), QtyToUp);
		else if(parseInt(QtyToUp) < 0)
			downQuantity(id.replace('quantity_', ''), QtyToUp);
	}
	else
		$('input[name='+ id +']').val($('input[name='+ id +'_hidden]').val());
	
	if (typeof(getCarrierListAndUpdate) != 'undefined')
		getCarrierListAndUpdate();
}

function deleteProductFromSummary(id)
{
	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;
	ids = id.split('_');
	productId = parseInt(ids[0]);
	if (typeof(ids[1]) != 'undefined')
		productAttributeId = parseInt(ids[1]);
	if (typeof(ids[2]) != 'undefined')
		customizationId = parseInt(ids[2]);
	if (typeof(ids[3]) != 'undefined')
		id_address_delivery = parseInt(ids[3]);
	$.ajax({
		type: 'GET',
		url: baseDir,
		async: true,
		cache: false,
		dataType: 'json',
		data: 'controller=cart'
			+'&ajax=true&delete&summary'
			+'&id_product='+productId
			+'&ipa='+productAttributeId
			+'&id_address_delivery='+id_address_delivery+ ( (customizationId != 0) ? '&id_customization='+customizationId : '')
			+'&token=' + static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(error in jsonData.errors)
				//IE6 bug fix
				if (error != 'indexOf')
					errors += jsonData.errors[error] + "\n";
			}
			else
			{
				if (jsonData.refresh)
					location.reload();
				if (parseInt(jsonData.summary.products.length) == 0)
				{
					$('#center_column').children().each(function() {
						if ($(this).attr('id') != 'emptyCartWarning' && $(this).attr('class') != 'breadcrumb' && $(this).attr('id') != 'cart_title')
						{
							$(this).fadeOut('slow', function () {
								$(this).remove();
							});
						}
					});
					$('#summary_products_label').remove();
					$('#emptyCartWarning').fadeIn('slow');
				}
				else
				{
					$('#product_'+ id).fadeOut('slow', function() {
							$(this).remove();
							cleanSelectAddressDelivery();
						});

					var exist = false;
					for (i=0;i<jsonData.summary.products.length;i++)
						if (jsonData.summary.products[i].id_product == productId)
							exist = true;

					// if all customization remove => delete product line
					if (!exist)
						$('#product_'+ productId+'_'+productAttributeId).fadeOut('slow', function() {
							$(this).remove();
						});
				}
				updateCartSummary(jsonData.summary);
				updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
				updateCustomizedDatas(jsonData.customizedDatas);
				if (typeof(getCarrierListAndUpdate) != 'undefined')
					getCarrierListAndUpdate();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
	});
}

function upQuantity(id, qty)
{
	if (typeof(qty) == 'undefined' || !qty)
		qty = 1;
	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;
	ids = id.split('_');
	productId = parseInt(ids[0]);
	if (typeof(ids[1]) != 'undefined')
		productAttributeId = parseInt(ids[1]);
	if (typeof(ids[2]) != 'undefined')
		customizationId = parseInt(ids[2]);
	if (typeof(ids[3]) != 'undefined')
		id_address_delivery = parseInt(ids[3]);
	$.ajax({
		type: 'GET',
		url: baseDir,
		async: true,
		cache: false,
		dataType: 'json',
		data: 'controller=cart'
			+'&ajax=true'
			+'&add'
			+'&getproductprice'
			+'&summary'
			+'&id_product='+productId
			+'&ipa='+productAttributeId
			+'&id_address_delivery='+id_address_delivery + ( (customizationId != 0) ? '&id_customization='+customizationId : '')
			+'&qty='+qty
			+'&token='+static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(error in jsonData.errors)
					//IE6 bug fix
					if(error != 'indexOf')
						errors += jsonData.errors[error] + "\n";
				alert(errors);
				$('input[name=quantity_'+ id +']').val($('input[name=quantity_'+ id +'_hidden]').val());
			}
			else
			{
				if (jsonData.refresh)
					location.reload();
				updateCustomizedDatas(jsonData.customizedDatas);
				updateCartSummary(jsonData.summary);
				updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
				if (typeof(getCarrierListAndUpdate) != 'undefined')
					getCarrierListAndUpdate();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
	});
}

function downQuantity(id, qty)
{
	var val = $('input[name=quantity_'+id+']').val();
	var newVal = val;
	if(typeof(qty)=='undefined' || !qty)
	{
		qty = 1;
		newVal = val - 1;
	}
	else if (qty < 0)
		qty = -qty;
	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;
	if (newVal > 0)
	{
		ids = id.split('_');
		productId = parseInt(ids[0]);
		if (typeof(ids[1]) != 'undefined')
			productAttributeId = parseInt(ids[1]);
		if (typeof(ids[2]) != 'undefined')
			customizationId = parseInt(ids[2]);
		if (typeof(ids[3]) != 'undefined')
			id_address_delivery = parseInt(ids[3]);
		$.ajax({
			type: 'GET',
			url: baseDir,
			async: true,
			cache: false,
			dataType: 'json',
			data: 'controller=cart'
				+'&ajax=true'
				+'&add'
				+'&getproductprice'
				+'&summary'
				+'&id_product='+productId
				+'&ipa='+productAttributeId
				+'&id_address_delivery='+id_address_delivery
				+'&op=down' + ( (customizationId != 0) ? '&id_customization='+customizationId : '')
				+'&qty='+qty
				+'&token='+static_token
				+'&allow_refresh=1',
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += jsonData.errors[error] + "\n";
					alert(errors);
					$('input[name=quantity_'+ id +']').val($('input[name=quantity_'+ id +'_hidden]').val());
				}
				else
				{
					if (jsonData.refresh)
						location.reload();
					updateCustomizedDatas(jsonData.customizedDatas);
					updateCartSummary(jsonData.summary);
					updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
					updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
					if (typeof(getCarrierListAndUpdate) != 'undefined')
						getCarrierListAndUpdate();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
		});

	}
	else
	{
		deleteProductFromSummary(id);
	}
}

function updateCartSummary(json)
{
	// Update products prices + discount
	var i;
	var nbrProducts = 0;

	if (typeof json == 'undefined')
		return;

	for (i=0;i<json.products.length;i++)
	{
		// if reduction, we need to show it in the cart by showing the initial price above the current one
		var reduction = json.products[i].reduction_applies;
		var initial_price_text = '';
		initial_price = '';
		if (typeof(json.products[i].price_without_quantity_discount) != 'undefined')
			initial_price = formatCurrency(json.products[i].price_without_quantity_discount, currencyFormat, currencySign, currencyBlank);
		var current_price = '';
		if (priceDisplayMethod != 0)
			current_price = formatCurrency(json.products[i].price, currencyFormat, currencySign, currencyBlank);
		else
			current_price = formatCurrency(json.products[i].price_wt, currencyFormat, currencySign, currencyBlank);
		if (reduction && typeof(initial_price) != 'undefined')
		{
			if (initial_price != '' && initial_price > current_price)
				initial_price_text = '<span style="text-decoration:line-through;">'+initial_price+'</span><br />';
		}

		key_for_blockcart = json.products[i].id_product+'_'+json.products[i].id_product_attribute;
		if (json.products[i].id_product_attribute == 0)
			key_for_blockcart = json.products[i].id_product;

		$('#cart_block_product_'+key_for_blockcart+' span.quantity').html(json.products[i].cart_quantity);

		if (priceDisplayMethod != 0)
		{
			$('#cart_block_product_'+key_for_blockcart+' span.price').html(formatCurrency(json.products[i].total, currencyFormat, currencySign, currencyBlank));
			$('#product_price_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_address_delivery).html(initial_price_text+current_price);
			$('#total_product_price_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_address_delivery).html(formatCurrency(json.products[i].total, currencyFormat, currencySign, currencyBlank));
		}
		else
		{
			$('#cart_block_product_'+key_for_blockcart+' span.price').html(formatCurrency(json.products[i].total_wt, currencyFormat, currencySign, currencyBlank));
			$('#product_price_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_address_delivery).html(initial_price_text+current_price);
			$('#total_product_price_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_address_delivery).html(formatCurrency(json.products[i].total_wt, currencyFormat, currencySign, currencyBlank));
		}

		nbrProducts += parseInt(json.products[i].cart_quantity);

		if(json.products[i].id_customization == null)
		{
			$('input[name=quantity_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_0_'+json.products[i].id_address_delivery+']').val(json.products[i].cart_quantity);
			$('input[name=quantity_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_0_'+json.products[i].id_address_delivery+'_hidden]').val(json.products[i].cart_quantity);
		}
		else
		{
			//$('input[name=quantity_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_customization+'_'+json.products[i].id_address_delivery+']')
			//	.val(json.products[i].cart_quantity);
			$('#cart_quantity_custom_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+'_'+json.products[i].id_address_delivery)
				.html(json.products[i].cart_quantity);
		}

		// Show / hide quantity button if minimal quantity
		if (parseInt(json.products[i].minimal_quantity) == parseInt(json.products[i].cart_quantity) && json.products[i].minimal_quantity != 1)
			$('#cart_quantity_down_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+Number(json.products[i].id_customization)+'_'+json.products[i].id_address_delivery).fadeTo('slow',0.3);
		else
			$('#cart_quantity_down_'+json.products[i].id_product+'_'+json.products[i].id_product_attribute+Number(json.products[i].id_customization)+'_'+json.products[i].id_address_delivery).fadeTo('slow',1);

	}

	// Update discounts
	if (json.discounts.length == 0)
	{
		$('.cart_discount').each(function(){$(this).remove()});
		$('.cart_total_voucher').remove();
	}
	else
	{
		if (priceDisplayMethod != 0)
			$('#total_discount').html(formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank));
		else
			$('#total_discount').html(formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank));

		$('.cart_discount').each(function(){
			var idElmt = $(this).attr('id').replace('cart_discount_','');
			var toDelete = true;

			for (i=0;i<json.discounts.length;i++)
			{
				if (json.discounts[i].id_discount == idElmt)
				{
					if (json.discounts[i].value_real != '!')
					{
						if (priceDisplayMethod != 0)
							$('#cart_discount_' + idElmt + ' td.cart_discount_price span.price-discount').html(formatCurrency(json.discounts[i].value_tax_exc * -1, currencyFormat, currencySign, currencyBlank));
						else
							$('#cart_discount_' + idElmt + ' td.cart_discount_price span.price-discount').html(formatCurrency(json.discounts[i].value_real * -1, currencyFormat, currencySign, currencyBlank));

					}
					toDelete = false;
				}
			}
			if (toDelete)
				$('#cart_discount_' + idElmt + ', #cart_total_voucher').fadeTo('fast', 0, function(){ $(this).remove(); });
		});
	}

	// Block cart
	if (priceDisplayMethod != 0)
	{
		$('#cart_block_shipping_cost').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
		$('#cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping_tax_exc, currencyFormat, currencySign, currencyBlank));
		$('#cart_block_total').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
	} else {
		$('#cart_block_shipping_cost').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
		$('#cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
		$('#cart_block_total').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
	}

	$('#cart_block_tax_cost').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));
	$('.ajax_cart_quantity').html(nbrProducts);

	// Cart summary
	$('#summary_products_quantity').html(nbrProducts+' '+(nbrProducts > 1 ? txtProducts : txtProduct));
	if (priceDisplayMethod != 0)
		$('#total_product').html(formatCurrency(json.total_products, currencyFormat, currencySign, currencyBlank));
	else
		$('#total_product').html(formatCurrency(json.total_products_wt, currencyFormat, currencySign, currencyBlank));
	$('#total_price').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
	$('#total_price_without_tax').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
	$('#total_tax').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));

	if (json.total_shipping <= 0)
		$('.cart_total_delivery').fadeOut();
	else
	{
		$('.cart_total_delivery').fadeIn();
		if (priceDisplayMethod != 0)
		{
			$('#total_shipping').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
		}
		else
		{
			$('#total_shipping').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
		}
	}

	if (json.free_ship > 0 && !json.is_virtual_cart)
	{
		$('.cart_free_shipping').fadeIn();
		$('#free_shipping').html(formatCurrency(json.free_ship, currencyFormat, currencySign, currencyBlank));
	}
	else
		$('.cart_free_shipping').hide();

	if (json.total_wrapping > 0)
	{
		$('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
		$('#total_wrapping').parent().show();
	}
	else
	{
		$('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
		$('#total_wrapping').parent().hide();
	}
	if (window.ajaxCart !== undefined)
		ajaxCart.refresh();
}

function updateCustomizedDatas(json)
{
	for(i in json)
		for(j in json[i])
			for(k in json[i][j])
				for(l in json[i][j][k])
				{
					$('input[name=quantity_'+i+'_'+j+'_'+l+'_'+k+'_hidden]').val(json[i][j][k][l]['quantity']);
					$('input[name=quantity_'+i+'_'+j+'_'+l+'_'+k+']').val(json[i][j][k][l]['quantity']);
				}
}

function updateHookShoppingCart(html)
{
	$('#HOOK_SHOPPING_CART').html(html);
}

function updateHookShoppingCartExtra(html)
{
	$('#HOOK_SHOPPING_CART_EXTRA').html(html);
}
function refreshDeliveryOptions()
{
	$.each($('.delivery_option_radio'), function() {
		if ($(this).attr('checked'))
		{
			if ($(this).parent().find('.delivery_option_carrier.not-displayable').length == 0)
				$(this).parent().find('.delivery_option_carrier').show();
			var carrier_id_list = $(this).val().split(',');
			carrier_id_list.pop();
			var it = this;
			$(carrier_id_list).each(function() {
				$(it).parent().find('input[value="'+this.toString()+'"]').change();
			});
		}
		else
			$(this).parent().find('.delivery_option_carrier').hide();
	});
}
$(document).ready(function() {
	
	refreshDeliveryOptions();
	
	$('.delivery_option_radio').live('change', function() {
		refreshDeliveryOptions();
	});
	
	$('#allow_seperated_package').live('click', function() {
		$.ajax({
			type: 'GET',
			url: baseDir,
			async: true,
			cache: false,
			data: 'controller=cart&ajax=true&allowSeperatedPackage&value='
				+($(this).attr('checked') ? '1' : '0')
				+'&token='+static_token
				+'&allow_refresh=1',
			success: function(jsonData)
			{
				if (typeof(getCarrierListAndUpdate) != 'undefined')
					getCarrierListAndUpdate();
			}
		});
	});
	
	$('#gift').checkboxChange(function() { $('#gift_div').show('slow'); }, function() { $('#gift_div').hide('slow'); });
	
	$('#enable-multishipping').checkboxChange(
		function() {
			$('.standard-checkout').hide(0);
			$('.multishipping-checkout').show(0);
		},
		function() {
			$('.standard-checkout').show(0);
			$('.multishipping-checkout').hide(0);
		}
	);
});

function updateExtraCarrier(id_delivery_option, id_address)
{
	if(typeof(orderOpcUrl) != 'undefined')
		var url = orderOpcUrl;
	else
		var url = orderUrl;
	
	$.ajax({
		type: 'POST',
		url: url,
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true'
			+'&method=updateExtraCarrier'
			+'&id_address='+id_address
			+'&id_delivery_option='+id_delivery_option
			+'&token='+static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			$('#HOOK_EXTRACARRIER_'+id_address).html(jsonData['content']);
		}
	});
}