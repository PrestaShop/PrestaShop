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


$(document).ready(function() {	
	bind_inputs();
	initCarrierWizard();
});

function initCarrierWizard()
{
	$("#carrier_wizard").smartWizard({
		'labelNext' : labelNext,
		'labelPrevious' : labelPrevious,
		'labelFinish' : labelFinish,
		'fixHeight' : 1,
		'onShowStep' : onShowStepCallback,
		'onLeaveStep' : onLeaveStepCallback,
		'onFinish' : onFinishCallback,
		'transitionEffect' : 'slideleft',
		'enableAllSteps' : enableAllSteps
	});
	displayRangeType();
}

function displayRangeType()
{
	if ($('input[name="shipping_method"]:checked').val() == 1)
		string = string_weight;
	else
		string = string_price;
	
	$('.range_type').html(string);
}

function onShowStepCallback()
{
	$('.anchor li a').each( function () {
		$(this).parent('li').addClass($(this).attr('class'));
	});
	$('#carrier_logo_block').prependTo($('div.content').filter(function() { return $(this).css('display') != 'none' }).children('.defaultForm').children('fieldset'));
	resizeWizard();
}

function onFinishCallback(obj, context)
{
	$('.wizard_error').remove();
	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		dataType: 'json',
		data : $('#carrier_wizard .stepContainer .content form').serialize() + '&action=finish_step&ajax=1',
		success : function(data) {
			if (data.has_error)
			{				
				displayError(data.errors, context.fromStep);
				resizeWizard();
			}
			else
				window.location.href = carrierlist_url;
		}
	});
}

function onLeaveStepCallback(obj, context)
{
	if (context.toStep == nbr_steps)
		displaySummary();
	
	return validateSteps(context.fromStep); // return false to stay on step and true to continue navigation 
}

function displaySummary()
{
	// used as buffer - you must not replace directly in the translation vars
	var tmp;

	// Carrier name
	$('#summary_name').html($('#name').val());
	
	// Delay and pricing
	tmp = summary_translation_meta_informations.replace('@s2', '<strong>' + $('#delay_1').val() + '</strong>');
	if ($('#is_free_on').attr('checked'))
		tmp = tmp.replace('@s1', summary_translation_free);
	else
		tmp = tmp.replace('@s1', summary_translation_paid);
	$('#summary_meta_informations').html(tmp);
	
	// Tax and calculation mode for the shipping cost
	tmp = summary_translation_shipping_cost.replace('@s2', '<strong>' + $('#id_tax_rules_group option:selected').text() + '</strong>');	
	if ($('#billing_price').attr('checked'))
		tmp = tmp.replace('@s1', summary_translation_price);
	else if ($('#billing_weight').attr('checked'))
		tmp = tmp.replace('@s1', summary_translation_weight);
	else
		tmp = tmp.replace('@s1', '<strong>' + summary_translation_undefined + '</strong>');
	$('#summary_shipping_cost').html(tmp);
	
	// Weight or price ranges
	$('#summary_range').html(summary_translation_range);
	var range_inf = summary_translation_undefined;
	var range_sup = summary_translation_undefined;
	$('input[name$="range_inf[]"]').each(function(){
		if (!isNaN(parseFloat($(this).val())) && (range_inf == summary_translation_undefined || range_inf < $(this).val()))
			range_inf = $(this).val();
	});
	$('input[name$="range_sup[]"]').each(function(){
		if (!isNaN(parseFloat($(this).val())) && (range_sup == summary_translation_undefined || range_sup > $(this).val()))
			range_sup = $(this).val();
	});
	$('#summary_range').html(
		$('#summary_range').html()
		.replace('@s1', '<strong>' + range_inf + '</strong>')
		.replace('@s2', '<strong>' + range_sup + '</strong>')
		.replace('@s3', '<strong>' + $('#range_behavior option:selected').text().toLowerCase() + '</strong>')
	);
	
	// Delivery zones
	$('#summary_zones').html('');
	$('.input_zone').each(function(){
		if ($(this).attr('checked'))
			$('#summary_zones').html($('#summary_zones').html() + '<li><strong>' + $(this).parent().prev().text() + '</strong></li>');
	});
	
	// Group restrictions
	$('#summary_groups').html('');
	$('input[name$="groupBox[]"]').each(function(){
		if ($(this).attr('checked'))
			$('#summary_groups').html($('#summary_groups').html() + '<li><strong>' + $(this).parent().next().next().text() + '</strong></li>');
	});
	
	// shop restrictions
	$('#summary_shops').html('');
	$('.input_shop').each(function(){
		if ($(this).attr('checked'))
			$('#summary_shops').html($('#summary_shops').html() + '<li><strong>' + $(this).parent().text() + '</strong></li>');
	});
}

function validateSteps(step_number)
{
	$('.wizard_error').remove();
	var is_ok = true;
	form = $('#carrier_wizard #step-'+step_number+' form');
	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		dataType: 'json',
		data : form.serialize()+'&step_number='+step_number+'&action=validate_step&ajax=1',
		success : function(datas)
		{
			if (datas.has_error)
			{
				is_ok = false;
				
				$('input').focus( function () {
					$(this).removeClass('field_error');
				});
				displayError(datas.errors, step_number);
				resizeWizard();
			}
		}
	});
	return is_ok;
}

function displayError(errors, step_number)
{
	$('.wizard_error').remove();
	str_error = '<div class="error wizard_error"><span style="float:right"><a id="hideError" href="#"><img alt="X" src="../img/admin/close.png" /></a></span><ul>';
	for (var error in errors)
	{
		$('#carrier_wizard').smartWizard('setError',{stepnum:step_number,iserror:true});
		$('input[name="'+error+'"]').addClass('field_error');
		str_error += '<li>'+errors[error]+'</li>';
	}
	$('#step-'+step_number).prepend(str_error+'</ul></div>');
}

function resizeWizard()
{
	resizeInterval = setInterval(function (){$("#carrier_wizard").smartWizard('fixHeight'); clearInterval(resizeInterval)}, 100);
}

function bind_inputs()
{
	$('tr.delete_range td button').off('click').on('click', function () {
		index = $(this).parent('td').index();
		$('tr.range_sup td:eq('+index+'), tr.range_inf td:eq('+index+'), tr.fees_all td:eq('+index+'), tr.delete_range td:eq('+index+')').remove();
		$('tr.fees').each( function () {
			$(this).children('td:eq('+index+')').remove();
		});
		rebuildTabindex();
		return false;
	});
	
	$('tr.fees_all td button').off('click').on('click', function () {
		index = $(this).parent('td').index();
		if (validateRange(index))
			enableRange(index);
		else
			disableRange(index);
		return false;
	});
	
	$('tr.fees td input:checkbox').off('change').on('change', function () {
				
		if($(this).is(':checked'))
		{
			$(this).closest('tr').children('td').each( function () {
				index = $(this).index();
				if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
					$(this).children('input:text').removeAttr('disabled');
			});
		}
		else
			$(this).closest('tr').children('td').children('input:text').attr('disabled', 'disabled');
		return false;
	});
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').focus( function () {
		$(this).removeClass('field_error');
	});
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').off('change').on('change', function () {
		index = $(this).parent('td').index();
		
		if ($('tr.fees_all td:eq('+index+')').hasClass('validated') || $('tr.fees_all td:eq('+index+')').hasClass('not_validated'))
		{
			if (validateRange(index))
				enableRange(index);
			else
				disableRange(index);
		}
	});
	
	$('tr.fees_all td input').off('change').on('change', function () {
		index = $(this).parent('td').index();
		val = $(this).val();
		$('tr.fees').each( function () {
			$(this).find('td:eq('+index+') input:text:enabled').val(val);
		});
		return false;
	});
	
	$('input[name="is_free"]').on('click', function() {
		var is_free = $(this);
		$("#step_carrier_ranges .margin-form").each(function() {
			var field = $(this).children().attr('name');
			if (typeof(field) != 'undefined' && field != 'is_free' && field != 'shipping_handling')
			{
				if (parseInt(is_free.val()))
				{
					$(this).hide();
					$(this).prev().hide();
				}
				else
				{
					$(this).show();
					$(this).prev().show();
				}
			}
		});
		if (parseInt(is_free.val()))
		{
			$('#zones_table').hide();
			$('.new_range').hide();
		}
		else
		{
			$('#zones_table').show();
			$('.new_range').show();
		}
		resizeWizard();
	});
	$('input[name="is_free"]:checked').click();
	
	$('input[name="shipping_method"]').on('click', function() {
		$.ajax({
			type:"POST",
			url : validate_url,
			async: false,
			dataType: 'html',
			data : 'id_carrier='+parseInt($('#id_carrier').val())+'&shipping_method='+parseInt($(this).val())+'&action=changeRanges&ajax=1',
			success : function(data) {
				$('#zone_ranges').replaceWith(data);
				displayRangeType();
				bind_inputs();
			}
		});
	});
}

function validateRange(index)
{
	//reset error css
	$('tr.range_sup td input:text').removeClass('field_error');
	$('tr.range_inf td input:text').removeClass('field_error');
	
	is_ok = true;
	range_sup = parseInt($('tr.range_sup td:eq('+index+')').children('input:text').val().trim());
	range_inf = parseInt($('tr.range_inf td:eq('+index+')').children('input:text').val().trim());

	if (isNaN(range_sup) || range_sup.length === 0)
	{
		$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
		is_ok = false;
	}
	
	if (isNaN(range_inf) || range_inf.length === 0)
	{
		$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
		is_ok = false;
	}
	
	if (is_ok)
	{
		if (range_inf >= range_sup)
		{
			$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
			$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
			is_ok = false;
		}
		//check if previous range is inf only if it's not the first range
		if (index > 2)
		{
			previous_range_sup = parseInt($('tr.range_sup td:eq('+(index -1)+')').children('input:text').val().trim());
			if (range_inf < previous_range_sup)
			{
				$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
				is_ok = false;
			}
		}
		//check if next range is sup only if it's not the last range
		if ($('tr.range_inf td:eq('+(index + 1)+')').length)
		{
			next_range_inf = parseInt($('tr.range_inf td:eq('+(index +1)+')').children('input:text').val().trim());

			if ((isNaN(range_sup) || range_sup.length === 0) && range_sup > next_range_inf)
			{
				$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
				is_ok = false;
			}
		}
		
	}
	return is_ok;
}

function enableRange(index)
{
	$('tr.fees').each( function () {
		//only enable fees for enabled zones
		if ($(this).children('td').children('input:checkbox').attr('checked') == 'checked')
			$(this).children('td:eq('+index+')').children('input').removeAttr('disabled');
	});
	$('span.fees_all').show();
	$('tr.fees_all td:eq('+index+')').children('input').show().removeAttr('disabled');
	$('tr.fees_all td:eq('+index+')').addClass('validated').removeClass('not_validated');
	$('tr.fees_all td:eq('+index+')').children('button').remove();
}

function disableRange(index)
{
	$('tr.fees').each( function () {
		//only enable fees for enabled zones
		if ($(this).children('td').children('input:checkbox').attr('checked') == 'checked')
			$(this).children('td:eq('+index+')').children('input').attr('disabled', 'disabled');
	});
	$('tr.fees_all td:eq('+index+')').children('input').attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').removeClass('validated').addClass('not_validated');
}

function add_new_range()
{
	last_sup_val = $('tr.range_sup td:last input').val();
	//add new rand sup input
	$('tr.range_sup td:last').after('<td class="center"><input name="range_sup[]" type="text" /><sup>*</sup></td>');
	//add new rand inf input
	$('tr.range_inf td:last').after('<td class="border_bottom center"><input name="range_inf[]" type="text" value="'+last_sup_val+'" /><sup>*</sup></td>');
	
	$('tr.fees_all td:last').after('<td class="center border_top border_bottom"><input style="display:none" type="text" /> <button class="button">'+labelValidate+'</button</td>');

	$('tr.fees').each( function () {
		$(this).children('td:last').after('<td class="center"><input disabled="disabled" name="fees['+$(this).data('zoneid')+'][]" type="text" /></td>');
	});
	$('tr.delete_range td:last').after('<td class="center"><button class="button">'+labelDelete+'</button</td>');
	
	resizeWizard();
	bind_inputs();
	rebuildTabindex();

	return false;
}

function delete_new_range()
{
	if ($('#new_range_form_placeholder').children('td').length = 1)
		return false;
}


function rebuildTabindex()
{
	i = 1;
	zones_nbr +=3; // corresponds to the third input text (max, min and all)
	$('#zones_table tr').each( function () 
	{
		$(this).children('td').each( function () 
		{
			if ($(this).index() > 2)
				j = i + zones_nbr;
			else if ($(this).index() == 2)
				j = i;

			if ($(this).index() >= 2 && $(this).find('input'))
				$(this).find('input').attr('tabindex', j);				
		});
		i ++;
	});
}