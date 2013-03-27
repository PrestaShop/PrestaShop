/*
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
*/

var current_product = null;
var ajaxQueries = new Array();

$(document).ready(function() {
	// Init all events
	init();

	$('img.js-disabled-action').css({"opacity":0.5});
});

function stopAjaxQuery() {
	if (typeof(ajaxQueries) == 'undefined')
		ajaxQueries = new Array();
	for(i = 0; i < ajaxQueries.length; i++)
		ajaxQueries[i].abort();
	ajaxQueries = new Array();
}

function updateInvoice(invoices)
{
	// Update select on product edition line
	$('.edit_product_invoice').each(function() {
		var selected = $(this).children('option:selected').val();

		$(this).children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);
			if (invoices[i].id == selected)
				option.attr('selected', true);

			$(this).append(option);
		}
	});

	// Update select on product addition line
	$('#add_product_product_invoice').each(function() {
		var parent = $(this).children('optgroup.existing');
		parent.children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);

			parent.append(option);
		}
		parent.children('option:first').attr('selected', true);
	});

	// Update select on product addition line
	$('#payment_invoice').each(function() {
		$(this).children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);

			$(this).append(option);
		}
	});
}

function updateDocuments(documents_html)
{
	$('#documents_table').attr('id', 'documents_table_old');
	$('#documents_table_old').after(documents_html);
	$('#documents_table_old').remove();
}

function updateShipping(shipping_html)
{
	$('#shipping_table').attr('id', 'shipping_table_old');
	$('#shipping_table_old').after(shipping_html);
	$('#shipping_table_old').remove();
}

function updateDiscountForm(discount_form_html)
{
	$('#voucher_form').html(discount_form_html);
}

function populateWarehouseList(warehouse_list)
{
	$('#add_product_product_warehouse_area').hide();
	if (warehouse_list.length > 1)
	{
		$('#add_product_product_warehouse_area').show();
	}
	var order_warehouse_list = $('#warehouse_list').val().split(',');
	$('#add_product_warehouse').html('');
	var warehouse_selected = false;
	$.each(warehouse_list, function() {
		if (warehouse_selected == false && $.inArray(this.id_warehouse, order_warehouse_list))
			warehouse_selected = this.id_warehouse;

		$('#add_product_warehouse').append($('<option value="' + this.id_warehouse + '">' + this.name + '</option>'));
	});
	if (warehouse_selected)
		$('#add_product_warehouse').val(warehouse_selected);
}

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
	var element_list = [];
	
	// Customized product
	if(element.hasClass('customized'))
	{
		var element_list = $('.customized-' + element.find('.edit_product_id_order_detail').val());
		element = $(element_list[0]);
	}

	var quantity = parseInt(element.find('td .edit_product_quantity').val());
	if (quantity < 1 || isNaN(quantity))
		quantity = 1;
	if (use_taxes)
		var price = parseFloat(element.find('td .edit_product_price_tax_incl').val());
	else
		var price = parseFloat(element.find('td .edit_product_price_tax_excl').val())

	if (price < 0 || isNaN(price))
		price = 0;
	
	// Customized product
	if (element_list.length)
	{
		var qty = 0;
		$.each(element_list, function(i, elm) {
			if($(elm).find('.edit_product_quantity').length)
			{
				qty += parseInt($(elm).find('.edit_product_quantity').val());
				subtotal = makeTotalProductCaculation($(elm).find('.edit_product_quantity').val(), price);
				$(elm).find('.total_product').html(formatCurrency(subtotal, currency_format, currency_sign, currency_blank));
			}
		});
		
		var total = makeTotalProductCaculation(qty, price);
		element.find('td.total_product').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
		element.find('td.productQuantity').html(qty);
	}
	else
	{
		var total = makeTotalProductCaculation(quantity, price);
		element.find('td.total_product').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
	}

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
	
	var element_list = [];
	if (element.parent().parent().find('.edit_product_id_order_detail').length)
		var element_list = $('.customized-' + element.parent().parent().find('.edit_product_id_order_detail').val());
	if (!element_list.length)
		element_list = $(element.parent().parent());

	var current_product_line = element.parent().parent();
	current_product_line.replaceWith(new_product_line);
	element_list.remove();

	new_product_line.find('td').each(function() {
		if (!$(this).is('.product_invoice'))
			$(this).fadeIn('slow');
	});
}

function updateAmounts(order)
{
	$('#total_products td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_products_wt), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_discounts td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_discounts_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_discounts_tax_incl > 0)
		$('#total_discounts').slideDown('slow');
	$('#total_wrapping td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_wrapping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_wrapping_tax_incl > 0)
		$('#total_wrapping').slideDown('slow');
	$('#total_shipping td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_shipping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_order td.amount').fadeOut('slow', function() {
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
	if (stock_management)
		$('#add_product_product_stock').html('0');
	current_product = null;
}

/**
 * This method allow to initialize all events
 */
function init()
{
	$('.add_product').unbind('click');
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
				id_customer: id_customer,
				product_search: function() { return $('#add_product_product_name').val(); }
			}
		}
	)
	.result(function(event, data, formatted) {
		if (!data)
		{
			$('tr#new_product input, tr#new_product select').each(function() {
				if ($(this).attr('id') != 'add_product_product_name')
					$('tr#new_product input, tr#new_product select').attr('disabled', true);
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
			if (stock_management)
				$('#add_product_product_stock').html(data.stock[0]);

			if (current_product.combinations.length !== 0)
			{
				// Reset combinations list
				$('select#add_product_product_attribute_id').html('');
				var defaultAttribute = 0;
				$.each(current_product.combinations, function() {
					$('select#add_product_product_attribute_id').append('<option value="'+this.id_product_attribute+'"'+(this.default_on == 1 ? ' selected="selected"' : '')+'>'+this.attributes+'</option>');
					if (this.default_on == 1)
					{
						if (stock_management)
							$('#add_product_product_stock').html(this.qty_in_stock);
						defaultAttribute = this.id_product_attribute;
					}
				});
				// Show select list
				$('#add_product_product_attribute_area').show();

				populateWarehouseList(current_product.warehouse_list[defaultAttribute]);
			}
			else
			{
				// Reset combinations list
				$('select#add_product_product_attribute_id').html('');
				// Hide select list
				$('#add_product_product_attribute_area').hide();

				populateWarehouseList(current_product.warehouse_list[0]);
			}
		}
	});

	$('select#add_product_product_attribute_id').unbind('change');
	$('select#add_product_product_attribute_id').change(function() {
		$('#add_product_product_price_tax_incl').val(current_product.combinations[$(this).val()].price_tax_incl);
		$('#add_product_product_price_tax_excl').val(current_product.combinations[$(this).val()].price_tax_excl);

		populateWarehouseList(current_product.warehouse_list[$(this).val()]);

		addProductRefreshTotal();
		if (stock_management)
			$('#add_product_product_stock').html(current_product.combinations[$(this).val()].qty_in_stock);
	});

	$('input#add_product_product_quantity').unbind('keyup');
	$('input#add_product_product_quantity').keyup(function() {
		if (stock_management)
		{
			var quantity = parseInt($(this).val());
			if (quantity < 1 || isNaN(quantity))
				quantity = 1;
			var stock_available = parseInt($('#add_product_product_stock').html());
			// stock status update
			if (quantity > stock_available)
				$('#add_product_product_stock').css('font-weight', 'bold').css('color', 'red').css('font-size', '1.2em');
			else
				$('#add_product_product_stock').css('font-weight', 'normal').css('color', 'black').css('font-size', '1em');
		}
		// total update
		addProductRefreshTotal();
	});

	$('#submitAddProduct').unbind('click');
	$('#submitAddProduct').click(function(e) {
		e.preventDefault();
		stopAjaxQuery();
		var go = true;

		if ($('input#add_product_product_id').val() == 0)
		{
			jAlert(txt_add_product_no_product);
			go = false;
		}

		if ($('input#add_product_product_quantity').val() == 0)
		{
			jAlert(txt_add_product_no_product_quantity);
			go = false;
		}

		if ($('input#add_product_product_price_excl').val() == 0)
		{
			jAlert(txt_add_product_no_product_price);
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

				query += $('#add_product_warehouse').serialize()+'&';
				query += $('tr#new_product select, tr#new_product input').serialize();
				if ($('select#add_product_product_invoice').val() == 0)
					query += '&'+$('tr#new_invoice select, tr#new_invoice input').serialize();

				var ajax_query = $.ajax({
					type: 'POST',
					url: admin_order_tab_link,
					cache: false,
					dataType: 'json',
					data : query,
					success : function(data)
					{
						if (data.result)
						{
							go = false;
							addViewOrderDetailRow(data.view);
							updateAmounts(data.order);
							updateInvoice(data.invoices);
							updateDocuments(data.documents_html);
							updateShipping(data.shipping_html);
							updateDiscountForm(data.discount_form_html);

							// Initialize all events
							init();

							$('.standard_refund_fields').hide();
							$('.partial_refund_fields').hide();
						}
						else
							jAlert(data.error);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown)
					{
						jAlert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					}
				});
				ajaxQueries.push(ajax_query);
			}
		}
	});

	$('.edit_shipping_number_link').unbind('click');
	$('.edit_shipping_number_link').click(function() {
		$(this).parent().find('.shipping_number_show').hide();
		$(this).parent().find('.shipping_number_edit').show();

		$(this).parent().find('.edit_shipping_number_link').hide();
		$(this).parent().find('.cancel_shipping_number_link').show();

		return false;
	});

	$('.cancel_shipping_number_link').unbind('click');
	$('.cancel_shipping_number_link').click(function() {
		$(this).parent().find('.shipping_number_show').show();
		$(this).parent().find('.shipping_number_edit').hide();

		$(this).parent().find('.edit_shipping_number_link').show();
		$(this).parent().find('.cancel_shipping_number_link').hide();

		return false;
	});

	$('#add_product_product_invoice').unbind('change');
	$('#add_product_product_invoice').change(function() {
		if ($(this).val() == '0')
			$('#new_invoice').slideDown('slow');
		else
			$('#new_invoice').slideUp('slow');
	});

	$('#add_product_product_price_tax_excl').unbind('keyup');
	$('#add_product_product_price_tax_excl').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_incl').val(ps_round(price_tax_excl * tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$('#add_product_product_price_tax_incl').unbind('keyup');
	$('#add_product_product_price_tax_incl').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_excl').val(ps_round(price_tax_incl / tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$('.edit_product_change_link').unbind('click');
	$('.edit_product_change_link').click(function() {
		$('.add_product_fields').hide();
		$('.standard_refund_fields').hide();
		$('.edit_product_fields').show();
		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();
		var element = $(this);
		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : {
				ajax: 1,
				token: token,
				action: 'loadProductInformation',
				id_order_detail: $(this).parent().parent().find('input.edit_product_id_order_detail').val(),
				id_address: id_address,
				id_order: id_order
			},
			success : function(data)
			{
				if (data.result)
				{
					current_product = data;
					
					var element_list = $('.customized-' + element.parent().parent().find('.edit_product_id_order_detail').val());
					if (!element_list.length)
						element_list = element.parent().parent();

					element_list.css('background-color', '#E8EDC2');

					element_list.find('td .product_price_show').hide();
					element_list.find('td .product_quantity_show').hide();
					element_list.find('td .product_price_edit').parent().attr('align', 'left');
					element_list.find('td .product_price_edit').show();
					element_list.find('td .product_quantity_edit').show();

					element_list.find('td.cancelCheck').hide();
					element_list.find('td.cancelQuantity').hide();
					element_list.find('td.product_invoice').show();

					$('td.product_action').attr('colspan', 3);
					$('th.edit_product_fields').show();
					$('th.edit_product_fields').attr('colspan',  2);
					element_list.find('td.product_action').attr('colspan', 1);

					element.parent().children('.delete_product_line').hide();
					element.parent().children('.edit_product_change_link').hide();
					element.parent().children('input[name=submitProductChange]').show();
					element.parent().children('.cancel_product_change_link').show();

					$('.standard_refund_fields').hide();
					$('.partial_refund_fields').hide();
				}
				else
					jAlert(data.error);
			}
		});

		return false;
	});

	$('.cancel_product_change_link').unbind('click');
	$('.cancel_product_change_link').click(function() {
		current_product = null;
		$('.edit_product_fields').hide();
		var element_list = $('.customized-' + $(this).parent().parent().find('.edit_product_id_order_detail').val());
		if (!element_list.length)
			element_list = $($(this).parent().parent());

		element_list.css('background-color', '#FFF');

		element_list.find('td .product_price_show').show();
		element_list.find('td .product_quantity_show').show();
		element_list.find('td .product_price_edit').parent().attr('align', 'center');
		element_list.find('td .product_price_edit').hide();
		element_list.find('td .product_quantity_edit').hide();

		element_list.find('td.product_invoice').hide();
		element_list.find('td.cancelCheck').show();
		element_list.find('td.cancelQuantity').show();

		element_list.find('.delete_product_line').show();
		element_list.find('.edit_product_change_link').show();
		element_list.find('input[name=submitProductChange]').hide();
		element_list.find('.cancel_product_change_link').hide();
		$('.standard_refund_fields').hide();
		return false;
	});

	$('input[name=submitProductChange]').unbind('click');
	$('input[name=submitProductChange]').click(function() {
		if ($(this).parent().parent().find('td .edit_product_quantity').val() <= 0)
		{
			jAlert(txt_add_product_no_product_quantity);
			return false;
		}

		if ($(this).parent().parent().find('td .edit_product_price').val() <= 0)
		{
			jAlert(txt_add_product_no_product_price);
			return false;
		}

		if (confirm(txt_confirm))
		{
			var element = $(this);
			
			var element_list = $('.customized-' + $(this).parent().parent().find('.edit_product_id_order_detail').val());

			query = 'ajax=1&token='+token+'&action=editProductOnOrder&id_order='+id_order+'&';

			if (element_list.length)
				query += element_list.parent().parent().find('input:visible, select:visible, .edit_product_id_order_detail').serialize();
			else
				query += element.parent().parent().find('input:visible, select:visible, .edit_product_id_order_detail').serialize();

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
						updateInvoice(data.invoices);
						updateDocuments(data.documents_html);
						updateDiscountForm(data.discount_form_html);

						// Initialize all events
						init();

						$('.standard_refund_fields').hide();
						$('.partial_refund_fields').hide();
					}
					else
						jAlert(data.error);
				}
			});
		}

		return false;
	});

	$('.edit_product_price_tax_excl').unbind('keyup');
	$('.edit_product_price_tax_excl').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_incl:visible').val(ps_round(price_tax_excl * tax_rate, 2));

		// Update total product
		editProductRefreshTotal($(this));
	});

	$('.edit_product_price_tax_incl').unbind('keyup');
	$('.edit_product_price_tax_incl').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_excl:visible').val(ps_round(price_tax_incl / tax_rate, 2));

		// Update total product
		editProductRefreshTotal($(this));
	});

	$('.edit_product_quantity').unbind('keyup');
	$('.edit_product_quantity').keyup(function() {
		var quantity = parseInt($(this).val());
		if (quantity < 1 || isNaN(quantity))
			quantity = 1;

		var stock_available = parseInt($(this).parent().parent().parent().find('td.product_stock').html());
		// total update
		editProductRefreshTotal($(this));
	});

	$('.delete_product_line').unbind('click');
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
					updateInvoice(data.invoices);
					updateDocuments(data.documents_html);
					updateDiscountForm(data.discount_form_html);
				}
				else
					jAlert(data.error);
			}
		});
		return false;
	});

	$('.js-set-payment').unbind('click');
	$('.js-set-payment').click(function() {
		var amount = $(this).attr('data-amount');
		$('input[name=payment_amount]').val(amount);
		var id_invoice = $(this).attr('data-id-invoice');
		$('select[name=payment_invoice] option[value='+id_invoice+']').attr('selected', true);

		$.scrollTo('#formAddPayment', 1000, {offset: -100});

		return false;
	});

	$('#add_voucher').unbind('click');
	$('#add_voucher').click(function() {
		$(this).parent().parent().hide();
		$('#voucher_form').parent().show();

		return false;
	});

	$('#cancel_add_voucher').unbind('click');
	$('#cancel_add_voucher').click(function() {
		$('#voucher_form').parent().hide();
		$('#add_voucher').parent().parent().show();

		return false;
	});

	$('#discount_type').unbind('click');
	$('#discount_type').change(function() {
		// Percent type
		if ($(this).val() == 1)
		{
			$('#discount_value_field').show();
			$('#discount_currency_sign').hide();
			$('#discount_value_help').hide();
			$('#discount_percent_symbol').show();
		}
		// Amount type
		else if ($(this).val() == 2)
		{
			$('#discount_value_field').show();
			$('#discount_percent_symbol').hide();
			$('#discount_value_help').show();
			$('#discount_currency_sign').show();
		}
		// Free shipping
		else if ($(this).val() == 3)
		{
			$('#discount_value_field').hide();
		}
	});

	$('#discount_all_invoices').unbind('change');
	$('#discount_all_invoices').change(function() {
		if ($(this).is(':checked'))
			$('select[name=discount_invoice]').attr('disabled', true);
		else
			$('select[name=discount_invoice]').attr('disabled', false);
	});

	$('.open_payment_information').unbind('click');
	$('.open_payment_information').click(function() {
		if ($(this).parent().parent().next('tr').is(':visible'))
			$(this).parent().parent().next('tr').hide();
		else
			$(this).parent().parent().next('tr').show();

		return false;
	});
}






/* Refund system script */
var flagRefund = '';

$(document).ready(function() {
	$('#desc-order-standard_refund').click(function() {

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

	$('#desc-order-partial_refund').click(function() {

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

function checkPartialRefundProductQuantity(it)
{
	if (parseInt($(it).val()) > parseInt($(it).parent().parent().find('.partialRefundProductQuantity').val()))
		$(it).val($(it).parent().parent().find('.partialRefundProductQuantity').val());
}
function checkPartialRefundProductAmount(it)
{
	if (parseInt($(it).val()) > parseInt($(it).parent().parent().find('.partialRefundProductAmount').val()))
		$(it).val($(it).parent().parent().find('.partialRefundProductAmount').val());
}