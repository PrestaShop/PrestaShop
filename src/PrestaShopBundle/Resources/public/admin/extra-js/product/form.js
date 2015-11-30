/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
	form.init();
	nav.init();
	redirectionStrategy.init();
	featuresCollection.init();
	defaultCategory.init();
	nestedCategories.init();
	formCategory.init();
	stock.init();
	supplier.init();
	combinations.init();
	combinationGenerator.init();
	specificPrices.init();
	warehouseCombinations.init();

	/** update price and shortcut price field on change */
	$('#form_step1_price_shortcut, #form_step2_price').keyup(function(){
		if($(this).attr('id') === 'form_step1_price_shortcut'){
			$('#form_step2_price').val($(this).val());
		}else{
			$('#form_step1_price_shortcut').val($(this).val());
		}
	});

	/** pack fields display management */
	$('#form_step1_type_product').change(function(){
		if($(this).val() == 1){
			$('#pack_stock_type').show();
			$('#js_form_step1_inputPackItems').show();
		}else{
			$('#pack_stock_type').hide();
			$('#js_form_step1_inputPackItems').hide();
		}
	});
	$('#form_step1_type_product').change();
});

/**
 * Redirection strategy management
 */
var redirectionStrategy = (function() {
	var redirectStrategyElems = $('.js-redirect-strategy', '#step6');
	var redirectTypeElem = $('#form_step6_redirect_type');
	var activeElem = $('input[name="form[step1][active]"]', '#step1');

	/**
	 * Hide or show all redirect fields
	 * @param {int} active - If product is active or not
	 */
	function hideShowRedirectElems(active){
		if(active == 1){
			redirectStrategyElems.hide();
		}else{
			redirectStrategyElems.show();
			hideShowRedirectToProduct();
		}

	}

	/** Hide or show the input product selector */
	function hideShowRedirectToProduct(){
		if(redirectTypeElem.val() == '404'){
			$('#id-product-redirected').hide();
		}else{
			$('#id-product-redirected').show();
		}
	}

	return {
		'init': function() {
			if($('input[name="form[step1][active]"]:checked', '#step1').val() == 1){
				redirectStrategyElems.hide();
			}else{
				hideShowRedirectToProduct();
			}

			/** On active button change */
			activeElem.change(function(){
				hideShowRedirectElems($(this).val());
			});

			/** On redirect type select change */
			redirectTypeElem.change(function(){
				hideShowRedirectToProduct();
			});
		}
	};
})();

/**
 * Nested categories management
 */
var nestedCategories = (function() {
	return {
		'init': function() {
			$('#form_step1_categories').categorytree();

			/** add to default category selector each pre seleted category */
			$.each($('#form_step1_categories input[type=checkbox]:checked'), function(){
				defaultCategory.add($(this));
			});

			/** On category event click, add it to default category selector */
			$(document).on('click', '#form_step1_categories input[type=checkbox]', function() {
				if($('#form_step1_categories input[type=checkbox]:checked').length === 0){
					return false;
				}
				defaultCategory.add($(this));
			});
		}
	};
})();

/**
 * Default category management
 */
var defaultCategory = (function() {
	var elem = $('#form_step1_id_category_default');
	return {
		'init': function() {
			/** remove all categories from selector, except pre defined */
			elem.find('option:not([value=' + elem.val() + '])').remove();
		},
		/**
		 * Add/remove a category to default category selector
		 * @param {object} obj - The clicked nested category
		 */
		'add': function(obj) {
			if (obj.is(':checked')){
				if(obj.val() != $('#form_step1_id_category_default').val()){
					elem.append('<option value="'+ obj.val() +'">'+ obj.parent().text() +'</option>');
				}
			} else  {
				elem.find('option[value=' + obj.val() + ']').remove();
			}
		}
	};
})();

/**
 * Form category management
 */
var formCategory = (function() {
	var elem = $('#form_step1_new_category');

	/** Send category form and it to nested categories */
	function send(){
		$.ajax({
			type: 'POST',
			url: elem.attr('data-action'),
			data: {
				'form[category][name]': $('#form_step1_new_category_name').val(),
				'form[category][id_parent]': $('#form_step1_new_category_id_parent').val(),
				'form[_token]': $('#form #form__token').val()
			},
			beforeSend: function() {
				$('button.submit', elem).attr('disabled', 'disabled');
				$('.help-block', elem).remove();
				$('*.has-error', elem).removeClass('has-error');
			},
			success: function(response){
				$('#form_step1_new_category_name').val('');
				var html = '<li><div class="checkbox"><label><input type="checkbox" name="form[step1][categories][tree][]" value="'+response.category.id+'">'+response.category.name[1]+'</label></div></li>';
				var parentElement = $('#form_step1_categories input[value='+response.category.id_parent+']').parent().parent();
				if(parentElement.next('ul').length === 0){
					html = '<ul>' + html + '</ul>';
					parentElement.append(html);
				}else{
					parentElement.next('ul').append(html);
				}
			},
			error: function(response){
				$.each(jQuery.parseJSON(response.responseText), function(key, errors){
					var html = '<span class="help-block"><ul class="list-unstyled">';
					$.each(errors, function(key, error){
						html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
					});
					html += '</ul></span>';

					$('#form_step1_new_'+key).parent().append(html);
					$('#form_step1_new_'+key).parent().addClass('has-error');
				});
			},
			complete: function(){
				$('#form_step1_new_category button.submit').removeAttr('disabled');
			}
		});
	}

	return {
		'init': function() {
			/** remove all categories from selector, except pre defined */
			elem.find('button.submit').click(function(){
				send();
			});
		}
	};
})();

/**
 * Feature collection management
 */
var featuresCollection = (function() {

	var collectionHolder = $('ul.featureCollection');
	var newItemBtn = $('<a href="#" class="btn btn-primary btn-xs">+</a>');
	var newItem = $('<li class="add"></li>').append(newItemBtn);
	var removeLink = '<a href="#" class="delete btn btn-primary btn-xs">-</a>';

	/** Add a feature */
	function add(){
		var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, collectionHolder.children().length);
		newItem.before($('<li></li>').prepend(removeLink, newForm));
	}

	/**
	 * Remove a feature
	 * @param {object} elem - The clicked link
	 */
	function remove(elem){
		elem.parent().remove();
	}

	return {
		'init': function() {
			/** Create add button, and vreate first form */
			collectionHolder.append(newItem);
			add();

			/** Click event on the add button */
			newItemBtn.on('click', function(e) {
				e.preventDefault();
				add();
			});

			/** Click event on the remove button */
			$(document).on('click', 'ul.featureCollection a.delete', function(e) {
				e.preventDefault();
				remove($(this));
			});

			/** On feature selector event change, refresh possible values list */
			$(document).on('change', 'ul.featureCollection select.feature-selector', function() {
				var selector = $(this).parent().parent().parent().find('.feature-value-selector');
				$.ajax({
					url: $(this).attr('data-action')+'/'+$(this).val(),
					success: function(response){
						selector.empty();
						$.each(response, function(key, val){
							selector.append($('<option></option>').attr('value', key).text(val));
						});
					}
				});
			});
		}
	};
})();

/**
 * Suppliers management
 */
var supplier = (function() {
	var defaultSupplierRow = $('#form_step6_default_supplier').parent().parent();
	var isInit = false;
	return {
		'init': function() {
			/** On supplier select, hide or show the default supplier selector */
			var supplierInput = $('#form_step6_suppliers input');
			supplierInput.change(function(){
				if(supplierInput.length >= 1 && $('#form_step6_suppliers input:checked').length >= 1){
					defaultSupplierRow.show();
				} else {
					defaultSupplierRow.hide();
				}
				supplierCombinations.refresh();
			});

			//default display
			if(supplierInput.length >= 1 && $('#form_step6_suppliers input:checked').length >= 1){
				defaultSupplierRow.show();
			} else {
				defaultSupplierRow.hide();
			}
		}
	};
})();

/**
 * Suppliercombination collection management
 */
var supplierCombinations = (function() {
	var collectionHolder = $('#supplier_combination_collection');

	return {
		'refresh': function() {
			var suppliers = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]:checked').map(function(){return $(this).val();}).get();
			var url = collectionHolder.attr('data-url')+'/'+$('#form_id_product').val()+(suppliers.length > 0 ? '/'+suppliers.join('-') : '');

			$.ajax({
				url: url,
				success: function(response){
					collectionHolder.empty().append(response);
				}
			});
		}
	};
})();

/**
 * Quantities management
 */
var stock = (function() {
	return {
		'init': function() {
			/** Update qty_0 and shortcut qty_0 field on change */
			$('#form_step1_qty_0_shortcut, #form_step3_qty_0').keyup(function(){
				if($(this).attr('id') === 'form_step1_qty_0_shortcut'){
					$('#form_step3_qty_0').val($(this).val());
				}else{
					$('#form_step1_qty_0_shortcut').val($(this).val());
				}
			});

			/** if GSA : Show depends_on_stock choice only if advanced_stock_management checked */
			$('#form_step3_advanced_stock_management').on('change', function(e) {
				if(e.target.checked){
					$('#depends_on_stock_div').show();
				}else{
					$('#depends_on_stock_div').hide();
				}
				warehouseCombinations.refresh();
			});

			/** if GSA activation change on 'depend on stock', update quantities fields */
			$('#form_step3_depends_on_stock_0, #form_step3_depends_on_stock_1').on('change', function(e) {
				stock.updateQtyFields();
				warehouseCombinations.refresh();
			});
			stock.updateQtyFields();
		},
		'updateQtyFields': function() {
			/** if combinations exists, hide common quantity field */
			if ($('#accordion_combinations > div.combination[id^="attribute_"]').length > 0) {
				$('#form_step3_qty_0').attr('readonly', 'readonly');
				$('#product_qty_0_shortcut_div').hide();

				if ($('#form_step3_depends_on_stock_1:checked').length == 1) {
					$('#accordion_combinations > div.combination[id^="attribute_"] input[id^="form_step3_combinations_"][id$="_attribute_quantity"]').removeAttr('readonly');
				} else {
					$('#accordion_combinations > div.combination[id^="attribute_"] input[id^="form_step3_combinations_"][id$="_attribute_quantity"]').attr('readonly', 'readonly');
				}
				return;
			} /** else, there is no combinations */

			/** if GSA and if is manual */
			if ($('#form_step3_depends_on_stock_1:checked').length == 1) {
				$('#form_step3_qty_0').removeAttr('readonly');
				$('#product_qty_0_shortcut_div').show();
				return;
			}

			/** display fallback */
			$('#form_step3_qty_0').attr('readonly', 'readonly');
			$('#product_qty_0_shortcut_div').hide();
		}
	};
})();


/**
 * Navigation management
 */
var nav = (function() {
	return {
		'init': function() {
			/** Manage tabls hash routes */
			var hash = document.location.hash;
			var prefix = 'tab-';
			if (hash) {
				$('.nav-tabs a[href=' + hash.replace(prefix,'') + ']').tab('show');
			}

			$('.nav-tabs a').on('shown.bs.tab', function (e) {
				if(e.target.hash) {
					onTabSwitch(e.target.hash);
					window.location.hash = e.target.hash.replace('#', '#' + prefix);
				}
			});

			$('.btn-next').click(function(){
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			});

			$('.btn-prev').click(function(){
				$('.nav-tabs > .active').prev('li').find('a').trigger('click');
			});

			/** auto save form on switching tabs (if form is not processing and product id is not defined) */
			function onTabSwitch(currentTab){
				if(currentTab != '#step1' && $('#form .btn-submit').attr('disabled') != 'disabled' && $('#form_id_product').val() === '0'){
					form.send();
				} else if (currentTab == '#step2' && $('#form_id_product').val() !== 0){
					/** each switch to price tab, reload combinations into specific price form */
					specificPrices.refreshCombinationsList();
				}
			}
		}
	};
})();

/**
 * Combinations creator management
 */
var combinationGenerator = (function() {

	/** Create Bloodhound engine */
	function getEngine(){
		return new Bloodhound({
			datumTokenizer: function(d) {
				return Bloodhound.tokenizers.whitespace(d.label);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			prefetch: {
				url: $('#form_step3_attributes').attr('data-prefetch'),
				cache: false
			}
		});
	}

	/** Generate combinations */
	function generate(){

		/**
		 * Combination row maker
		 * @param {object} attribute
		 */
		var combinationRowMaker = function(attribute){
			var combinationsLength = $('#accordion_combinations').children().length;
			var form = $('#accordion_combinations').attr('data-prototype').replace(/__name__/g, combinationsLength);

			var row = '<div class="panel panel-default combination" id="attribute___id_attribute__">\
				<div class="panel-title">\
					<div class="col-lg-4 pull-left">\
						<a data-toggle="collapse" data-parent="#accordion_combinations" href="#combination_form___name__">__combination_name__</a>\
					</div>\
					<div class="col-lg-4 pull-left">\
					    <span class="small col-lg-4 attribute-weight">'+ attribute.attribute_weight +' '+ $('#accordion_combinations').attr('data-weight-unit') +'</span>\
						<span class="small col-lg-4 attribute-price-display">0,00 €</span>\
						<span class="small col-lg-4 attribute-quantity">0</span>\
					</div>\
				</div>\
				<div class="col-lg-2 pull-right text-right">\
					<a class="btn btn-default btn-sm" data-toggle="collapse" data-parent="#accordion_combinations" href="#combination_form___name__">Open</a>\
					<a href="__delete_link__" class="btn btn-default btn-sm delete" data="__id_attribute__">delete</a>\
				</div>\
				<div class="clearfix"></div>\
				<div id="combination_form___name__" class="panel-collapse collapse">\
					<div class="panel-body">__form__</div>\
				</div>\
			</div>';

			var newRow = row.replace(/__name__/g, combinationsLength)
				.replace(/__combination_name__/g, attribute.name)
				.replace(/__delete_link__/g, $('#accordion_combinations').attr('data-action-delete')+'/'+attribute.id_product_attribute+'/'+$('#form_id_product').val())
				.replace(/__id_attribute__/g, attribute.id_product_attribute)
				.replace(/__form__/g, form);

			$('#accordion_combinations').prepend(newRow);
			$('#form_step3_combinations_' + combinationsLength + '_id_product_attribute').val(attribute.id_product_attribute);

			stock.updateQtyFields();
		};

		$.ajax({
			type: 'POST',
			url: $('#form_step3_attributes').attr('data-action'),
			data: $('#attributes-generator input.attribute-generator, #form_id_product').serialize(),
			beforeSend: function() {
				$('#create-combinations').attr('disabled', 'disabled');
			},
			success: function(response){
				$.each(response, function(key, val){
					combinationRowMaker(val);
				});

				/** initialize form */
				$('input.attribute-generator').remove();
				$('#attributes-generator div.token').remove();
			},
			complete: function(){
				$('#create-combinations').removeAttr('disabled');
				supplierCombinations.refresh();
				warehouseCombinations.refresh();
			}
		});
	}

	return {
		'init': function() {
			/** init input typeahead */
			$('#form_step3_attributes').tokenfield({typeahead: [
				null, {
					source: getEngine(),
					display: 'label'
				}]
			});

			/** On event "tokenfield:createdtoken" : store attributes in input when add a token */
			$('#form_step3_attributes').on('tokenfield:createdtoken', function(e) {
				$('#attributes-generator').append('<input type="hidden" id="attribute-generator-'+e.attrs.value+'" class="attribute-generator" value="'+e.attrs.value+'" name="options['+e.attrs.data.id_group+']['+e.attrs.value+']" />');
			});

			/** On event "tokenfield:removedtoken" : remove stored attributes input when remove token */
			$('#form_step3_attributes').on('tokenfield:removedtoken', function(e) {
				$('#attribute-generator-' + e.attrs.value).remove();
			});

			$('#create-combinations').click(function(){
				generate();
			});
		}
	};
})();

/**
 * Combination management
 */
var combinations = (function() {
	/**
	 * Remove a combination
	 * @param {object} elem - The clicked link
	 */
	function remove(elem){
		var combinationElem = $('#attribute_'+elem.attr('data'));
		$.ajax({
			type: 'GET',
			url: elem.attr('href'),
			beforeSend: function() {
				elem.attr('disabled', 'disabled');
			},
			success: function(response) {
				combinationElem.remove();
				showSuccessMessage(response.message);
				stock.updateQtyFields();
			},
			error: function(response){
				showErrorMessage(jQuery.parseJSON(response.responseText).message);
			},
			complete: function(){
				elem.removeAttr('disabled');
				supplierCombinations.refresh();
				warehouseCombinations.refresh();
			}
		});
	}

	return {
		'init': function() {
			var weightUnit = $('#accordion_combinations').attr('data-weight-unit');

			/** delete combination */
			$(document).on('click', '#accordion_combinations .delete', function(e) {
				e.preventDefault();
				remove($(this));
			});

			/** on change quantity, update field quantity row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_quantity"]', function() {
				$(this).closest('div.panel.combination').find('span.attribute-quantity').html($(this).val());
			});

			/** on change weigth, update weight row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_weight"]', function() {
				var impactField = $(this).closest('div.panel.combination').find('select[id^="form_step3_combinations_"][id$="_attribute_weight_impact"]');
				var impact = impactField.val() === '0' ? '1' : impactField.val();
				$(this).closest('div.panel.combination').find('span.attribute-weight').html(impact * $(this).val() + ' ' + weightUnit);
			});

			/** on change weigth impact, update weight row */
			$(document).on('change', 'select[id^="form_step3_combinations_"][id$="_attribute_weight_impact"]', function() {
				$(this).closest('div.panel.combination').find('input[id^="form_step3_combinations_"][id$="_attribute_weight"]').keyup();
			});

			/** on change price, update price row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_price"]', function() {
				var impactField = $(this).closest('div.panel.combination').find('select[id^="form_step3_combinations_"][id$="_attribute_price_impact"]');
				var impact = impactField.val() === '0' ? '1' : impactField.val();
				$(this).closest('div.panel.combination').find('span.attribute-price-display').html(formatCurrency(impact * $(this).val()));
			});

			/** on change price impact, update price row */
			$(document).on('change', 'select[id^="form_step3_combinations_"][id$="_attribute_price_impact"]', function() {
				$(this).closest('div.panel.combination').find('input[id^="form_step3_combinations_"][id$="_attribute_price"]').keyup();
			});
		}
	};
})();

/**
 * Specific prices management
 */
var specificPrices = (function() {
	var elem = $('#js-specific-price-list');

	/** Get all specific prices */
	function getAll() {
		if($('#form_id_product').val() === '0') {
			return;
		}
		$.ajax({
			type: 'GET',
			url: elem.attr('data')+'/'+$('#form_id_product').val(),
			success: function(specific_prices){
				var tbody = elem.find('tbody');
				tbody.find('tr').remove();

				if(specific_prices.length > 0){
					elem.removeClass('hide');
				} else {
					elem.addClass('hide');
				}

				$.each(specific_prices, function(key, specific_price){
					var row = '<tr>\
						<td>'+ specific_price.rule_name +'</td>\
						<td>'+ specific_price.attributes_name +'</td>\
						<td>'+ specific_price.shop +'</td>\
						<td>'+ specific_price.currency +'</td>\
						<td>'+ specific_price.country +'</td>\
						<td>'+ specific_price.group +'</td>\
						<td>'+ specific_price.customer +'</td>\
						<td>'+ specific_price.fixed_price +'</td>\
						<td>'+ specific_price.impact +'</td>\
						<td>'+ specific_price.period +'</td>\
						<td>'+ specific_price.from_quantity +'</td>\
						<td>'+ (specific_price.can_delete ? '<a href="'+ $('#js-specific-price-list').attr('data-action-delete')+'/'+specific_price.id_specific_price +'" class="btn btn-default js-delete">X</a>' : '') +'</td>\
					</tr>';

					tbody.append(row);
				});
			}
		});
	}

	/**
	 * Add a specific price
	 * @param {object} elem - The clicked link
	 */
	function add(elem) {
		$.ajax({
			type: 'POST',
			url: $('#specific_price_form').attr('data-action'),
			data: $('#form_step2_specific_price input, #form_step2_specific_price select, #form_id_product').serialize(),
			beforeSend: function() {
				elem.attr('disabled', 'disabled');
			},
			success: function(response){
				showSuccessMessage(translate_javascripts['Form update success']);
				getAll();
			},
			complete: function(){
				elem.removeAttr('disabled');
			},
			error: function(errors){
				showErrorMessage(errors.responseJSON);
			}
		});
	}

	/**
	 * Remove a specific price
	 * @param {object} elem - The clicked link
	 */
	function remove(elem) {
		var parentElem = elem.parent().parent();
		$.ajax({
			type: 'GET',
			url: elem.attr('href'),
			beforeSend: function() {
				elem.attr('disabled', 'disabled');
			},
			success: function(response){
				parentElem.remove();
				showSuccessMessage(response);
			},
			error: function(response){
				showErrorMessage(response.responseJSON);
			},
			complete: function(){
				elem.removeAttr('disabled');
			}
		});
	}

	/** refresh combinations list selector for specific price form */
	function refreshCombinationsList() {
		var elem = $('#form_step2_specific_price_sp_id_product_attribute');
		var url = elem.attr('data-action')+'/'+$('#form_id_product').val();

		$.ajax({
			type: 'GET',
			url: url,
			success: function(combinations){
				/** remove all options except first one */
				elem.find('option:gt(0)').remove();

				$.each(combinations, function(key, combination){
					elem.append('<option value="'+combination.id+'">'+combination.name+'</option>');
				});
			}
		});
	}

	return {
		'init': function() {
			/** set the default price to for specific price form */
			$('#form_step2_specific_price_sp_price').val($('#form_step2_price').val());
			this.getAll();

			$('#specific_price_form .js-save').click(function(){
				add($(this));
			});

			$(document).on('click', '#js-specific-price-list .js-delete', function(e) {
				e.preventDefault();
				remove($(this));
			});
		},
		'getAll': function() {
			getAll();
		},
		'refreshCombinationsList': function() {
			refreshCombinationsList();
		}
	};
})();

/**
 * Warehouse combination collection management (ASM only)
 */
var warehouseCombinations = (function() {
	var collectionHolder = $('#warehouse_combination_collection');

	return {
		'init': function() {
			// toggle all button action
			$(document).on('click', 'div[id^="warehouse_combination_"] button.check_all_warehouse', function() {
				var checkboxes = $(this).closest('div[id^="warehouse_combination_"]').find('input[type="checkbox"][id$="_activated"]');
				checkboxes.prop('checked', checkboxes.filter(':checked').size() == 0);
			});
			// location disablation depending on 'stored' checkbox
			$(document).on('change', 'div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]', function() {
				var checked = $(this).prop('checked');
				var location = $(this).closest('div.form-group').find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');
				location.prop('disabled', !checked);
				if (!checked) location.val('');
			});
			this.locationDisabler();
		},
		'locationDisabler': function() {
			$('div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]', collectionHolder).each(function() {
				var checked = $(this).prop('checked');
				var location = $(this).closest('div.form-group').find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');
				location.prop('disabled', !checked);
			});
		},
		'refresh': function() {
			var show = $('input#form_step3_advanced_stock_management:checked').size() > 0;
			if (show) {
				var url = collectionHolder.attr('data-url') + '/' + $('#form_id_product').val();
				$.ajax({
					url: url,
					success: function (response) {
						collectionHolder.empty().append(response);
						collectionHolder.show();
						warehouseCombinations.locationDisabler();
					}
				});
			} else {
				collectionHolder.hide();
			}
		}
	};
})();

/**
 * Form management
 */
var form = (function() {
	var elem = $('#form');

	function send() {
		var data = $('input, textarea, select', elem).not(':input[type=button], :input[type=submit], :input[type=reset]').serialize();
		$.ajax({
			type: 'POST',
			data: data,
			beforeSend: function() {
				$('.btn-submit', elem).attr('disabled', 'disabled');
				$('.help-block').remove();
				$('*.has-error').removeClass('has-error');
			},
			success: function(response){
				$('#form_id_product').val(response.product.id);
				showSuccessMessage(translate_javascripts['Form update success']);
			},
			error: function(response){
				var tabsWithErrors = [];
				showErrorMessage(translate_javascripts['Form update errors']);

				$.each(jQuery.parseJSON(response.responseText), function(key, errors){
					tabsWithErrors.push(key);

					var html = '<span class="help-block"><ul class="list-unstyled">';
					$.each(errors, function(key, error){
						html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
					});
					html += '</ul></span>';

					$('#form_'+key).parent().append(html);
					$('#form_'+key).parent().addClass('has-error');
				});

				/** find first tab with error, then switch to it */
				var tabIndexError = tabsWithErrors[0].split('_')[0];
				$('.nav-tabs li a[href="#'+tabIndexError+'"]').tab('show');

				/** scroll to 1st error */
				$('html, body').animate({
					scrollTop: $('.has-error').first().offset().top - $('.page-head').height() - $('.navbar-header').height()
				}, 500);
			},
			complete: function(){
				$('.btn-submit', elem).removeAttr('disabled');
			}
		});
	}

	return {
		'init': function() {
			elem.submit(function( event ) {
				event.preventDefault();
				send();
			});
		},
		'send': function() {
			send();
		}
	};
})();
