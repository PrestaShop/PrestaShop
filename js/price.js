/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function getTax()
{
	if (noTax)
		return 0;
	var selectedTax = document.getElementById('id_tax_rules_group');
	var taxId = selectedTax.options[selectedTax.selectedIndex].value;
	return taxesArray[taxId];
}

function getEcotaxTaxIncluded()
{
	return ps_round(ecotax_tax_excl * (1 + ecotaxTaxRate), 2);
}

function getEcotaxTaxExcluded()
{
	return ecotax_tax_excl;
}

function formatPrice(price)
{
	var fixedToSix = (Math.round(price * 1000000) / 1000000);
	return (Math.round(fixedToSix) == fixedToSix + 0.000001 ? fixedToSix + 0.000001 : fixedToSix);
}

function calcPrice()
{
	var priceType = $('#priceType').val();
	if (priceType == 'TE')
		calcPriceTI();
	else
		calcPriceTE();
}

function calcPriceTI()
{
	var tax = getTax();
	var priceTE = parseFloat(document.getElementById('priceTEReal').value.replace(/,/g, '.'));
	var newPrice = priceTE * ((tax / 100) + 1);
	document.getElementById('priceTI').value = (isNaN(newPrice) == true || newPrice < 0) ? '' :
		ps_round(newPrice, 2);
	document.getElementById('finalPrice').innerHTML = (isNaN(newPrice) == true || newPrice < 0) ? '' :
		ps_round(newPrice, 2).toFixed(2);
	document.getElementById('finalPriceWithoutTax').innerHTML = (isNaN(priceTE) == true || priceTE < 0) ? '' :
		(ps_round(priceTE, 2) + getEcotaxTaxExcluded()).toFixed(2);
	calcReduction();
	$('#priceTI').val((parseFloat($('#priceTI').val()) + getEcotaxTaxIncluded()).toFixed(2));
	$('#finalPrice').html(parseFloat($('#priceTI').val()).toFixed(2));
}

function calcPriceTE()
{
	ecotax_tax_excl =  $('#ecotax').val() / (1 + ecotaxTaxRate);
	var tax = getTax();
	var priceTI = parseFloat(document.getElementById('priceTI').value.replace(/,/g, '.'));
	var newPrice = ps_round(priceTI - getEcotaxTaxIncluded(), 2) / ((tax / 100) + 1);
	document.getElementById('priceTE').value = (isNaN(newPrice) == true || newPrice < 0) ? '' :
		ps_round(newPrice.toFixed(6), 6);
	document.getElementById('priceTEReal').value = (isNaN(newPrice) == true || newPrice < 0) ? 0 : ps_round(newPrice, 9);
	document.getElementById('finalPrice').innerHTML = (isNaN(newPrice) == true || newPrice < 0) ? '' :
		ps_round(priceTI.toFixed(6), 6);
	document.getElementById('finalPriceWithoutTax').innerHTML = (isNaN(newPrice) == true || newPrice < 0) ? '' :
		ps_round(newPrice.toFixed(6), 6) + getEcotaxTaxExcluded();
	calcReduction();
}

function calcImpactPriceTI()
{
	var tax = getTax();
	var priceTE = parseFloat(document.getElementById('attribute_priceTEReal').value.replace(/,/g, '.'));
	var newPrice = priceTE * ((tax / 100) + 1);
	$('#attribute_priceTI').val((isNaN(newPrice) == true || newPrice < 0) ? '' : ps_round(newPrice, 2).toFixed(2));
	var total = ps_round((parseFloat($('#attribute_priceTI').val())*parseInt($('#attribute_price_impact').val())+parseFloat($('#finalPrice').html())), 2);
	if (isNaN(total) || total < 0)
		$('#attribute_new_total_price').html('0.00');
	else
		$('#attribute_new_total_price').html(total);
}

function calcImpactPriceTE()
{
	var tax = getTax();
	var priceTI = parseFloat(document.getElementById('attribute_priceTI').value.replace(/,/g, '.'));
	priceTI = (isNaN(priceTI)) ? 0 : ps_round(priceTI);
	var newPrice = ps_round(priceTI, 2) / ((tax / 100) + 1);
	$('#attribute_price').val((isNaN(newPrice) == true || newPrice < 0) ? '' : ps_round(newPrice, 6).toFixed(6));
	$('#attribute_priceTEReal').val((isNaN(newPrice) == true || newPrice < 0) ? 0 : ps_round(newPrice, 9));
	var total = ps_round((parseFloat($('#attribute_priceTI').val())*parseInt($('#attribute_price_impact').val())+parseFloat($('#finalPrice').html())), 2);
	if (isNaN(total) || total < 0)
		$('#attribute_new_total_price').html('0.00');
	else
		$('#attribute_new_total_price').html(total);
}

function calcReduction()
{
	if (parseFloat($('#reduction_price').val()) > 0)
		reductionPrice();
	else if (parseFloat($('#reduction_percent').val()) > 0)
		reductionPercent();
}

function reductionPrice()
{
	var tax = getTax();
	var price    = document.getElementById('priceTI');
	var priceWhithoutTaxes = document.getElementById('priceTE');
	var newprice = document.getElementById('finalPrice');
	var newpriceWithoutTax = document.getElementById('finalPriceWithoutTax');
	var curPrice = price.value;

	document.getElementById('reduction_percent').value = 0;
	if (isInReductionPeriod())
	{
		var rprice = document.getElementById('reduction_price');
		if (parseFloat(curPrice) <= parseFloat(rprice.value))
			rprice.value = curPrice;
		if (parseFloat(rprice.value) < 0 || isNaN(parseFloat(curPrice)))
			rprice.value = 0;
		curPrice = curPrice - rprice.value;
	}

	newprice.innerHTML = (ps_round(parseFloat(curPrice),2) + getEcotaxTaxIncluded()).toFixed(2);
	var rpriceWithoutTaxes = ps_round(rprice.value / ((tax / 100) + 1), 2);
	newpriceWithoutTax.innerHTML = ps_round(priceWhithoutTaxes.value - rpriceWithoutTaxes,2).toFixed(2);
}

function reductionPercent()
{
	var tax = getTax();
	var price    = document.getElementById('priceTI');
	var newprice = document.getElementById('finalPrice');
	var newpriceWithoutTax = document.getElementById('finalPriceWithoutTax');
	var curPrice = price.value;

	document.getElementById('reduction_price').value = 0;
	if (isInReductionPeriod())
	{
		var newprice = document.getElementById('finalPrice');
		var rpercent = document.getElementById('reduction_percent');

		if (parseFloat(rpercent.value) >= 100)
			rpercent.value = 100;
		if (parseFloat(rpercent.value) < 0)
			rpercent.value = 0;
		curPrice = price.value * (1 - (rpercent.value / 100));
	}

	newprice.innerHTML = (ps_round(parseFloat(curPrice),2) + getEcotaxTaxIncluded()).toFixed(2);
	newpriceWithoutTax.innerHTML = ps_round(parseFloat(ps_round(curPrice, 2) / ((tax / 100) + 1)),2).toFixed(2);
}

function isInReductionPeriod()
{
	var start  = document.getElementById('reduction_from').value;
	var end    = document.getElementById('reduction_to').value;

	if (start == end && start != "" && start != "0000-00-00 00:00:00") return true;

	var sdate  = new Date(start.replace(/-/g,'/'));
	var edate  = new Date(end.replace(/-/g,'/'));
	var today  = new Date();

	return (sdate <= today && edate >= today);
}

function decimalTruncate(source, decimals)
{
	if (typeof(decimals) == 'undefined')
		decimals = 6;
	source = source.toString();
	var pos = source.indexOf('.');
	return parseFloat(source.substr(0, pos + decimals + 1));
}

function unitPriceWithTax(type)
{
	var tax = getTax();
	var priceWithTax = parseFloat(document.getElementById(type+'_price').value.replace(/,/g, '.'));
	var newPrice = priceWithTax * ((tax / 100) + 1);
	$('#'+type+'_price_with_tax').html((isNaN(newPrice) == true || newPrice < 0) ? '0.00' : ps_round(newPrice, 2).toFixed(2));
}

function unitySecond()
{
	$('#unity_second').html($('#unity').val());
	if ($('#unity').get(0).value.length > 0)
	{
		$('#unity_third').html($('#unity').val());
		$('#tr_unit_impact').show();
	}
	else
		$('#tr_unit_impact').hide();
}

function changeCurrencySpecificPrice(index)
{
	var id_currency = $('#spm_currency_' + index).val();
	if (currencies[id_currency]["format"] == 2 || currencies[id_currency]["format"] == 4)
	{
		$('#spm_currency_sign_pre_' + index).html('');
		$('#spm_currency_sign_post_' + index).html(' ' + currencies[id_currency]["sign"]);
	}
	else if (currencies[id_currency]["format"] == 1 || currencies[id_currency]["format"] == 3)
	{
		$('#spm_currency_sign_post_' + index).html('');
		$('#spm_currency_sign_pre_' + index).html(currencies[id_currency]["sign"] + ' ');
	}
}

