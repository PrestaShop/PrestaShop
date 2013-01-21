/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function addVariation(id_variation)
{
	var idRow = $('#variation_table tr').length + 1;
	var newRow = '<tr id="myvar_tr_'+idRow+'">';
	newRow += '<td style="font-weight: bold;">'+writeName+'</td>';
	newRow += '<td><input type="text" value="'+((id_variation != -1) ? themes[id_variation] : '')+'" name="themevariationname_'+idRow+'" maxlength="'+name_length+'" /></td>';
	newRow += '<td><select id="myvar_'+idRow+'" name="myvar_'+idRow+'">';
	newRow += '<option value="-1">'+select_default+'</option>';
	
	var val = 0;
	while (themes[val])
	{
		newRow += '<option value="'+themes[val]+'"';
		if (id_variation == val)
			newRow += ' selected="selected"';
		newRow += '>'+themes[val]+'</option>';
		val++;
	}
	newRow += '</select></td>';
	newRow += '<td style="font-weight: bold;">'+compafrom+'</td>';
	newRow += '<td><input type="text" value="'+compatibility_from+'" name="compafrom_'+idRow+'" maxlength="'+compatibility_length+'" /></td>';
	newRow += '<td style="font-weight: bold;">'+compato+'</td>';
	newRow += '<td><input type="text" value="'+compatibility_to+'" name="compato_'+idRow+'" maxlength="'+compatibility_length+'" /></td>';
	
	if ($('#variation_table tr').length > 0)
		$('#variation_table tr:last').after(newRow);
	else
		$('#variation_table').html(newRow);
	
	$('#myvar_'+idRow).after('&nbsp;<a href="javascript:removeVariation('+$('#variation_table tr').length+');" id="my_var_remove_'+$('#variation_table tr').length+'"><img src="'+delete_img+'" title="delete" alt="delete" /></a>');
		
}

function removeVariation(id)
{

	$('#myvar_'+id).val(-1);
	$('#myvar_tr_'+id).hide();
		
}

/*
** --------------------------------------------
*/

function addDocumentation(id_variation)
{
	var idRow = $('#documentation_table tr').length + 1;
	var newRow = '<tr id="mydoc_tr_'+idRow+'">';
	newRow += '<td style="font-weight: bold;">'+writeName+'</td>';
	newRow += '<td><input type="text" value="'+doc_default_val+'" name="documentationName_'+idRow+'" maxlength="'+name_length+'" /></td>';
	newRow += '<td style="font-weight: bold;">'+path+'</td>';

	newRow += '<td><input type="file" id="mydoc_'+idRow+'" name="mydoc_'+idRow+'" /></td>';
	
	if ($('#documentation_table tr').length > 0)
		$('#documentation_table tr:last').after(newRow);
	else
		$('#documentation_table').html(newRow);
	
	$('#mydoc_'+idRow).after('&nbsp;<a href="javascript:removeDocumentation('+$('#documentation_table tr').length+');" id="my_doc_remove_'+$('#documentation_table tr').length+'"><img src="'+delete_img+'" title="delete" alt="delete" /></a>');
		
}

function removeDocumentation(id)
{
	$('#mydoc_'+id).parent().html('<input type="file" name="mydoc_'+id+'" />');
	$('#mydoc_tr_'+id).hide();
}

$('document').ready(function()
{
	if (themes.length > 0)
	{
		var count = 0;
		while (themes_id[count] != null && count < 10)
			addVariation(themes_id[count++]);
	}
	else
		addVariation(0);
	addDocumentation(0);
});