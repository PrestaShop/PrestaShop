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
	refreshDashbard();
});


function refreshDashbard()
{
	datas = {
		ajax:true,
		action:'refreshDashboard'
		};
	$.ajax({
		url : dashboard_ajax_url,
		data : datas,
		dataType: 'json',
		success : function(widgets){
			for (var name in widgets)
				for (data_type in widgets[name])
					window[data_type](widgets[name][data_type]);
		},
		error : function(data){
			alert("[TECHNICAL ERROR]");
		}
	});
}

function data_value(datas)
{
	for (var data_id in datas)
		$('#'+data_id).html(datas[data_id]);
}

function data_trends(datas)
{
	for (var data_id in datas)
	{
		$('#'+data_id).html(datas[data_id]['value']);
		if (datas[data_id]['way'] == 'down')
			$('#'+data_id).parent().removeClass('dash_trend_up').addClass('dash_trend_down');
		else
			$('#'+data_id).parent().removeClass('dash_trend_down').addClass('dash_trend_up');
	}
	
}