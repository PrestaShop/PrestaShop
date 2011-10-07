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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function updateStateByIdCountry()
{
	$.ajax({
		type: 'POST',
		url: baseDir + 'modules/carriercompare/ajax.php',
		data: 'method=getStates&id_country=' + $('#id_country').val(),
		dataType: 'json',
		success: function(json) {
			$('#id_state').children().remove();
			$('#states').slideUp('slow');
			if (json.length)
			{
				for (state in json)
				{
					$('#id_state').append('<option value=\''+json[state].id_state+'\' '+(id_state == json[state].id_state ? 'selected="selected"' : '')+'>'+json[state].name+'</option>');
				}
				$('#states').slideDown('slow');
			}
		}
	});
}

function updateCarriersList()
{
	$.ajax({
		type: 'POST',
		url: baseDir + 'modules/carriercompare/ajax.php',
		data: 'method=getCarriers&id_country=' + $('#id_country').val() + '&id_state=' + $('#id_state').val() + '&zipcode=' + $('#zipcode').val(),
		dataType: 'json',
		success: function(json) {
			$('#carriers_list').children().remove();
			$('#availableCarriers').slideUp('slow');
			$('#noCarrier').slideUp('slow');
			if (json.length)
			{
				for (carrier in json)
				{
					var html = '<tr class="'+(carrier % 2 ? 'alternate_' : '')+'item">'+
							'<td class="carrier_action radio">'+
							'<input type="radio" name="id_carrier" value="'+json[carrier].id_carrier+'" id="id_carrier'+json[carrier].id_carrier+'" '+(id_carrier == json[carrier].id_carrier ? 'checked="checked"' : '')+'/>'+
							'</td>'+
							'<td class="carrier_name">'+
							'<label for="id_carrier'+json[carrier].id_carrier+'">'+
							(json[carrier].img ? '<img src="'+json[carrier].img+'" alt="'+json[carrier].name+'" />' : json[carrier].name)+
							'</label>'+
						'</td>'+
						'<td class="carrier_infos">'+json[carrier].delay+'</td>'+
						'<td class="carrier_price">';
					
					if (json[carrier].price)
					{
						html += '<span class="price">'+(displayPrice == 1 ? formatCurrency(json[carrier].price_tax_exc, currencyFormat, currencySign, currencyBlank) : formatCurrency(json[carrier].price, currencyFormat, currencySign, currencyBlank))+'</span>';
					}
					else
					{
						html += txtFree;
					}
					html += '</td>'+
							'</tr>';
					$('#carriers_list').append(html);
				}
				$('#availableCarriers').slideDown('slow');
			}
			else
				$('#noCarrier').slideDown('slow');
		}
	});
}

function saveSelection()
{
	$.ajax({
		type: 'POST',
		url: baseDir + 'modules/carriercompare/ajax.php',
		data: 'method=saveSelection&' + $('#compare_shipping_form').serialize(),
		dataType: 'json',
		success: function(json) {
			$('#carriercompare_errors_list').children().remove();
			$('#carriercompare_errors').slideUp('slow');
			if (json.length)
			{
				for (error in json)
				{
					$('#carriercompare_errors_list').append('<li>'+json[error]+'</li>');
				}
				$('#carriercompare_errors').slideDown('slow');
			}
			else
			{
				$('#carriercompare_submit').fadeOut('slow');
				document.location.href = document.location.href;
			}
		}
	});
	return false;
}

$(document).ready(function() {
	$('#id_country').change(function(){
		updateStateByIdCountry();
		updateCarriersList();
	});
	$('#id_state').change(function(){
		updateCarriersList();
	});
	$('#carriercompare_submit').click(function(){
		saveSelection();
		return false;
	});
	updateStateByIdCountry();
	updateCarriersList();
});
