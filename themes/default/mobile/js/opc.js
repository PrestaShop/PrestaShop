$( '.prestashop-page' ).live( 'pageinit' , function() {
	initEvent();
});
$( '.prestashop-page' ).live( 'pagechange' , function() {
	initEvent();
});

function initEvent()
{
	$('.qty-field').change(function() {
		var initial_quantity = $(this).data('initial-quantity');
		var current_quantity = $(this).val();

		if (initial_quantity != current_quantity)
		{
			var op = 'up';
			if (initial_quantity > current_quantity)
				op = 'down';

			var qty = Math.abs(current_quantity - initial_quantity);

			$.mobile.showPageLoadingMsg();
			$.ajax({
				url: baseDir,
				async: true,
				cache: false,
				data: 'controller=cart&add&id_product='+$(this).data('id-product')+'&ipa='+$(this).data('id-product-attribute')+'&op='+op+'&qty='+qty+'&id_address_delivery=0&token='+static_token,
				success: function()
				{
					window.location.href = orderOpcUrl;
				}
			});
		}
	});

	$('.address-field').change(function() {
		$.mobile.showPageLoadingMsg();
		$.ajax({
			url: baseDir,
			async: true,
			cache: false,
			data: 'controller=order-opc&ajax=true&mobile_theme=true&method=updateAddressesSelected&id_address_delivery=' + $('#delivery-address-choice').val() + '&id_address_invoice=' + $('#invoice-address-choice').val() + '&token=' + static_token,
			success: function()
			{
				window.location.href = orderOpcUrl;
			}
		});
	});

	//
	$("#addressesAreEquals").bind("change", function(event, ui) {
		$("#address_invoice_form").toggle();
	});

	$('.delivery_option_radio').click(function() {
		updateCarrierSection($(this));
	});

	$('#gift').click(function() {
		// Gift checkbox update
		giftShowDiv();
		updateCarrierSection($(this));
	});

	$('#gift_div').change(function() {
		updateCarrierSection($(this));
	});

	$('#cgv').click(function() {
		if ($('#cgv:checked').length != 0)
			var checked = 1;
		else
			var checked = 0;
		$.ajax({
			type: 'POST',
			url: orderOpcUrl,
			async: true,
			cache: false,
			dataType : "json",
			data: 'ajax=true&method=updateTOSStatusAndGetPayments&checked=' + checked + '&token=' + static_token,
			success: function(json)
			{
				window.location.href = orderOpcUrl+'#cgv_checkbox';
				window.location.reload(true);
			}
		});
	});
}

function giftShowDiv()
{
	if ($('#gift').is(':checked'))
		$('#gift_div').show();
	else
		$('#gift_div').hide();
}

function updateCarrierSection(elm)
{
	var recyclablePackage = 0;
	var gift = 0;
	var giftMessage = '';

	var delivery_option_radio = $('.delivery_option_radio_carrier');
	var delivery_option_params = '&';
	$.each(delivery_option_radio, function(i) {
		if ($(this).prop('checked'))
			delivery_option_params += $(delivery_option_radio[i]).attr('name') + '=' + $(delivery_option_radio[i]).val() + '&';
	});
	if (delivery_option_params == '&')
		delivery_option_params = '&delivery_option=&'

	if ($('input#recyclable:checked').length)
		recyclablePackage = 1;
	if ($('input#gift:checked').length)
	{
		gift = 1;
		giftMessage = encodeURIComponent($('textarea#gift_message').val());
	}

	$.ajax({
		type: 'POST',
		url: orderOpcUrl,
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=updateCarrierAndGetPayments' + delivery_option_params + 'recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
		success: function(jsonData)
		{
			if (!elm.is('#gift'))
				window.location.href = orderOpcUrl+'#delivery_choose';
		}
	});
}
