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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var ajax_running_timeout = null;

if (!id_language)
	var id_language = Number(1);

function str2url(str,encoding,ucfirst)
{
	str = str.toUpperCase();
	str = str.toLowerCase();

	str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]\\u00A1-\\uFFFF/g,'');
	str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g,'');
	str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');

	// Add special char not used for url rewrite
	str = str.replace(/[ ]/g, '-');
	str = str.replace(/[\/\\"'|,;]*/g, '');

	if (ucfirst == 1) {
		var first_char = str.charAt(0);
		str = first_char.toUpperCase()+str.slice(1);
	}

	return str;
}

function copy2friendlyURL()
{
	$('#link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8').replace('%', ''));
	if ($('#friendly-url'))
		$('#friendly-url').html($('#link_rewrite_' + id_language).val());
	// trigger onchange event to use anything binded there
	$('#link_rewrite_' + id_language).change(); 
	return;
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
	var link = $('#link_rewrite_' + id_language);
	if (link[0])
	{
		link.val(str2url($('#link_rewrite_' + id_language).val(), 'UTF-8'));
		$('#seo #friendly-url').text(link.val());
	}
}

function toggleLanguageFlags(elt)
{
	$(elt).parents('.displayed_flag').siblings('.language_flags').toggle();
}

// Kept for retrocompatibility only (out of AdminProducts & AdminCategories)
function changeLanguage(field, fieldsString, id_language_new, iso_code)
{
    $('div[id^='+field+'_]').hide();
	var fields = fieldsString.split('¤');
	for (var i = 0; i < fields.length; ++i)
	{
		$('div[id^='+fields[i]+'_]').hide();
		$('#'+fields[i]+'_'+id_language_new).show();
		$('#'+'language_current_'+fields[i]).attr('src', '../img/l/' + id_language_new + '.jpg');
	}
	$('#languages_' + field).hide();
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

	// For multishop checkboxes
	$('.multishop_lang_'+id_language_new).show().siblings('div[class^=\'multishop_lang_\']').hide();

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
		if (objType == 'checkbox' && objName != 'checkme')
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
function noComma(elem)
{
 	getE(elem).value = getE(elem).value.replace(new RegExp(',', 'g'), '.');
}

/* Help boxes */
if (typeof helpboxes != 'undefined' && helpboxes)
{
	$(function()
	{
		if ($('input'))
		{
			//Display by rollover
			$('input').mouseover(function() {
			$(this).parent().find('.hint:first').css('display', 'block');
			});
			$('input').mouseout(function() { $(this).parent().find('.hint:first').css('display', 'none'); });

			//display when you press the tab key
			$('input').keydown(function (e) {
				if ( e.keyCode === 9 ){
					$('input').focus(function() { $(this).parent().find('.hint:first').css('display', 'block'); });
					$('input').blur(function() { $(this).parent().find('.hint:first').css('display', 'none'); });
				}
			});
		}
		if ($('select'))
		{
			//Display by rollover
			$('select').mouseover(function() {
			$(this).parent().find('.hint:first').css('display', 'block');
			});
			$('select').mouseout(function() { $(this).parent().find('.hint:first').css('display', 'none'); });

			//display when you press the tab key
			$('select').keydown(function (e) {
				if ( e.keyCode === 9 ){
					$('select').focus(function() { $(this).parent().find('.hint:first').css('display', 'block'); });
					$('select').blur(function() { $(this).parent().find('.hint:first').css('display', 'none'); });
				}
			});
		}
		if ($('span.title_box'))
		{
			//Display by rollover
			$('span.title_box').mouseover(function() {
				//get reference to the hint box
				var parent = $(this).parent();
				var box = parent.find('.hint:first');

				if (box.length > 0)
				{
					//gets parent position
					var left_position = parent.offset().left;

					//gets width of the box
					var box_width = box.width();

					//gets width of the screen
					var document_width = $(document).width();

					//changes position of the box if needed
					if (document_width < (left_position + box_width))
						box.css('margin-left', '-' + box_width + 'px');

					//shows the box
					box.css('display', 'block');
				}
			});
			$('span.title_box').mouseout(function() { $(this).parent().find('.hint:first').css('display', 'none'); });
		}
	});
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

function viewTemplates(id_select, prefix, ext)
{
	var loc = $(id_select).val();
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

function chooseTypeTranslation(id_lang)
{
	getE('translation_lang').value = id_lang;
	document.getElementById('typeTranslationForm').submit();
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
				jAlert(txtConfirm + ' : \'' + ' ' + productName + '\' ! \n\n' + txtExplain + ' ('+ totalCancel + ' > ' + totalAvailable +')' + '\n ');
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

function toggleShippingCost(obj)
{
	generateDiscount = $(obj).parent().find('#generateDiscount').attr("checked");
	generateCreditSlip = $(obj).parent().find('#generateCreditSlip').attr("checked");
	if (generateDiscount != 'checked' && generateCreditSlip != 'checked')
	{
		$(obj).parent().find('#spanShippingBack input[type=checkbox]').attr("checked", false);
		$(obj).parent().find('#spanShippingBack').css('display', 'none');
	}
	else
		$(obj).parent().find('#spanShippingBack').css('display', 'block');
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
	$('#cancelQuantity_' + id_order_detail).val($(itself).prop('checked') ? quantity : '');
}

function stockManagementActivationAuthorization()
{
	if (getE('PS_STOCK_MANAGEMENT_on').checked)
	{
		getE('PS_ORDER_OUT_OF_STOCK_on').disabled = false;
		getE('PS_ORDER_OUT_OF_STOCK_off').disabled = false;
		getE('PS_DISPLAY_QTIES_on').disabled = false;
		getE('PS_DISPLAY_QTIES_off').disabled = false;
		getE('PS_ADVANCED_STOCK_MANAGEMENT_on').disabled = false;
		getE('PS_ADVANCED_STOCK_MANAGEMENT_off').disabled = false;
	}
	else
	{
		getE('PS_DISPLAY_QTIES_off').checked = true;
		getE('PS_DISPLAY_QTIES_on').disabled = 'disabled';
		getE('PS_DISPLAY_QTIES_off').disabled = 'disabled';
		getE('PS_ORDER_OUT_OF_STOCK_on').checked = true;
		getE('PS_ORDER_OUT_OF_STOCK_on').disabled = 'disabled';
		getE('PS_ORDER_OUT_OF_STOCK_off').disabled = 'disabled';
		getE('PS_ADVANCED_STOCK_MANAGEMENT_off').checked = true;
		getE('PS_ADVANCED_STOCK_MANAGEMENT_on').disabled = 'disabled';
		getE('PS_ADVANCED_STOCK_MANAGEMENT_off').disabled = 'disabled';
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
	if ($('#need_zip_code_on').prop('checked') == false)
	{
		$('.zip_code_format').hide();
		$('#zip_code_format').val('');
	}
	else
		$('.zip_code_format').show();
}

function spreadFees(id_range)
{
	newVal = $('#fees_all_'+id_range).val().replace(/,/g, '.');
	$('.fees_'+id_range).val(newVal);
}

function clearAllFees(id_range)
{
	$('#fees_all_'+id_range).val('');
}

function toggleDraftWarning(show)
{
	if (show)
		$('.draft').hide();
	else
		$('.draft').show();
}

function showOptions(show)
{
	if (show)
		$('tr#product_options').show();
	else
		$('tr#product_options').hide();
}

function submitAddProductAndPreview()
{
	$('#fakeSubmitAddProductAndPreview').attr('name','submitAddProductAndPreview');
	$('#product_form').submit();
}

function submitAddcmsAndPreview()
{
	$('#previewSubmitAddcmsAndPreview').attr('name','submitAddcmsAndPreview');
	$('#cms').submit();
}

function checkAllMultishopDefaultValue(item)
{
	$(item).parent().find('input[name^=\'multishopOverrideOption\']').each(function(k, v)
	{
		$(v).attr('checked', item.checked);
		var name = $(v).attr('name');
		checkMultishopDefaultValue(v, name.substr(24, name.length - 25));
	})
}

function checkMultishopDefaultValue(obj, key)
{
	if (!$(obj).prop('checked') || $('#'+key).hasClass('isInvisible'))
	{
		$('#conf_id_'+key+' input, #conf_id_'+key+' textarea, #conf_id_'+key+' select').attr('disabled', true);
		$('#conf_id_'+key+' label.conf_title').addClass('isDisabled');
	}
	else
	{
		$('#conf_id_'+key+' input, #conf_id_'+key+' textarea, #conf_id_'+key+' select').attr('disabled', false);
		$('#conf_id_'+key+' label.conf_title').removeClass('isDisabled');
	}
	$('#conf_id_'+key+' .preference_default_multishop input').attr('disabled', false);
}

function doAdminAjax(data, success_func, error_func)
{
	$.ajax(
	{
		url : 'index.php',
		data : data,
		success : function(data){
			if (success_func)
				return success_func(data);

			data = $.parseJSON(data);
			if(data.confirmations.length != 0)
				showSuccessMessage(data.confirmations);
			else
				showErrorMessage(data.error);
		},
		error : function(data){
			if (error_func)
				return error_func(data);

			alert("[TECHNICAL ERROR]");
		}
	});
}

/** display a success message in a #ajax_confirmation container
 * @param string msg string to display
 */
function showSuccessMessage(msg, delay)
{
	if (!delay)
		delay = 3000;
	$("#ajax_confirmation")
		.html("<div class=\"conf\">"+msg+"</div>").show().delay(delay).fadeOut("slow");
}

/** display a warning message in a #ajax_confirmation container
 * @param string msg string to display
 */
function showErrorMessage(msg, delay)
{
	if (!delay)
		delay = 5000;
	$("#ajax_confirmation")
		.html("<div class=\"error\">"+msg+"</div>").show().delay(delay).fadeOut("slow");
}

$(document).ready(function()
{
	$('select.chosen').each(function(k, item){
		$(item).chosen();
		if ($(item).hasClass('no-search'))
			$(item).next().find('.chzn-search').hide();
	});

	$('.isInvisible input, .isInvisible select, .isInvisible textarea').attr('disabled', true);
	$('.isInvisible label.conf_title').addClass('isDisabled');

	// Disable options fields for each row with a multishop default checkbox
	$('.preference_default_multishop input[type=checkbox]').each(function(k, v)
	{
		var key = $(v).attr('name');
		var len = key.length;
		checkMultishopDefaultValue(v, key.substr(24, len - 25));
	});

	$(".copy2friendlyUrl").live('keyup change',function(e){
		if(!isArrowKey(e))
			return copy2friendlyURL();
	});

	// on live will make this binded for dynamic content
	$(".updateCurrentText").live('keyup change',function(e){
		if(typeof e == KeyboardEvent)
			if(isArrowKey(e))
				return;

		updateCurrentText()
	});

	$(".copyMeta2friendlyURL").live('keyup change',function(e){
		if(!isArrowKey(e))
			return copyMeta2friendlyURL()
	});

	// Adding a button to top
	var scroll = $('#scrollTop a');
	var view = $(window);

	scroll.click(function(){
		$.scrollTo('#top_container', 1200, { offset: -100 });
	});

	view.bind("scroll", function(e) {
		var heightView = view.height();
		if (scroll.offset())
			var btnPlace = scroll.offset().top;
		else
			var btnPlace = 0;
		if (heightView < btnPlace)
			scroll.show();
		else
			scroll.hide();
	});

	$('#ajax_running').ajaxStart(function() {
		ajax_running_timeout = setTimeout(function() {showAjaxOverlay()}, 1000);
	});

	$('#ajax_running').ajaxStop(function() {
		$(this).slideUp('fast');
		clearTimeout(ajax_running_timeout);
	});

	$('#ajax_running').ajaxError(function() {
		$(this).slideUp('fast');
		clearTimeout(ajax_running_timeout);
	});
});

// Delete all tags HTML
function stripHTML(oldString)
{
	var newString = '';
	var inTag = false;
	for(var i = 0; i < oldString.length; i++) {
		if(oldString.charAt(i) == '<') inTag = true;
		if(oldString.charAt(i) == '>') {
			if(oldString.charAt(i+1)!='<')
			{
				inTag = false;
				i++;
			}
		}
		if(!inTag) newString += oldString.charAt(i);
	}
	return newString;
}

/**
 * Display a loading bar while an ajax call is ongoing.
 *
 * To prevent the loading bar display for a specific ajax call, set the beforeSend event in your ajax declaration:
 * 		beforeSend : function(data)
 		{
 			// don't display the loading notification bar
 			clearTimeout(ajax_running_timeout);
 		}
 */
function showAjaxOverlay()
{
	$('#ajax_running').slideDown('fast');
	clearTimeout(ajax_running_timeout);
}

function display_action_details(row_id, controller, token, action, params) {
	var id = action+'_'+row_id;
	var current_element = $('#details_'+id);
	if (!current_element.data('dataMaped')) {
		var ajax_params = {
			'id': row_id,
			'controller': controller,
			'token': token,
			'action': action,
			'ajax': true
		};

		$.each(params, function(k, v)
		{
			ajax_params[k] = v;
		});

		$.ajax({
			url: 'index.php',
			data: ajax_params,
			dataType: 'json',
			cache: false,
			context: current_element,
			async: false,
			success: function(data) {
				if (typeof(data.use_parent_structure) == 'undefined' || (data.use_parent_structure == true))
				{
					if (current_element.parent().parent().hasClass('alt_row'))
						var alt_row = true;
					else
						var alt_row = false;
					current_element.parent().parent().after($('<tr class="details_'+id+' small '+(alt_row ? 'alt_row' : '')+'"></tr>')
						.append($('<td style="border:none!important;" class="empty"></td>')
						.attr('colspan', current_element.parent().parent().find('td').length)));
					$.each(data.data, function(it, row)
					{
						var bg_color = ''; // Color
						if (row.color)
							bg_color = 'style="background:' + row.color +';"';

						var content = $('<tr class="action_details details_'+id+' '+(alt_row ? 'alt_row' : '')+'"></tr>');
						content.append($('<td class="empty"></td>'));
						var first = true;
						var count = 0; // Number of non-empty collum
						$.each(row, function(it)
						{
							if(typeof(data.fields_display[it]) != 'undefined')
								count++;
						});
						$.each(data.fields_display, function(it, line)
						{
							if (typeof(row[it]) == 'undefined')
							{
								if (first || count == 0)
									content.append($('<td class="'+current_element.align+' empty"' + bg_color + '></td>'));
								else
									content.append($('<td class="'+current_element.align+'"' + bg_color + '></td>'));
							}
							else
							{
								count--;
								if (first)
								{
									first = false;
									content.append($('<td class="'+current_element.align+' first"' + bg_color + '>'+row[it]+'</td>'));
								}
								else if (count == 0)
									content.append($('<td class="'+current_element.align+' last"' + bg_color + '>'+row[it]+'</td>'));
								else
									content.append($('<td class="'+current_element.align+' '+count+'"' + bg_color + '>'+row[it]+'</td>'));
							}
						});
						content.append($('<td class="empty"></td>'));
						current_element.parent().parent().after(content.show('slow'));
					});
				}
				else
				{
					if (current_element.parent().parent().hasClass('alt_row'))
						var content = $('<tr class="details_'+id+' alt_row"></tr>');
					else
						var content = $('<tr class="details_'+id+'"></tr>');
					content.append($('<td style="border:none!important;">'+data.data+'</td>').attr('colspan', current_element.parent().parent().find('td').length));
					current_element.parent().parent().after(content);
					current_element.parent().parent().parent().find('.details_'+id).hide();
				}
				current_element.data('dataMaped',true);
				current_element.data('opened', false);
				
				if (typeof(initTableDnD) != 'undefined')
					initTableDnD('.details_'+id+' table.tableDnD');
			}
		});
	}

	if (current_element.data('opened'))
	{
		current_element.find('img').attr('src', '../img/admin/more.png');
		current_element.parent().parent().parent().find('.details_'+id).hide('fast');
		current_element.data('opened', false);
	}
	else
	{
		current_element.find('img').attr('src', '../img/admin/less.png');
		current_element.parent().parent().parent().find('.details_'+id).show('fast');
		current_element.data('opened', true);
	}
}
