/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 10575 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var current_product = null;

$(document).ready(function() {
	$('.add_product').click(function() {
		$('.cancel_product_change_link:visible').trigger('click');
		$('.add_product_fields').show();
		$('.edit_product_fields').hide();
		$('.standard_refund_fields').hide();
		$('.partial_refund_fields').hide();
		$('tr#new_product').slideDown('fast', function () {
			$('tr#new_product td').fadeIn('fast');
		});
		
		$.scrollTo('tr#new_product', 1200, {offset: -100});
		
		return false;
	});
	
	$("#add_product_product_name").autocomplete(admin_order_tab_link, 
		{
			minChars: 3,
			max: 10,
			width: 500,
			selectFirst: false,
			scroll: false,
			dataType: "json",
			highlightItem: true,
			formatItem: function(data, i, max, value, term) {
				return value;
			},
			parse: function(data) {
				var products = new Array();
				for (var i = 0; i < data.products.length; i++)
					products[i] = { data: data.products[i], value: data.products[i].name };
				return products;
			},
			extraParams: {
				ajax: true,
				token: token,
				action: 'searchProducts',
				id_lang: id_lang,
				id_currency: id_currency,
				id_address: id_address,
				product_search: function() { return $('#add_product_product_name').val(); }
			}
		}
	)
	.result(function(event, data, formatted) {
		if (!data)
		{
			$('tr#new_product input, tr#new_product select').each(function() {
				if ($(this).attr('id') != 'add_product_product_name')
					$('tr#new_product input, tr#new_product select').attr('disabled', 'disabled');
			});
		}
		else
		{
			$('tr#new_product input, tr#new_product select').removeAttr('disabled');
			// Keep product variable
			current_product = data;
			$('#add_product_product_id').val(data.id_product);
			$('#add_product_product_name').val(data.name);
			$('#add_product_product_price_tax_incl').val(data.price_tax_incl);
			$('#add_product_product_price_tax_excl').val(data.price_tax_excl);
			addProductRefreshTotal();
			$('#add_product_product_stock').html(data.qty_in_stock);
			
			if (current_product.combinations.length !== 0)
			{
				// Reset combinations list
				$('select#add_product_product_attribute_id').html('');
				$.each(current_product.combinations, function() {
					$('select#add_product_product_attribute_id').append('<option value="'+this.id_product_attribute+'"'+(this.default_on == 1 ? ' selected="selected"' : '')+'>'+this.attributes+'</option>');
					if (this.default_on == 1)
						$('#add_product_product_stock').html(this.qty_in_stock);
				});
				// Show select list
				$('#add_product_product_attribute_area').show();
			}
			else
			{
				// Reset combinations list
				$('select#add_product_product_attribute_id').html('');
				// Hide select list
				$('#add_product_product_attribute_area').hide();
			}
		}
	});
	
	$('select#add_product_product_attribute_id').change(function() {
		$('#add_product_product_price_tax_incl').val(current_product.combinations[$(this).val()].price_tax_incl);
		$('#add_product_product_price_tax_excl').val(current_product.combinations[$(this).val()].price_tax_excl);
		
		addProductRefreshTotal();
		
		$('#add_product_product_stock').html(current_product.combinations[$(this).val()].qty_in_stock);
	});
	
	$('input#add_product_product_quantity').keyup(function() {
		var quantity = parseInt($(this).val()); 
		if (quantity < 1 || isNaN(quantity))
			quantity = 1;
		var stock_available = parseInt($('#add_product_product_stock').html());
		// total update
		addProductRefreshTotal();
		
		// stock status update
		if (quantity > stock_available)
			$('#add_product_product_stock').css('font-weight', 'bold').css('color', 'red').css('font-size', '1.2em');
		else
			$('#add_product_product_stock').css('font-weight', 'normal').css('color', 'black').css('font-size', '1em');;
	});
	
	$('#submitAddProduct').click(function() {
		var go = true;
		
		if ($('input#add_product_product_id').val() == 0)
		{
			alert(txt_add_product_no_product);
			go = false;
		}
		
		if ($('input#add_product_product_quantity').val() == 0)
		{
			alert(txt_add_product_no_product_quantity);
			go = false;
		}
		
		if ($('input#add_product_product_price_excl').val() == 0)
		{
			alert(txt_add_product_no_product_price);
			go = false;
		}
		
		if (go)
		{
			if (parseInt($('input#add_product_product_quantity').val()) > parseInt($('#add_product_product_stock').html()))
				go = confirm(txt_add_product_stock_issue);
			
			if (go && $('select#add_product_product_invoice').val() == 0)
				go = confirm(txt_add_product_new_invoice);
			
			if (go)
			{
				var query = 'ajax=1&token='+token+'&action=addProductOnOrder&id_order='+id_order+'&';
				
				query += $('tr#new_product select, tr#new_product input').serialize();
				if ($('select#add_product_product_invoice').val() == 0)
					query += '&'+$('tr#new_invoice select, tr#new_invoice input').serialize();
				
				$.ajax({
					type: 'POST',
					url: admin_order_tab_link,
					cache: false,
					dataType: 'json',
					data : query,
					success : function(data)
					{
						if (data.result)
						{
							addViewOrderDetailRow(data.view);
							updateAmounts(data.order);
							$('.standard_refund_fields').hide();
							$('.partial_refund_fields').hide();
						}
						else
							alert(data.error);
					}
				});
			}	
		}
	});
	
	$('#edit_shipping_cost_link').click(function() {
		$('#shipping_cost_show').hide();
		$('#shipping_cost_edit').show();
		
		$('#edit_shipping_cost_link').hide();
		$('#cancel_shipping_cost_link').show();
		
		return false;
	});
	
	$('#cancel_shipping_cost_link').click(function() {
		$('#shipping_cost_show').show();
		$('#shipping_cost_edit').hide();
		
		$('#edit_shipping_cost_link').show();
		$('#cancel_shipping_cost_link').hide();
		
		return false;
	});
	
	$('.edit_shipping_number_link').click(function() {
		$(this).parent().find('.shipping_number_show').hide();
		$(this).parent().find('.shipping_number_edit').show();
		
		$(this).parent().find('.edit_shipping_number_link').hide();
		$(this).parent().find('.cancel_shipping_number_link').show();
		
		return false;
	});
	
	$('.cancel_shipping_number_link').click(function() {
		$(this).parent().find('.shipping_number_show').show();
		$(this).parent().find('.shipping_number_edit').hide();
		
		$(this).parent().find('.edit_shipping_number_link').show();
		$(this).parent().find('.cancel_shipping_number_link').hide();
		
		return false;
	});
	
	$('#add_product_product_invoice').change(function() {
		if ($(this).val() == '0')
			$('#new_invoice').slideDown('slow');
		else
			$('#new_invoice').slideUp('slow');
	});
	
	$('#add_product_product_price_tax_excl').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;
		
		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_incl').val(ps_round(price_tax_excl * tax_rate, 2));
		
		// Update total product
		addProductRefreshTotal();
	});

	$('#add_product_product_price_tax_incl').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;
		
		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_excl').val(ps_round(price_tax_incl / tax_rate, 2));
		
		// Update total product
		addProductRefreshTotal();
	});
	
	$('.edit_product_change_link').click(function() {
		$('.add_product_fields').hide();
		$('.standard_refund_fields').hide();
		$('.edit_product_fields').show();
		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();
		
		query = 'ajax=1&token='+token+'&action=loadProductInformation&id_order_detail='+
				$(this).parent().parent().find('input.edit_product_id_order_detail').val()+'&id_address='+id_address;
		var element = $(this);
		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : query,
			success : function(data)
			{
				if (data.result)
				{
					current_product = data;
					element.parent().parent().css('background-color', '#E8EDC2');

					element.parent().parent().find('td .product_price_show').hide();
					element.parent().parent().find('td .product_quantity_show').hide();
					element.parent().parent().find('td .product_price_edit').parent().attr('align', 'left');
					element.parent().parent().find('td .product_price_edit').show();
					element.parent().parent().find('td .product_quantity_edit').show();
					
					element.parent().parent().find('td.cancelCheck').hide();
					element.parent().parent().find('td.cancelQuantity').hide();
					element.parent().parent().find('td.product_invoice').show();
					
					element.parent().children('.delete_product_line').hide();
					element.parent().children('.edit_product_change_link').hide();
					element.parent().children('input[name=submitProductChange]').show();
					element.parent().children('.cancel_product_change_link').show();

					$('.standard_refund_fields').hide();
					$('.partial_refund_fields').hide();
				}
				else
					alert(data.error);
			}
		});

		return false;
	});
	
	$('.cancel_product_change_link').click(function() {
		current_product = null;
		$('.edit_product_fields').show();
		$(this).parent().parent().css('background-color', '#FFF');
		
		$(this).parent().parent().find('td .product_price_show').show();
		$(this).parent().parent().find('td .product_quantity_show').show();
		$(this).parent().parent().find('td .product_price_edit').parent().attr('align', 'center');
		$(this).parent().parent().find('td .product_price_edit').hide();
		$(this).parent().parent().find('td .product_quantity_edit').hide();
		
		$(this).parent().parent().find('td.product_invoice').hide();
		$(this).parent().parent().find('td.cancelCheck').show();
		$(this).parent().parent().find('td.cancelQuantity').show();

		$(this).parent().children('.delete_product_line').show();
		$(this).parent().children('.edit_product_change_link').show();
		$(this).parent().children('input[name=submitProductChange]').hide();
		$(this).parent().children('.cancel_product_change_link').hide();
		$('.standard_refund_fields').hide();
		return false;
	});
	
	$('input[name=submitProductChange]').click(function() {
		if ($(this).parent().parent().find('td .edit_product_quantity').val() <= 0)
		{
			alert(txt_add_product_no_product_quantity);
			return false;
		}
		
		if ($(this).parent().parent().find('td .edit_product_price').val() <= 0)
		{
			alert(txt_add_product_no_product_price);
			return false;
		}
		
		if (confirm(txt_confirm))
		{
			var element = $(this);
			
			query = 'ajax=1&token='+token+'&action=editProductOnOrder&id_order='+id_order+'&'+
					element.parent().parent().find('input:visible, select:visible, input.edit_product_id_order_detail').serialize();
			
			$.ajax({
				type: 'POST',
				url: admin_order_tab_link,
				cache: false,
				dataType: 'json',
				data : query,
				success : function(data)
				{
					if (data.result)
					{
						refreshProductLineView(element, data.view);
						updateAmounts(data.order);
						$('.standard_refund_fields').hide();
						$('.partial_refund_fields').hide();
					}
					else
						alert(data.error);
				}
			});
		}
		
		return false;
	});
	
	$('.edit_product_price_tax_excl').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;
		
		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_incl:visible').val(ps_round(price_tax_excl * tax_rate, 2));
		
		// Update total product
		editProductRefreshTotal($(this));
	});
	
	$('.edit_product_price_tax_incl').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;
		
		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_excl:visible').val(ps_round(price_tax_incl / tax_rate, 2));
		
		// Update total product
		editProductRefreshTotal($(this));
	});
	
	$('.edit_product_quantity').keyup(function() {
		var quantity = parseInt($(this).val()); 
		if (quantity < 1 || isNaN(quantity))
			quantity = 1;
		
		var stock_available = parseInt($(this).parent().parent().parent().find('td.product_stock').html());
		// total update
		editProductRefreshTotal($(this));
	});
	
	$('.delete_product_line').click(function() {
		if (!confirm(txt_confirm))
			return false;
		
		var tr_product = $(this).parent().parent();
		var id_order_detail = $(this).parent().parent().find('td .edit_product_id_order_detail').val();
		var query = 'ajax=1&action=deleteProductLine&token='+token+'&id_order_detail='+id_order_detail+'&id_order='+id_order;
		
		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : query,
			success : function(data)
			{
				if (data.result)
				{
					tr_product.fadeOut('slow', function() {
						$(this).remove();
					});
					updateAmounts(data.order);
				}
				else
					alert(data.error);
			}
		});
		return false;
	});
});

function addProductRefreshTotal()
{
	var quantity = parseInt($('#add_product_product_quantity').val());
	if (quantity < 1|| isNaN(quantity))
		quantity = 1;
	if (use_taxes)
		var price = parseFloat($('#add_product_product_price_tax_incl').val());
	else
		var price = parseFloat($('#add_product_product_price_tax_excl').val());
	
	if (price < 0 || isNaN(price))
		price = 0;
	var total = makeTotalProductCaculation(quantity, price);
	$('#add_product_product_total').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
}

function editProductRefreshTotal(element)
{
	element = element.parent().parent().parent();
	
	var quantity = parseInt(element.find('td .edit_product_quantity').val());
	if (quantity < 1 || isNaN(quantity))
		quantity = 1;
	if (use_taxes)
		var price = parseFloat(element.find('td .edit_product_price_tax_incl').val());
	else
		var price = parseFloat(element.find('td .edit_product_price_tax_excl').val())
	
	if (price < 0 || isNaN(price))
		price = 0;
	
	var total = makeTotalProductCaculation(quantity, price);
	element.find('td.total_product').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
}

function makeTotalProductCaculation(quantity, price)
{
	return Math.round(quantity * price * 100) / 100;
}

function addViewOrderDetailRow(view)
{
	html = $(view);
	html.find('td').hide();
	$('tr#new_invoice').hide();
	$('tr#new_product').hide();
	
	// Initialize fields
	closeAddProduct();
	
	$('tr#new_product').before(html);
	html.find('td').each(function() {
		if (!$(this).is('.product_invoice'))
			$(this).fadeIn('slow');
	});
}

function refreshProductLineView(element, view)
{
	var new_product_line = $(view);
	new_product_line.find('td').hide();
	
	var current_product_line = element.parent().parent();
	current_product_line.before(new_product_line);
	current_product_line.remove();
	
	new_product_line.find('td').each(function() {
		if (!$(this).is('.product_invoice'))
			$(this).fadeIn('slow');
	});
}

function updateAmounts(order)
{
	$('#total_products td:last').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_products_wt), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_discounts td:last').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_discounts_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_discounts_tax_incl > 0)
		$('#total_discounts').slideDown('slow');
	$('#total_wrapping td:last').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_wrapping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_wrapping_tax_incl > 0)
		$('#total_wrapping').slideDown('slow');
	$('#total_shipping td:last').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_shipping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_order td:last').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_paid_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('.total_paid').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_paid_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('.alert').slideDown('slow');
	$('#product_number').fadeOut('slow', function() {
		var old_quantity = parseInt($(this).html());
		$(this).html(old_quantity + 1);
		$(this).fadeIn('slow');
	});
}

function closeAddProduct()
{
	$('tr#new_invoice').hide();
	$('tr#new_product').hide();
	
	// Initialize fields
	$('tr#new_product select, tr#new_product input').each(function() {
		if (!$(this).is('.button'))
			$(this).val('')
	});
	$('tr#new_invoice select, tr#new_invoice input').val('');
	$('#add_product_product_quantity').val('1');
	$('#add_product_product_attribute_id option').remove();
	$('#add_product_product_attribute_area').hide();
	$('#add_product_product_stock').html('0');
	current_product = null;
}







/* Refund system script */
var flagRefund = '';

$(document).ready(function() {
	$('.standard_refund').click(function() {

		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();

		$.scrollTo('#refundForm', 1200, {offset: -100});

		if (flagRefund == 'standard')
		{
			flagRefund = '';
			$('.partial_refund_fields').hide();
			$('.standard_refund_fields').hide();
		}
		else
		{
			flagRefund = 'standard';
			$('.partial_refund_fields').hide();
			$('.standard_refund_fields').fadeIn();
		}
		
		return false;
	});

	$('.partial_refund').click(function() {

		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();

		$.scrollTo('#refundForm', 1200, {offset: -100});

		if (flagRefund == 'partial')
		{
			flagRefund = '';
			$('.partial_refund_fields').hide();
			$('.standard_refund_fields').hide();
		}
		else
		{
			flagRefund = 'partial';
			$('.standard_refund_fields').hide();
			$('.partial_refund_fields').fadeIn();
		}
	
		return false;
	});
});






