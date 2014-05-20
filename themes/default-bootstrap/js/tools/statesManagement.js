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
//global variables
var countriesNeedIDNumber = [];
var countriesNeedZipCode = [];

$(document).ready(function(){
	setCountries();
	bindStateInputAndUpdate();
	bindUniform();
	bindPostcode();
	bindCheckbox();
	$(document).on('click', '#invoice_address', function(e){
		bindCheckbox();
	});
});

function setCountries()
{
	if (typeof countries !== 'undefined' && countries)
	{
		var countriesPS = [];
	    for (var i in countries)
		{
			var id_country = countries[i]['id_country'];
			if (typeof countries[i]['states'] !== 'undefined' && countries[i]['states'] && countries[i]['contains_states'])
			{
				countriesPS[id_country] = [];
	    		for (var j in countries[i]['states'])
					countriesPS[parseInt(id_country)].push({'id' : parseInt(countries[i]['states'][j]['id_state']), 'name' : countries[i]['states'][j]['name']});
			}
			if (typeof countries[i]['need_identification_number'] !== 'undefined' && parseInt(countries[i]['need_identification_number']) > 0)
				countriesNeedIDNumber.push(parseInt(countries[i]['id_country']));
			if (typeof countries[i]['need_zip_code'] !== 'undefined' && parseInt(countries[i]['need_zip_code']) > 0)
				countriesNeedZipCode[parseInt(countries[i]['id_country'])] = countries[i]['zip_code_format'];
		}
	}
	countries =  countriesPS;
}

function bindCheckbox()
{
	if ($('#invoice_address:checked').length > 0)
	{
		$('#opc_invoice_address').slideDown('slow');
		if ($('#company_invoice').val() == '')
			$('#vat_number_block_invoice').hide();
		bindUniform();
	}
	else
		$('#opc_invoice_address').slideUp('slow');
}

function bindUniform()
{
	$("select.form-control,input[type='radio'],input[type='checkbox']").uniform(); 
}

function bindPostcode()
{
	$(document).on('keyup', 'input[name=postcode]', function(e)
	{
		$(this).val($(this).val().toUpperCase());
	});
}

function bindStateInputAndUpdate()
{
	$('.id_state, .dni, .postcode').css({'display':'none'});
	updateState();
	updateNeedIDNumber();
	updateZipCode();

	$(document).on('change', '#id_country', function(e)
	{
		updateState();
		updateNeedIDNumber();
		updateZipCode();
	});

	if ($('#id_country_invoice').length !== 0)
	{
		$(document).on('change', '#id_country_invoice', function(e)
		{
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		});
		updateState('invoice');
		updateNeedIDNumber('invoice');
		updateZipCode('invoice');
	}

	if (typeof idSelectedState !== 'undefined' && idSelectedState)
		$('.id_state option[value=' + idSelectedState + ']').prop('selected', true);
	if (typeof idSelectedStateInvoice !== 'undefined' && idSelectedStateInvoice)
		$('.id_state_invoice option[value=' + idSelectedStateInvoice + ']').prop('selected', true);
}

function updateState(suffix)
{
	$('#id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')+' option:not(:first-child)').remove();
	if (typeof countries !== 'undefined')
		var states = countries[parseInt($('#id_country' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).val())];
	if (typeof states !== 'undefined')
	{
		$(states).each(function(key, item){
			$('#id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).append('<option value="' + parseInt(item.id) + '">' + item.name + '</option>');
		});

		$('.id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');
		$('#id_state, #id_state_invoice').uniform();
	}
	else
		$('.id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('#id_country' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).val());
	if (typeof countriesNeedIDNumber !== 'undefined' && in_array(idCountry, countriesNeedIDNumber))
	{
		$('.dni' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');
		$('#dni').uniform();
	}
	else
		$('.dni' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('#id_country' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).val());
	if (typeof countriesNeedZipCode !== 'undefined' && typeof countriesNeedZipCode[idCountry] !== 'undefined')
	{
		$('.postcode' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');
		$('#postcode').uniform();
	}
	else
		$('.postcode'+(typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}