/*
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready( function () {
	
	$('#calendar_form input[type="submit"]').on('click', function (elt) {
		setDashboardDateRange(elt.currentTarget.name);
		return false;
	});

	refreshDashboard();
});

function refreshDashboard(module_name)
{
	module_list = new Array();
	
	if (typeof(module_name) == 'undefined')
	{
		$('.widget').each( function () {
			module_list.push($(this).attr('id'));
			$(this).addClass('loading');
		});
	}
	else
	{
		module_list.push(module_name);
		$('#'+module_name+' section').each( function (){
			$(this).addClass('loading');
		});
	}
	console.log(module_list);
	for (var module_id in module_list)
	{
		$.ajax({
			url : dashboard_ajax_url,
			data : {
				ajax:true,
				action:'refreshDashboard',
				module:module_list[module_id],
				dashboard_use_push:parseInt(dashboard_use_push)
				},
			dataType: 'json',
			success : function(widgets){
				for (var widget_name in widgets)
					for (data_type in widgets[widget_name])
						window[data_type](widget_name, widgets[widget_name][data_type]);

				if (parseInt(dashboard_use_push) == 1)
					refreshDashboard();
			},
			error : function(data){
				//@TODO display errors
			}
		});
	}
}

function setDashboardDateRange(action)
{
	$('#datepickerFrom, #datepickerTo').parent('.input-group').removeClass('has-error');
	data = 'ajax=true&action=setDashboardDateRange&submitDatePicker=true&'+$('#calendar_form').serialize()+'&'+action+'=1';
	$.ajax({
			url : adminstats_ajax_url,
			data : data,
			dataType: 'json',
			type: 'POST',
			success : function(jsonData){
				if (!jsonData.has_errors)
				{
					refreshDashboard();
					$('#datepickerFrom').val(jsonData.date_from);
					$('#datepickerTo').val(jsonData.date_to);
				}
				else
					$('#datepickerFrom, #datepickerTo').parent('.input-group').addClass('has-error');
			},
			error : function(data){
				//@TODO display errors
			}
		});
}

function data_value(widget_name, data)
{
	for (var data_id in data)
	{
		$('#'+data_id+' ').html(data[data_id]);
		$('#'+data_id+', #'+widget_name).closest('section').removeClass('loading');
	}
}

function data_trends(widget_name, data)
{
	for (var data_id in data)
	{
		$('#'+data_id).html(data[data_id]['value']);
		if (data[data_id]['way'] == 'down')
			$('#'+data_id).parent().removeClass('dash_trend_up').addClass('dash_trend_down');
		else
			$('#'+data_id).parent().removeClass('dash_trend_down').addClass('dash_trend_up');
		$('#'+data_id).closest('section').removeClass('loading');
	}
}

function data_table(widget_name, data)
{
	for (var data_id in data)
	{
		//fill header
		tr = '<tr>';
		for (var header in data[data_id].header)
		{
			head = data[data_id].header[header];
			th = '<th '+ (head.class ? ' class="'+head.class+'" ' : '' )+ ' '+(head.id ? ' id="'+head.id+'" ' : '' )+'>';
			th += (head.wrapper_start ? ' '+head.wrapper_start+' ' : '' );
			th += head.title;
			th += (head.wrapper_stop ? ' '+head.wrapper_stop+' ' : '' );
			th += '</th>';
			tr += th;
		}
		tr += '</tr>';
		$('#'+data_id+' thead').html(tr);
		
		//fill body
		$('#'+data_id+' tbody').html('');
		if (data[data_id].body.length)
			for (var body_content_id in data[data_id].body)
			{
				tr = '<tr>';
				for (var body_content in data[data_id].body[body_content_id])
				{
					body = data[data_id].body[body_content_id][body_content];
					td = '<td '+ (body.class ? ' class="'+body.class+'" ' : '' )+ ' '+(body.id ? ' id="'+body.id+'" ' : '' )+'>';
					td += (body.wrapper_start ? ' '+body.wrapper_start+' ' : '' );
					td += body.value;
					td += (body.wrapper_stop ? ' '+body.wrapper_stop+' ' : '' );
					td += '</td>';
					tr += td;
				}
				tr += '</tr>';
				$('#'+data_id+' tbody').append(tr);
			}
		else
			$('#'+data_id+' tbody').html('<tr><td class="text-center" colspan="'+data[data_id].header.length+'">'+no_results_translation+'</td></tr>');
	}
}