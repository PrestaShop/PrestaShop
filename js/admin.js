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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!id_language)
	var id_language = Number(1);

function str2url(str,encoding,ucfirst)
{
	str = str.toUpperCase();
	str = str.toLowerCase();

	str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
	str = str.replace(/[\u00E7\u010D\u0107\u0106]/g,'c');
	str = str.replace(/[\u010F]/g,'d');
	str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g,'e');
	str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
	str = str.replace(/[\u0142\u0141]/g,'l');
	str = str.replace(/[\u00F1\u0148]/g,'n');
	str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g,'o');
	str = str.replace(/[\u0159]/g,'r');
	str = str.replace(/[\u015B\u015A\u0161]/g,'s');
	str = str.replace(/[\u00DF]/g,'ss');
	str = str.replace(/[\u0165]/g,'t');
	str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g,'u');
	str = str.replace(/[\u00FD\u00FF]/g,'y');
	str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g,'z');
	str = str.replace(/[\u00E6]/g,'ae');
	str = str.replace(/[\u0153]/g,'oe');
	str = str.replace(/[\u013E\u013A]/g,'l');
	str = str.replace(/[\u0155]/g,'r');

	str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
	str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
	str = str.replace(/[ ]/g,'-');
	str = str.replace(/[\/]/g,'-');

	if (ucfirst == 1) {
		c = str.charAt(0);
		str = c.toUpperCase()+str.slice(1);
	}

	return str;
}

function strToAltImgAttr(str,encoding,ucfirst)
{
	str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
	str = str.replace(/[\u00E7\u010D\u0107\u0106]/g,'c');
	str = str.replace(/[\u010F]/g,'d');
	str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g,'e');
	str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
	str = str.replace(/[\u0142\u0141]/g,'l');
	str = str.replace(/[\u00F1\u0148]/g,'n');
	str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g,'o');
	str = str.replace(/[\u0159]/g,'r');
	str = str.replace(/[\u015B\u015A\u0161]/g,'s');
	str = str.replace(/[\u00DF]/g,'ss');
	str = str.replace(/[\u0165]/g,'t');
	str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g,'u');
	str = str.replace(/[\u00FD\u00FF]/g,'y');
	str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g,'z');
	str = str.replace(/[\u00E6]/g,'ae');
	str = str.replace(/[\u0153]/g,'oe');
	str = str.replace(/[\u013E\u013A]/g,'l');
	str = str.replace(/[\u0155]/g,'r');

	str = str.replace(/[^a-zA-Z0-9\s\'\:\/\[\]-]/g,'');
	str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');

	if (ucfirst == 1) {
		c = str.charAt(0);
		str = c.toUpperCase()+str.slice(1);
	}

	return str;
}

function copy2friendlyURL()
{
	$('#link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8'));
}

function copyMeta2friendlyURL()
{
	$('#input_link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8'));
}

function updateCurrentText()
{
	$('#current_product').html($('#name_' + id_language).val());
}
function updateFriendlyURLByName()
{
	$('#link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val(), 'UTF-8'));
	$('#friendly-url').html($('#link_rewrite_' + id_language).val());
}
function updateFriendlyURL()
{
	$('#link_rewrite_' + id_language).val(str2url($('#link_rewrite_' + id_language).val(), 'UTF-8'));
	$('#seo #friendly-url').text($('#link_rewrite_' + id_language).val());
}

function toggleLanguageFlags(elt)
{
	$(elt).parents('.displayed_flag').siblings('.language_flags').toggle();
}

// Kept for retrocompatibility only (out of AdminProducts & AdminCategories)
function changeLanguage(field, fieldsString, id_language_new, iso_code)
{
	var fields = fieldsString.split('¤');
	for (var i = 0; i < fields.length; ++i)
	{
		getE(fields[i] + '_' + id_language).style.display = 'none';
		getE(fields[i] + '_' + id_language_new).style.display = 'block';
		getE('language_current_' + fields[i]).src = '../img/l/' + id_language_new + '.jpg';
	}
 	getE('languages_' + field).style.display = 'none';
	id_language = id_language_new;
}

function changeFormLanguage(id_language_new, iso_code, employee_cookie)
{
	$('.translatable').each(function() {
		$(this).find('.lang_' + id_language_new)
			.show()
			.siblings('div:not(.displayed_flag):not(.clear)').hide();
		$('.language_current').attr('src', '../img/l/' + id_language_new + '.jpg');
	});
	$('.language_flags').hide();
	if (employee_cookie)
		$.post("ajax.php", { form_language_id: id_language_new });
	id_language = id_language_new;
	updateFriendlyURL();
	updateCurrentText();
}

function displayFlags(languages, defaultLanguageID, employee_cookie)
{
	if ($('.translatable'))
	{
		$('.translatable').each(function() {
			if (!$(this).find('.displayed_flag').length > 0) {
				$.each(languages, function(key, language) {
					if (language['id_lang'] == defaultLanguageID)
					{
						defaultLanguage = language;
						return false;
					}
				});
				var displayFlags = $('<div></div>')
					.addClass('displayed_flag')
					.append($('<img>')
						.addClass('language_current')
						.addClass('pointer')
						.attr('src', '../img/l/' + defaultLanguage['id_lang'] + '.jpg')
						.attr('alt', defaultLanguage['name'])
						.click(function() {
							toggleLanguageFlags(this);
						})
					);
				var languagesFlags = $('<div></div>')
					.addClass('language_flags')
					.html('Choose language:<br /><br />');
				$.each(languages, function(key, language) {
					var img = $('<img>')
						.addClass('pointer')
						.css('margin', '0 2px')
						.attr('src', '../img/l/' + language['id_lang'] + '.jpg')
						.attr('alt', language['name'])
						.click(function() {
							changeFormLanguage(language['id_lang'], language['iso_code'], employee_cookie);
						});
					languagesFlags.append(img);
				});
				if ($(this).find('p:last-child').hasClass('clear'))
					$(this).find('p:last-child').before(displayFlags).before(languagesFlags);
				else
					$(this).append(displayFlags).append(languagesFlags);
			}
		});
	}
}

function checkAll(pForm)
{
	for (i = 0, n = pForm.elements.length; i < n; i++)
	{
		var objName = pForm.elements[i].name;
		var objType = pForm.elements[i].type;
		if (objType = 'checkbox' && objName != 'checkme')
		{
			box = eval(pForm.elements[i]);
			box.checked = !box.checked;
		}
	}
}

function checkDelBoxes(pForm, boxName, parent)
{
	for (i = 0; i < pForm.elements.length; i++)
		if (pForm.elements[i].name == boxName)
			pForm.elements[i].checked = parent;
}

function checkPaymentBoxes(name, module)
{
	setPaymentBoxes(name, module);
	current = $('input#checkedBox_'+ name +'_'+ module + '[type=hidden]');
	$('form#form_'+ name +' input[type=checkbox]').each(
		function()
		{
			if ($(this).attr('name') == module + '_' + name + '[]')
				$(this).attr("checked", ((current.val() == 'checked') ? true : false));
		}
	);
	current.val() == 'checked' ? current.val('unchecked') : current.val('checked');
}

function setPaymentBoxes(name, module)
{
	current = $('input#checkedBox_'+ name +'_'+ module + '[type=hidden]');
	total = 0;
	checked = 0;
	$('form#form_'+ name +' input[type=checkbox]').each(
		function()
		{
			if ($(this).attr('name') == module + '_' + name + '[]')
			{
				($(this).attr("checked") ? checked++ : '');
				total++;
			}
		}
	);
	(checked == total) ? current.val('unchecked') : current.val('checked');
}

function getE(name)
{
	if (document.getElementById)
		var elem = document.getElementById(name);
	else if (document.all)
		var elem = document.all[name];
	else if (document.layers)
		var elem = document.layers[name];
	return elem;
}

function changeFormParam(pForm, url, gid)
{
	pForm.action = url;
	pForm.elements["groupid"].value = gid;
}

function addAccessory(event, data, formatted)
{
	if (data == null)
		return false;
	var productId = data[1];
	var productName = data[0];

	var $divAccessories = $('#divAccessories');
	var $inputAccessories = $('#inputAccessories');
	var $nameAccessories = $('#nameAccessories');

	/* delete product from select + add product line to the div, input_name, input_ids elements */
	$divAccessories.html($divAccessories.html() + productName + ' <span onclick="delAccessory(' + productId + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />');
	$nameAccessories.val($nameAccessories.val() + productName + '¤');
	$inputAccessories.val($inputAccessories.val() + productId + '-');
	$('#product_autocomplete_input').val('');
	$('#product_autocomplete_input').setOptions({
		extraParams: {excludeIds : $('#inputAccessories').val().replace(/\-/g,',').replace(/\,$/,'')}
	});
}


function delAccessory(id)
{
	var div = getE('divAccessories');
	var input = getE('inputAccessories');
	var name = getE('nameAccessories');

	// Cut hidden fields in array
	var inputCut = input.value.split('-');
	var nameCut = name.value.split('¤');

	if (inputCut.lenght != nameCut.lenght)
		return alert('Bad size');

	// Reset all hidden fields
	input.value = '';
	name.value = '';
	div.innerHTML = '';
	for (i in inputCut)
	{
		// If empty, error, next
		if (!inputCut[i] || !nameCut[i])
			continue ;

		// Add to hidden fields no selected products OR add to select field selected product
		if (inputCut[i] != id)
		{
			input.value += inputCut[i] + '-';
			name.value += nameCut[i] + '¤';
			div.innerHTML += nameCut[i] + ' <span onclick="delAccessory(' + inputCut[i] + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
		}
		else
			$('#selectAccessories').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
	}
}

function dontChange(srcText)
{
	if (srcText == '')
		return false;
	if (window.search_texts)
		for (var i in search_texts)
			if (srcText == search_texts[i])
				return false;
	return true;
}

function queryType()
{
	var search_type = getE('bo_search_type').value;
	var bo_query = getE('bo_query');

	if (!dontChange(bo_query.value))
		bo_query.value = search_texts[search_type];
}

function formSubmit(e, button)
{
	var key;

	key = window.event ? window.event.keyCode : e.which;
	if (key == 13)
	{
		getE(button).focus();
		getE(button).click();
	}
}
function	noComma(elem)
{
 	getE(elem).value = getE(elem).value.replace(new RegExp(',', 'g'), '.');
}

/* Help boxes */
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

function helpboxParser(current)
{
 	// While the span exists and we didn't find the right one, for each attribute, if attribute is "name" and has value == "help_box"
	for (var j = 0; j < current.parentNode.getElementsByTagName('span').length; j++)
		for(var k = 0; k < current.parentNode.getElementsByTagName('span')[j].attributes.length; k++)
			if (current.parentNode.getElementsByTagName('span')[j].attributes[k].name === 'name' && current.parentNode.getElementsByTagName('span')[j].attributes[k].nodeValue === 'help_box')
				return j;

	return -1;
}

if (helpboxes)
{
	$(function()
	{
		if ($('input'))
		{
			$('input').focus(function() { $(this).parent().find('.hint:first').css('display', 'block'); });
			$('input').blur(function() { $(this).parent().find('.hint:first').css('display', 'none'); });
		}
	});
}

function changePic(id_product, id_image)
{
 	if (id_image == -1)
 	{
 		getE('pic').style.display = 'none';
 		return;
 	}
 	getE('pic').style.display = 'block';
	getE('pic').src = '../img/p/'+parseInt(id_product)+'-'+parseInt(id_image)+'.jpg';
}

/* Code generator for Affiliation and vourchers */
function gencode(size)
{
	getE('code').value = '';
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for (var i = 1; i <= size; ++i)
		getE('code').value += chars.charAt(Math.floor(Math.random() * chars.length));
}

function free_shipping()
{
	if (getE('id_discount_type').value == 3 && getE('discount_value').value == '')
		getE('discount_value').value = '0';
}

var newWin = null;

function closeWin ()
{
	if (newWin != null)
		if (!newWin.closed)
			newWin.close();
}

function openWin(url, title, width, height, top, left)
{
	var options;
	var sizes;

	closeWin();
	options = 'toolbar=0, location=0, directories=0, statfr=no, menubar=0, scrollbars=yes, resizable=yes';
	sizes = 'width='+width+', height='+height+', top='+top+', left='+left+'';
	newWin = window.open(url, title, options+', '+sizes);
	newWin.focus();
}

function	viewTemplates(id_select, id_lang, prefix, ext)
{
	var id_list = document.getElementById(id_select);
	var loc = id_list.options[id_list.selectedIndex].value;
	if (loc != 0)
		openWin (prefix+loc+ext, 'tpl_viewing', '520', '400', '50', '300');
	return ;
}

function validateImportation(mandatory)
{
    var type_value = [];
	var seted_value = [];
	var elem;
	var col = 'unknow';

	toggle(getE('error_duplicate_type'), false);
	toggle(getE('required_column'), false);
    for (i = 0; elem = getE('type_value['+i+']'); i++)
    {
		if (seted_value[elem.options[elem.selectedIndex].value])
		{
			scroll(0,0);
			toggle(getE('error_duplicate_type'), true);
			return false;
		}
		else if (elem.options[elem.selectedIndex].value != 'no')
			seted_value[elem.options[elem.selectedIndex].value] = true;
	}
	for (needed in mandatory)
		if (!seted_value[mandatory[needed]])
		{
			scroll(0,0);
			toggle(getE('required_column'), true);
			getE('missing_column').innerHTML = mandatory[needed];
			elem = getE('type_value[0]');
			for (i = 0; i < elem.length; ++i)
			{
				if (elem.options[i].value == mandatory[needed])
				{
					getE('missing_column').innerHTML = elem.options[i].innerHTML;
					break ;
				}
			}
			return false
		}
}

function askFeatureName(selected, selector)
{
	var elem;

	if (selected.value == 'feature')
	{
		$('#features_' + selector).show();
		$('#feature_name_' + selector).attr('name', selected.name);
	}
}

function replaceFeature(toReplace, selector)
{
	var elem;

	if ($('#feature_name_' + selector).val() == '')
		return false;

	elem = getE(toReplace);
	elem.options[elem.selectedIndex].text = $('#feature_name_' + selector).val();
	elem.options[elem.selectedIndex].value = '#F_' + $('#feature_name_' + selector).val();
	$('#features_' + selector).toggle();
	$('#feature_name_' + selector).val('');
	$('#feature_name_' + selector).attr('name', '');
}

/* Manage default category on page: edit product */
function checkDefaultCategory(category_id)
{
	var oldCheckbox = $('.id_category_default');
	oldCheckbox.removeClass('id_category_default');
	var checkbox = $('#categoryBox_'+category_id);
	checkbox.attr('checked', 'checked');
	checkbox.addClass('id_category_default');
}

function checkDefaultGroup(group_id)
{
	var oldCheckbox = $('.id_group_default');
	oldCheckbox.removeClass('id_group_default');
	var checkbox = $('#groupBox_'+group_id);
	checkbox.attr('checked', 'checked');
	checkbox.addClass('id_group_default');
}

function chooseTypeTranslation(id_lang)
{
	getE('translation_lang').value = id_lang;
	document.getElementById('typeTranslationForm').submit();
}


function showDiv(select_id, while_id, dest)
{
	var select = document.getElementById(select_id);
	if (select.options[select.selectedIndex].value == while_id)
		return toggle(getE(dest), true);
	return toggle(getE(dest));
}

function orderDeleteProduct(txtConfirm, txtExplain)
{
	ret = true;
	$('table#cancelProducts input[type=checkbox]:checked').each(
		function()
		{
			totalCancel = parseInt($(this).parent().parent().find('td.cancelQuantity input[type=text]').val());
			totalQty = parseInt($(this).parent().find('input#totalQty[type=hidden]').val());
			totalQtyReturn = parseInt($(this).parent().find('input#totalQtyReturn[type=hidden]').val());
			productName = $(this).parent().find('input#productName[type=hidden]').val();
			totalAvailable = totalQty - totalQtyReturn;
			if (totalCancel > totalAvailable)
			{
				alert(txtConfirm + ' : \'' + ' ' + productName + '\' ! \n\n' + txtExplain + ' ('+ totalCancel + ' > ' + totalAvailable +')' + '\n ');
				ret = false;
			}
		}
	);
	return ret;
}

function selectCheckbox(obj)
{
	$(obj).parent().parent().find('td.cancelCheck input[type=checkbox]').attr("checked", true);
}

function toogleShippingCost(obj)
{
	generateDiscount = $(obj).parent().find('#generateDiscount').attr("checked");
	generateCreditSlip = $(obj).parent().find('#generateCreditSlip').attr("checked");
	if (generateDiscount != true && generateCreditSlip != true)
	{
		$(obj).parent().find('#spanShippingBack input[type=checkbox]').attr("checked", false);
		$(obj).parent().find('#spanShippingBack').css('display', 'none');
	}
	else
		$(obj).parent().find('#spanShippingBack').css('display', 'block');
}

function removeLabel(label, fieldType, type)
{
	$(label).remove();
	if (fieldType == 0)
	{
		if (type == 0)
			customizationUploadableFileNumber--;
		else
			uploadableFileLabel--;
	}
	else
	{
		if (type == 0)
			customizationTextFieldNumber--;
		else
			textFieldLabel--;
	}
}

function browseAndRemoveLabels(newCustomizationFieldNumber, customizationFieldNumber, fieldType, type)
{
	var $current = $('body').find('div[id^="' + (type == 0 ? 'label' : 'newLabel') + 'Container_' + fieldType + '_"]');
	var ids = new Array();
	var pos = $current.length - 1;

	$current.each(function() {
		ids[pos--] = $(this).attr('id');
	});

	for (var i = 0; i < $current.length; i++)
		if (customizationFieldNumber > newCustomizationFieldNumber)
		{
			removeLabel($('#'+ids[i]), fieldType, type);
			customizationFieldNumber--;
		}
	return customizationFieldNumber;
}

function displayCustomizationProperties(type, force)
{
	var newCustomizationFieldNumber = Math.abs(type == 0 ? parseInt($('#uploadable_files').val()) : parseInt($('#text_fields').val()));
	var customizationFieldNumber = Math.abs(type == 0 ? (parseInt(customizationUploadableFileNumber) + parseInt(uploadableFileLabel)) : (parseInt(customizationTextFieldNumber) + parseInt(textFieldLabel)));
	var label = type == 0 ? parseInt(uploadableFileLabel) : parseInt(textFieldLabel);
	var target = type == 0 ? '#customizationFileProperties' : '#customizationTextFieldProperties';
	/* Add some fields */
	if (newCustomizationFieldNumber > customizationFieldNumber || force)
	{
		var content = '';
		var j = label;

		for (var i = 0; i < newCustomizationFieldNumber - customizationFieldNumber; i++, j++)
		{
			var fieldsName = 'newLabel_' + type + '_' + j;
			var fieldsContainerName = 'newLabelContainer_' + type + '_' + j;
			content += '<div id="' + fieldsContainerName + '">';
			/* Generates input field */
			for (k = 0; k < languages.length; k++)
				content += '<div id="' + fieldsName + '_' + languages[k][0] + '" style="display: ' + (parseInt(languages[k][0]) == parseInt(defaultLanguage) ? 'block' : 'none') + '; clear: left; float: left;">' + newLabel + ' #' + (j + 1) + ' <input type="text" name="' + fieldsName + '_' + languages[k][0] + '" value="" /></div>';
			/* Generates language selector & require checkbox */
			content += '<div class="display_flags"><img src="../img/l/' + parseInt(defaultLanguage) + '.jpg" class="pointer" id="language_current_' + fieldsName + '" onclick="showLanguages(\'' + fieldsName + '\');" alt="" /></div><div style="float: left; margin-left: 16px;"><input type="checkbox" name="require_' + type + '_' + j + '" value="1" /> ' + required + '</div><div id="languages_' + fieldsName + '" class="language_flags">' + choose_language + '<br /><br />';
			/* Generate language flags */
			for (k = 0; k < languages.length; k++)
				content += '<img src="../img/l/' + parseInt(languages[k][0]) + '.jpg" class="pointer" alt="' + languages[k][2] + '" title="' + languages[k][2] + '" onclick="changeLanguage(\'' + fieldsName + '\', \'' + fieldsName + '\', ' + parseInt(languages[k][0]) + ', \'' + languages[k][1] + '\');" />';
			content += '</div></div>';
			if (type == 0)
				uploadableFileLabel++;
			else
				textFieldLabel++;
		}
		$(target).append(content);
	}
	/* Remove */
	else
	{
		customizationFieldNumber = browseAndRemoveLabels(newCustomizationFieldNumber, customizationFieldNumber, type, 1);
		browseAndRemoveLabels(newCustomizationFieldNumber, customizationFieldNumber, type, 0);
	}
}

function showAttributeColorGroup(name, container)
{
	var id_list;
	var value;

	id_list = document.getElementById(name);
	value = id_list.options[id_list.selectedIndex].value;
	if (attributesGroups[value])
		$('#colorAttributeProperties').fadeIn();
	else
		$('#colorAttributeProperties').fadeOut();
}

function orderOverwriteMessage(sl, text)
{
	var $zone = $('#txt_msg');
	var sl_value = sl.options[sl.selectedIndex].value;

	if (sl_value != '0')
	{
		if ($zone.val().length > 0 && !confirm(text))
			return ;
		$zone.val(sl_value);
	}
}

function setCancelQuantity(itself, id_order_detail, quantity)
{
	$('#cancelQuantity_' + id_order_detail).val($(itself).attr('checked') ? quantity : '');
}

function stockManagementActivationAuthorization()
{
	if (getE('PS_STOCK_MANAGEMENT_on').checked)
	{
		getE('PS_ORDER_OUT_OF_STOCK_on').disabled = false;
		getE('PS_ORDER_OUT_OF_STOCK_off').disabled = false;
		getE('PS_DISPLAY_QTIES_on').disabled = false;
		getE('PS_DISPLAY_QTIES_off').disabled = false;
	}
	else
	{
		getE('PS_DISPLAY_QTIES_off').checked = true;
		getE('PS_DISPLAY_QTIES_on').disabled = 'disabled';
		getE('PS_DISPLAY_QTIES_off').disabled = 'disabled';
		getE('PS_ORDER_OUT_OF_STOCK_on').checked = true;
		getE('PS_ORDER_OUT_OF_STOCK_on').disabled = 'disabled';
		getE('PS_ORDER_OUT_OF_STOCK_off').disabled = 'disabled';
	}
}

function hookCheckboxes(id, opt, champ)
{
	if (opt == 1 && champ.checked == false)
		$('#Ghook'+id).attr('checked', false);
	else if (opt == 0)
	{
		if (champ.checked)
			$('.hook'+id).attr('checked', "checked");
		else
			$('.hook'+id).attr('checked', false);
	}
}

function changeCMSActivationAuthorization()
{
	if (getE('PS_CONDITIONS_on').checked)
		getE('PS_CONDITIONS_CMS_ID').disabled = false;
	else
		getE('PS_CONDITIONS_CMS_ID').disabled = 'disabled';
}

function disableZipFormat()
{
	if ($('#need_zip_code_on').attr('checked') == false)
	{
		$('.zip_code_format').hide();
		$('#zip_code_format').val('');
	}
	else
		$('.zip_code_format').show();
}


function spreadFees(id_range)
{
	newVal = $('#fees_all_'+id_range).val();
	$('.fees_'+id_range).val(newVal);
}

function clearAllFees(id_range)
{
	$('#fees_all_'+id_range).val('');
}

function toggleDraftWarning(show)
{
	if (show)
		$('.draft').slideDown('slow');
	else
		$('.draft').slideUp('slow');
}

function showOptions(show)
{
	if (show)
		$('tr#product_options').slideDown('slow');
	else
		$('tr#product_options').slideUp('slow');
}

function submitAddProductAndPreview()
{
	$('#fakeSubmitAddProductAndPreview').attr('name','submitAddProductAndPreview');
	$('#product').submit();
}

function submitAddcmsAndPreview()
{
	$('#previewSubmitAddcmsAndPreview').attr('name','submitAddcmsAndPreview');
	$('#cms').submit();
}

function showHelp(url, label, iso_lang, ps_version, doc_version, country)
{
    trackClickOnHelp(label, doc_version);
    $('#help-'+label).attr('src','../img/admin/help2.png');
    window.open(url +'/'+iso_lang+'/doc/'+label+'?version='+ps_version+'&country='+country+'#', '_blank', 'scrollbars=yes,menubar=no,toolbar=no,location=no,width=517,height=600');
    return false;
}


function trackClickOnHelp(label, doc_version)
{
   	$.ajax({
		url: 'ajax.php',
		data: 'submitTrackClickOnHelp&label='+ label +'&version='+doc_version
	});
}

