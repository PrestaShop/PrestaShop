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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	var id_cart = {$cart->id|intval};
	var id_customer = '';
	var changed_shipping_price = false;
	var shipping_price_selected_carrier = '';
	var current_index = '{$current}&token={$token}';
	var admin_cart_link = '{$link->getAdminLink('AdminCarts')}';
	var cart_quantity = new Array();
	var currencies = new Array();
	var id_currency = '';
	var id_lang = '';
	var txt_show_carts = '{l s='Show carts and orders for this customer'}';
	var txt_hide_carts = '{l s='Hide carts and orders for this customer'}';
	var defaults_order_state = new Array();
	var customization_errors = false;
	var pic_dir = '{$pic_dir}';
	{foreach from=$defaults_order_state key='module' item='id_order_state'}
		defaults_order_state['{$module}'] = '{$id_order_state}';
	{/foreach}
	$(document).ready(function() {
		$('#customer').typeWatch({
			captureLength: 1,
			highlight: true,
			wait: 100,
			callback: function(){ searchCustomers(); }
			});
		$('#product').typeWatch({
			captureLength: 1,
			highlight: true,
			wait: 750,
			callback: function(){ searchProducts(); }
		});
		$('#payment_module_name').change(function() {
			var id_order_state = defaults_order_state[this.value];
			if (typeof(id_order_state) == 'undefined')
				id_order_state = defaults_order_state['other'];
			$('#id_order_state').val(id_order_state);
		});
		$("#id_address_delivery").change(function() {
			updateAddresses();
		});
		$("#id_address_invoice").change(function() {
			updateAddresses();
		});
		$('#id_currency').change(function() {
			updateCurrency();
		});
		$('#id_lang').change(function(){
			updateLang();
		});
		$('#delivery_option,#carrier_recycled_package,#order_gift,#gift_message').change(function() {
			updateDeliveryOption();
		});
		$('#shipping_price').change(function() {
			if ($(this).val() != shipping_price_selected_carrier)
				changed_shipping_price = true;
		});
		$('#show_old_carts').click(function() {
			if ($('#old_carts_orders:visible').length == 0)
			{
				$(this).html(txt_hide_carts);
				$('#old_carts_orders').slideDown('slow');
			}
			else
			{
				$(this).html(txt_show_carts);
				$('#old_carts_orders').slideUp('slow');
			}
			return false;
		});
		$('#send_email_to_customer').click(function(){
			sendMailToCustomer();
			return false;
		});
		$('#show_old_carts').click();
		$('#payment_module_name').change();
		$.ajaxSetup({ type:"post" });
		$("#voucher").autocomplete('{$link->getAdminLink('AdminCartRules')}', {
					minChars: 3,
					max: 15,
					width: 250,
					selectFirst: false,
					scroll: false,
					dataType: "json",
					formatItem: function(data, i, max, value, term) {
						return value;
					},
					parse: function(data) {
						if (!data.found)
							$('#vouchers_err').html('{l s='No voucher found'}').show();
						else
							$('#vouchers_err').hide();
						var mytab = new Array();
						for (var i = 0; i < data.vouchers.length; i++)
							mytab[mytab.length] = { data: data.vouchers[i], value: data.vouchers[i].name+' - '+data.vouchers[i].description };
						return mytab;
					},
					extraParams: {
						ajax: "1",
						token: "{getAdminToken tab='AdminCartRules'}",
						tab: "AdminCartRules",
						action: "searchCartRuleVouchers"
					}
				}
			)
			.result(function(event, data, formatted) {
				$('#voucher').val(data.name);
				add_cart_rule(data.id_cart_rule);
			});
		{if $cart->id}
			setupCustomer({$cart->id_customer|intval});
			useCart('{$cart->id|intval}');
		{/if}

		$('.delete_product').live('click', function(e) {
			e.preventDefault();
			var to_delete = $(this).attr('rel').split('_');
			deleteProduct(to_delete[1], to_delete[2], to_delete[3]);
		});
		$('.delete_discount').live('click', function(e) {
			e.preventDefault();
			deleteVoucher($(this).attr('rel'));
		});
		$('.use_cart').live('click', function(e) {
			e.preventDefault();
			useCart($(this).attr('rel'));
			return false;
		});
		$('#free_shipping').click(function() {
			var free_shipping = 0;
			if (this.checked)
				free_shipping = 1;
			$.ajax({
				type:"POST",
				url: "{$link->getAdminLink('AdminCarts')}",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "{getAdminToken tab='AdminCarts'}",
					tab: "AdminCarts",
					action: "updateFreeShipping",
					id_cart: id_cart,
					id_customer: id_customer,
					'free_shipping': free_shipping
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});

		$('.duplicate_order').live('click', function(e) {
			e.preventDefault();
			duplicateOrder($(this).attr('rel'));
		});
		$('.cart_quantity').live('change', function(e) {
			e.preventDefault();
			if ($(this).val() != cart_quantity[$(this).attr('rel')])
			{
				var product = $(this).attr('rel').split('_');
				updateQty(product[0], product[1], product[2], $(this).val() - cart_quantity[$(this).attr('rel')]);
			}
		});
		$('.increaseqty_product, .decreaseqty_product').live('click', function(e) {
			e.preventDefault();
			var product = $(this).attr('rel').split('_');
			var sign = '';
			if ($(this).hasClass('decreaseqty_product'))
				sign = '-';
			updateQty(product[0], product[1],product[2], sign+1);
		});
		$('#id_product').live('keydown', function(e) {
			$(this).click();
			return true;
		});
		$('#id_product, .id_product_attribute').live('change', function(e) {
			e.preventDefault();
			displayQtyInStock(this.id);
		});
		$('#id_product, .id_product_attribute').live('keydown', function(e) {
			$(this).change();
			return true;
		});
		$('.product_unit_price').live('change', function(e) {
			e.preventDefault();
			var product = $(this).attr('rel').split('_');
			updateProductPrice(product[0], product[1], $(this).val());
		});
		$('#order_message').live('change', function(e) {
			e.preventDefault();
			$.ajax({
				type:"POST",
				url: "{$link->getAdminLink('AdminCarts')}",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "{getAdminToken tab='AdminCarts'}",
					tab: "AdminCarts",
					action: "updateOrderMessage",
					id_cart: id_cart,
					id_customer: id_customer,
					message: $(this).val()
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});
		resetBind();
	});

	function resetBind()
	{
		$('.fancybox').fancybox({
			'type': 'iframe',
			'width': '60%',
			'height': '100%'
		});
		/*$("#new_address").fancybox({
			onClosed: useCart(id_cart)
		});*/
	}

	function add_cart_rule(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "addVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
				$('#voucher').val('');
				var errors = '';
				if (res.errors.length > 0)
				{
					$.each(res.errors, function() {
						errors += this+'<br/>';
					});
					$('#vouchers_err').html(errors).show();
				}
				else
					$('#vouchers_err').hide();
			}
		});
	}

	function updateProductPrice(id_product, id_product_attribute, new_price)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateProductPrice",
				id_cart: id_cart,
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customer: id_customer,
				price: new_price
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function displayQtyInStock(id)
	{
		var id_product = $('#id_product').val();
		if ($('#ipa_' + id_product + ' option').length)
			var id_product_attribute = $('#ipa_' + id_product).val();
		else
			var id_product_attribute = 0;

		$('#qty_in_stock').html(stock[id_product][id_product_attribute]);
	}

	function duplicateOrder(id_order)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "duplicateOrder",
				id_order: id_order,
				id_customer: id_customer
				},
			success : function(res)
			{
				id_cart = res.cart.id;
				$('#id_cart').val(id_cart);
				displaySummary(res);
			}
		});
	}

	function useCart(id_new_cart)
	{
		id_cart = id_new_cart;
		$('#id_cart').val(id_cart);
		$('#id_cart').val(id_cart);
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "getSummary",
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function getSummary()
	{
		useCart(id_cart);
	}

	function deleteVoucher(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "deleteVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function deleteProduct(id_product, id_product_attribute, id_customization)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "deleteProduct",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function searchCustomers()
	{
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminOrders')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{$token}",
				tab: "AdminOrders",
				action: "searchCustomers",
				customer_search: $('#customer').val()},
			success : function(res)
			{
				if(res.found)
				{
					var html = '<ul>';
					$.each(res.customers, function() {
						html += '<li class="customerCard"><div class="customerName"><a class="fancybox" href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a><span class="customerBirthday"> '+((this.birthday != '0000-00-00') ? this.birthday : '')+'</span></div>';
						html += '<div class="customerEmail"><a href="mailto:'+this.email+'">'+this.email+'</div>';
						html += '<a onclick="setupCustomer('+ this.id_customer+');return false;" href="#" class="id_customer button">{l s='Choose'}</a></li>';
					});
					html += '</ul>';
				}
				else
					html = '<div class="warn">{l s='No customers found'}</div>';
				$('#customers').html(html);
				resetBind();
			}
		});
	}

	function setupCustomer(idCustomer)
	{
		$('#products_part').show();
		$('#vouchers_part').show();
		$('#address_part').show();
		$('#carriers_part').show();
		$('#summary_part').show();
		var address_link = $('#new_address').attr('href');
		id_customer = idCustomer;
		$('#new_address').attr('href', address_link.replace(/id_customer=[0-9]+/, 'id_customer='+id_customer));
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminCarts')}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "searchCarts",
				id_customer: id_customer,
				id_cart: id_cart
			},
			success : function(res)
			{
				if(res.found)
				{
					var html_carts = '';
					var html_orders = '';
					$.each(res.carts, function() {
						html_carts += '<tr><td>'+this.id_cart+'</td><td>'+this.date_add+'</td><td>'+this.total_price+'</td>';
						html_carts += '<td><a title="{l s='View this cart'}" class="fancybox" href="index.php?tab=AdminCarts&id_cart='+this.id_cart+'&viewcart&token={getAdminToken tab='AdminCarts'}&liteDisplaying=1#"><img src="../img/admin/details.gif" /></a>';
						html_carts += '<a href="#" title="{l s='Use this cart'}" class="use_cart" rel="'+this.id_cart+'"><img src="../img/admin/duplicate.png" /></a></td></tr>';
					});
					$.each(res.orders, function() {
						html_orders += '<tr><td>'+this.id_order+'</td><td>'+this.date_add+'</td><td>'+(this.nb_products ? this.nb_products : '0')+'</td><td>'+this.total_paid_real+'</span></td><td>'+this.payment+'</td><td>'+this.order_state+'</td>';
						html_orders += '<td><a title="{l s='View this order'}" class="fancybox" href="{$link->getAdminLink('AdminOrders')}&id_order='+this.id_order+'&vieworder&liteDisplaying=1#"><img src="../img/admin/details.gif" /></a>';
						html_orders += '<a href="#" "title="{l s='Duplicate this order'}" class="duplicate_order" rel="'+this.id_order+'"><img src="../img/admin/duplicate.png" /></a></td></tr>';
					});
					$('#nonOrderedCarts table tbody').html(html_carts);
					$('#lastOrders table tbody').html(html_orders);
				}
				if (res.id_cart)
				{
					id_cart = res.id_cart;
					$('#id_cart').val(id_cart);
				}
				displaySummary(res);
				resetBind();
				updateCurrencySign();
			}
		});
	}

	function updateDeliveryOptionList(delivery_option_list)
	{
		var html = '';
		if (delivery_option_list.length > 0)
		{
			$.each(delivery_option_list, function() {
				html += '<option value="'+this.key+'" '+(($('#delivery_option').val() == this.key) ? 'selected="selected"' : '')+'>'+this.name+'</option>';
			});
			$('#carrier_form').show();
			$('#delivery_option').html(html);
			$('#carriers_err').hide();
		}
		else
		{
			$('#carrier_form').hide();
			$('#carriers_err').show().html('{l s='No carrier can be applied to this order'}');
		}
	}

	function searchProducts()
	{
		$('#products_part').show();
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminOrders')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{$token}",
				tab: "AdminOrders",
				action: "searchProducts",
				id_cart: id_cart,
				id_customer: id_customer,
				id_currency: id_currency,
				product_search: $('#product').val()},
			success : function(res)
			{
				var products_found = '';
				var attributes_html = '';
				var customization_html = '';
				stock = {};

				if(res.found)
				{
					if (!customization_errors)
						$('#products_err').hide();
					else
						customization_errors = false;
					$('#products_found').show();
					products_found += '<label>{l s='Product:'}</label><select id="id_product" onclick="display_product_attributes();display_product_customizations();">';
					attributes_html += '<label>{l s='Combination:'}</label>';
					$.each(res.products, function() {
						products_found += '<option '+(this.combinations.length > 0 ? 'rel="'+this.qty_in_stock+'"' : '')+' value="'+this.id_product+'">'+this.name+(this.combinations.length == 0 ? ' - '+this.formatted_price : '')+'</option>';
						attributes_html += '<select class="id_product_attribute" id="ipa_'+this.id_product+'" style="display:none;">';
						var id_product = this.id_product;
						stock[id_product] = new Array();
						if (this.customizable == '1')
						{
							customization_html += '<fieldset class="width3"><legend>{l s='Customization'}</legend><form id="customization_'+id_product+'" class="id_customization" method="post" enctype="multipart/form-data" action="'+admin_cart_link+'" style="display:none;">';
							customization_html += '<input type="hidden" name="id_product" value="'+id_product+'" />';
							customization_html += '<input type="hidden" name="id_cart" value="'+id_cart+'" />';
							customization_html += '<input type="hidden" name="action" value="updateCustomizationFields" />';
							customization_html += '<input type="hidden" name="id_customer" value="'+id_customer+'" />';
							customization_html += '<input type="hidden" name="ajax" value="1" />';
							$.each(this.customization_fields, function() {
								customization_html += '<p><label for="customization_'+id_product+'_'+this.id_customization_field+'">';
								if (this.required == 1)
									customization_html += '<sup>*</sup>';
								customization_html += this.name+'{l s=':'}</label>';
								if (this.type == 0)
									customization_html += '<input class="customization_field" type="file" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								else if (this.type == 1)
									customization_html += '<input class="customization_field" type="text" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								customization_html += '</p>';
							});
							customization_html += '</fieldset></form>';
						}

						$.each(this.combinations, function() {
							attributes_html += '<option rel="'+this.qty_in_stock+'" '+(this.default_on == 1 ? 'selected="selected"' : '')+' value="'+this.id_product_attribute+'">'+this.attributes+' - '+this.formatted_price+'</option>';
							stock[id_product][this.id_product_attribute] = this.qty_in_stock;
						});

						stock[this.id_product][0] = this.stock[0];
						attributes_html += '</select>';
					});
					products_found += '</select>';
					$('#products_found #product_list').html(products_found);
					$('#products_found #attributes_list').html(attributes_html);
					$('#products_found #customization_list').contents().find('body').html(customization_html);
					display_product_attributes();
					display_product_customizations();
					$('#id_product').change();
				}
				else
				{
					$('#products_found').hide();
					$('#products_err').html('{l s='No products found'}');
					$('#products_err').show();
				}
				resetBind();
			}
		});
	}

	function display_product_customizations()
	{
		if ($('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).children().length === 0)
			$('#customization_list').hide();
		else
		{
			$('#customization_list').show();
			$('#products_found #customization_list').contents().find('.id_customization').hide();
			$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).show();
			$('#products_found #customization_list').css('height',$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).height()+95+'px');
		}
	}

	function display_product_attributes()
	{
		if ($('#ipa_'+$('#id_product option:selected').val()+' option').length === 0)
			$('#attributes_list').hide();
		else
		{
			$('#attributes_list').show();
			$('.id_product_attribute').hide();
			$('#ipa_'+$('#id_product option:selected').val()).show();
		}
	}

	function updateCartProducts(products, gifts, id_address_delivery)
	{
		var cart_content = '';
		$.each(products, function() {
			var id_product = Number(this.id_product);
			var id_product_attribute = Number(this.id_product_attribute);
			cart_quantity[Number(this.id_product)+'_'+Number(this.id_product_attribute)+'_'+Number(this.id_customization)] = this.cart_quantity;
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td><td><input type="text" size="7" rel="'+this.id_product+'_'+this.id_product_attribute+'" class="product_unit_price" value="'+this.price+'" />&nbsp;<span class="currency_sign"></span></td><td>';
			cart_content += (!this.id_customization ? '<div style="float:left;"><a href="#" class="increaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><img src="../img/admin/up.gif" /></a><br /><a href="#" class="decreaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'"><img src="../img/admin/down.gif" /></a></div>' : '');
			cart_content += (!this.id_customization ? '<div style="float:left;"><input type="text" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" class="cart_quantity" size="2" value="'+this.cart_quantity+'" />' : '');
			cart_content += (!this.id_customization ? '<a href="#" class="delete_product" rel="delete_'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><img src="../img/admin/delete.gif" /></a></div>' : '');
			cart_content += '</td><td>'+this.total+'&nbsp;<span class="currency_sign"></span></td></tr>';
			if (this.id_customization && this.id_customization != 0)
			{
				$.each(this.customized_datas[this.id_product][this.id_product_attribute][id_address_delivery], function() {
					var customized_desc = '';
					if(this.datas[1].length)
					{
						$.each(this.datas[1],function() {
							customized_desc += this.name+':'+this.value+'<br />';
							id_customization = this.id_customization;
						});
					}
					if(this.datas[0] && this.datas[0].length)
					{
						$.each(this.datas[0],function() {
							customized_desc += this.name+':<img src="'+pic_dir+this.value+'_small" /><br />';
							id_customization = this.id_customization;
						});
					}
			cart_content += '<tr><td></td><td>'+customized_desc+'</td><td></td><td></td><td>';
			cart_content += '<div style="float:left;"><a href="#" class="increaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><img src="../img/admin/up.gif" /></a><br /><a href="#" class="decreaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'"><img src="../img/admin/down.gif" /></a></div>';
			cart_content += '<div style="float:left;"><input type="text" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization +'" class="cart_quantity" size="2" value="'+this.quantity+'" />';
			cart_content += '<a href="#" class="delete_product" rel="delete_'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><img src="../img/admin/delete.gif" /></a>';
			cart_content += '</div></td><td></td></tr>';
				});
			}
		});

		$.each(gifts, function() {
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td>';
			cart_content += '<td>{l s='Gift !'}</td><td>'+this.cart_quantity+'</td><td>{l s='Gift !'}</td></tr>';
		});
		$('#customer_cart tbody').html(cart_content);
	}

	function updateCartVouchers(vouchers)
	{
		var vouchers_html = '';
		if (vouchers.length > 0)
		{
			$.each(vouchers, function() {
				vouchers_html += '<tr><td>'+this.name+'</td><td>'+this.description+'</td><td>'+this.value_real+'</td><td><a href="#" class="delete_discount" rel="'+this.id_discount+'"><img src="../img/admin/delete.gif" /></a></td></tr>';
			});
			$('#voucher_list').show();
		}
		else
			$('#voucher_list').hide();

		$('#voucher_list tbody').html(vouchers_html);
	}

	function updateCartPaymentList(payment_list)
	{
		$('#payment_list').html(payment_list);
	}

	function displaySummary(jsonSummary)
	{
		updateCartProducts(jsonSummary.summary.products, jsonSummary.summary.gift_products, jsonSummary.cart.id_address_delivery);
		updateCartVouchers(jsonSummary.summary.discounts);
		updateAddressesList(jsonSummary.addresses, jsonSummary.cart.id_address_delivery, jsonSummary.cart.id_address_invoice);

		if (!jsonSummary.summary.products.length || !jsonSummary.addresses.length || !jsonSummary.delivery_option_list)
			$('#carriers_part,#summary_part').hide();
		else
			$('#carriers_part,#summary_part').show();

		updateDeliveryOptionList(jsonSummary.delivery_option_list);

		if (jsonSummary.cart.gift == 1)
			$('#order_gift').attr('checked', true);
		else
			$('#carrier_gift').removeAttr('checked');
		if (jsonSummary.cart.recyclable == 1)
			$('#carrier_recycled_package').attr('checked', true);
		else
			$('#carrier_recycled_package').removeAttr('checked');
		if (jsonSummary.free_shipping == 1)
			$('#free_shipping').attr('checked', true);
		else
			$('#free_shipping').removeAttr('checked');

		$('#gift_message').html(jsonSummary.cart.gift_message);
		if(!changed_shipping_price)
			$('#shipping_price').html('<b>'+jsonSummary.summary.total_shipping+'</b>');
		shipping_price_selected_carrier = jsonSummary.summary.total_shipping;

		$('#total_vouchers').html(jsonSummary.summary.total_discounts_tax_exc);
		$('#total_shipping').html(jsonSummary.summary.total_shipping_tax_exc);
		$('#total_taxes').html(jsonSummary.summary.total_tax);
		$('#total_without_taxes').html(jsonSummary.summary.total_price_without_tax);
		$('#total_with_taxes').html(jsonSummary.summary.total_price);
		$('#total_products').html(jsonSummary.summary.total_products);
		id_currency = jsonSummary.cart.id_currency;
		$('#id_currency option').removeAttr('selected');
		$('#id_currency option[value="'+id_currency+'"]').attr('selected', true);
		updateCurrencySign();
		id_lang = jsonSummary.cart.id_lang;
		$('#id_lang option').removeAttr('selected');
		$('#id_lang option[value="'+id_lang+'"]').attr('selected', true);
		$('#send_email_to_customer').attr('rel', jsonSummary.link_order);
		$('#go_order_process').attr('href', jsonSummary.link_order);
		$('#order_message').val(jsonSummary.order_message);
		resetBind();
	}

	function updateQty(id_product, id_product_attribute, id_customization, qty)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateQty",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				qty: qty,
				id_customer: id_customer,
				id_cart: id_cart,
				},
			success : function(res)
			{
					displaySummary(res);
					var errors = '';
					if(res.errors.length)
					{
						$.each(res.errors, function() {
							errors += this+'<br />';
						});
						$('#products_err').show();
					}
					else
						$('#products_err').hide();
					$('#products_err').html(errors);
			}
		});
	}

	function resetShippingPrice()
	{
		$('#shipping_price').val(shipping_price_selected_carrier);
		changed_shipping_price = false;
	}

	function addProduct()
	{
		var id_product = $('#id_product option:selected').val();
		$('#products_found #customization_list').contents().find('#customization_'+id_product).submit();
		if (customization_errors)
			$('#products_err').show();
		else
		{
			$('#products_err').hide();
			updateQty(id_product, $('#ipa_'+id_product+' option:selected').val(), 0, $('#qty').val());
		}
	}

	function updateCurrency()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateCurrency",
				id_currency: $('#id_currency option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
					displaySummary(res);
			}
		});
	}

	function updateLang()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "admincarts",
				action: "updateLang",
				id_lang: $('#id_lang option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
					displaySummary(res);
			}
		});
	}

	function updateDeliveryOption()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateDeliveryOption",
				delivery_option: $('#delivery_option option:selected').val(),
				gift: $('#order_gift').is(':checked')?1:0,
				gift_message: $('#gift_message').val(),
				recyclable: $('#carrier_recycled_package').is(':checked')?1:0,
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function updateCurrencySign()
	{
		$('.currency_sign').html(currencies[id_currency]);
	}

	function sendMailToCustomer()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminOrders')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminOrders'}",
				tab: "AdminOrders",
				action: "sendMailValidateOrder",
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				if (res.errors)
					$('#send_email_feedback').removeClass('conf').addClass('error');
				else
					$('#send_email_feedback').removeClass('error').addClass('conf');
				$('#send_email_feedback').html(res.result);
			}
		});
	}

	function updateAddressesList(addresses, id_address_delivery, id_address_invoice)
	{
		var addresses_delivery_options = '';
		var addresses_invoice_options = '';
		var address_invoice_detail = '';
		var address_delivery_detail = '';
		$.each(addresses, function() {
			if (this.id_address == id_address_invoice)
				address_invoice_detail = this.company+' '+this.firstname+' '+this.lastname+'<br />'+this.address1+'<br />'+this.address2+'<br />'+this.postcode+' '+this.city+' '+this.country;
			if(this.id_address == id_address_delivery)
				address_delivery_detail = this.company+' '+this.firstname+' '+this.lastname+'<br />'+this.address1+'<br />'+this.address2+'<br />'+this.postcode+' '+this.city+' '+this.country;

			addresses_delivery_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_delivery ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
			addresses_invoice_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_invoice ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
		});
		if (addresses.length == 0)
		{
			$('#addresses_err').show().html('{l s='You must add at least one address to process the order.'}');
			$('#address_delivery, #address_invoice').hide();
		}
		else
		{
			$('#addresses_err').hide();
			$('#address_delivery, #address_invoice').show();
		}

		$('#id_address_delivery').html(addresses_delivery_options);
		$('#id_address_invoice').html(addresses_invoice_options);
		$('#address_delivery_detail').html(address_delivery_detail);
		$('#address_invoice_detail').html(address_invoice_detail);
	}

	function updateAddresses()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateAddresses",
				id_customer: id_customer,
				id_cart: id_cart,
				id_address_delivery: $('#id_address_delivery option:selected').val(),
				id_address_invoice: $('#id_address_invoice option:selected').val()
				},
			success : function(res)
			{
				displaySummary(res);
				updateDeliveryOption();
			}
		});
	}
</script>

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>

<fieldset id="customer_part">
	<legend><img src="../img/admin/tab-customers.gif" />{l s='Customer'}</legend>
	<label>{l s='Search customers:'}</label>
	<div class="margin-form">
		<input type="text" id="customer" value="" />
		<p>{l s='Search a customer by tapping the first letters of his name'}</p>
		<a class="fancybox button" href="{$link->getAdminLink('AdminCustomers')|escape:'htmlall':'UTF-8'}&addcustomer&liteDisplaying=1&submitFormAjax=1#">
			<img src="../img/admin/add.gif" title="new"/><span>{l s='Add new customer'}</span>
		</a>
	</div>
	<div id="customers">
	</div>
</fieldset><br />
<form action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&submitAdd{$table}=1" method="post" autocomplete="off">
<fieldset id="products_part" style="display:none;"><legend><img src="../img/t/AdminCatalog.gif" />{l s='Cart'}</legend>
	<div>
		<label>{l s='Search a product:'} </label>
		<div class="margin-form">
			<input type="hidden" value="" id="id_cart" name="id_cart" />
			<input type="text" id="product" value="" />
			<p>{l s='Search a product by tapping the first letters of his name'}</p>
		</div>
		<div id="products_found">
			<div id="product_list">
			</div>
			<div id="attributes_list">
			</div>
			<iframe id="customization_list" style="border:0px;overflow:hidden;width:100%">
				<html>
				<head>
					{if isset($css_files_orders)}
						{foreach from=$css_files_orders key=css_uri item=media}
							<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
						{/foreach}
					{/if}
				</head>
				<body>
				</body>
				</html>
			</iframe>
			<p><label for="qty">{l s='Quantity:'}</label><input type="text" name="qty" id="qty" value="1" />&nbsp;<b>{l s='In stock:'}</b>&nbsp;<span id="qty_in_stock"></span></p>
			<div class="margin-form">
				<p><input type="submit" onclick="addProduct();return false;" class="button" id="submitAddProduct" value="{l s='Add to cart'}"/></p>
			</div>
		</div>
	</div>
	<div id="products_err" class="warn" style="display:none;"></div>
	<div>
		<table cellspacing="0" cellpadding="0" class="table width5" id="customer_cart">
				<colgroup>
					<col width="50px">
					<col width="">
					<col width="90px">
					<col width="100px">
					<col width="50px">
					<col width="50px">
				</colgroup>
			<thead>
				<tr>
					<th height="39px">{l s='Product'}</th>
					<th>{l s='Description'}</th>
					<th>{l s='Ref'}</th>
					<th>{l s='Unit price'}</th>
					<th style="width: 80px;">{l s='Qty'}</th>
					<th style="width: 80px;">{l s='Price'}</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<p><b>{l s='The prices are without taxes.'}</b></p>
	</div>
	<div>
		<p><label for="id_currency">{l s='Currency:'}</label>
			<script type="text/javascript">
				{foreach from=$currencies item='currency'}
					currencies['{$currency.id_currency}'] = '{$currency.sign}';
				{/foreach}
			</script>
			<select id="id_currency" name="id_currency">
			{foreach from=$currencies item='currency'}
				<option rel="{$currency.iso_code}" value="{$currency.id_currency}">{$currency.name}</option>
			{/foreach}
			</select>
		</p>
		<p>
		<label for="id_lang">{l s='Language:'}</label>
		<select id="id_lang" name="id_lang">
			{foreach from=$langs item='lang'}
				<option value="{$lang.id_lang}">{$lang.name}</option>
			{/foreach}
		</select>
		</p>
	</div>
	<div class="separation"></div>
	<div id="carts">
		<p><a href="#" id="show_old_carts" class="button"></a></p>
		<div id="old_carts_orders">
			<div id="nonOrderedCarts">
				<h3>{l s='Carts:'}</h3>
				<table cellspacing="0" cellpadding="0" class="table  width5">
					<colgroup>
						<col width="10px">
						<col width="">
						<col width="70px">
						<col width="50px">
					</colgroup>
				<thead>
					<tr>
						<th height="39px" class="left">{l s='ID'}</th>
						<th class="left">{l s='Date'}</th>
						<th class="left">{l s='Total'}</th>
						<th class="left">{l s='Action'}</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div id="lastOrders">
				<h3>{l s='Orders:'}</h3>
				<table cellspacing="0" cellpadding="0" class="table  width5">
					<colgroup>
						<col width="10px">
						<col width="50px">
						<col width="">
						<col width="90px">
						<col width="100px">
						<col width="250px">
						<col width="50px">
					</colgroup>
					<thead>
						<tr>
							<th height=39px" class="left">{l s='ID'}</th>
							<th class="left">{l s='Date'}</th>
							<th class="left">{l s='Products'}</th>
							<th class="left">{l s='Total paid'}</th>
							<th class="left">{l s='Payment'}</th>
							<th class="left">{l s='Status'}</th>
							<th class="left">{l s='Action'}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</fieldset>
<br />
<fieldset id="vouchers_part" style="display:none;">
	<legend><img src="../img/t/AdminCartRules.gif" />{l s='Vouchers'}</legend>
	<p>
		<label>{l s='Search a voucher:'} </label>
		<input type="text" id="voucher" value="" />
		<a class="fancybox button" href="{$link->getAdminLink('AdminCartRules')|escape:'htmlall':'UTF-8'}&addcart_rule&liteDisplaying=1&submitFormAjax=1#"><img src="../img/admin/add.gif" title="new"/>{l s='Add new voucher'}</a>
	</p>
	<div class="margin-form">
		<table cellspacing="0" cellpadding="0" class="table" id="voucher_list">
			<thead>
				<tr>
					<th class="left">{l s='Name'}</th>
					<th class="left">{l s='Description'}</th>
					<th class="left">{l s='Value'}</th>
					<th class="left">{l s='Action'}</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div id="vouchers_err" class="warn"></div>
</fieldset>
<br />
<fieldset id="address_part" style="display:none;">
	<legend><img src="../img/t/AdminAddresses.gif" />{l s='Addresses'}</legend>
	<div id="addresses_err" class="warn" style="display:none;"></div>
	<div id="address_delivery">
		<h3>{l s='Delivery:'}</h3>
		<select id="id_address_delivery" name="id_address_delivery">
		</select>
		<div id="address_delivery_detail">
		</div>
	</div>
	<div id="address_invoice">
		<h3>{l s='Invoice:'}</h3>
		<select id="id_address_invoice" name="id_address_invoice">
		</select>
		<div id="address_invoice_detail">
		</div>
	</div>
<a class="fancybox button" id="new_address" href="{$link->getAdminLink('AdminAddresses')|escape:'htmlall':'UTF-8'}&addaddress&id_customer=42&liteDisplaying=1&submitFormAjax=1#"><img src="../img/admin/add.gif" title="new"/>{l s='Add new address'}</a>
</fieldset>
<br />
<fieldset id="carriers_part" style="display:none;">
	<legend><img src="../img/t/AdminCarriers.gif" />{l s='Shipping'}</legend>
	<div id="carriers_err" style="display:none;" class="warn"></div>
	<div id="carrier_form">
		<div>
			<p>
				<label>{l s='Delivery option:'} </label>
				<select name="delivery_option" id="delivery_option">
				</select>
			</p>
			<p>
				<label for="shipping_price">{l s='Shipping price:'}</label> <span id="shipping_price"  name="shipping_price"></span>&nbsp;<span class="currency_sign"></span>&nbsp;
			</p>
			<p>
				<label for="free_shipping">{l s='Free shipping:'}</label>
				<input type="checkbox" id="free_shipping" name="free_shipping" value="1" />
			</p>
		</div>
		<div id="float:left;">
			{if $recyclable_pack}
				<p><input type="checkbox" name="carrier_recycled_package" value="1" id="carrier_recycled_package" />  <label for="carrier_recycled_package">{l s='Recycled package'}</label></p>
			{/if}
			{if $gift_wrapping}
				<p><input type="checkbox" name="order_gift" id="order_gift" value="1" /> <label for="order_gift">{l s='Gift'}</label></p>
				<p><label for="gift_message">{l s='Gift message:'}</label><textarea id="gift_message" cols="40" rows="4"></textarea></p>
			{/if}
		</div>
	</div>
</fieldset>
<br />
<fieldset id="summary_part" style="display:none;">
	<legend><img src="../img/t/AdminPayment.gif" />{l s='Summary'}</legend>
	<div id="send_email_feedback"></div>
	<div id="cart_summary" style="clear:both;float:left;">
		<ul>
			<li><span class="total_cart">{l s='Total products:'}</span><span id="total_products"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart">{l s='Total vouchers:'}</span><span id="total_vouchers"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart">{l s='Total shipping:'}</span><span id="total_shipping"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart">{l s='Total taxes:'}</span><span id="total_taxes"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart">{l s='Total without taxes:'}</span><span id="total_without_taxes"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart">{l s='Total with taxes:'}</span><span id="total_with_taxes"></span><span class="currency_sign"></span></li>
		</ul>
	</div>
	<div class="order_message_right">
		<label for="order_message">{l s='Order message:'}</label>
		<div class="margin-form">
			<textarea name="order_message" id="order_message" rows="3" cols="45"></textarea>
		</div>
		<div class="margin-form">
			<a href="#" id="send_email_to_customer" class="button">{l s='Send an e-mail to the customer with the link to process the payment.'}</a>
		</div>
		<div class="margin-form">
			<a target="_blank" id="go_order_process" href="" class="button">{l s='Go on payment page to process the payment.'}</a>
		</div>
		<label>{l s='Payment:'}</label>
		<div class="margin-form">
			<select name="payment_module_name" id="payment_module_name">
				{foreach from=$payment_modules item='module'}
					<option value="{$module->name}" {if isset($smarty.post.payment_module_name) && $module->name == $smarty.post.payment_module_name}selected="selected"{/if}>{$module->displayName}</option>
				{/foreach}
			</select>
		</div>
		<label>{l s='Order status:'}</label>
		<div class="margin-form">
			<select name="id_order_state" id="id_order_state">
				{foreach from=$order_states item='order_state'}
					<option value="{$order_state.id_order_state}" {if isset($smarty.post.id_order_state) && $order_state.id_order_state == $smarty.post.id_order_state}selected="selected"{/if}>{$order_state.name}</option>
				{/foreach}
			</select>
		</div>
		<div class="margin-form">
			<input type="submit" name="submitAddOrder" class="button" value="{l s='Create the order'}" />
		</div>
	</div>
</fieldset>
</form>
<div id="loader_container">
	<div id="loader">
	</div>
</div>

