/*
* 2007-2013 PrestaShop
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

var query;
var lang = Array();

function setLang(array_lang) { lang = array_lang; }

function getQuery() {
 	var result;
 	
 	result = query;
 	if (result == null) {
 		if (window.XMLHttpRequest)
 			result = new XMLHttpRequest();
 		else if (window.ActiveXObject)
		 	result = new ActiveXObject('Microsoft.XMLHTTP');
 	}
 	return result;
}

function onQueryChange() {
 	if (query.readyState == 4 && query.status == 200)
 		document.getElementById('ajax_confirmation').innerHTML = '<span class="green bold">'+lang[0]+'</span>';
}

function request_failed() { alert(lang[1]); }

function showActivity() {
 	document.getElementById('ajax_confirmation').innerHTML = '<span class="bold">'+lang[2]+'</span>';
}

function check_for_all_accesses(tabsize, tabnumber)
{
	var i = 0;
	var res = 0;
	var right = 0;
	var rights = new Array('view', 'add', 'edit', 'delete', 'all'); 

	while (i != parseInt(tabsize) + 1)
	{
		if ($('#view'+i).prop('checked') == false || $('#edit'+i).prop('checked') == false || $('#add'+i).prop('checked') == false || $('#delete'+i).prop('checked') == false)
			$('#all'+i).attr('checked', false);
		else
			$('#all'+i).attr('checked', "checked");
		i++;
	}
	right = 0;
	while (right != 5)
	{
		res = 0;
		i = 0;
		while (i != tabsize)
		{
			if ($('#'+rights[right]+i).prop('checked') == true)
				res++;
			i++;
		}
		if (res == tabnumber - 1)
			$('#'+rights[right]+'all').attr('checked', "checked");
		else
			$('#'+rights[right]+'all').attr('checked', false);
		right++;
	}
}

function perfect_access_js_gestion(src, action, id_tab, tabsize, tabnumber, table)
{
 	if (id_tab == '-1' && action == 'all')
 	{
 		$(table+' .add').attr('checked', src.checked);
 		$(table+' .edit').attr('checked', src.checked);
 		$(table+' .delete').attr('checked', src.checked);
		$(table+' .view').attr('checked', src.checked);
		$(table+' .all').attr('checked', src.checked);
 	}
	else if (action == 'all')
		$(table+' .'+id_tab).attr('checked', src.checked);
 	else if (id_tab == '-1')
 		$(table+' .'+action).attr('checked', src.checked);
	check_for_all_accesses(tabsize, tabnumber);
}

function ajax_power(src, action, id_tab, id_profile, token, tabsize, tabnumber)
{
	query = getQuery();
	perfect_access_js_gestion(src, action, id_tab, tabsize, tabnumber);
	if (query != null) {
	 	try {
		 	query.open('POST', 'index.php?tab=AdminAccess', true);
		 	query.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		 	query.onreadystatechange = onQueryChange;
		 	query.send('submitAddaccess=1&action='+action+'&perm='+parseInt(src.checked ? '1' : status = '0')+'&id_tab='+parseInt(id_tab)+'&id_profile='+parseInt(id_profile)+'&token='+token);
		 	showActivity();
		}
		catch(exc) {
			request_failed();
		}
	}
	else
		alert(lang[3]);
}

function redirect(new_page) { window.location = new_page; }