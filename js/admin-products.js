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

// array of product tab objects containing methods and dom bindings
// The ProductTabsManager instance will make sure the onReady() methods of each tabs are executed once the tab has loaded
var product_tabs = [];

product_tabs['Customization'] = new function(){
	this.onReady = function(){
		if (display_multishop_checkboxes)
		ProductMultishop.checkAllCustomization();
	}
}
product_tabs['Combinations'] = new function(){
	var self = this;
	this.bindEdit = function(){
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
					self.removeButtonCombination('update');
					$.scrollTo('#add_new_combination', 1200, { offset: -100 });
					var wholesale_price = Math.abs(data[0]['wholesale_price']);
					var price = data[0]['price'];
					var weight = data[0]['weight'];
					var unit_impact = data[0]['unit_price_impact'];
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
					self.fillCombination(
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
						available_date
					);
					calcImpactPriceTI();
				}
			});
		}
	};

	this.defaultProductAttribute = function(url, item){
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

					// Reset previous default attribute display
					var previous = $('a[name=is_default]');
					previous.closest('tr').attr('style', '');
					previous.show();
					previous.attr('name', '');

					// Update new default attribute display
					$(item).closest('tr').css('background','#BDE5F8');
					$(item).hide();
					$(item).attr('name', 'is_default');
				}
				else
					showErrorMessage(data.message);
			}
		});
	};

	this.bindDefault = function(){
		$('a[name=is_default]').hide();
		$('table[name=list_table]').delegate('a.default', 'click', function(e){
			e.preventDefault();
			self.defaultProductAttribute(this.href, this);
		});
	};

	this.deleteProductAttribute = function(url, parent){
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
	};

	this.bindDelete = function() {
		$('table[name=list_table]').delegate('a.delete', 'click', function(e){
			e.preventDefault();
			self.deleteProductAttribute(this.href, $(this).closest('tr'));
		});
	};

	this.removeButtonCombination = function(item)
	{
		$('#add_new_combination').show();
		$('.process-icon-newCombination').removeClass('toolbar-new');
		$('.process-icon-newCombination').addClass('toolbar-cancel');
		$('#desc-product-newCombination div').html($('#ResetBtn').val());
		$('id_product_attribute').val(0);
		self.init_elems();
	};

	this.addButtonCombination = function(item)
	{
		$('#add_new_combination').hide();
		$('.process-icon-newCombination').removeClass('toolbar-cancel');
		$('.process-icon-newCombination').addClass('toolbar-new');
		$('#desc-product-newCombination div').html(msg_new_combination);
	};

	this.bindToggleAddCombination = function (){
		$('#desc-product-newCombination').click(function() {
			if ($('.process-icon-newCombination').hasClass('toolbar-new'))
				self.removeButtonCombination('add');
			else
			{
				self.addButtonCombination('add');
				$('#id_product_attribute').val(0);
			}
		});
	};

	this.fillCombination = function(wholesale_price, price_impact, weight_impact, unit_impact, reference,
	ean, quantity, image, old_attr, id_product_attribute, default_attribute, eco_tax, upc, minimal_quantity, available_date)
	{
		var link = '';
		self.init_elems();
		$('#stock_mvt_attribute').show();
		$('#initial_stock_attribute').hide();
		$('#attribute_quantity').html(quantity);
		$('#attribute_quantity').show();
		$('#attr_qty_stock').show();

		$('#attribute_minimal_quantity').val(minimal_quantity);

		getE('attribute_reference').value = reference;

		getE('attribute_ean13').value = ean;
		getE('attribute_upc').value = upc;
		getE('attribute_wholesale_price').value = Math.abs(wholesale_price);
		getE('attribute_price').value = ps_round(Math.abs(price_impact), 2);
		getE('attribute_priceTEReal').value = Math.abs(price_impact);
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

		$("#add_new_combination").show();

		/* Reset all combination images */
		combinationImages = $('#id_image_attr').find("input[id^=id_image_attr_]");
		combinationImages.each(function() {
			this.checked = false;
		});

		/* Check combination images */
		if (typeof(combination_images[id_product_attribute]) != 'undefined')
			for (i = 0; i < combination_images[id_product_attribute].length; i++)
				$('#id_image_attr_' + combination_images[id_product_attribute][i]).attr('checked', true);
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
	};

	this.init_elems = function()
	{
		var impact = getE('attribute_price_impact');
		var impact2 = getE('attribute_weight_impact');
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
	};

	this.onReady = function(){
		self.bindEdit();
		self.bindDefault();
		self.bindDelete();
		self.bindToggleAddCombination();
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllCombinations();
	};
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
	$("#disableSaveMessage").remove();
	if ($("#name_" + id_lang_default).val() == "" && (!display_multishop_checkboxes || $('input[name=\'multishop_check[name][' + id_lang_default + ']\']').prop('checked')))
	{
		msg[i++] = empty_name_msg;
	}
	// check friendly_url_[defaultlangid] only if name is ok
	else if ($("#link_rewrite_" + id_lang_default).val() == "" && (!display_multishop_checkboxes || $('input[name=\'link_rewrite[name][' + id_lang_default + ']\']').prop('checked')))
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

product_tabs['Seo'] = new function(){
	var self = this;

	this.onReady = function() {
		// Enable writing of the product name when the friendly url field in tab SEO is loaded
		$('.copy2friendlyUrl').removeAttr('disabled');

		displayFlags(languages, id_language, allowEmployeeFormLang);

		if (display_multishop_checkboxes)
			ProductMultishop.checkAllSeo();
	};
}

product_tabs['Prices'] = new function(){
	var self = this;
	// Bind to show/hide new specific price form
	this.toggleSpecificPrice = function (){
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
	};

	/**
	 * Ajax call to delete a specific price
	 *
	 * @param ids
	 * @param token
	 * @param parent
	 */
	this.deleteSpecificPrice = function (url, parent){
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
				if (data !== null)
				{
					if (data.status == 'ok')
					{
						showSuccessMessage(data.message);
						parent.remove();
					}
					else
						showErrorMessage(data.message);
				}
			}
		});
	};

	// Bind to delete specific price link
	this.bindDelete = function(){
		$('#specific_prices_list').delegate('a[name="delete_link"]', 'click', function(e){
			e.preventDefault();
			self.deleteSpecificPrice(this.href, $(this).parents('tr'));
		})
	};

	this.loadInformations = function(select_id, action)
	{
		id_shop = $('#sp_id_shop').val();
		$.ajax({
			url: product_url + '&action='+action+'&ajax=true&id_shop='+id_shop,
			success: function(data) {
				$(select_id + ' option').not(':first').remove();
				$(select_id).append(data);
			}
		});
	}

	this.onReady = function(){
		self.toggleSpecificPrice();
		self.deleteSpecificPrice();
		self.bindDelete();

		$('#sp_id_shop').change(function() {
			self.loadInformations('#sp_id_group','getGroupsOptions');
			self.loadInformations('#spm_currency_0', 'getCurrenciesOptions');
			self.loadInformations('#sp_id_country', 'getCountriesOptions');
		});
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllPrices();
	};
}

product_tabs['Associations'] = new function(){
	var self = this;
	this.initAccessoriesAutocomplete = function (){
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
			}).result(self.addAccessory);

		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : self.getAccessoriesIds()
			}
		});
	};

	this.getAccessoriesIds = function()
	{
		if ($('#inputAccessories').val() === undefined)
			return '';
		var ids = id_product + ',';
		ids += $('#inputAccessories').val().replace(/\\-/g,',').replace(/\\,$/,'');
		ids = ids.replace(/\,$/,'');

		return ids;
	}

	this.addAccessory = function(event, data, formatted)
	{
		if (data == null)
			return false;
		var productId = data[1];
		var productName = data[0];

		var $divAccessories = $('#divAccessories');
		var $inputAccessories = $('#inputAccessories');
		var $nameAccessories = $('#nameAccessories');

		/* delete product from select + add product line to the div, input_name, input_ids elements */
		$divAccessories.html($divAccessories.html() + productName + ' <span class="delAccessory" name="' + productId + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />');
		$nameAccessories.val($nameAccessories.val() + productName + '¤');
		$inputAccessories.val($inputAccessories.val() + productId + '-');
		$('#product_autocomplete_input').val('');
		$('#product_autocomplete_input').setOptions({
			extraParams: {excludeIds : self.getAccessoriesIds()}
		});
	};

	this.delAccessory = function(id)
	{
		var div = getE('divAccessories');
		var input = getE('inputAccessories');
		var name = getE('nameAccessories');

		// Cut hidden fields in array
		var inputCut = input.value.split('-');
		var nameCut = name.value.split('¤');

		if (inputCut.length != nameCut.length)
			return jAlert('Bad size');

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
				div.innerHTML += nameCut[i] + ' <span class="delAccessory" name="' + inputCut[i] + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
			}
			else
				$('#selectAccessories').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
		}

		$('#product_autocomplete_input').setOptions({
			extraParams: {excludeIds : self.getAccessoriesIds()}
		});
	};

	/**
	 * Update the manufacturer select element with the list of existing manufacturers
	 */
	this.getManufacturers = function(){
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
	};

	this.onReady = function(){
		self.initAccessoriesAutocomplete();
		self.getManufacturers();
		$('#divAccessories').delegate('.delAccessory', 'click', function(){
			self.delAccessory($(this).attr('name'));
		});

		if (display_multishop_checkboxes)
			ProductMultishop.checkAllAssociations();
	};
}

product_tabs['Attachments'] = new function(){
	var self = this;
	this.bindAttachmentEvents = function (){
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
	};

	this.onReady = function(){
		self.bindAttachmentEvents();
	};
}

product_tabs['Informations'] = new function(){
	var self = this;
	this.bindAvailableForOrder = function (){
		$("#available_for_order").click(function()
		{
			if ($(this).is(':checked') || ($('input[name=\'multishop_check[show_price]\']').lenght && !$('input[name=\'multishop_check[show_price]\']').prop('checked')))
			{
				$('#show_price').attr('checked', true);
				$('#show_price').attr('disabled', true);
			}
			else
			{
				$('#show_price').attr('disabled', false);
			}
		});
				
		if ($('#active_on').prop('checked'))
		{
			showRedirectProductOptions(false);
			showRedirectProductSelectOptions(false);
		}
		else
			showRedirectProductOptions(true);
			
		$('#redirect_type').change(function () {
			redirectSelectChange();
		});
		
		$('#related_product_autocomplete_input')
			.autocomplete('ajax_products_list.php?excludeIds='+id_product, {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
						return item[0]+' - '+item[1];
				}
			}).result(function(e, i){  
					if(i != undefined)
						addRelatedProduct(i[1], i[0]);
					$(this).val('');
		       });
		 addRelatedProduct(id_product_redirected, product_name_redirected);
	};

	this.bindTagImage = function (){
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
	};

	this.switchProductType = function(){
		if (product_type == product_type_pack)
		{
			$('#pack_product').attr('checked', true);
		}
		else if (product_type == product_type_virtual)
		{
			$('#virtual_product').attr('checked', true);
			$('#condition').attr('disabled', true);
			$('#condition option[value=new]').attr('selected', true);
		}
		else
		{
			$('#simple_product').attr('checked', true);
		}

		$('input[name="type_product"]').live('click', function()
		{
			// Reset settings
			$('li.tab-row a[id*="Pack"]').hide();
			$('li.tab-row a[id*="VirtualProduct"]').hide();
			$('div.ppack').hide();
			$('div.is_virtual_good').hide();
			$('#is_virtual').val(0);
			tabs_manager.onLoad('VirtualProduct', function(){
				$('#is_virtual_good').removeAttr('checked');
			});

			product_type = $(this).val();

			// until a product is added in the pack
			// if product is PTYPE_PACK, save buttons will be disabled
			if (product_type == product_type_pack)
			{
				//when you change the type of the product, directly go to the pack tab
				$('li.tab-row a[id*="Pack"]').show().click();
				$('#ppack').val(1).attr('checked', true).attr('disabled', true);
				$('#ppackdiv').show();
				// If the pack tab has not finished loaded the changes will be made when the loading event is triggered
				$("#product-tab-content-Pack").bind('loaded', function(){
					$('#ppack').val(1).attr('checked', true).attr('disabled', true);
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
				if (has_combinations)
				{
					$('#simple_product').attr('checked', true);
					$('#warn_virtual_combinations').show();
				}
				else
				{
					$('li.tab-row a[id*="VirtualProduct"]').show().click();
					$('#is_virtual').val(1);

					tabs_manager.onLoad('VirtualProduct', function(){
						$('#is_virtual_good').attr('checked', true);
						$('#virtual_good').show();
					});

					tabs_manager.onLoad('Quantities', function(){
						$('.stockForVirtualProduct').hide();
					});

					$('li.tab-row a[id*="Shipping"]').hide();

					tabs_manager.onLoad('Informations', function(){
						$('#condition').attr('disabled', true);
						$('#condition option[value=refurbished]').removeAttr('selected');
						$('#condition option[value=used]').removeAttr('selected');
					});
				}
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
	};
	this.onReady = function(){
		self.bindAvailableForOrder();
		self.bindTagImage();
		self.switchProductType();

		if (display_multishop_checkboxes)
		{
			ProductMultishop.checkAllInformations();
			var active_click = function()
			{
				if (!$('input[name=\'multishop_check[active]\']').prop('checked'))
				{
					$('.draft').hide();
					showOptions(true);
				}
				else
				{
					var checked = $('#active_on').prop('checked');
					toggleDraftWarning(checked);
					showOptions(checked);
				}
			};
			$('input[name=\'multishop_check[active]\']').click(active_click);
			active_click();
		}
	};
}

product_tabs['Pack'] = new function(){
	var self = this;
	this.bindPackEvents = function (){
		if ($('#ppack').prop('checked'))
		{
			$('#ppack').attr('disabled', true);
			$('#ppackdiv').show();
		}

		$('.delPackItem').live('click', function(){
			delPackItem($(this).attr('name'));
		})

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
				excludeVirtuals : 1,
				exclude_packs: 1
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
			divContent += '<span class="delPackItem" name="' + curPackItemId + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';

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
						div.innerHTML += nameCut[i] + ' <span class="delPackItem" name="' + inputQty[1] + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
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
			if ($('#inputPackItems').val() === undefined)
				return '';
			var ids = '';
			if (typeof(id_product) != 'undefined')
				ids += id_product + ',';
			ids += $('#inputPackItems').val().replace(/\d*x/g, '').replace(/\-/g,',');
			ids = ids.replace(/\,$/,'');
			return ids;
		}
	};

	this.onReady = function(){
		self.bindPackEvents();
	}
}

product_tabs['Features'] = new function(){
	this.onReady = function(){
		displayFlags(languages, id_language, allowEmployeeFormLang);
	}
}

product_tabs['Quantities'] = new function(){
	var self = this;
	this.ajaxCall = function(data){
		data.ajaxProductQuantity = 1;
		data.id_product = id_product;
		data.token = token;
		data.ajax = 1;
		data.controller = "AdminProducts";
		data.action = "productQuantity";
		showAjaxMsg(quantities_ajax_waiting);
		$.ajax({
			type: "POST",
			url: "ajax-tab.php",
			data: data,
			dataType: 'json',
			async : true,
			success: function(msg)
			{
				if (msg.error)
				{
					showAjaxError(msg.error);
					return;
				}
				showAjaxSuccess(quantities_ajax_success);
			},
			error: function(msg)
			{
				showAjaxError(msg.error);
			}
		});

		function showAjaxError(msg)
		{
			$('#available_quantity_ajax_error_msg').html(msg);
			$('#available_quantity_ajax_error_msg').show();
			$('#available_quantity_ajax_msg').hide();
			$('#available_quantity_ajax_success_msg').hide();
		}

		function showAjaxSuccess(msg)
		{
			$('#available_quantity_ajax_success_msg').html(msg);
			$('#available_quantity_ajax_error_msg').hide();
			$('#available_quantity_ajax_msg').hide();
			$('#available_quantity_ajax_success_msg').show();
		}

		function showAjaxMsg(msg)
		{
			$('#available_quantity_ajax_msg').html(msg);
			$('#available_quantity_ajax_error_msg').hide();
			$('#available_quantity_ajax_msg').show();
			$('#available_quantity_ajax_success_msg').hide();
		}
	};

	this.refreshQtyAvailabilityForm = function()
	{
		if ($('#depends_on_stock_0').prop('checked'))
		{
			$('.available_quantity').find('input').show();
			$('.available_quantity').find('span').hide();
		}
		else
		{
			$('.available_quantity').find('input').hide();
			$('.available_quantity').find('span').show();
		}
	};

	this.onReady = function(){
		$('#available_date').datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		$('.depends_on_stock').click(function(e)
		{
			self.refreshQtyAvailabilityForm();
			self.ajaxCall( { actionQty: 'depends_on_stock', value: $(this).val() } );
			if($(this).val() == 0)
				$('.available_quantity input').trigger('change');
		});

		$('.advanced_stock_management').click(function(e)
		{
			var val = 0;
			if ($(this).prop('checked'))
				val = 1;

			self.ajaxCall({actionQty: 'advanced_stock_management', value: val});
			if (val == 1)
			{
				$(this).val(1);
				$('#depends_on_stock_1').attr('disabled', false);
			}
			else
			{
				$(this).val(0);
				$('#depends_on_stock_1').attr('disabled', true);
				$('#depends_on_stock_0').attr('checked', true);
				self.ajaxCall({actionQty: 'depends_on_stock', value: 0});
				self.refreshQtyAvailabilityForm();
			}
			self.refreshQtyAvailabilityForm();
		});

		$('.available_quantity').find('input').change(function(e, init_val)
		{
			self.ajaxCall({actionQty: 'set_qty', id_product_attribute: $(this).parent().attr('id').split('_')[1], value: $(this).val()});
		});

		$('.out_of_stock').click(function(e)
		{
			self.refreshQtyAvailabilityForm();
			self.ajaxCall({actionQty: 'out_of_stock', value: $(this).val()});
		});
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllQuantities();

		self.refreshQtyAvailabilityForm();
	};
}

product_tabs['Suppliers'] = new function(){
	var self = this;

	this.manageDefaultSupplier = function() {
		var default_is_ok = false;
		var availables_radio_buttons = [];
		var radio_buttons = $('input[name="default_supplier"]');

		for (i=0; i<radio_buttons.length; i++)
		{
			var item = $(radio_buttons[i]);

			if (item.is(':disabled'))
			{
				if (item.is(':checked'))
				{
					item.removeAttr("checked");
					default_is_ok = false;
				}
			}
			else
			{
				availables_radio_buttons.push(item);
			}
		}

		if (default_is_ok == false)
		{
			for (i=0; i<availables_radio_buttons.length; i++)
			{
				var item = $(availables_radio_buttons[i]);

				if (item.is(':disabled') == false)
				{
					item.attr("checked", true);
					default_is_ok = true;
				}
				break;
			}
		}
	};

	this.onReady = function(){
		$('.supplierCheckBox').live('click', function() {
			var check = $(this);
			var checkbox = $('#default_supplier_'+check.val());
			if (this.checked)
			{
				// enable default radio button associated
				checkbox.removeAttr('disabled');
			}
			else
			{
				// disable default radio button associated
				checkbox.attr('disabled', true);
			}
			//manage default supplier check
			self.manageDefaultSupplier();
		});

		// @TODO: a better way to fix the accordion wrong size bug when the selected page is this page
		setTimeout(function() {
			$('#suppliers_accordion').accordion();
			// If one second was not enough to display page, another resize is needed
			setTimeout(function() {
				$('#suppliers_accordion').accordion('resize');
			}, 3000);
		}, 1000);

		// Resize the accordion once the page is visible because of the bug with accordions initialized
		// inside a display:none block not having the correct size.
		$('#suppliers_accordion').parents('.product-tab-content').bind('displayed', function(){
			$('#suppliers_accordion').accordion("resize");
		});
	};
}

product_tabs['VirtualProduct'] = new function(){
	var self = this;

	this.onReady = function(){
		$(".datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		if ($('#is_virtual_good').prop('checked'))
		{
			$('#virtual_good').show();
			$('#virtual_good_more').show();
		}

		$('.is_virtual_good').hide();

		if ( $('input[name=is_virtual_file]:checked').val() == 1)
		{
			$('#virtual_good_more').show();
			$('#is_virtual_file_product').show();
		}
		else
		{
			$('#virtual_good_more').hide();
			$('#is_virtual_file_product').hide();
		}

		$('input[name=is_virtual_file]').live('change', function(e) {
			if($(this).val() == '1')
			{
				$('#virtual_good_more').show();
				$('#is_virtual_file_product').show();
			}
			else
			{
				$('#virtual_good_more').hide();
				$('#is_virtual_file_product').hide();
			}
		});

		// Bind file deletion
		$(('#product-tab-content-VirtualProduct')).delegate('a.delete_virtual_product', 'click', function(e){
			e.preventDefault();
			if (!$('#virtual_product_id').val())
			{
				$('#upload_input').show();
				$('#virtual_product_name').val('');
				$('#virtual_product_file').val('');
				$('#upload-confirmation').hide().find('span').remove();
			}
			else
			{
				var object = this;
				ajaxAction(this.href, 'deleteVirtualProduct', function(){
					$(object).closest('tr').remove();
					$('#upload_input').show();
					$('#virtual_product_name').val('');
					$('#virtual_product_file').val('');
					$('#virtual_product_id').remove();
				});
			}
		});
	}
}

product_tabs['Warehouses'] = new function(){
	var self = this;

	this.onReady = function(){
		$('.check_all_warehouse').click(function() {
			var check = $(this);
			//get all checkboxes of current warehouse
			var checkboxes = $('input[name*="'+check.val()+'"]');

			for (i=0; i<checkboxes.length; i++)
			{
				var item = $(checkboxes[i]);

				if (item.is(':checked'))
				{
					item.removeAttr("checked");
				}
				else
				{
					item.attr("checked", true);
				}
			}
		});

		// @TODO: a better way to fix the accordion wrong size bug when the selected page is this page
		setTimeout(function() {
			$('#warehouse_accordion').accordion();
		}, 500);

		// Resize the accordion once the page is visible because of the bug with accordions initialized
		// inside a display:none block not having the correct size.
		$('#warehouse_accordion').parents('.product-tab-content').bind('displayed', function(){
			$('#warehouse_accordion').accordion("resize");
		});
	};
}

/**
 * Update the product image list position buttons
 *
 * @param DOM table imageTable
 */
function refreshImagePositions(imageTable)
{
	var reg = /_[0-9]$/g;
	var up_reg  = new RegExp("imgPosition=[0-9]+&");

	imageTable.find("tbody tr").each(function(i,el) {
		$(el).find("td.positionImage").html(i + 1);
	});
	imageTable.find("tr td.dragHandle a:hidden").show();
	imageTable.find("tr td.dragHandle:first a:first").hide();
	imageTable.find("tr td.dragHandle:last a:last").hide();
}

/**
 * Generic ajax call for actions expecting a json return
 *
 * @param url
 * @param action
 * @param success_callback called if the return status is 'ok' (optional)
 * @param failure_callback called if the return status is not 'ok' (optional)
 */
function ajaxAction (url, action, success_callback, failure_callback){
	$.ajax({
		url: url,
		data: {
			id_product: id_product,
			action: action,
			ajax: true
		},
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.confirmations);
				if (typeof success_callback == 'function')
					success_callback();
			}
			else
			{
				showErrorMessage(data.error);
				if (typeof failure_callback == 'function')
					failure_callback();
			}
		},
		error : function(data){
			showErrorMessage(("[TECHNICAL ERROR]"));
		}
	});
};

var ProductMultishop = new function()
{
	var self = this;
	this.load_tinymce = {};

	this.checkField = function(checked, id, type)
	{
		checked = !checked;
		switch (type)
		{
			case 'tinymce' :
				$('#'+id).attr('disabled', checked);
				if (typeof self.load_tinymce[id] == 'undefined')
					self.load_tinymce[id] = checked;
				else
				{
					if (checked)
						tinyMCE.get(id).hide();
					else
						tinyMCE.get(id).show();
				}
				break;

			case 'radio' :
				$('input[name=\''+id+'\']').attr('disabled', checked);
				break;

			case 'show_price' :
				if ($('input[name=\'available_for_order\']').prop('checked'))
					checked = true;
				$('input[name=\''+id+'\']').attr('disabled', checked);
				break;

			case 'price' :
				$('#priceTE').attr('disabled', checked);
				$('#priceTI').attr('disabled', checked);
				break;

			case 'unit_price' :
				$('#unit_price').attr('disabled', checked);
				$('#unity').attr('disabled', checked);
				break;

			case 'attribute_price_impact' :
				$('#attribute_price_impact').attr('disabled', checked);
				$('#attribute_price').attr('disabled', checked);
				$('#attribute_priceTI').attr('disabled', checked);
				break;

			case 'category_box' :
				$('#categories-treeview input[type=checkbox]').attr('disabled', checked);
				break;

			case 'attribute_weight_impact' :
				$('#attribute_weight_impact').attr('disabled', checked);
				$('#attribute_weight').attr('disabled', checked);
				break;

			case 'attribute_unit_impact' :
				$('#attribute_unit_impact').attr('disabled', checked);
				$('#attribute_unity').attr('disabled', checked);
				break;

			default :
				$('#'+id).attr('disabled', checked);
				break;
		}
	};

	this.checkAllInformations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[active]\']').prop('checked'), 'active', 'radio');
		ProductMultishop.checkField($('input[name=\'multishop_check[visibility]\']').prop('checked'), 'visibility');
		ProductMultishop.checkField($('input[name=\'multishop_check[available_for_order]\']').prop('checked'), 'available_for_order');
		ProductMultishop.checkField($('input[name=\'multishop_check[show_price]\']').prop('checked'), 'show_price', 'show_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[online_only]\']').prop('checked'), 'online_only');
		ProductMultishop.checkField($('input[name=\'multishop_check[condition]\']').prop('checked'), 'condition');
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[name]['+v.id_lang+']\']').prop('checked'), 'name_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[description_short]['+v.id_lang+']\']').prop('checked'), 'description_short_'+v.id_lang, 'tinymce');
			ProductMultishop.checkField($('input[name=\'multishop_check[description]['+v.id_lang+']\']').prop('checked'), 'description_'+v.id_lang, 'tinymce');
		});
	};

	this.checkAllPrices = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[wholesale_price]\']').prop('checked'), 'wholesale_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[price]\']').prop('checked'), 'price', 'price');
		ProductMultishop.checkField($('input[name=\'multishop_check[id_tax_rules_group]\']').prop('checked'), 'id_tax_rules_group');
		ProductMultishop.checkField($('input[name=\'multishop_check[unit_price]\']').prop('checked'), 'unit_price', 'unit_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[on_sale]\']').prop('checked'), 'on_sale');
		ProductMultishop.checkField($('input[name=\'multishop_check[on_sale]\']').prop('checked'), 'ecotax');
	};

	this.checkAllSeo = function()
	{
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_title]['+v.id_lang+']\']').prop('checked'), 'meta_title_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_description]['+v.id_lang+']\']').prop('checked'), 'meta_description_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_keywords]['+v.id_lang+']\']').prop('checked'), 'meta_keywords_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[link_rewrite]['+v.id_lang+']\']').prop('checked'), 'link_rewrite_'+v.id_lang);
		});
	};
	
	this.checkAllQuantities = function()
	{
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[available_later]['+v.id_lang+']\']').prop('checked'), 'available_later_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[available_now]['+v.id_lang+']\']').prop('checked'), 'available_now_'+v.id_lang);
		});
	};

	this.checkAllAssociations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[id_category_default]\']').prop('checked'), 'id_category_default');
		ProductMultishop.checkField($('input[name=\'multishop_check[id_category_default]\']').prop('checked'), 'categories-treeview', 'category_box');
	};

	this.checkAllCustomization = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[uploadable_files]\']').prop('checked'), 'uploadable_files');
		ProductMultishop.checkField($('input[name=\'multishop_check[text_fields]\']').prop('checked'), 'text_fields');
	};

	this.checkAllCombinations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_wholesale_price]\']').prop('checked'), 'attribute_wholesale_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_price_impact]\']').prop('checked'), 'attribute_price_impact', 'attribute_price_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_weight_impact]\']').prop('checked'), 'attribute_weight_impact', 'attribute_weight_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_unit_impact]\']').prop('checked'), 'attribute_unit_impact', 'attribute_unit_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_ecotax]\']').prop('checked'), 'attribute_ecotax');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_minimal_quantity]\']').prop('checked'), 'attribute_minimal_quantity');
		ProductMultishop.checkField($('input[name=\'multishop_check[available_date_attribute]\']').prop('checked'), 'available_date_attribute');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_default]\']').prop('checked'), 'attribute_default');
	};
};

var tabs_manager = new ProductTabsManager();
tabs_manager.setTabs(product_tabs);

$(document).ready(function() {
	// The manager schedules the onReady() methods of each tab to be called when the tab is loaded
	tabs_manager.init();
	updateCurrentText();
	$("#name_" + id_lang_default + ",#link_rewrite_" + id_lang_default)
		.live("change", function(e)
		{
			$(this).trigger("handleSaveButtons");
		});
	// bind that custom event
	$("#name_" + id_lang_default + ",#link_rewrite_" + id_lang_default)
		.live("handleSaveButtons", function(e)
		{
			handleSaveButtons()
		});

	// Pressing enter in an input field should not submit the form
	$('#product_form').delegate('input', 'keypress', function(e){
			var code = null;
		code = (e.keyCode ? e.keyCode : e.which);
		return (code == 13) ? false : true;
	});
});
