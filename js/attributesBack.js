/*
* 2007-2011 PrestaShop
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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var storeUsedGroups = {};

function fillCombination(wholesale_price, price_impact, weight_impact, unit_impact, reference,
ean, quantity, image, old_attr, id_product_attribute, default_attribute, eco_tax, upc, minimal_quantity, available_date,
virtual_product_name_attribute, virtual_product_filename_attribute, virtual_product_nb_downloable, virtual_product_expiration_date_attribute, 
virtual_product_nb_days, is_shareable)
{
	var link = '';
	init_elems();
	$('#stock_mvt_attribute').show();
	$('#initial_stock_attribute').hide();
	$('#attribute_quantity').html(quantity);
	$('#attribute_quantity').show();
	$('#attr_qty_stock').show();

	$('#attribute_minimal_quantity').val(minimal_quantity);

	getE('attribute_reference').value = reference;
	
	getE('virtual_product_name_attribute').value = virtual_product_name_attribute;
	getE('virtual_product_nb_downloable_attribute').value = virtual_product_nb_downloable;
	getE('virtual_product_expiration_date_attribute').value = virtual_product_nb_downloable;
	getE('virtual_product_expiration_date_attribute').value = virtual_product_expiration_date_attribute;
	getE('virtual_product_nb_days_attribute').value = virtual_product_nb_days;
	
	getE('attribute_ean13').value = ean;
	getE('attribute_upc').value = upc;
	getE('attribute_wholesale_price').value = Math.abs(wholesale_price);
	getE('attribute_price').value = ps_round(Math.abs(price_impact), 2);
	getE('attribute_weight').value = Math.abs(weight_impact);
	getE('attribute_unity').value = Math.abs(unit_impact);
	if ($('#attribute_ecotax').length != 0)
		getE('attribute_ecotax').value = eco_tax;

	if (default_attribute == 1)
		getE('attribute_default').checked = true;
	else
		getE('attribute_default').checked = false;

	if (price_impact < 0)
	{
		getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = -1;
		getE('attribute_price_impact').selectedIndex = 2;
	}
	else if (!price_impact)
	{
		getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = 0;
		getE('attribute_price_impact').selectedIndex = 0;
	}
	else if (price_impact > 0)
	{
		getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = 1;
		getE('attribute_price_impact').selectedIndex = 1;
	}
	if (weight_impact < 0)
	{
		getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = -1;
		getE('attribute_weight_impact').selectedIndex = 2;
	}
	else if (!weight_impact)
	{
		getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = 0;
		getE('attribute_weight_impact').selectedIndex = 0;
	}
	else if (weight_impact > 0)
	{
		getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = 1;
		getE('attribute_weight_impact').selectedIndex = 1;
	}
	if (unit_impact < 0)
	{
		getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = -1;
		getE('attribute_unit_impact').selectedIndex = 2;
	}
	else if (!unit_impact)
	{
		getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = 0;
		getE('attribute_unit_impact').selectedIndex = 0;
	}
	else if (unit_impact > 0)
	{
		getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = 1;
		getE('attribute_unit_impact').selectedIndex = 1;
	}

	if (is_shareable > 0)
		$("#virtual_product_is_shareable_attribute").attr("checked", "checked");
	
	if (id_product_attribute != '' && virtual_product_filename_attribute != '')
		$("#gethtmlink").show();
	link = $("#make_downloadable_product_attribute").attr('href');		
	$("#make_downloadable_product_attribute").attr('href', link+"&id_product_attribute="+id_product_attribute);

	$("#virtual_product_filename_attribute").val(virtual_product_filename_attribute);
	$("#add_new_combination").show();

	/* Reset all combination images */
	combinationImages = $('#id_image_attr').find("input[id^=id_image_attr_]");
	combinationImages.each(function() {
		this.checked = false;
	});

	/* Check combination images */
	if (typeof(combination_images[id_product_attribute]) != 'undefined')
		for (i = 0; i < combination_images[id_product_attribute].length; i++)
			$('#id_image_attr_' + combination_images[id_product_attribute][i]).attr('checked', 'checked');
	check_impact();
	check_weight_impact();
	check_unit_impact();

	var elem = getE('product_att_list');

	for (var i = 0; i < old_attr.length; i++)
	{
		var opt = document.createElement('option');
		opt.text = old_attr[i++];
		opt.value = old_attr[i];
		try {
			elem.add(opt, null);
		}
		catch(ex) {
			elem.add(opt);
		}
	}
	getE('id_product_attribute').value = id_product_attribute;

	$('#available_date_attribute').val(available_date);
}

function populate_attrs()
{
	var attr_group = getE('attribute_group');
	if (!attr_group)
		return;
	var attr_name = getE('attribute');
	var number = attr_group.options.length ? attr_group.options[attr_group.selectedIndex].value : 0;

	if (!number)
	{
		attr_name.options.length = 0;
		attr_name.options[0] = new Option('---', 0);
		return;
	}

	var list = attrs[number];
	attr_name.options.length = 0;

	for(i = 0; i < list.length; i += 2)
		attr_name.options[i / 2] = new Option(list[i + 1], list[i]);
}

function check_impact()
{
	if ($('#attribute_price_impact').get(0).selectedIndex == 0)
	{
		$('#attribute_price').val('0.00');
		$('#span_impact').hide();
	}
	else
		$('#span_impact').show();
}

function check_weight_impact()
{
	if ($('#attribute_weight_impact').get(0).selectedIndex == 0)
	{
		$('#span_weight_impact').hide();
		$('#attribute_weight').val('0.00');
	}
	else
		$('#span_weight_impact').show();
}

function check_unit_impact()
{
	if ($('#attribute_unit_impact').get(0).selectedIndex == 0)
	{
		$('#span_unit_impact').hide();
		$('#attribute_unity').val('0.00');
	}
	else
		$('#span_unit_impact').show();
}

function init_elems()
{
	var elem = getE('product_att_list');

	if (elem.length)
		for (i = elem.length - 1; i >= 0; i--)
			if (elem[i])
				elem.remove(i);

	$('input[name="id_image_attr[]"]').each(function (){
		$(this).attr('checked', false);
	});
	
	$('#attribute_default').attr('checked', false);

	getE('attribute_price_impact').selectedIndex = 0;
	getE('attribute_weight_impact').selectedIndex = 0;
	getE('attribute_unit_impact').selectedIndex = 0;
	$('#span_unit_impact').hide();
	$('#unity_third').html($('#unity_second').html());

	if ($('#unity').is())
		if ($('#unity').get(0).value.length > 0)
			$('#tr_unit_impact').show();
		else
			$('#tr_unit_impact').hide();
	try
	{
		if (impact.options[impact.selectedIndex].value == 0)
			$('#span_impact').hide();
		if (impact2.options[impact.selectedIndex].value == 0)
			getE('span_weight_impact').style.display = 'none';
	}
	catch (e)
	{
		$('#span_impact').hide();
		getE('span_weight_impact').style.display = 'none';
	}
}

function attr_selectall()
{
	var elem = getE('product_att_list');
	if (elem)
	{
		var i;
		for (i = 0; i < elem.length; i++)
			elem.options[i].selected = true;
	}
}

function del_attr_multiple()
{
	var attr = getE('attribute_group');

	if (!attr)
		return ;
	var length = attr.length;
	var target;

	for (var i = 0; i < length; ++i)
	{
		elem = attr.options[i];
		if (elem.selected)
		{
			target = getE('table_' + elem.parentNode.getAttribute('name'));
			if (target && getE('result_' + elem.getAttribute('name')))
			{
				target.removeChild(getE('result_' + elem.getAttribute('name')));
				if (!target.lastChild || !target.lastChild.id)
					toggle(target.parentNode, false);
			}
		}
	}
}

function create_attribute_row(id, id_group, name, price, weight)
{
	var html = '';
	html += '<tr id="result_'+id+'">';
	html += 		'<td><input type="hidden" value="'+id+'" name="options['+id_group+']['+id+']" />'+name+'</td>';
	html += 		'<td>'+i18n_tax_exc+'<input id="related_to_price_impact_ti_'+id+'" class="price_impact" style="width:50px" type="text" value="'+price+'" name="price_impact_'+id+'" onkeyup="calcPrice($(this), false)"></td>';
	html += 		'<td>'+i18n_tax_inc+'<input id="related_to_price_impact_'+id+'" class="price_impact_ti" style="width:50px" type="text" value="" name="price_impact_ti_'+id+'" onkeyup="calcPrice($(this), true)"></td>';
	html += 		'<td><input style="width:50px" type="text" value="'+weight+'" name="weight_impact['+id+']"></td>';
	html += '</tr>';

	return html;
}

function add_attr_multiple()
{
	var attr = getE('attribute_group');
	if (!attr)
		return ;
	var length = attr.length;
	var target;
	var new_elem;

	for (var i = 0; i < length; ++i)
	{
		elem = attr.options[i];
		if (elem.selected)
		{
			name = elem.parentNode.getAttribute('name');
			target = $('#table_' + name);
			if (target && !getE('result_' + elem.getAttribute('name')))
			{
				new_elem = create_attribute_row(elem.getAttribute('name'), elem.parentNode.getAttribute('name'), elem.value, '0.00', '0.00');
				target.append(new_elem);
				toggle(target.parent()[0], true);
			}
		}
	}
}

/**
 * Delete one or several attributes from the declination multilist
 */
function del_attr()
{
	$('#product_att_list option:selected').each(function()
	{
		delete storeUsedGroups[$(this).attr('groupid')];
		$(this).remove();
	});
}

/**
 * Add an attribute from a group in the declination multilist
 */
function add_attr()
{
	var attr_group = $('#attribute_group option:selected');
	if (attr_group.val() == 0)
		return jAlert(msg_combination_1);

	var attr_name = $('#attribute option:selected');
	if (attr_name.val() == 0)
		return jAlert(msg_combination_2);
	
	if (attr_group.val() in storeUsedGroups)
		return jAlert(msg_combination_3);

	storeUsedGroups[attr_group.val()] = true;
	$('<option></option>')
		.attr('value', attr_name.val())
		.attr('groupid', attr_group.val())
		.text(attr_group.text() + ' : ' + attr_name.text())
		.appendTo('#product_att_list');
}

function openCloseLayer(whichLayer)
{
	if (document.getElementById)
		var style = document.getElementById(whichLayer).style;
	else if (document.all)
		var style = document.all[whichLayer].style;
	else if (document.layers)
		var style = document.layers[whichLayer].style;
	style.display = style.display == 'none' ? 'block' : 'none';
}

$(document).ready(function(){
	$('#product_form').submit(function(){
		attr_selectall();
		// If the new combination form is hidden, remove it so that empty fields are not submitted
		if ($('#add_new_combination').is(':hidden'))
			$('#add_new_combination').remove();
	});
});