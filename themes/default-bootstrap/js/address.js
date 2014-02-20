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
var countriesNeedIDNumber = [];
var countriesNeedZipCode = [];

$(document).ready(function(){
	set_countries();
	vat_number();

	$('#company').on('input',function(){
		vat_number();
	});

	if (typeof idSelectedState !== 'undefined' && idSelectedState)
		$('.id_state option[value=' + idSelectedState + ']').attr('selected', true);

	$('#id_country').on('change', function(){
		if (typeof vatnumber_ajax_call !== 'undefined' && vatnumber_ajax_call)
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: baseDir + 'modules/vatnumber/ajax.php?id_country=' + parseInt($(this).val()) + '&rand=' + new Date().getTime(),
				success: function(isApplicable){
					if(isApplicable == "1")
					{
						$('#vat_area').show();
						$('#vat_number').show();
					}
					else
						$('#vat_area').hide();
				}
			});
	});
});

function set_countries()
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
					countriesPS[id_country].push({'id' : countries[i]['states'][j]['id_state'], 'name' : countries[i]['states'][j]['name']});
			}
			if (typeof countries[i]['need_identification_number'] !== 'undefined')
				countriesNeedIDNumber.push(countries[i]['id_country']);
			if (typeof countries[i]['need_zip_code'] !== 'undefined')
				countriesNeedZipCode[countries[i]['id_country']] = countries[i]['zip_code_format'];
		}
	}
	countries =  countriesPS;
}

function vat_number()
{
	if ($('#company').val() != '')
		$('#vat_number').show();
	else
		$('#vat_number').hide();
}