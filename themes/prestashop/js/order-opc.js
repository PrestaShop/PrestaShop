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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function updateCarrierList(json)
{
	var carriers = json.carriers;
	
	/* contains all carrier available for this address */
	if (carriers.length == 0)
	{
		checkedCarrier = 0;
		$('input[name=id_carrier]:checked').attr('checked', false);
		$('#noCarrierWarning').show();
		$('#extra_carrier').hide();
		$('#recyclable_block').hide();
		$('table#carrierTable:visible').hide();
	}
	else
	{
		checkedCarrier = json.checked;
		var html = '';
		for (i=0;i<carriers.length; i++)
		{
			var itemType = '';
			
			if (i == 0)
				itemType = 'first_item ';
			else if (i == carriers.length-1)
				itemType = 'last_item ';
			if (i % 2)
				itemType = itemType + 'alternate_item';
			else
				itemType = itemType + 'item';
			
			var name = carriers[i].name;
			if (carriers[i].img != '')
				name = '<img src="'+carriers[i].img+'" alt="" />';
				
			html = html + 
			'<tr class="'+itemType+'">'+
				'<td class="carrier_action radio"><input type="radio" name="id_carrier" value="'+carriers[i].id_carrier+'" id="id_carrier'+carriers[i].id_carrier+'"  onclick="updateCarrierSelectionAndGift();" '+((checkedCarrier == carriers[i].id_carrier || carriers.length == 1) ? 'checked="checked"' : '')+' /></td>'+
				'<td class="carrier_name"><label for="id_carrier'+carriers[i].id_carrier+'">'+name+'</label></td>'+
				'<td class="carrier_infos">'+carriers[i].delay+'</td>'+
				'<td class="carrier_price"><span class="price">'+formatCurrency(carriers[i].price, currencyFormat, currencySign, currencyBlank)+'</span>';
			if (taxEnabled && displayPrice == 0)
				html = html + ' ' + txtWithTax;
			else
				html = html + ' ' + txtWithoutTax;
			html = html + '</td>'+
			'</tr>';
		}
		if (json.HOOK_EXTRACARRIER !== null && json.HOOK_EXTRACARRIER != undefined) html += json.HOOK_EXTRACARRIER;
		$('#noCarrierWarning').hide();
		$('#extra_carrier:hidden').show();
		$('table#carrierTable tbody').html(html);
		$('table#carrierTable:hidden').show();
		$('#recyclable_block:hidden').show();
	}
	
	/* update hooks for carrier module */
	$('#HOOK_BEFORECARRIER').html(json.HOOK_BEFORECARRIER);
}

function updatePaymentMethods(json)
{
	$('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
	$('#opc_payment_methods-content div#HOOK_PAYMENT').html(json.HOOK_PAYMENT);
}

function updateAddressSelection()
{
	var idAddress_delivery = ($('input#opc_id_address_delivery').length == 1 ? $('input#opc_id_address_delivery').val() : $('select#id_address_delivery').val());
	var idAddress_invoice = ($('input#opc_id_address_invoice').length == 1 ? $('input#opc_id_address_invoice').val() : ($('input[type=checkbox]#addressesAreEquals:checked').length == 1 ? idAddress_delivery : ($('select#id_address_invoice').length == 1 ? $('select#id_address_invoice').val() : idAddress_delivery)));
	
	$('#opc_account-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$('#opc_payment_methods-overlay').fadeIn('slow');
	
	$.ajax({
           type: 'POST',
           url: orderOpcUrl,
           async: true,
           cache: false,
           dataType : "json",
           data: 'ajax=true&method=updateAddressesSelected&id_address_delivery=' + idAddress_delivery + '&id_address_invoice=' + idAddress_invoice + '&token=' + static_token,
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
				}
				else
				{
					updateCarrierList(jsonData);
					updatePaymentMethods(jsonData);
					updateCartSummary(jsonData.summary);
					updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
					updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
					if ($('#gift-price').length == 1)
						$('#gift-price').html(jsonData.gift_price);
					$('#opc_account-overlay').fadeOut('slow');
					$('#opc_delivery_methods-overlay').fadeOut('slow');
					$('#opc_payment_methods-overlay').fadeOut('slow');
				}
			},
           error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
	});
}

function getCarrierListAndUpdate()
{
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getCarrierList&token=' + static_token,
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
				}
				else
					updateCarrierList(jsonData);
				$('#opc_delivery_methods-overlay').fadeOut('slow');
			}
	});
}

function updateCarrierSelectionAndGift()
{
	var recyclablePackage = 0;
	var gift = 0;
	var giftMessage = '';
	var idCarrier = 0;

	if ($('input#recyclable:checked').length)
		recyclablePackage = 1;
	if ($('input#gift:checked').length)
	{
		gift = 1;
		giftMessage = encodeURIComponent($('textarea#gift_message').val());
	}
	
	if ($('input[name=id_carrier]:checked').length)
	{
		idCarrier = $('input[name=id_carrier]:checked').val();
		checkedCarrier = idCarrier;
	}
	
	$('#opc_payment_methods-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$.ajax({
       type: 'POST',
       url: orderOpcUrl,
       async: false,
       cache: false,
       dataType : "json",
       data: 'ajax=true&method=updateCarrierAndGetPayments&id_carrier=' + idCarrier + '&recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
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
    		}
    		else
    		{
    			updateCartSummary(jsonData.summary);
    			updatePaymentMethods(jsonData);
    			updateHookShoppingCart(jsonData.summary.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.summary.HOOK_SHOPPING_CART_EXTRA);
				$('#opc_payment_methods-overlay').fadeOut('slow');
				$('#opc_delivery_methods-overlay').fadeOut('slow');
    		}
    	},
       error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save carrier \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
   });
}

function confirmFreeOrder()
{
	if ($('#opc_new_account-overlay').length != 0)
		$('#opc_new_account-overlay').fadeIn('slow');
	else
		$('#opc_account-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$('#opc_payment_methods-overlay').fadeIn('slow');
	$.ajax({
		type: 'POST',
		url: orderOpcUrl,
		async: true,
		cache: false,
		dataType : "html",
		data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
		success: function(html)
		{
			var array_split = html.split(':');
			if (array_split[0] === 'freeorder')
	   		{
	   			if (isGuest)
	   				document.location.href = guestTrackingUrl+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
	   			else
	   				document.location.href = historyUrl;
	   		}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to confirm the order \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
	});
}

function saveAddress(type)
{
	if (type != 'delivery' && type != 'invoice')
		return false;
	
	var params = 'firstname='+encodeURIComponent($('#firstname'+(type == 'invoice' ? '_invoice' : '')).val())+'&lastname='+encodeURIComponent($('#lastname'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'company='+encodeURIComponent($('#company'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'vat_number='+encodeURIComponent($('#vat_number'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'dni='+encodeURIComponent($('#dni'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'address1='+encodeURIComponent($('#address1'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'address2='+encodeURIComponent($('#address2'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'postcode='+encodeURIComponent($('#postcode'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'city='+encodeURIComponent($('#city'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'id_country='+encodeURIComponent($('#id_country'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'id_state='+encodeURIComponent($('#id_state'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'other='+encodeURIComponent($('#other'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'phone='+encodeURIComponent($('#phone'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'phone_mobile='+encodeURIComponent($('#phone_mobile'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	params += 'alias='+encodeURIComponent($('#alias'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
	// Clean the last &
	params = params.substr(0, params.length-1);

	var result = false;
	
	$.ajax({
       type: 'POST',
       url: addressUrl,
       async: false,
       cache: false,
       dataType : "json",
       data: 'ajax=true&submitAddress=true&type='+type+'&'+params+'&token=' + static_token,
       success: function(jsonData)
       {
			if (jsonData.hasError)
			{
       			var tmp = '';
       			var i = 0;
				for(error in jsonData.errors)
					//IE6 bug fix
					if(error != 'indexOf')
					{
						i = i+1;
						tmp += '<li>'+jsonData.errors[error]+'</li>';
					}
				tmp += '</ol>';
				var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
				$('#opc_account_errors').html(errors).slideDown('slow');
				$.scrollTo('#opc_account_errors', 800);
				$('#opc_new_account-overlay').fadeOut('slow');
				$('#opc_delivery_methods-overlay').fadeOut('slow');
				$('#opc_payment_methods-overlay').fadeOut('slow');
				result = false;
			}
			else
			{
				// update addresses id
				$('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
				$('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
	
				result = true;
			}
		},
       error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
    });

	return result;
}

function updateNewAccountToAddressBlock()
{
	$('#opc_new_account-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$('#opc_payment_methods-overlay').fadeIn('slow');
	$.ajax({
		type: 'POST',
		url: orderOpcUrl,
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=getAddressBlockAndCarriersAndPayments&token=' + static_token ,
		success: function(json)
		{
			$('#opc_new_account').fadeOut('fast', function() {
				$('#opc_new_account').html(json.order_opc_adress);
				// update block user info
				if (json.block_user_info != '' && $('#header_user').length == 1)
				{
					$('#header_user').fadeOut('slow', function() {
						$(this).attr('id', 'header_user_old').after(json.block_user_info).fadeIn('slow');
						$('#header_user_old').remove();
					});
				}
				$('#opc_new_account').fadeIn('fast', function() {
					updateAddressesDisplay(true);
					updateCarrierList(json.carrier_list);
					updatePaymentMethods(json);
					if ($('#gift-price').length == 1)
						$('#gift-price').html(json.gift_price);
					$('#opc_delivery_methods-overlay').fadeOut('slow');
					$('#opc_payment_methods-overlay').fadeOut('slow');
				});
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
	});
}

$(function() {
	// GUEST CHECKOUT / NEW ACCOUNT MANAGEMENT
	if ((!isLogged) || (isGuest))
	{
		if (guestCheckoutEnabled && !isLogged)
		{
			$('#opc_account_choice').show();
			$('#opc_account_form').hide();
			$('#opc_invoice_address').hide();
			
			$('#opc_createAccount').click(function() {
				$('.is_customer_param').show();
				$('#opc_account_form').slideDown('slow');
				$('#is_new_customer').val('1');
				$('#opc_account_choice').hide();
				$('#opc_invoice_address').hide();
				updateState();
				updateNeedIDNumber();
				updateZipCode();
			});
			$('#opc_guestCheckout').click(function() {
				$('.is_customer_param').hide();
				$('#opc_account_form').slideDown('slow');
				$('#is_new_customer').val('0');
				$('#opc_account_choice').hide();
				$('#opc_invoice_address').hide();
				$('#new_account_title').html(txtInstantCheckout);
				updateState();
				updateNeedIDNumber();
				updateZipCode();
			});
		}
		else if (isGuest)
		{
			$('.is_customer_param').hide();
			$('#opc_account_form').show('slow');
			$('#is_new_customer').val('0');
			$('#opc_account_choice').hide();
			$('#opc_invoice_address').hide();
			$('#new_account_title').html(txtInstantCheckout);
			updateState();
			updateNeedIDNumber();
			updateZipCode();
		}
		else
		{
			$('#opc_account_choice').hide();
			$('#is_new_customer').val('1');
			$('.is_customer_param').show();
			$('#opc_account_form').show();
			$('#opc_invoice_address').hide();
			updateState();
			updateNeedIDNumber();
			updateZipCode();
		}
		
		// LOGIN FORM
		$('#openLoginFormBlock').click(function() {
			$('#openNewAccountBlock').show();
			$(this).hide();
			$('#login_form_content').slideDown('slow');
			$('#new_account_form_content').slideUp('slow');
			return false;
		});
		// LOGIN FORM SENDING
		$('#SubmitLogin').click(function() {
			$.ajax({
				type: 'POST',
				url: authenticationUrl,
				async: false,
				cache: false,
				dataType : "json",
				data: 'SubmitLogin=true&ajax=true&email='+encodeURIComponent($('#login_email').val())+'&passwd='+encodeURIComponent($('#passwd').val())+'&token=' + static_token ,
				success: function(jsonData)
				{
					if (jsonData.hasError)
					{
						var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+txtErrors+':</b><ol>';
						for(error in jsonData.errors)
							//IE6 bug fix
							if(error != 'indexOf')
								errors += '<li>'+jsonData.errors[error]+'</li>';
						errors += '</ol>';
						$('#opc_login_errors').html(errors).slideDown('slow');
					}
					else
					{
						// update token
						static_token = jsonData.token;
						updateNewAccountToAddressBlock();
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
			});
			return false;
		});
		
		// INVOICE ADDRESS
		$('#invoice_address').click(function() {
			if ($('#invoice_address:checked').length > 0)
			{
				$('#opc_invoice_address').slideDown('slow');
				if ($('#company_invoice').val() == '')
					$('#vat_number_block_invoice').hide();
				updateState('invoice');
				updateNeedIDNumber('invoice');
				updateZipCode('invoice');
			}
			else
				$('#opc_invoice_address').slideUp('slow');
		});
		
		// VALIDATION / CREATION AJAX
		$('#submitAccount').click(function() {
			$('#opc_new_account-overlay').fadeIn('slow');
			$('#opc_delivery_methods-overlay').fadeIn('slow');
			$('#opc_payment_methods-overlay').fadeIn('slow');
			
			// RESET ERROR(S) MESSAGE(S)
			$('#opc_account_errors').html('').slideUp('slow');
			
			if ($('input#opc_id_customer').val() == 0)
			{
				var callingFile = authenticationUrl;
				var params = 'submitAccount=true&';
			}
			else
			{
				var callingFile = orderOpcUrl;
				var params = 'method=editCustomer&';
			}
			
			$('#opc_account_form input:visible').each(function() {
				if ($(this).is('input[type=checkbox]'))
				{
					if ($(this).is(':checked'))
						params += encodeURIComponent($(this).attr('name'))+'=1&';
				}
				else if ($(this).is('input[type=radio]'))
				{
					if ($(this).is(':checked'))
						params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
				}
				else
					params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
			});
			$('#opc_account_form select:visible').each(function() {
				params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
			});
			params += 'customer_lastname='+encodeURIComponent($('#customer_lastname').val())+'&';
			params += 'customer_firstname='+encodeURIComponent($('#customer_firstname').val())+'&';
			params += 'alias='+encodeURIComponent($('#alias').val())+'&';
			params += 'is_new_customer='+encodeURIComponent($('#is_new_customer').val())+'&';
			// Clean the last &
			params = params.substr(0, params.length-1);
			
			$.ajax({
				type: 'POST',
				url: callingFile,
				async: false,
				cache: false,
				dataType : "json",
				data: 'ajax=true&'+params+'&token=' + static_token ,
				success: function(jsonData)
				{
					if (jsonData.hasError)
					{
						var tmp = '';
						var i = 0;
						for(error in jsonData.errors)
							//IE6 bug fix
							if(error != 'indexOf')
							{
								i = i+1;
								tmp += '<li>'+jsonData.errors[error]+'</li>';
							}
						tmp += '</ol>';
						var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
						$('#opc_account_errors').html(errors).slideDown('slow');
						$.scrollTo('#opc_account_errors', 800);
					}

					isGuest = ($('#is_new_customer').val() == 1 ? 0 : 1);
					
					if (jsonData.id_customer != undefined && jsonData.id_customer != 0 && jsonData.isSaved)
					{
						// update token
						static_token = jsonData.token;
						
						// update addresses id
						$('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
						$('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
						
						// It's not a new customer
						if ($('input#opc_id_customer').val() != '0')
						{
							if (!saveAddress('delivery'))
								return false;
						}
						
						// update id_customer
						$('input#opc_id_customer').val(jsonData.id_customer);
						
						if ($('#invoice_address:checked').length != 0)
						{
							if (!saveAddress('invoice'))
								return false;
						}
						
						// update id_customer
						$('input#opc_id_customer').val(jsonData.id_customer);
						
						// force to refresh carrier list
						if (isGuest)
						{
							$('#opc_account_saved').fadeIn('slow');
							$('#submitAccount').hide();
							updateAddressSelection();
						}
						else
							updateNewAccountToAddressBlock();
					}
					$('#opc_new_account-overlay').fadeOut('slow');
					$('#opc_delivery_methods-overlay').fadeOut('slow');
					$('#opc_payment_methods-overlay').fadeOut('slow');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save account \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
			});
			return false;
		});
	}
	
	// Order message update
	$('#message').blur(function() {
		$('#opc_delivery_methods-overlay').fadeIn('slow');
		$.ajax({
           type: 'POST',
           url: orderOpcUrl,
           async: true,
           cache: false,
           dataType : "json",
           data: 'ajax=true&method=updateMessage&message=' + encodeURIComponent($('#message').val()) + '&token=' + static_token ,
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
				}
           		else
           			$('#opc_delivery_methods-overlay').fadeOut('slow');
			},
           error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to save message \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
       });
	});
	
	// Recyclable checkbox
	$('input#recyclable').click(function() {
		updateCarrierSelectionAndGift();
	});
	
	// Gift checkbox update
	$('input#gift').click(function() {
		if ($('input#gift').is(':checked'))
			$('p#gift_div').show();
		else
			$('p#gift_div').hide();
		updateCarrierSelectionAndGift();
	});
	
	if ($('input#gift').is(':checked'))
		$('p#gift_div').show();
	else
		$('p#gift_div').hide();
	
	// Gift message update
	$('textarea#gift_message').blur(function() {
		updateCarrierSelectionAndGift();
	});
	
	// TOS
	$('#cgv').click(function() {
		if ($('#cgv:checked').length != 0)
			var checked = 1;
		else
			var checked = 0;
		
		$('#opc_payment_methods-overlay').fadeIn('slow');
		$.ajax({
           type: 'POST',
           url: orderOpcUrl,
           async: true,
           cache: false,
           dataType : "json",
           data: 'ajax=true&method=updateTOSStatusAndGetPayments&checked=' + checked + '&token=' + static_token,
           success: function(json)
           {
				$('div#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
				$('#opc_payment_methods-content div#HOOK_PAYMENT').html(json.HOOK_PAYMENT);
				$('#opc_payment_methods-overlay').fadeOut('slow');
           }
       });
	});
	
	$('#opc_account_form input,select,textarea').change(function() {
		if ($(this).is(':visible'))
		{
			$('#opc_account_saved').fadeOut('slow');
			$('#submitAccount').show();
		}
	});
	
});
