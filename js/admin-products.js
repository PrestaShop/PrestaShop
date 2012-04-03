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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// array of product tab objects containing methods and dom bindings
// The ProductTabsManager instance will make sure the onReady() methods of each tabs are executed once the tab has loaded
var product_tabs = [];

product_tabs['Combinations'] = {
	'bindEdit' : function(){
		$('table[name=list_table]').delegate('a.edit', 'click', function(e){
			e.preventDefault();
			editProductAttribute(this.href, $(this).closest('tr'));
		});

		function editProductAttribute (url, parent){
			$.ajax({
				url: url,
				data: {
					id_product: id_product,
					ajax: true,
					action: 'editProductAttribute'
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data) {
					// color the selected line
					parent.siblings().removeClass('selected-line');
					parent.addClass('selected-line');

					$('#add_new_combination').show();
					$('#attribute_quantity').show();
					$('#product_att_list').html('');
					removeButtonCombination('update');
					$.scrollTo('#add_new_combination', 1200, { offset: -100 });
					var wholesale_price = Math.abs(data[0]['wholesale_price']);
					var price = Math.abs(data[0]['price']);
					var weight = Math.abs(data[0]['weight']);
					var unit_impact = Math.abs(data[0]['unit_price_impact']);
					var reference = data[0]['reference'];
					var ean = data[0]['ean13'];
					var quantity = data[0]['quantity'];
					var image = false;
					var product_att_list = new Array();
					for(i=0;i<data.length;i++)
					{
						product_att_list.push(data[i]['group_name']+' : '+data[i]['attribute_name']);
						product_att_list.push(data[i]['id_attribute']);
					}

					var id_product_attribute = data[0]['id_product_attribute'];
					var default_attribute = data[0]['default_on'];
					var eco_tax = data[0]['ecotax'];
					var upc = data[0]['upc'];
					var minimal_quantity = data[0]['minimal_quantity'];
					var available_date = data[0]['available_date'];
					var virtual_product_name_attribute = data[0]['display_filename'];
					var virtual_product_filename_attribute = data[0]['display_filename'];
					var virtual_product_nb_downloable = data[0]['nb_downloadable'];
					var virtual_product_expiration_date_attribute = data[0]['date_expiration'];
					var virtual_product_nb_days = data[0]['nb_days_accessible'];
					var is_shareable = data[0]['is_shareable'];
					if (wholesale_price != 0 && wholesale_price > 0)
					{
						$("#attribute_wholesale_price_full").show();
						$("#attribute_wholesale_price_blank").hide();
					}
					else
					{
						$("#attribute_wholesale_price_full").hide();
						$("#attribute_wholesale_price_blank").show();
					}
					fillCombination(
						wholesale_price,
						price,
						weight,
						unit_impact,
						reference,
						ean,
						quantity,
						image,
						product_att_list,
						id_product_attribute,
						default_attribute,
						eco_tax,
						upc,
						minimal_quantity,
						available_date,
						virtual_product_name_attribute,
						virtual_product_filename_attribute,
						virtual_product_nb_downloable,
						virtual_product_expiration_date_attribute,
						virtual_product_nb_days,
						is_shareable
					);
					calcImpactPriceTI();
				}
			});
		}
	},
	'bindDefault' : function(){
		$('table[name=list_table]').delegate('a.default', 'click', function(e){
			e.preventDefault();
			defaultProductAttribute(this.href, $(this).closest('tr'));
		});

		function defaultProductAttribute (url, parent){
			$.ajax({
				url: url,
				data: {
					id_product: id_product,
					action: 'defaultProductAttribute',
					ajax: true
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data) {
					if (data.status == 'ok')
					{
						showSuccessMessage(data.message);
						$('table.table').find('tr').attr('style', function() {
							var style = $(this).attr('style');
							if (style)
							{
								$(this).attr('style', '');
								var ids = $(this).find('a.edit').attr('ids');
								var token = $(this).find('a.edit').attr('token');
								$(this).find('a.edit').after("<a title=\"Default\" onclick=\"javascript:defaultProductAttribute('"+ids+"', '"+token+"', $(this).parent('td').parent('tr'));\" class=\"pointer default\"><img alt=\"Default\" src=\"../img/admin/asterisk.gif\"></a>");
							}
						});
						parent.find('a.default').hide();
						parent.css('background','#BDE5F8');
					}
					else
						showErrorMessage(data.message);
				}
			});
		}
	},
	'bindDelete' : function() {
		$('table[name=list_table]').delegate('a.delete', 'click', function(e){
			e.preventDefault();
			deleteProductAttribute(this.href, $(this).closest('tr'));
		});

		function deleteProductAttribute(url, parent){
			$.ajax({
				url: url,
				data: {
					id_product: id_product,
					action: 'deleteProductAttribute',
					ajax: true
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data) {
					if (data.status == 'ok')
					{
						showSuccessMessage(data.message);
						parent.remove();
					}
					else
						showErrorMessage(data.message);
				}
			});
		}
	},
	'bindToggleAddCombination' : function (){
		$('#desc-product-newCombination').click(function() {
			if ($('.process-icon-newCombination').hasClass('toolbar-new'))
				removeButtonCombination('add');
			else
				addButtonCombination('add');

			function removeButtonCombination(item)
			{
				$('#add_new_combination').show();
				$('.process-icon-newCombination').removeClass('toolbar-new');
				$('.process-icon-newCombination').addClass('toolbar-cancel');
				$('#desc-product-newCombination div').html($('#ResetBtn').val());
				$('id_product_attribute').val(0);
				init_elems();
			}

			function addButtonCombination(item)
			{
				$('#add_new_combination').hide();
				$('.process-icon-newCombination').removeClass('toolbar-cancel');
				$('.process-icon-newCombination').addClass('toolbar-new');
				$('#desc-product-newCombination div').html(msg_new_combination);
			}
		});
	},
	'onReady' : function(){
		product_tabs['Combinations'].bindEdit();
		product_tabs['Combinations'].bindDefault();
		product_tabs['Combinations'].bindDelete();
		product_tabs['Combinations'].bindToggleAddCombination();
	}
}

/**
 * hide save and save-and-stay buttons
 * 
 * @access public
 * @return void
 */
function disableSave()
{
		$('#desc-product-save').hide();
		$('#desc-product-save-and-stay').hide();
}

/**
 * show save and save-and-stay buttons
 *
 * @access public
 * @return void
 */
function enableSave()
{
		$('#desc-product-save').show();
		$('#desc-product-save-and-stay').show();
}

function handleSaveButtons(e)
{
	//product_type = $("input[name=type_product]:checked").val();
	msg = [];
	var i = 0;
	// relative to type of product
	if (product_type == product_type_pack)
		msg[i++] = handleSaveButtonsForPack();
	else if (product_type == product_type_pack)
		msg[i++] = handleSaveButtonsForVirtual();
	else
		msg[i++] = handleSaveButtonsForSimple();
	
	// common for all products
	// name[defaultlangid]
	$("#disableSaveMessage").remove();
	if ($("#name_"+defaultLanguage.id_lang).val() == "")
	{
		msg[i++] = empty_name_msg;
	}
	// check friendly_url_[defaultlangid] only if name is ok
	else if ($("#link_rewrite_"+defaultLanguage.id_lang).val() == "")
		msg[i++] = empty_link_rewrite_msg;

	if (msg.length == 0)
	{
		$("#disableSaveMessage").remove();
		enableSave();
	}
	else
	{
		$("#disableSaveMessage").remove();
		do_not_save = false;
		for (var key in msg)
		{
			if (msg != "")
			{
				if (do_not_save == false)
				{
					$(".leadin").append('<div id="disableSaveMessage" class="warn"></div>');
					warnDiv = $("#disableSaveMessage");
					do_not_save = true;
				}
				warnDiv.append('<p id="'+key+'">'+msg[key]+'</p>');
			}
		}
		if (do_not_save)
			disableSave();
		else
			enableSave();
	}
}

function handleSaveButtonsForSimple()
{
	return "";
}

function handleSaveButtonsForVirtual()
{
	return "";
}

function handleSaveButtonsForPack()
{
	// if no item left in the pack, disable save buttons
	if ($("#inputPackItems").val() == "")
		return empty_pack_msg;
	else
		return "";
}

product_tabs['Seo'] = {
	// Enable writing of the product name when the friendly url field in tab SEO is loaded
	'enableProductName' : function (){
		$('.copy2friendlyUrl').removeAttr('disabled');
	},
	'onReady' : function() {
		product_tabs['Seo'].enableProductName();
	}
}

product_tabs['Prices'] = {
	// Bind to show/hide new specific price form
	'toggleSpecificPrice' : function (){
		$('#show_specific_price').click(function()
		{
			$('#add_specific_price').slideToggle();

			$('#add_specific_price').append('<input type="hidden" name="submitPriceAddition"/>');

			$('#hide_specific_price').show();
			$('#show_specific_price').hide();
			return false;
		});

		$('#hide_specific_price').click(function()
		{
			$('#add_specific_price').slideToggle();
			$('#add_specific_price').find('input[name=submitPriceAddition]').remove();

			$('#hide_specific_price').hide();
			$('#show_specific_price').show();
			return false;
		});
	},
	/**
	 * Ajax call to delete a specific price
	 *
	 * @param ids
	 * @param token
	 * @param parent
	 */
	'deleteSpecificPrice' : function (url, parent){
		$.ajax({
			url: url,
			data: {
				ajax: true
			},
			context: document.body,
			dataType: 'json',
			context: this,
			async: false,
			success: function(data) {
				if (data.status == 'ok')
				{
					showSuccessMessage(data.message);
					parent.remove();
				}
				else
					showErrorMessage(data.message);
			}
		});
	},
	// Bind to delete specific price link
	'bindDelete' : function(){
		$('#specific_prices_list').delegate('a[name="delete_link"]', 'click', function(e){
			e.preventDefault();
			deleteSpecificPrice(this.href, $(this).parents('tr'));
		})
	},
	'onReady' : function(){
		product_tabs['Prices'].toggleSpecificPrice();
		product_tabs['Prices'].deleteSpecificPrice();
		product_tabs['Prices'].bindDelete();
	}
}

product_tabs['Associations'] = {
	'initAccessoriesAutocomplete' : function (){
		$('#product_autocomplete_input')
			.autocomplete('ajax_products_list.php', {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result(addAccessory);

		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : getAccessorieIds()
			}
		});

		function getAccessorieIds()
		{
			var ids = id_product + ',';
			ids += $('#inputAccessories').val().replace(/\\-/g,',').replace(/\\,$/,'');
			ids = ids.replace(/\,$/,'');

			return ids;
		}
	},
	/**
	 * Update the manufacturer select element with the list of existing manufacturers
	 */
	'getManufacturers' : function(){
		$.ajax({
				url: 'ajax-tab.php',
				cache: false,
				dataType: 'json',
				data: {
					ajaxProductManufacturers:"1",
					ajax : '1',
					token : token,
					controller : 'AdminProducts',
					action : 'productManufacturers'
				},
				success: function(j) {
					var options = $('select#id_manufacturer').html();
					if (j)
					for (var i = 0; i < j.length; i++)
						options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
					$("select#id_manufacturer").html(options);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
				}
		});
	},
	'onReady' : function(){
		product_tabs['Associations'].initAccessoriesAutocomplete();
		product_tabs['Associations'].getManufacturers();
	}
}

product_tabs['Attachments'] = {
	'bindAttachmentEvents' : function (){
		$("#addAttachment").live('click', function() {
			$("#selectAttachment2 option:selected").each(function(){
				var val = $('#arrayAttachments').val();
				var tab = val.split(',');
				for (var i=0; i < tab.length; i++)
					if (tab[i] == $(this).val())
						return false;
				$('#arrayAttachments').val(val+$(this).val()+',');
			});
			return !$("#selectAttachment2 option:selected").remove().appendTo("#selectAttachment1");
		});
		$("#removeAttachment").live('click', function() {
			$("#selectAttachment1 option:selected").each(function(){
				var val = $('#arrayAttachments').val();
				var tab = val.split(',');
				var tabs = '';
				for (var i=0; i < tab.length; i++)
					if (tab[i] != $(this).val())
					{
						tabs = tabs+','+tab[i];
						$('#arrayAttachments').val(tabs);
					}
			});
			return !$("#selectAttachment1 option:selected").remove().appendTo("#selectAttachment2");
		});
		$("#product").submit(function() {
			$("#selectAttachment1 option").each(function(i) {
				$(this).attr("selected", "selected");
			});
		});
	},
	'onReady' : function(){
		product_tabs['Attachments'].bindAttachmentEvents();
	}
}

product_tabs['Informations'] = {
	'bindAvailableForOrder' : function (){
		$("#available_for_order").click(function(){
			if ($(this).is(':checked'))
			{
				$('#show_price').attr('checked', 'checked');
				$('#show_price').attr('disabled', 'disabled');
			}
			else
			{
				$('#show_price').attr('disabled', '');
			}
		});
	},
	'bindTagImage' : function (){
		function changeTagImage(){
			var smallImage = $('input[name=smallImage]:checked').attr('value');
			var leftRight = $('input[name=leftRight]:checked').attr('value');
			var imageTypes = $('input[name=imageTypes]:checked').attr('value');
			var tag = '[img-'+smallImage+'-'+leftRight+'-'+imageTypes+']';
			$('#resultImage').val(tag);
		}
		changeTagImage();
		$('#createImageDescription input').change(function(){
			changeTagImage();
		});

		var i = 0;
		$('.addImageDescription').click(function(){
			if (i == 0){
				$('#createImageDescription').animate({
					opacity: 1, height: 'toggle'
					}, 500);
				i = 1;
			}else{
				$('#createImageDescription').animate({
					opacity: 0, height: 'toggle'
					}, 500);
				i = 0;
			}
		});
	},
	'switchProductType' : function(){
		if (product_type == product_type_pack)
		{
			$('#pack_product').attr('checked', 'checked');
		}
		else if (product_type == product_type_virtual)
		{
			$('#virtual_product').attr('checked', 'checked');
			$('#condition').attr('disabled', 'disabled');
			$('#condition option[value=new]').attr('selected', 'selected');
		}
		else
		{
			$('#simple_product').attr('checked', 'checked');
		}

		$('input[name="type_product"]').live('click', function()
		{
			// Reset settings
			$('li.tab-row a[id*="Pack"]').hide();
			$('li.tab-row a[id*="VirtualProduct"]').hide();
			$('div.ppack').hide();
			$('#is_virtual_good').removeAttr('checked');
			$('div.is_virtual_good').hide();
			$('#is_virtual').val(0);
			$("#virtual_good_attributes").hide();

			product_type = $(this).val();

			// until a product is added in the pack
			// if product is PTYPE_PACK, save buttons will be disabled
			if (product_type == product_type_pack)
			{
				//when you change the type of the product, directly go to the pack tab
				$('li.tab-row a[id*="Pack"]').show().click();
				$('#ppack').val(1).attr('checked', true).attr('disabled', 'disabled');
				$('#ppackdiv').show();
				// If the pack tab has not finished loaded the changes will be made when the loading event is triggered
				$("#product-tab-content-Pack").bind('loaded', function(){
					$('#ppack').val(1).attr('checked', true).attr('disabled', 'disabled');
					$('#ppackdiv').show();
				});
				$("#product-tab-content-Quantities").bind('loaded', function(){
					$('.stockForVirtualProduct').show();
				});

				$('li.tab-row a[id*="Shipping"]').show();
				$('#condition').removeAttr('disabled');
				$('#condition option[value=new]').removeAttr('selected');
				$('.stockForVirtualProduct').show();
				// if pack is enabled, if you choose pack, automatically switch to pack page
			}
			else if (product_type == product_type_virtual)
			{
				$('li.tab-row a[id*="VirtualProduct"]').show().click();

				tabs_manager.onLoad('VirtualProduct', function(){
					$('#is_virtual_good').attr('checked', true);
					$('#virtual_good').show();
					$('#is_virtual').val(1);
					$("#virtual_good_attributes").show();
				});

				tabs_manager.onLoad('Quantities', function(){
					$('.stockForVirtualProduct').hide();
				});

				$('li.tab-row a[id*="Shipping"]').hide();

				tabs_manager.onLoad('Informations', function(){
					$('#condition').attr('disabled', 'disabled');
					$('#condition option[value=refurbished]').removeAttr('selected');
					$('#condition option[value=used]').removeAttr('selected');
				});
			}
			else
			{
				// 3rd case : product_type is PTYPE_SIMPLE (0)
				$('li.tab-row a[id*="Shipping"]').show();
				$('#condition').removeAttr('disabled');
				$('#condition option[value=new]').removeAttr('selected');
				$('.stockForVirtualProduct').show();
			}
			// this handle the save button displays and warnings
			handleSaveButtons();
		});
	},
	/*'setup_tinymce': function(){
		// change each by click to load only on click
		$(".autoload_rte").each(function(e){
			tinySetup({
				mode :"exact",
				editor_selector :"autoload_rte",
				elements : $(this).attr("id"),
				theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
				setup : function(ed) {
					//Count the total number of the field
					ed.onKeyUp.add(function(ed, e) {
						tinyMCE.triggerSave();
						textarea = $('#'+ed.id);
						max = textarea.parent('div').find('span.counter').attr('max');
						if (max != 'none')
						{
							textarea_value = textarea.val();
							count = stripHTML(textarea_value).length;
							rest = max - count;
							if (rest < 0)
								textarea.parent('div').find('span.counter').html('<span style="color:red;"> ' + mce_maximum + ' ' + max + ' ' + mce_characters + ' : ' + rest + '</span>');
							else
								textarea.parent('div').find('span.counter').html(' ');
						}
					});
				}
			});
		});
	},*/
	'onReady' : function(){
		product_tabs['Informations'].bindAvailableForOrder();
		product_tabs['Informations'].bindTagImage();
		product_tabs['Informations'].switchProductType();
	}
}

product_tabs['Pack'] = {
	'bindPackEvents' : function (){
		if ($('#ppack').attr('checked'))
		{
			$('#ppack').attr('disabled', 'disabled');
			$('#ppackdiv').show();
		}

		$('div.ppack').hide();

		$('#curPackItemName').autocomplete('ajax_products_list.php', {
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
			},
			extraParams: {
				excludeIds : getSelectedIds(),
				excludeVirtuals : 1
			}
		}).result(function(event, item){
			$('#curPackItemId').val(item[1]);
		});

		$('#add_pack_item').bind('click', addPackItem);

		function addPackItem()
		{
			var curPackItemId = $('#curPackItemId').val();
			var curPackItemName = $('#curPackItemName').val();
			var curPackItemQty = $('#curPackItemQty').val();
			if (curPackItemId == '' || curPackItemName == '')
			{
				jAlert(msg_select_one);
				return false;
			}
			else if (curPackItemId == '' || curPackItemQty == '')
			{
				jAlert(msg_set_quantity);
				return false;
			}

			var lineDisplay = curPackItemQty+ 'x ' +curPackItemName;

			var divContent = $('#divPackItems').html();
			divContent += lineDisplay;
			divContent += '<span onclick="delPackItem(' + curPackItemId + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';

			// QTYxID-QTYxID
			// @todo : it should be better to create input for each items and each qty
			// instead of only one separated by x, - and ¤
			var line = curPackItemQty+ 'x' +curPackItemId;

			$('#inputPackItems').val($('#inputPackItems').val() + line  + '-');
			$('#divPackItems').html(divContent);
				$('#namePackItems').val($('#namePackItems').val() + lineDisplay + '¤');

			$('#curPackItemId').val('');
			$('#curPackItemName').val('');
			$('p.listOfPack').show();

			$('#curPackItemName').setOptions({
				extraParams: {
					excludeIds :  getSelectedIds()
				}
			});
			// show / hide save buttons
			// if product has a name
			handleSaveButtons();
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

			// if no item left in the pack, disable save buttons
			handleSaveButtons();
		}

		function getSelectedIds()
		{
			var ids = '';
			if (typeof(id_product) != 'undefined')
				ids += id_product + ',';
			ids += $('#inputPackItems').val().replace(/\d*x/g, '').replace(/\-/g,',');
			ids = ids.replace(/\,$/,'');
			return ids;
		}
	},
	'onReady' : function(){
		product_tabs['Pack'].bindPackEvents();
	}
}

product_tabs['Features'] = {
	'onReady' : function(){
		displayFlags(languages, id_language, allowEmployeeFormLang);
	}
}

var tabs_manager = new ProductTabsManager();
tabs_manager.setTabs(product_tabs);

$(document).ready(function() {
	// The manager schedules the onReady() methods of each tab to be called when the tab is loaded
	tabs_manager.onReady();

	updateCurrentText();

	$("#name_"+defaultLanguage.id_lang+",#link_rewrite_"+defaultLanguage.id_lang)
		.live("change", function(e)
		{
			if(typeof e == KeyboardEvent)
				if(isArrowKey(e))
					return;
			$(this).trigger("handleSaveButtons");
		});
	// bind that custom event
	$("#name_"+defaultLanguage.id_lang+",#link_rewrite_"+defaultLanguage.id_lang)
		.live("handleSaveButtons", function(e)
		{
			handleSaveButtons()
		});
	updateFriendlyURL();

	// Pressing enter in an input field should not submit the form
	$('#product_form').delegate('input', 'keypress', function(e){
			var code = null;
		code = (e.keyCode ? e.keyCode : e.which);
		return (code == 13) ? false : true;
	});
});
