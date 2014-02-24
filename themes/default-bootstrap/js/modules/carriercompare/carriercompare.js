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
var ajaxQueries = new Array();

$(document).ready(function(){		
	$(document).on('change', '#id_country', function(){
		resetAjaxQueries();
		updateStateByIdCountry();
	});

	if (typeof SE_RefreshMethod !='undefined' && SE_RefreshMethod == 0)
	{
		$(document).on('change', '#id_state', function(){
			resetAjaxQueries();
			updateCarriersList();
		});

		$(document).on('keyup', '#zipcode', function(e){
			if (e.keyCode == '13')
			{		
				resetAjaxQueries();
				updateCarriersList();
			}												
		});
	}

	$(document).on('click', '#update_carriers_list', function(){
		updateCarriersList();
	});

	$(document).on('click', '#carriercompare_submit', function(e){
		e.preventDefault();
		resetAjaxQueries();
		saveSelection();
	});

	updateStateByIdCountry();
});

function displayWaitingAjax(type, message)
{
	$('#SE_AjaxDisplay').find('p').html(message);
	$('#SE_AjaxDisplay').css('display', type);
}

function updateStateByIdCountry()
{
	$('#id_state').children().remove();
	$('#availableCarriers').slideUp('fast');
	$('#states').slideUp('fast');
	if (typeof SE_RefreshStateTS !='undefined')
		displayWaitingAjax('block', SE_RefreshStateTS);
	
	var query = $.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseDir + 'modules/carriercompare/ajax.php' + '?rand=' + new Date().getTime(),
		data: 'method=getStates&id_country=' + $('#id_country').val(),
		dataType: 'json',
		success: function(json) {
			if (json && json.length)
			{
				for (state in json)
				{
					$('#id_state').append('<option value=\''+json[state].id_state+'\' '+'>'+json[state].name+'</option>');
				}
				$('#states').slideDown('fast');
			}
			if (typeof SE_RefreshMethod !='undefined' && SE_RefreshMethod == 0)
				updateCarriersList();
			displayWaitingAjax('none', '');
			$('#id_state').uniform();
		}
	});
	ajaxQueries.push(query);
}

function updateCarriersList()
{
	$('#carriercompare_errors_list').children().remove();	
	$('#availableCarriers').slideUp('normal', function(){		
		$(this).find(('tbody')).children().remove();				
		$('#noCarrier').slideUp('fast');
		displayWaitingAjax('block', SE_RetrievingInfoTS);
		
		var query = $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/carriercompare/ajax.php' + '?rand=' + new Date().getTime(),
			data: 'method=getCarriers&id_country=' + $('#id_country').val() + '&id_state=' + $('#id_state').val() + '&zipcode=' + $('#zipcode').val(),
			dataType: 'json',
			success: function(json) {
				if (json.length)
				{
					for (carrier in json)
					{
						var html = '<tr class="'+(carrier % 2 ? 'alternate_' : '')+'item">'+
								'<td class="carrier_action">'+
								'<input type="radio" name="id_carrier" value="'+json[carrier].id_carrier+'" id="id_carrier'+json[carrier].id_carrier+'" '+(id_carrier == json[carrier].id_carrier ? 'checked="checked"' : '')+'/>'+
								'</td>'+
								'<td class="carrier_name">'+
								'<label for="id_carrier'+json[carrier].id_carrier+'">'+
								(json[carrier].img ? '<img src="'+json[carrier].img+'" alt="'+json[carrier].name+'" />' : json[carrier].name)+
								'</label>'+
							'</td>'+
							'<td class="carrier_infos">'+((json[carrier].delay != null) ? json[carrier].delay : '') +'</td>'+
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
					displayWaitingAjax('none', '');
					$('#availableCarriers').slideDown();
				}
				else
				{
					displayWaitingAjax('none', '');
					$('#noCarrier').slideDown();
				}
			}
		});
		ajaxQueries.push(query);
	});
}
                               
function saveSelection()
{
	$('#carriercompare_errors').slideUp();
	$('#carriercompare_errors_list').children().remove();
	displayWaitingAjax('block', SE_RedirectTS);

	var query = $.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseDir + 'modules/carriercompare/ajax.php' + '?rand=' + new Date().getTime(),
		data: 'method=saveSelection&' + $('#compare_shipping_form').serialize(),
		dataType: 'json',
		success: function(json) {
			if (json.length)
			{
				for (error in json)
					$('#carriercompare_errors_list').append('<li>'+json[error]+'</li>');
				$('#carriercompare_errors').slideDown();
				displayWaitingAjax('none', '');
			}
			else
			{
				$('.SE_SubmitRefreshCard').fadeOut('fast');
				location.reload(true);
			}
		}
	});
	ajaxQueries.push(query);
	return false;
}

function resetAjaxQueries()
{
	for (i = 0; i < ajaxQueries.length; ++i)
		ajaxQueries[i].abort();
	ajaxQueries = new Array();
}