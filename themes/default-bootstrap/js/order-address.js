/*
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
*/

$(document).ready(function()
{
	if (typeof(formatedAddressFieldsValuesList) !== 'undefined')
		updateAddressesDisplay(true);
});

//update the display of the addresses
function updateAddressesDisplay(first_view)
{
	// update content of delivery address
	updateAddressDisplay('delivery');
	var txtInvoiceTitle = "";
	try{
		var adrs_titles = getAddressesTitles();
		txtInvoiceTitle = adrs_titles.invoice;
	}
	catch (e)
	{}
	// update content of invoice address
	//if addresses have to be equals...
	if ($('input[type=checkbox]#addressesAreEquals:checked').length === 1 && ($('#multishipping_mode_checkbox:checked').length === 0))
	{
		if ($('#multishipping_mode_checkbox:checked').length === 0) {
			$('#address_invoice_form:visible').hide('fast');
		}
		$('ul#address_invoice').html($('ul#address_delivery').html());
		$('ul#address_invoice li.address_title').html(txtInvoiceTitle);
	}
	else
	{
		$('#address_invoice_form:hidden').show('fast');
		if ($('#id_address_invoice').val())
			updateAddressDisplay('invoice');
		else
		{
			$('ul#address_invoice').html($('ul#address_delivery').html());
			$('ul#address_invoice li.address_title').html(txtInvoiceTitle);
		}	
	}
	if(!first_view)
	{
		if (orderProcess === 'order')
			updateAddresses();
	}
	return true;
}

function updateAddressDisplay(addressType)
{
	if (formatedAddressFieldsValuesList.length <= 0)
		return false;

	var idAddress = parseInt($('#id_address_' + addressType + '').val());
	buildAddressBlock(idAddress, addressType, $('#address_' + addressType));

	// change update link
	var link = $('ul#address_' + addressType + ' li.address_update a').attr('href');
	var expression = /id_address=\d+/;
	if (link)
	{
		link = link.replace(expression, 'id_address=' + idAddress);
		$('ul#address_' + addressType + ' li.address_update a').attr('href', link);
	}
}

function updateAddresses()
{
	var idAddress_delivery = parseInt($('#id_address_delivery').val());
	var idAddress_invoice = $('input[type=checkbox]#addressesAreEquals:checked').length === 1 ? idAddress_delivery : parseInt($('#id_address_invoice').val());
	
   	if(isNaN(idAddress_delivery) == false && isNaN(idAddress_invoice) == false)	
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				'multi-shipping': $('#id_address_delivery:hidden').length,
				id_address_delivery: idAddress_delivery,
				id_address_invoice: idAddress_invoice,
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
}