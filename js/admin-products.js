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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function addPackItem()
{
	if ($('#curPackItemId').val() == '' || $('#curPackItemName').val() == '')
	{
		alert(msg_select_one);
		return false;
	}
	else if ($('#curPackItemId').val() == '' || $('#curPackItemQty').val() == '')
	{
		alert(msg_set_quantity);
		return false;
	}

	var lineDisplay = $('#curPackItemQty').val()+ 'x ' +$('#curPackItemName').val();

	var divContent = $('#divPackItems').html();
	divContent += lineDisplay;
	divContent += '<span onclick="delPackItem(' + $('#curPackItemId').val() + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';

	// QTYxID-QTYxID
	var line = $('#curPackItemQty').val()+ 'x' +$('#curPackItemId').val();


	$('#inputPackItems').val($('#inputPackItems').val() + line  + '-');
	$('#divPackItems').html(divContent);
		$('#namePackItems').val($('#namePackItems').val() + lineDisplay + 'Â¤');

	$('#curPackItemId').val('');
	$('#curPackItemName').val('');

	$('#curPackItemName').setOptions({
		extraParams: {
			excludeIds :  getSelectedIds()
		}
	});
}

function delPackItem(id)
{
	var reg = new RegExp('-', 'g');
	var regx = new RegExp('x', 'g');

	var div = getE('divPackItems');
	var input = getE('inputPackItems');
	var name = getE('namePackItems');
	var select = getE('curPackItemId');
	var select_quantity = getE('curPackItemQty');

	var inputCut = input.value.split(reg);
	var nameCut = name.value.split(new RegExp('¤', 'g'));

	input.value = '';
	name.value = '';
	div.innerHTML = '';

	for (var i = 0; i < inputCut.length; ++i)
		if (inputCut[i])
		{
			var inputQty = inputCut[i].split(regx);
			if (inputQty[1] != id)
			{
				input.value += inputCut[i] + '-';
				name.value += nameCut[i] + '¤';
				div.innerHTML += nameCut[i] + ' <span onclick="delPackItem(' + inputQty[1] + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
			}
		}

	$('#curPackItemName').setOptions({
		extraParams: {
			excludeIds :  getSelectedIds()
		}
	});
}

/* function autocomplete */
urlToCall = null;
function getSelectedIds()
{
	// input lines QTY x ID-
	var ids = id_product + ',';
	ids += $('#inputPackItems').val().replace(/\\d+x/g, '').replace(/\-/g,',');
	ids = ids.replace(/\,$/,'');
	return ids;
}

$(document).ready(function() {
	updateCurrentText();
	updateFriendlyURL();
	$.ajax({
		url: 'ajax-tab.php',
		cache: false,
		dataType: 'json',
		data: {
			ajaxProductManufacturers:"1",
			ajax : '1',
			token : token,
			controller : 'AdminProducts',
			action : 'productManufacturers',
		},
		success: function(j) {
			var options = $('select#id_manufacturer').html();
			if (j)
			for (var i = 0; i < j.length; i++)
				options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
			$("select#id_manufacturer").replaceWith("<select id=\"id_manufacturer\">"+options+"</select>");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
		}

	});

	$(function() {
		$('#curPackItemName')
			.autocomplete('ajax_products_list.php', {
				delay: 100,
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				// param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete
				multipleSeparator:'||',
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result(function(event, item){
				$('#curPackItemId').val(item[1]);
			});
			$('#curPackItemName').setOptions({
				extraParams: {
					excludeIds : getSelectedIds(), excludeVirtuals : 1
				}
			});
	});
	
	$("#is_virtual_good").change(function(e)
	{
		$(".toggleVirtualPhysicalProduct").toggle();
	});


	if ($("#is_virtual_good").attr("checked"))
	{
		$("#virtual_good").show();
		$("#virtual_good_more").show();
	}

	if ( $("input[name=is_virtual_file]:checked").val() == 1)
	{
		$("#virtual_good_more").show();
		$("#virtual_good_attributes").show();
		$("#is_virtual_file_product").show();
	}
	else
	{
		$("#virtual_good_more").hide();
		$("#virtual_good_attributes").hide();
		$("#is_virtual_file_product").hide();
	}

	$("input[name=is_virtual_file]").live("change", function() {
		if($(this).val() == "1")
		{
			$("#virtual_good_more").show();
			$("#virtual_good_attributes").show();
			$("#is_virtual_file_product").show();
		}
		else
		{
			$("#virtual_good_more").hide();
			$("#virtual_good_attributes").hide();
			$("#is_virtual_file_product").hide();
		}
	});

	$("input[name=is_virtual_good]").live("change", function() {
		if($(this).attr("checked"))
		{
			$("#is_virtual").val(1);
		}
		else
		{
			$("#is_virtual").val(0);
		}
	});

});