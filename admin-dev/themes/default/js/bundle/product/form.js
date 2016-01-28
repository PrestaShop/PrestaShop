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
	featuresCollection.init();
	relatedProduct.init();
	manufacturer.init();
	displayFormCategory.init();
	defaultCategory.init();
	nestedCategories.init();
	formCategory.init();
	stock.init();
	supplier.init();
	combinations.init();
	combinationGenerator.init();
	specificPrices.init();
	warehouseCombinations.init();
	customFieldCollection.init();
	virtualProduct.init();
	attachmentProduct.init();
	imagesProduct.init();
	priceCalculation.init();
	displayFieldsManager.refresh();
	displayFieldsManager.init();
	seo.init();
	tags.init();
	rightSidebar.init();

	/** Type product fields display management */
	$('#form_step1_type_product').change(function(){
		displayFieldsManager.refresh();
	});

	/** Attach date picker */
	$('.datepicker').datetimepicker({
		locale: iso_user,
		format: 'YYYY-MM-DD'
	});
});


/**
 * Manage show or hide fields
 */
var displayFieldsManager = (function() {

	var typeProduct = $('#form_step1_type_product');
	var showVariationsSelector = $('#show_variations_selector');
	var combinations = $('#combinations');

	return {
		'init': function() {
			/** Type product fields display management */
			$('#form_step1_type_product').change(function(){
				displayFieldsManager.refresh();
			});

			$('#form .form-input-title input').on('click', function() {
				$(this).select();
			});
		},
		'refresh': function() {
			$('#virtual_product').hide();
			$('#form-nav a[href="#step3"]').text(translate_javascripts['Quantity']);

			/** product type switch */
			if(typeProduct.val() == 1) {
				$('#pack_stock_type, #js_form_step1_inputPackItems').show();
				$('#form-nav a[href="#step4"]').show();
				showVariationsSelector.hide();
				showVariationsSelector.find('input[value="0"]').attr('checked', true);
			}else{
				$('#virtual_product, #pack_stock_type, #js_form_step1_inputPackItems').hide();
				$('#form-nav a[href="#step4"]').show();

				if(typeProduct.val() == 2){
					showVariationsSelector.hide();
					$('#virtual_product').show();
					$('#form-nav a[href="#step4"]').hide();
					showVariationsSelector.find('input[value="0"]').attr('checked', true);
					$('#form-nav a[href="#step3"]').text(translate_javascripts['Virtual product']);
				}else{
					showVariationsSelector.show();
					$('#form-nav a[href="#step3"]').text(translate_javascripts['Quantity']);
				}
			}

			/** check quantity / combinations display */
			if(showVariationsSelector.find('input:checked').val() == 1 || $('#accordion_combinations tr').length > 0){
				combinations.show();

				$('#specific-price-combination-selector').removeClass('hide').show();
				$('#form-nav a[href="#step3"]').text(translate_javascripts['Combinations']);
				$('#product_qty_0_shortcut_div, #quantity-no-attribute, #step3_minimal_quantity').hide();
			} else {
				combinations.hide();
				$('#specific-price-combination-selector').hide();
				$('#product_qty_0_shortcut_div, #quantity-no-attribute, #step3_minimal_quantity').show();
			}
			if ($('#combinations_thead').next().children().length) {
				$('#combinations_thead').show();
			} else {
				$('#combinations_thead').hide();
			}

			/** Tooltip for product type combinations */
			if ($('input[name="show_variations"][value="1"]:checked').length >= 1) {
				$('#product_type_combinations_shortcut').show();
			} else {
				$('#product_type_combinations_shortcut').hide();
			}
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
 * Display category form management
 */
var displayFormCategory = (function() {
	var parentElem = $('#add-categories');
	return {
		'init': function() {
			/** Click event on the add button */
			parentElem.find('a.open').on('click', function(e) {
				e.preventDefault();
				parentElem.find('#add-categories-content').removeClass('hide');
				$(this).hide();
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

				//inject new category into category tree
				var html = '<li><div class="checkbox"><label><input type="checkbox" name="form[step1][categories][tree][]" value="'+response.category.id+'">'+response.category.name[1]+'</label></div></li>';
				var parentElement = $('#form_step1_categories input[value='+response.category.id_parent+']').parent().parent();
				if(parentElement.next('ul').length === 0){
					html = '<ul>' + html + '</ul>';
					parentElement.append(html);
				}else{
					parentElement.next('ul').append(html);
				}

				//inject new category in parent category selector
				$('#form_step1_new_category_id_parent').append('<option value="' + response.category.id + '">' + response.category.name[1] + '</option>');
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

	/** Add a feature */
	function add(){
		var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, collectionHolder.children().length);
		collectionHolder.append('<li>'+newForm+'</li>');
	}

	return {
		'init': function() {
			/** Click event on the add button */
			$('#features a.add').on('click', function(e) {
				e.preventDefault();
				add();
				$('#features-content').removeClass('hide');
			});

			/** Click event on the remove button */
			$(document).on('click', 'ul.featureCollection .delete', function(e) {
				e.preventDefault();
				$(this).parent().parent().parent().remove();
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
 * Related product management
 */
var relatedProduct = (function() {
	var parentElem = $('#related-product');

	return {
		'init': function() {
			/** Click event on the add button */
			parentElem.find('a.open').on('click', function(e) {
				e.preventDefault();
				parentElem.find('#related-content').removeClass('hide');
				$(this).hide();
			});
		}
	};
})();

/**
 * Manufacturer management
 */
var manufacturer = (function() {
	var parentElem = $('#manufacturer');

	return {
		'init': function() {
			/** Click event on the add button */
			parentElem.find('a.open').on('click', function(e) {
				e.preventDefault();
				parentElem.find('#manufacturer-content').removeClass('hide');
				$(this).hide();
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
	var id_product = $('#form_id_product').val();
	var collectionHolder = $('#supplier_combination_collection');

	return {
		'refresh': function() {
			var suppliers = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]:checked').map(function(){return $(this).val();}).get();
			var url = collectionHolder.attr('data-url')+'/'+id_product+(suppliers.length > 0 ? '/'+suppliers.join('-') : '');

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
			$('#form_step3_depends_on_stock_0, #form_step3_depends_on_stock_1, #form_step3_advanced_stock_management').on('change', function(e) {
				displayFieldsManager.refresh();
				warehouseCombinations.refresh();
			});
			displayFieldsManager.refresh();
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
				$('#form-nav a[href=' + hash.replace(prefix,'') + ']').tab('show');
			}

			$('#form-nav a').on('shown.bs.tab', function (e) {
				if(e.target.hash) {
					onTabSwitch(e.target.hash);
					window.location.hash = e.target.hash.replace('#', '#' + prefix);
				}
			});

			/** on tab switch */
			function onTabSwitch(currentTab){
				if (currentTab == '#step2'){
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
	var id_product = $('#form_id_product').val();

	/** Generate combinations */
	function generate(){
		/**
		 * Combination row maker
		 * @param {object} attribute
		 */
		var combinationRowMaker = function(form){
			var combinationsLength = $('#accordion_combinations').children().length;
			var newForm = form.replace(/product_combination\[/g, 'form[step3][combinations]['+combinationsLength+'][')
				.replace(/id="product_combination_/g, 'id="form_step3_combinations_'+combinationsLength+'_')
				.replace(/__loop_index__/g, combinationsLength);

			$('#accordion_combinations').prepend(newForm);
			displayFieldsManager.refresh();
			combinations.refreshImagesCombination();
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
			/** Create Bloodhound engine */
			var engine = new Bloodhound({
				datumTokenizer: function(d) {
					return Bloodhound.tokenizers.whitespace(d.label);
				},
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				prefetch: {
					url: $('#form_step3_attributes').attr('data-prefetch'),
					cache: false
				}
			});

			/** Filter suggestion with selected tokens */
			var filter = function(suggestions) {
				var selected = [];
				$('#attributes-generator input.attribute-generator').each(function(){
					selected.push($(this).val());
				});

				return $.grep(suggestions, function(suggestion) {
					return $.inArray(suggestion.value, selected) === -1 && $.inArray('group-' + suggestion.data.id_group, selected) === -1;
				});
			}

			/** init input typeahead */
			$('#form_step3_attributes').tokenfield({typeahead: [{
					hint: false,
					cache: false
				}, {
					source: function(query, syncResults) {
						engine.search(query, function(suggestions) {
							syncResults(filter(suggestions));
						});
					},
					display: 'label'
				}]
			});

			/** On event "tokenfield:createtoken" : stop event if its not a typehead result */
			$('#form_step3_attributes').on('tokenfield:createtoken', function(e) {
				if(!e.attrs.data){
					return false;
				}
			});

			/** On event "tokenfield:createdtoken" : store attributes in input when add a token */
			$('#form_step3_attributes').on('tokenfield:createdtoken', function(e) {
				if(e.attrs.data){
					$('#attributes-generator').append('<input type="hidden" id="attribute-generator-'+e.attrs.value+'" class="attribute-generator" value="'+e.attrs.value+'" name="options['+e.attrs.data.id_group+']['+e.attrs.value+']" />');
				}
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
	var id_product = $('#form_id_product').val();

	/**
	 * Remove a combination
	 * @param {object} elem - The clicked link
	 */
	function remove(elem){
		var combinationElem = $('#attribute_'+elem.attr('data'));

		modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
			onContinue: function(){
				$.ajax({
					type: 'GET',
					url: elem.attr('href'),
					beforeSend: function() {
						elem.attr('disabled', 'disabled');
					},
					success: function(response) {
						combinationElem.remove();
						showSuccessMessage(response.message);
						displayFieldsManager.refresh();
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
		}).show();
	}

	return {
		'init': function() {
			var _this = this;
			var weightUnit = $('#accordion_combinations').attr('data-weight-unit');

			/** delete combination */
			$(document).on('click', '#accordion_combinations .delete', function(e) {
				e.preventDefault();
				remove($(this));
			});

			/** on change quantity, update field quantity row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_quantity"]', function() {
				var id_attribute = $(this).closest('.combination-form').attr('data');
				$('#accordion_combinations #attribute_'+id_attribute).find('.attribute-quantity input').val($(this).val());
			});

			/** on change shortcut quantity, update form field quantity */
			$(document).on('keyup', '.attribute-quantity input', function() {
				var id_attribute = $(this).closest('.combination').attr('data');
				$('#combination_form_'+id_attribute).find('input[id^="form_step3_combinations_"][id$="_attribute_quantity"]').val($(this).val());
			});

			/** on change weigth, update weight row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_weight"]', function() {
				var id_attribute = $(this).closest('.combination-form').attr('data');
				$('#accordion_combinations #attribute_'+id_attribute).find('.attribute-weight').html($(this).val() + ' ' + weightUnit);
			});

			/** on change price, update price row */
			$(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_price"]', function() {
				var id_attribute = $(this).closest('.combination-form').attr('data');
				$('#accordion_combinations #attribute_'+id_attribute).find('.attribute-price-display').html(formatCurrency(parseFloat($(this).val())));
			});

			/** on change images selection */
			$(document).on('click', '#form .product-combination-image', function() {
				var input = $(this).find('input');
				var isChecked = input.prop('checked');
				input.prop('checked', isChecked ? false : true);

				if(isChecked){
					$(this).removeClass('img-highlight');

				}else{
					$(this).addClass('img-highlight');
				}

				_this.refreshDefaultImage();
			});

			/** Combinations fields display management */
			$('#combinations').hide();
			$('#show_variations_selector input').change(function(){
				displayFieldsManager.refresh();

				if($(this).val() == 0){
					//if combination(s) exists, alert user for deleting it
					if($('#accordion_combinations .combination').length > 0){
						modalConfirmation.create(translate_javascripts['Are you sure to disable variations ? they will all be deleted'], null,{
							onCancel: function(){
								$('#show_variations_selector input[value="1"]').attr('checked', true);
								displayFieldsManager.refresh();
							},
							onContinue: function(){
								$.ajax({
									type: 'GET',
									url: $('#accordion_combinations').attr('data-action-delete-all') + '/' + $('#form_id_product').val(),
									success: function(response){
										$('#accordion_combinations .combination').remove();
										displayFieldsManager.refresh();
									}, error: function(response){
										showErrorMessage(jQuery.parseJSON(response.responseText).message);
									},
								});
							}
						}).show();
					}
				}
			});


			this.refreshImagesCombination();

			/** open combination form */
			$(document).on('click', '#accordion_combinations .btn-open', function(e) {
				e.preventDefault();
				var contentElem = $($(this).attr('href'));

				/** create combinations navigation */
				var navElem = contentElem.find('.nav');
				var id_attribute = contentElem.attr('data');
				var prevCombinationId = $('#accordion_combinations tr[data="' + id_attribute + '"]').prev().attr('data');
				var nextCombinationId = $('#accordion_combinations tr[data="' + id_attribute + '"]').next().attr('data');
				navElem.find('.prev, .next').hide();
				if(prevCombinationId){
					navElem.find('.prev').attr('data', prevCombinationId).show();
				}
				if(nextCombinationId){
					navElem.find('.next').attr('data', nextCombinationId).show();
				}

				/** init combination tax include price */
				priceCalculation.impactTaxInclude(contentElem.find('.attribute_priceTE'));

				contentElem.insertBefore('#form-nav').removeClass('hide').show();
				$('#form-nav, #form_content').hide();
			});

			/** close combination form */
			$(document).on('click', '#form .combination-form .btn-back', function(e) {
				e.preventDefault();
				$(this).closest('.combination-form').hide();
				$('#form-nav, #form_content').show();
			});

			/** switch combination form */
			$(document).on('click', '#form .combination-form .nav button', function(e) {
				e.preventDefault();
				$('.combination-form').hide();
				$('#accordion_combinations .combination[data="' + $(this).attr('data') + '"] .btn-open').click();
			});
		},
		'refreshDefaultImage': function() {
			var productDefaultImageUrl = null;
			var productCoverImageElem = $('#product-images-dropzone').find('.iscover');

			/** get product cover image */
			if(productCoverImageElem.length == 1){
				var imgElem = productCoverImageElem.parent().find('.dz-image');

				/** Dropzone.js workaround : If this is a fresh upload image, look up for an img, else find a background url*/
				if(imgElem.find('img').length){
					productDefaultImageUrl = imgElem.find('img').attr('src');
				} else {
					productDefaultImageUrl = imgElem.css('background-image')
						.replace(/^url\(["']?/, '')
						.replace(/["']?\)$/, '');
				}
			}

			$.each($('#form .combination-form'), function(key, elem){
				var defaultImageUrl = productDefaultImageUrl;

				/** get first selected image */
				var defaultImageElem = $(elem).find('.product-combination-image input:checked:first');
				if(defaultImageElem.length == 1){
					defaultImageUrl = defaultImageElem.parent().find('img').attr('src');
				}

				if(defaultImageUrl){
					var img = '<img src="' + defaultImageUrl + '" class="img-responsive" style="max-width:50px" />';
					$('#accordion_combinations #attribute_'+$(elem).attr('data')).find('td.img').html(img);
				}
			});
		},
		'refreshImagesCombination': function() {
			var _this = this;
			var target = $('#accordion_combinations');
			if(target.find('.combination').length == 0){
				return;
			}

			$.ajax({
				type: 'GET',
			 	url: target.attr('data-action-refresh-images')+'/' + id_product,
			 	success: function(response){
					$.each(response, function(id, combinationImages){
						var combinationElem = target.find('.combination[data="'+ id +'"]');
						var imagesElem = combinationElem.find('.images');
						var index = combinationElem.attr('data-index');

						imagesElem.html('');
						$.each(combinationImages, function(key, image){
							var row = '<div class="product-combination-image ' + (image.id_image_attr ? 'img-highlight' : '') + '">\
					 			<input type="checkbox" name="form[step3][combinations][' + index + '][id_image_attr][]" value="' + image.id + '" '+ (image.id_image_attr ? 'checked="checked"' : '') +'>\
					 			<img src="' + image.base_image_url + '-small_default.' + image.format + '" alt="" />\
					 		</div>';

							imagesElem.append(row);
						});
					});

					_this.refreshDefaultImage();
			 	}
			});
		}
	};
})();

/**
 * Specific prices management
 */
var specificPrices = (function() {
	var id_product = $('#form_id_product').val();
	var elem = $('#js-specific-price-list');

	/** Get all specific prices */
	function getAll() {
		$.ajax({
			type: 'GET',
			url: elem.attr('data')+'/'+id_product,
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
						<td>'+ specific_price.currency +'</td>\
						<td>'+ specific_price.country +'</td>\
						<td>'+ specific_price.group +'</td>\
						<td>'+ specific_price.customer +'</td>\
						<td>'+ specific_price.fixed_price +'</td>\
						<td>'+ specific_price.impact +'</td>\
						<td>'+ specific_price.period +'</td>\
						<td>'+ specific_price.from_quantity +'</td>\
						<td>'+ (specific_price.can_delete ? '<a href="'+ $('#js-specific-price-list').attr('data-action-delete')+'/'+specific_price.id_specific_price +'" class="btn btn-default js-delete"><i class="icon-trash"></i></a>' : '') +'</td>\
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
			data: $('#specific_price_form input, #specific_price_form select, #form_id_product').serialize(),
			beforeSend: function() {
				elem.attr('disabled', 'disabled');
			},
			success: function(response){
				showSuccessMessage(translate_javascripts['Form update success']);
				$('#specific_price_form .js-cancel').click();
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
		modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
			onContinue: function(){
				$.ajax({
					type: 'GET',
					url: elem.attr('href'),
					beforeSend: function() {
						elem.attr('disabled', 'disabled');
					},
					success: function(response){
						getAll();
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
		}).show();
	}

	/** refresh combinations list selector for specific price form */
	function refreshCombinationsList() {
		var elem = $('#form_step2_specific_price_sp_id_product_attribute');
		var url = elem.attr('data-action')+'/'+id_product;

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

			$('#specific-price .add').click(function(){
				$(this).hide();
			});

			$('#specific_price_form .js-cancel').click(function(){
				$('#specific-price .add').click().show();
			});

			$('#specific_price_form .js-save').click(function(){
				add($(this));
			});

			$(document).on('click', '#js-specific-price-list .js-delete', function(e) {
				e.preventDefault();
				remove($(this));
			});

			$('#form_step2_specific_price_sp_reduction_type').change(function(){
				if($(this).val() == 'percentage'){
					$('#form_step2_specific_price_sp_reduction_tax').hide();
				}else{
					$('#form_step2_specific_price_sp_reduction_tax').show();
				}
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
	var id_product = $('#form_id_product').val();
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
				var url = collectionHolder.attr('data-url') + '/' + id_product;
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

	function send(redirect) {
		var data = $('input, textarea, select', elem).not(':input[type=button], :input[type=submit], :input[type=reset]').serialize();
		$.ajax({
			type: 'POST',
			data: data,
			beforeSend: function() {
				$('#submit', elem).attr('disabled', 'disabled');
				$('.btn-submit', elem).attr('disabled', 'disabled');
				$('.help-block').remove();
				$('*.has-error').removeClass('has-error');
			},
			success: function(response){
				if(redirect){
					window.location = redirect;
				}
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
				$('#form-nav li a[href="#'+tabIndexError+'"]').tab('show');

				/** scroll to 1st error */
				$('html, body').animate({
					scrollTop: $('.has-error').first().offset().top - $('.page-head').height() - $('.navbar-header').height()
				}, 500);
			},
			complete: function(){
				$('#submit', elem).removeAttr('disabled');
				$('.btn-submit', elem).removeAttr('disabled');
			}
		});
	}

	function switchLanguage(iso_code) {
		$('div.translations.tabbable > div > div.tab-pane:not(.translation-label-'+iso_code+')').removeClass('active');
		$('div.translations.tabbable > div > div.tab-pane.translation-label-'+iso_code).addClass('active');
	}

	return {
		'init': function() {
			/** prevent form submit on ENTER keypress */
			jwerty.key('enter', function(e) {
				e.preventDefault();
			});

			/** create keyboard event for save */
			jwerty.key('ctrl+S', function(e) {
				e.preventDefault();
				send();
			});

			/** create keyboard event for save & duplicate */
			jwerty.key('ctrl+D', function(e) {
				e.preventDefault();
				send($('nav.navbar-form-footer .duplicate').attr('data-redirect'));
			});

			/** create keyboard event for save & new */
			jwerty.key('ctrl+P', function(e) {
				e.preventDefault();
				send($('nav.navbar-form-footer .new-product').attr('data-redirect'));
			});

			/** create keyboard event for save & go catalog */
			jwerty.key('ctrl+Q', function(e) {
				e.preventDefault();
				send($('nav.navbar-form-footer .go-catalog').attr('data-redirect'));
			});

			elem.submit(function( event ) {
				event.preventDefault();
				send();
			});

			elem.find('#form_switch_language').change(function( event ) {
				event.preventDefault();
				switchLanguage(event.target.value);
			});

			/** on save with duplicate|new */
			$('.btn-submit', elem).click(function(){
				send($(this).attr('data-redirect'));
			});

			/** on active field change, send form */
			$('#form_step1_active', elem).change(function(){
				send();
			});

			/** on delete product */
			$('.form-action-bar .delete', elem).click(function(e){
				e.preventDefault();
				var _this = $(this);
				modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
					onContinue: function(){
						window.location = _this.attr('href');
					}
				}).show();
			});
		},
		'send': function() {
			send();
		},
		'switchLanguage': function(iso_code) {
			switchLanguage(iso_code);
		}
	};
})();


/**
 * Custom field collection management
 */
var customFieldCollection = (function() {

	var collectionHolder = $('ul.customFieldCollection');
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

			/** Click event on the add button */
			newItemBtn.on('click', function(e) {
				e.preventDefault();
				add();
			});

			/** Click event on the remove button */
			$(document).on('click', 'ul.customFieldCollection a.delete', function(e) {
				e.preventDefault();
				remove($(this));
			});
		}
	};
})();

/**
 * virtual product management
 */
var virtualProduct = (function() {
	var id_product = $('#form_id_product').val();

	return {
		'init': function() {
			$(document).on('change', 'input[name="form[step3][virtual_product][is_virtual_file]"]', function() {
				if($(this).val() == 1){
					$('#virtual_product_content').show();
				}else{
					$('#virtual_product_content').hide();

					//delete virtual product
					$.ajax({
						type: 'GET',
						url: $('#virtual_product').attr('data-action-remove')+'/'+id_product,
						success: function(response){
							//empty form
							$('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
							$('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
							$('#form_step3_virtual_product_name').val('');
							$('#form_step3_virtual_product_nb_downloable').val(0);
							$('#form_step3_virtual_product_expiration_date').val('');
							$('#form_step3_virtual_product_nb_days').val(0);
						}
					});
				}
			});

			if($('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val() == 1){
				$('#virtual_product_content').show();
			}else{
				$('#virtual_product_content').hide();
			}

			/** delete attached file */
			$('#form_step3_virtual_product_file_details .delete').click(function(e){
				e.preventDefault();
				var _this = $(this);

				modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
					onContinue: function(){
						$.ajax({
							type: 'GET',
							url: _this.attr('href')+'/'+id_product,
							success: function(response){
								$('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
								$('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
							}
						});
					}
				}).show();
			});

			/** save virtual product */
			$('#form_step3_virtual_product_save').click(function(){
				var _this = $(this);
				var data = new FormData();

				if($('#form_step3_virtual_product_file')[0].files[0]) {
					data.append('product_virtual[file]', $('#form_step3_virtual_product_file')[0].files[0]);
				}
				data.append('product_virtual[is_virtual_file]', $('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val());
				data.append('product_virtual[name]', $('#form_step3_virtual_product_name').val());
				data.append('product_virtual[nb_downloable]', $('#form_step3_virtual_product_nb_downloable').val());
				data.append('product_virtual[expiration_date]', $('#form_step3_virtual_product_expiration_date').val());
				data.append('product_virtual[nb_days]', $('#form_step3_virtual_product_nb_days').val());

				$.ajax({
					type: 'POST',
					url: $('#virtual_product').attr('data-action')+'/'+id_product,
					data: data,
					contentType: false,
					processData: false,
					beforeSend: function() {
						_this.prop('disabled', 'disabled');
						$('.help-block').remove();
						$('*.has-error').removeClass('has-error');
					},
					success: function(response){
						showSuccessMessage(translate_javascripts['Form update success']);
						if(response.file_download_link){
							$('#form_step3_virtual_product_file_details a.download').attr('href', response.file_download_link)
							$('#form_step3_virtual_product_file_input').removeClass('show').addClass('hide');
							$('#form_step3_virtual_product_file_details').removeClass('hide').addClass('show');
						}
					},
					error: function(response){
						$.each(jQuery.parseJSON(response.responseText), function(key, errors){
							var html = '<span class="help-block"><ul class="list-unstyled">';
							$.each(errors, function(key, error){
								html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
							});
							html += '</ul></span>';

							$('#form_step3_virtual_product_'+key).parent().append(html);
							$('#form_step3_virtual_product_'+key).parent().addClass('has-error');
						});
					},
					complete: function(){
						_this.removeProp('disabled');
					}
				});
			});
		}
	};
})();

/**
 * attachment product management
 */
var attachmentProduct = (function() {
	var id_product = $('#form_id_product').val();

	return {
		'init': function() {
			var buttonSave = $('#form_step6_attachment_product_add');

			/** add attachment */
			$('#form_step6_attachment_product_add').click(function(){
				var _this = $(this);
				var data = new FormData();

				if($('#form_step6_attachment_product_file')[0].files[0]) {
					data.append('product_attachment[file]', $('#form_step6_attachment_product_file')[0].files[0]);
				}
				data.append('product_attachment[name]', $('#form_step6_attachment_product_name').val());
				data.append('product_attachment[description]', $('#form_step6_attachment_product_description').val());

				$.ajax({
					type: 'POST',
					url: $('#form_step6_attachment_product').attr('data-action')+'/'+id_product,
					data: data,
					contentType: false,
					processData: false,
					beforeSend: function() {
						buttonSave.prop('disabled', 'disabled');
						$('.help-block').remove();
						$('*.has-error').removeClass('has-error');
					},
					success: function(response){
						$('#form_step6_attachment_product_file').val('');
						$('#form_step6_attachment_product_name').val('');
						$('#form_step6_attachment_product_description').val('');

						//inject new attachment in attachment list
						if(response.id){
							$('#form_step6_attachments')
								.append($('<option></option>')
									.attr('value', response.id)
									.text(response.real_name)
									.prop('selected', true)
								);
						}
					},
					error: function(response){
						$.each(jQuery.parseJSON(response.responseText), function(key, errors){
							var html = '<span class="help-block"><ul class="list-unstyled">';
							$.each(errors, function(key, error){
								html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
							});
							html += '</ul></span>';

							$('#form_step6_attachment_product_'+key).parent().append(html);
							$('#form_step6_attachment_product_'+key).parent().addClass('has-error');
						});
					},
					complete: function(){
						buttonSave.removeProp('disabled');
					}
				});
			});
		}
	};
})();

/**
 * images product management
 */
var imagesProduct = (function() {
	var id_product = $('#form_id_product').val();

	return {
		'init': function() {
			Dropzone.autoDiscover = false;
			var dropZoneElem = $('#product-images-dropzone');
			var errorElem = $('#product-images-dropzone-error');

			//on click image, display custom form
			$(document).on('click', '#product-images-dropzone .dz-preview', function() {
				if(!$(this).attr('data-id')){
					return;
				}
				formImagesProduct.form($(this).attr('data-id'));
			});

			var dropzoneOptions = {
				url: dropZoneElem.attr('url-upload')+'/'+id_product,
				paramName: "form[file]",
				maxFilesize: dropZoneElem.attr('data-max-size'),
				addRemoveLinks: true,
				clickable: true,
				thumbnailWidth: 130,
				thumbnailHeight: null,
				acceptedFiles: 'image/*',
				dictDefaultMessage: '<h4>'+translate_javascripts['Drop pictures here']+'</h4>'+translate_javascripts['or select files']+'<div>' + translate_javascripts['files recommandations'] + '</div>',
				dictRemoveFile: translate_javascripts['Delete'],
				dictFileTooBig: translate_javascripts['ToLargeFile'],
				dictCancelUpload: translate_javascripts['Delete'],
				sending: function (file, response) {
					errorElem.html('');
				},
				queuecomplete: function(){
					dropZoneElem.sortable('enable');
				},
				processing: function(){
					dropZoneElem.sortable('disable');
				},
				success: function (file, response) {
					//manage error on uploaded file
					if(response.error != 0){
						errorElem.append('<p>' + file.name + ': ' + response.error + '</p>');
						this.removeFile(file);
						return;
					}

					//define id image to file preview
					$(file.previewElement).attr('data-id', response.id);
					$(file.previewElement).addClass('ui-sortable-handle');

					if(response.cover == 1){
						imagesProduct.updateDisplayCover(response.id);
					}

					combinations.refreshImagesCombination();
				},
				error: function (file, response) {
					var message = '';
					if($.type(response) === "string"){
						message = response;
					}else if(response.message){
						message = response.message;
					}

					if(message == ''){
						return;
					}

					//append new error
					errorElem.append('<p>' + file.name + ': ' + message + '</p>');

					//remove uploaded item
					this.removeFile(file);
				},
				init: function () {
					//if already images uploaded, mask drop file message
					if(dropZoneElem.find('.dz-preview').length){
						dropZoneElem.addClass('dz-started');
					}

					//init sortable
					dropZoneElem.sortable({
						opacity: 0.9,
						containment: "parent",
						distance: 32,
						tolerance: "pointer",
						cursorAt: { left: 64, top: 64 },
						stop: function(event, ui) {
							var sort = {};
							$.each(dropZoneElem.find('.dz-preview'), function( index, value ) {
								if(!$(value).attr('data-id')) {
									sort = false;
									return;
								}
								sort[$(value).attr('data-id')] = index+1;
							});

							//if sortable ok, update it
							if(sort){
								$.ajax({
									type: 'POST',
									url: dropZoneElem.attr('url-position'),
									data: {json: JSON.stringify(sort)}
								});
							}
						},
						start: function(event, ui) {
							//init zindex
							dropZoneElem.find('.dz-preview').css('zIndex', 1);
							ui.item.css('zIndex', 10);
						}
					});

					dropZoneElem.disableSelection();
				}
			};

			dropZoneElem.dropzone(jQuery.extend(dropzoneOptions));
		},
		'updateDisplayCover': function(id_image) {
			$('#product-images-dropzone .dz-preview .iscover').remove();
			$('#product-images-dropzone .dz-preview[data-id="' + id_image + '"]')
				.append('<div class="iscover">' + translate_javascripts['Cover'] + '</div>');
		}
	};
})();


var formImagesProduct = (function() {
	var dropZoneElem = $('#product-images-dropzone');
	var formZoneElem = $('#product-images-form-container');

	return {
		'form': function(id) {
			$.ajax({
				url: dropZoneElem.attr('url-update')+'/'+id,
				success: function(response){
					formZoneElem.find('#product-images-form').html(response);
				},
				complete: function(){
					formZoneElem.show();
				}
			});
		},
		'send': function(id) {
			$.ajax({
				type: 'POST',
				url: dropZoneElem.attr('url-update')+'/'+id,
				data: formZoneElem.find('input').serialize(),
				beforeSend: function() {
					formZoneElem.find('.actions button').prop('disabled', 'disabled');
					formZoneElem.find('.help-block').remove();
					formZoneElem.find('*.has-error').removeClass('has-error');
				},
				success: function(response){
					if(formZoneElem.find('#form_image_cover:checked').length){
						imagesProduct.updateDisplayCover(id);
					}
				},
				error: function(response){
					if(response && response.responseText != '') {
						$.each(jQuery.parseJSON(response.responseText), function (key, errors) {
							var html = '<span class="help-block"><ul class="list-unstyled">';
							$.each(errors, function (key, error) {
								html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
							});
							html += '</ul></span>';

							$('#form_image_' + key).parent().append(html);
							$('#form_image_' + key).parent().addClass('has-error');
						});
					}
				},
				complete: function(){
					formZoneElem.find('.actions button').removeAttr('disabled');
				}
			});
		},
		'delete': function(id) {
			modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
				onContinue: function(){
					$.ajax({
						url: dropZoneElem.attr('url-delete')+'/'+id,
						complete: function(){
							formZoneElem.find('.close').click();
							dropZoneElem.find('.dz-preview[data-id="' + id + '"]').remove();
							combinations.refreshImagesCombination();
						}
					});
				}
			}).show();
		},
		'close': function() {
			formZoneElem.find('#product-images-form').html('');
			formZoneElem.hide();
		}
	};
})();

/**
 * Price calculation
 */
var priceCalculation = (function() {
	var priceHTElem = $('#form_step2_price');
	var priceHTShortcutElem = $('#form_step1_price_shortcut');
	var priceTTCElem = $('#form_step2_price_ttc');
	var priceTTCShorcutElem = $('#form_step1_price_ttc_shortcut');
	var ecoTaxElem = $('#form_step2_ecotax');
	var taxElem = $('#form_step2_id_tax_rules_group');
	var displayPricePrecision = priceHTElem.attr('data-display-price-precision');
	var ecoTaxRate = ecoTaxElem.attr('data-eco-tax-rate');

	/**
	 * Add taxes to a price
	 * @param {float} Price without tax
	 * @param {array} Rates rates to apply
	 * @param {int} computation_method The computation calculate method
	 */
	function addTaxes(price, rates, computation_method) {
		var price_with_taxes = price;
		if (computation_method == 0) {
			for (i in rates) {
				price_with_taxes *= (1 + rates[i] / 100);
				break;
			}
		} else if (computation_method == 1) {
			var rate = 0;
			for (i in rates) {
				rate += rates[i];
			}
			price_with_taxes *= (1 + rate / 100);
		} else if (computation_method == 2) {
			for (i in rates) {
				price_with_taxes *= (1 + rates[i] / 100);
			}
		}

		return price_with_taxes;
	}

	/**
	 * Remove taxes from a price
	 * @param {float} Price with tax
	 * @param {array} Rates rates to apply
	 * @param {int} computation_method The computation calculate method
	 */
	function removeTaxes(price, rates, computation_method)
	{
		if (computation_method == 0) {
			for (i in rates) {
				price /= (1 + rates[i] / 100);
				break;
			}
		} else if (computation_method == 1) {
			var rate = 0;
			for (i in rates) {
				rate += rates[i];
			}
			price /= (1 + rate / 100);
		} else if (computation_method == 2) {
			for (i in rates) {
				price /= (1 + rates[i] / 100);
			}
		}

		return price;
	}

	function getEcotaxTaxIncluded()
	{
		var ecotax_tax_excl =  ecoTaxElem.val() / (1 + ecoTaxRate);
		return ps_round(ecotax_tax_excl * (1 + ecoTaxRate), 2);
	}

	function getEcotaxTaxExcluded()
	{
		return ecoTaxElem.val() / (1 + ecoTaxRate);
	}

	return {
		'init': function() {
			/** on update tax recalculate tax include price */
			taxElem.change(function(){
				priceCalculation.taxInclude();
				priceTTCElem.change();
			});

			/** update without tax price and shortcut price field on change */
			$('#form_step1_price_shortcut, #form_step2_price').keyup(function(){
				if($(this).attr('id') === 'form_step1_price_shortcut'){
					$('#form_step2_price').val($(this).val());
				}else{
					$('#form_step1_price_shortcut').val($(this).val());
				}

				priceCalculation.taxInclude();
			});

			/** update HT price and shortcut price field on change */
			$('#form_step1_price_ttc_shortcut, #form_step2_price_ttc').keyup(function(){
				if($(this).attr('id') === 'form_step1_price_ttc_shortcut'){
					$('#form_step2_price_ttc').val($(this).val());
				}else{
					$('#form_step1_price_ttc_shortcut').val($(this).val());
				}

				priceCalculation.taxExclude();
			});

			/** on price change, update final retails prices */
			$('#form_step2_price, #form_step2_price_ttc').change(function(){
				$('#final_retail_price_te').text(formatCurrency(parseFloat($('#form_step2_price').val())));
				$('#final_retail_price_ti').text(formatCurrency(parseFloat($('#form_step2_price_ttc').val())));
			});

			/** update HT price and shortcut price field on change */
			$('#form_step2_ecotax').keyup(function(){
				priceCalculation.taxExclude();
			});

			/** combinations : update TTC price field on change */
			$('.combination-form .attribute_priceTE').keyup(function(){
				priceCalculation.impactTaxInclude($(this));
			});
			/** combinations : update HT price field on change */
			$('.combination-form .attribute_priceTI').keyup(function(){
				priceCalculation.impactTaxExclude($(this));
			});

			priceCalculation.taxInclude();

			$('#form_step2_price, #form_step2_price_ttc').change();
		},
		'taxInclude': function() {
			var price = parseFloat(priceHTElem.val().replace(/,/g, '.'));
			if(isNaN(price)){
				price = 0;
			}

			var rates = taxElem.find('option:selected').attr('data-rates').split(',');
			var computation_method = taxElem.find('option:selected').attr('data-computation-method');
			var newPrice = ps_round(addTaxes(price, rates, computation_method), displayPricePrecision) + getEcotaxTaxIncluded();

			priceTTCElem.val(newPrice);
			priceTTCShorcutElem.val(newPrice);
		},
		'taxExclude': function() {
			var price = parseFloat(priceTTCElem.val().replace(/,/g, '.'));
			if(isNaN(price)){
				price = 0;
			}

			var rates = taxElem.find('option:selected').attr('data-rates').split(',');
			var computation_method = taxElem.find('option:selected').attr('data-computation-method');
			var newPrice = ps_round(removeTaxes(ps_round(price - getEcotaxTaxIncluded(), displayPricePrecision), rates, computation_method), displayPricePrecision);

			priceHTElem.val(newPrice);
			priceHTShortcutElem.val(newPrice);
		},
		'impactTaxInclude': function(obj) {
			var price = parseFloat(obj.val().replace(/,/g, '.'));
			var targetInput = obj.parent().parent().parent().find('input.attribute_priceTI');
			if(isNaN(price)){
				targetInput.val(0);
				return;
			}
			var rates = taxElem.find('option:selected').attr('data-rates').split(',');
			var computation_method = taxElem.find('option:selected').attr('data-computation-method');
			var newPrice = ps_round(addTaxes(price, rates, computation_method));

			targetInput.val(newPrice);
		},
		'impactTaxExclude': function(obj) {
			var price = parseFloat(obj.val().replace(/,/g, '.'));
			var targetInput = obj.parent().parent().parent().find('input.attribute_priceTE');
			if(isNaN(price)){
				targetInput.val(0);
				return;
			}
			var rates = taxElem.find('option:selected').attr('data-rates').split(',');
			var computation_method = taxElem.find('option:selected').attr('data-computation-method');
			var newPrice = ps_round(removeTaxes(ps_round(price, displayPricePrecision), rates, computation_method), displayPricePrecision);

			targetInput.val(newPrice);
		}
	};
})();


/**
 * modal confirmation management
 */
var modalConfirmation = (function() {
	var modal = $('#confirmation_modal');
	var actionsCallbacks = {
		onCancel: function(){
			return;
		},
		onContinue: function(){
			return;
		}
	};

	modal.find('button.cancel').click(function(){
		if (typeof actionsCallbacks.onCancel == 'function') {
			actionsCallbacks.onCancel();
		}
		modalConfirmation.hide();
	});

	modal.find('button.continue').click(function(){
		if (typeof actionsCallbacks.onContinue == 'function') {
			actionsCallbacks.onContinue();
		}
		modalConfirmation.hide();
	});

	return {
		'create': function(content, title, callbacks) {
			if(title != null){
				modal.find('.modal-title').html(title);
			}
			if(content != null){
				modal.find('.modal-body').html(content);
			}

			actionsCallbacks = callbacks;
			return this;
		},
		'show': function() {
			modal.modal('show');
		},
		'hide': function() {
			modal.modal('hide');
		}
	};
})();

var rightSidebar = (function() {
	return {
		'init': function() {
			/** Right sidebar should shrink only step1 tab content */
			$(document).on('show.bs.sidebar', function(e) {
				$('div#step1 > div.right-sidebar-flex').removeClass('col-lg-12').addClass('col-lg-9');

				/** Lazy load of product list */
				var url = $(e.target).attr('data-url');
				if (url) {
					rightSidebar.loadQuickNav(url, e.target);
				}
			});
			$(document).on('hide.bs.sidebar', function(e) {
				$('div#step1 > div.right-sidebar-flex').removeClass('col-lg-9').addClass('col-lg-12');
			});
		},
		'loadQuickNav': function(url, sidebar) {
			/** Loads inner HTML in the sidebar container */
			$(sidebar).load(url, function() {
				$(this).removeAttr('data-url');
				$('ul.pagination > li > a[href]', this).on('click', function(e) {
					e.preventDefault();
					rightSidebar.navigationChange($(e.target).attr('href'), $(sidebar));
				});
				$('ul.pagination > li > input[name="paginator_jump_page"]', this).on('keyup', function(e) {
					if (e.which == 13) { // ENTER
						e.preventDefault();
						var val = parseInt($(e.target).val());
						var limit = $(e.target).attr('pslimit');
						var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
						rightSidebar.navigationChange(url, $(sidebar));
					}
				});
			});
		},
		'navigationChange': function(url, sidebar) {
			console.log('pouet');
			rightSidebar.loadQuickNav(url, sidebar);
		}
	};
})();

/**
 * Manage seo
 */
var seo = (function() {
	var redirectTypeElem = $('#form_step5_redirect_type');

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

			hideShowRedirectToProduct();

			/** On redirect type select change */
			redirectTypeElem.change(function(){
				hideShowRedirectToProduct();
			});


			/** Reset all languages title to friendly url*/
			$('#seo-url-regenerate').click(function(){
				$.each($('.form-input-title input'), function(){
					var id_lang = $(this).attr('name').match(/\d+/)[0];
					$('#form_step5_link_rewrite_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
				});
			});
		}
	};
})();


/**
 * Tags management
 */
var tags = (function() {
	return {
		'init': function() {
			$('#form_step6_tags .tokenfield').tokenfield();
		}
	};
})();
