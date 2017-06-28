/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
  form.init();
  nav.init();
  featuresCollection.init();
  displayFormCategory.init();
  formCategory.init();
  stock.init();
  supplier.init();
  specificPrices.init();
  warehouseCombinations.init();
  customFieldCollection.init();
  virtualProduct.init();
  attachmentProduct.init();
  imagesProduct.init();
  priceCalculation.init();
  displayFieldsManager.refresh();
  displayFieldsManager.init(virtualProduct);
  seo.init();
  tags.init();
  rightSidebar.init();
  recommendedModules.init();
  BOEvent.emitEvent("Product Categories Management started", "CustomEvent");
  BOEvent.emitEvent("Product Default category Management started", "CustomEvent");
  BOEvent.emitEvent("Product Manufacturer Management started", "CustomEvent");
  BOEvent.emitEvent("Product Related Management started", "CustomEvent");
  BOEvent.emitEvent("Modal confirmation started", "CustomEvent");
  BOEvent.emitEvent("Product Combinations Management started", "CustomEvent");

  /** Type product fields display management */
  $('#form_step1_type_product').change(function() {
    displayFieldsManager.refresh();
  });

  // Validate price fields on input change
  $(".money-type input[type='text']").change(function validate() {
    var inputValue = priceCalculation.normalizePrice($(this).val());
    var parsedValue = truncateDecimals(inputValue, 6);

    $(this).val(parsedValue);
  });

  /** Attach date picker */
  $('.datepicker').datetimepicker({
    locale: full_language_code,
    format: 'YYYY-MM-DD'
  });

  /** tooltips should be hidden when we move to another tab */
  $('#form-nav').on('click','.nav-item', function clearTooltipsAndPopovers() {
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('[data-toggle="popover"]').popover('hide');
  });
});

/**
 * Manage show or hide fields
 */
var displayFieldsManager = (function() {

  var typeProduct = $('#form_step1_type_product');
  var showVariationsSelector = $('#show_variations_selector');
  var combinationsBlock = $('#combinations');
  var managedVirtualProduct;

  return {
    'init': function (virtualProduct) {
      managedVirtualProduct = virtualProduct;

      /** Type product fields display management */
      $('#form_step1_type_product').change(function () {
        displayFieldsManager.refresh();
      });

      $('#form .form-input-title input').on('focus', function () {
        $(this).select();
      });

      this.initVisibilityRule();

      /** Tax rule dropdown shortcut */
      $('a#tax_rule_shortcut_opener').on('click', function () {
        // lazy instantiated
        var duplicate = $('#form_step2_id_tax_rules_group_shortcut');
        if (duplicate.length == 0) {
          var origin = $('select#form_step2_id_tax_rules_group');
          duplicate = origin.clone(false).attr('id', 'form_step2_id_tax_rules_group_shortcut');
          origin.on('change', function () {
            duplicate.val(origin.val()); // no change() here to avoid infinite loop.
          });
          duplicate.on('change', function () {
            origin.val(duplicate.val()).change();
          });
          duplicate.appendTo($('#tax_rule_shortcut'));
        }
        duplicate.parent().parent().show();

        return false;
      });
    },
    /**
     * When a product is available for order, its price should be visible,
     * whereas products unavailable for order can have their prices visible or hidden.
     */
    'initVisibilityRule': function () {
      var showPriceSelector = '.js-show-price';
      var availableForOrderSelector = '.js-available-for-order';

      var applyVisibilityRule = function applyVisibilityRule() {
        var $availableForOrder = $(availableForOrderSelector + ' input');
        var $showPrice = $(showPriceSelector + ' input');
        var $showPriceColumn = $(showPriceSelector);
        if ($availableForOrder.prop('checked')) {
          $showPrice.prop('checked', true);
          $showPriceColumn.addClass('hide');
        } else {
          $showPriceColumn.removeClass('hide');
        }
      };
      $(availableForOrderSelector + ' .checkbox').on('click', applyVisibilityRule);
      applyVisibilityRule();
    },
    'refresh': function () {
      this.checkAccessVariations();
      $('#virtual_product').hide();
      $('#form-nav a[href="#step3"]').text(translate_javascripts['Quantities']);

      /** product type switch */

      if (typeProduct.val() === '1') {
        $('#pack_stock_type, #js_form_step1_inputPackItems').show();
        $('#form-nav a[href="#step4"]').show();
        showVariationsSelector.hide();
        showVariationsSelector.find('input[value="0"]').attr('checked', true);
      } else {
        $('#virtual_product, #pack_stock_type, #js_form_step1_inputPackItems').hide();
        $('#form-nav a[href="#step4"]').show();

        if (typeProduct.val() === '2') {
          showVariationsSelector.hide();
          $('#virtual_product').show();
          $('#form-nav a[href="#step4"]').hide();
          showVariationsSelector.find('input[value="0"]').attr('checked', true);
          $('#form-nav a[href="#step3"]').text(translate_javascripts['Virtual product']);
        } else {
          showVariationsSelector.show();
          $('#form-nav a[href="#step3"]').text(translate_javascripts['Quantities']);
        }
      }

      // Switching from a product type to another which is not "Virtual product",
      // triggers the destruction of pre-existing virtual product
      var shouldDestroyVirtualProduct = typeProduct.val() !== '2';
      if (shouldDestroyVirtualProduct && managedVirtualProduct !== undefined) {
        managedVirtualProduct.destroy();
      }

      /** check quantity / combinations display */
      if (showVariationsSelector.find('input:checked').val() === '1' || $('#accordion_combinations tr:not(#loading-attribute)').length > 0) {
        combinationsBlock.show();

        $('#specific-price-combination-selector').removeClass('hide').show();
        $('#form-nav a[href="#step3"]').text(translate_javascripts['Combinations']);
        $('#product_qty_0_shortcut_div, #quantities').hide();
      } else {
        combinationsBlock.hide();
        $('#specific-price-combination-selector').hide();
        $('#product_qty_0_shortcut_div, #quantities').show();
      }

      /** Tooltip for product type combinations */
      if ($('input[name="show_variations"][value="1"]:checked').length >= 1) {
        $('#product_type_combinations_shortcut').show();
      } else {
        $('#product_type_combinations_shortcut').hide();
      }
    },
    'getProductType': function () {
      switch (typeProduct.val()) {
        case '0':
          return 'standard';
          break;
        case '1':
          return 'pack';
          break;
        case '2':
          return 'virtual';
          break;
        default:
          return 'standard';
      }
    },
    /**
     * Product pack or virtual can't have variations
     * Warn e-merchant.
     * @param errorMessage
     */
    'checkAccessVariations': function () {
      if ((showVariationsSelector.find('input:checked').val() === '1' || $('#accordion_combinations tr:not(#loading-attribute)').length > 0) && (typeProduct.val() === '1' || typeProduct.val() === '2')) {
        var typeOfProduct = this.getProductType();
        var errorMessage = "You can't create " + typeOfProduct + " product with variations. Are you sure to disable variations ? they will all be deleted.";
        modalConfirmation.create(translate_javascripts[errorMessage], null, {
          onCancel: function () {
            typeProduct.val(0).change();
            /* else the radio bouton is not display even if checked attribute is true */
            $('#show_variations_selector input[value="1"]').click();
          },
          onContinue: function () {
            $.ajax({
              type: 'GET',
              url: $('#accordion_combinations').attr('data-action-delete-all').replace(/delete-all\/\d+/, 'delete-all/' + $('#form_id_product').val()),
              success: function () {
                $('#accordion_combinations .combination').remove();
                displayFieldsManager.refresh();
              },
              error: function (response) {
                showErrorMessage(jQuery.parseJSON(response.responseText).message);
              },
            });
          }
        }).show();
      }
    }
  }
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
 * Form category management
 */
var formCategory = (function() {
  var elem = $('#form_step1_new_category');

  /** Send category form and it to nested categories */
  function send(form) {
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
        $('ul.text-danger', elem).remove();
        $('*.has-danger', elem).removeClass('has-danger');
        $('*.has-danger').removeClass('has-danger');
      },
      success: function(response) {
        //inject new category into category tree
        var html = '<li>' +
          '<div class="checkbox js-checkbox">' +
            '<label>' +
              '<input type="checkbox" name="form[step1][categories][tree][]" checked value="'+response.category.id+'">' +
                response.category.name[1] +
            '</label>' +
            '<div class="radio pull-right">' +
              '<input type="radio" value="'+response.category.id+'" name="ignore" class="default-category">' +
            '</div>' +
          '</div>' +
          '</li>';

        var parentElement = $('#form_step1_categories input[value='+response.category.id_parent+']').parent().parent();
        if(parentElement.next('ul').length === 0){
          html = '<ul>' + html + '</ul>';
          parentElement.append(html);
        } else {
          parentElement.next('ul').append(html);
        }

        //inject new category in parent category selector
        $('#form_step1_new_category_id_parent').append('<option value="' + response.category.id + '">' + response.category.name[1] + '</option>');

        // create label
        var tag = {
          'name': response.category.name[1],
          'id': response.category.id,
          'breadcrumb': ''
        };
        productCategoriesTags.createTag(tag);

        //hide the form
        form.hideBlock();
      },
      error: function(response) {
        $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
          var html = '<ul class="list-unstyled text-danger">';
          $.each(errors, function(key, error) {
            html += '<li>' + error + '</li>';
          });
          html += '</ul>';

          $('#form_step1_new_' + key).parent().append(html);
          $('#form_step1_new_' + key).parent().addClass('has-danger');
        });
      },
      complete: function() {
        $('#form_step1_new_category button.submit').removeAttr('disabled');
      }
    });
  }

  return {
    'init': function() {
      var that = this;
      /** remove all categories from selector, except pre defined */
      $('#add-categories button.save').click(function(){
        send(that);
      });
      $('#add-categories button[type="reset"]').click(function(){
        that.hideBlock();
      });
    },
    'hideBlock': function() {
      $('#form_step1_new_category_name').val('');
      $('#add-category-button').show();
      $('#add-categories-content').addClass('hide');
    }
  };
})();

/**
 * Feature collection management
 */
var featuresCollection = (function() {

  var collectionHolder = $('.feature-collection');

  /** Add a feature */
  function add() {
    var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, collectionHolder.children('.row').length);
    collectionHolder.append(newForm);
    prestaShopUiKit.initSelects();
  }

  return {
    'init': function() {
      /** Click event on the add button */
      $('#features .add').on('click', function(e) {
        e.preventDefault();
        add();
        $('#features-content').removeClass('hide');
      });

      /** Click event on the remove button */
      $(document).on('click', '.feature-collection .delete', function(e) {
        e.preventDefault();
        var _this = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function() {
            _this.parent().parent().parent().remove();
          }
        }).show();
      });

      /** On feature selector event change, refresh possible values list */
      $(document).on('change', '.feature-collection select.feature-selector', function(event) {
        var that = event.currentTarget;
        var $row = $($(that).parents('.row')[0]);
        var $selector = $row.find('.feature-value-selector');

        if('' !== $(this).val()) {
          $.ajax({
            url: $(this).attr('data-action').replace(/\/\d+(?=\?.*)/, '/' + $(this).val()),
            success: function(response) {
              $selector.prop('disabled', response.length === 0);
              $selector.empty();
              $.each(response, function(index, elt) {
                // the placeholder shouldn't be posted.
                if ('0' == elt.id) {
                  elt.id = '';
                }
                $selector.append($('<option></option>').attr('value', elt.id).text(elt.value));
              });
            }
          });
        }
      });

      var $featuresContainer = $('#features-content');

      $featuresContainer.on('change', '.row select, .row input[type="text"]', function onChange(event){
        var that = event.currentTarget;
        var $row = $($(that).parents('.row')[0]);
        var $definedValueSelector = $row.find('.feature-value-selector');
        var $customValueSelector = $row.find('input[type=text]');

        // if feature has changed we need to reset values
        if ($(that).hasClass('feature-selector')) {
          $customValueSelector.val('');
          $definedValueSelector.val('');
        }
      });
    }
  };
})();

/**
 * Suppliers management
 */
var supplier = (function() {

  var supplierInputManage = function(input) {
    var supplierDefaultInput = $('#form_step6_suppliers input[name="form[step6][default_supplier]"][value=' + $(input).val() +']');
    if ($(input).is(':checked')) {
      supplierDefaultInput.prop('disabled', false).show();
    } else {
      supplierDefaultInput.prop('disabled', true).hide();
    }
  };

  return {
    'init': function() {
      /** On supplier select, hide or show the default supplier selector */
      var supplierInput = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]');
      supplierInput.change(function() {
        supplierInputManage($(this));
        supplierCombinations.refresh();
      });

      //default display
      $('#form_step6_suppliers input[name="form[step6][suppliers][]"]').map(function() {
        supplierInputManage($(this));
      });
    }
  };
})();

/**
 * Supplier combination collection management
 */
var supplierCombinations = (function() {
  var id_product = $('#form_id_product').val();
  var collectionHolder = $('#supplier_combination_collection');

  return {
    'refresh': function() {
      var suppliers = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]:checked').map(function() {
        return $(this).val();
      }).get();
      var url = collectionHolder.attr('data-url')
        .replace(
          /refresh-product-supplier-combination-form\/\d+\/\d+/,
          'refresh-product-supplier-combination-form/' + id_product + (suppliers.length > 0 ? '/' + suppliers.join('-') : '')
        );
      $.ajax({
        url: url,
        success: function(response) {
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
      $('#form_step1_qty_0_shortcut, #form_step3_qty_0').keyup(function() {
        if ($(this).attr('id') === 'form_step1_qty_0_shortcut') {
          $('#form_step3_qty_0').val($(this).val());
        } else {
          $('#form_step1_qty_0_shortcut').val($(this).val());
        }
      });

      /** if GSA : Show depends_on_stock choice only if advanced_stock_management checked */
      $('#form_step3_advanced_stock_management').on('change', function(e) {
        if (e.target.checked) {
          $('#depends_on_stock_div').show();
        } else {
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
      var formNav = $("#form-nav");
      var prefix = 'tab-';
      if (hash) {
        formNav.find("a[href='" + hash.replace(prefix, '') + "']").tab('show');
      }

      formNav.find("a").on('shown.bs.tab', function(e) {
        if (e.target.hash) {
          onTabSwitch(e.target.hash);
          window.location.hash = e.target.hash.replace('#', '#' + prefix);
        }
      });

      /** on tab switch */
      function onTabSwitch(currentTab) {
        if (currentTab === '#step2') {
          /** each switch to price tab, reload combinations into specific price form */
          specificPrices.refreshCombinationsList();
        }
      }
    }
  };
})();

/**
 * Specific prices management
 */
var specificPrices = (function() {
  var id_product = $('#form_id_product').val();
  var elem = $('#js-specific-price-list');
  var leaveInitialPrice = $('#form_step2_specific_price_leave_bprice');
  var productPriceField = $('#form_step2_specific_price_sp_price');
  var discountTypeField = $('#form_step2_specific_price_sp_reduction_type');
  var discountTaxField = $('#form_step2_specific_price_sp_reduction_tax');
  var initSpecificPriceForm = new Object();

  /** Get all specific prices */
  function getInitSpecificPriceForm() {
    $('#specific_price_form').find('select,input').each(function() {
        initSpecificPriceForm[$(this).attr('id')] = $(this).val();
    });
    $('#specific_price_form').find('input:checkbox').each(function() {
        initSpecificPriceForm[$(this).attr('id')] = $(this).prop('checked');
    });
  }

  /** Get all specific prices */
  function getAll() {
    var url = elem.attr('data').replace(/list\/\d+/, 'list/' + id_product);

    $.ajax({
      type: 'GET',
      url: url,
      success: function(specific_prices) {
        var tbody = elem.find('tbody');
        tbody.find('tr').remove();

        if (specific_prices.length > 0) {
          elem.removeClass('hide');
        } else {
          elem.addClass('hide');
        }

        $.each(specific_prices, function(key, specific_price) {
          var row = '<tr>' +
            '<td>' + specific_price.rule_name + '</td>' +
            '<td>' + specific_price.attributes_name + '</td>' +
            '<td>' + specific_price.currency + '</td>' +
            '<td>' + specific_price.country + '</td>' +
            '<td>' + specific_price.group + '</td>' +
            '<td>' + specific_price.customer + '</td>' +
            '<td>' + specific_price.fixed_price + '</td>' +
            '<td>' + specific_price.impact + '</td>' +
            '<td>' + specific_price.period + '</td>' +
            '<td>' + specific_price.from_quantity + '</td>' +
            '<td>' + (specific_price.can_delete ? '<a href="' + $('#js-specific-price-list').attr('data-action-delete').replace(/delete\/\d+/, 'delete/' + specific_price.id_specific_price) + '" class="js-delete delete"><i class="material-icons">delete</i></a>' : '') + '</td>' +
            '</tr>';

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
      success: function() {
        showSuccessMessage(translate_javascripts['Form update success']);
        $('#specific_price_form .js-cancel').click();
        getAll();
      },
      complete: function() {
        elem.removeAttr('disabled');
      },
      error: function(errors) {
        showErrorMessage(errors.responseJSON);
      }
    });
  }

  /**
   * Remove a specific price
   * @param {object} elem - The clicked link
   */
  function remove(elem) {
    modalConfirmation.create(translate_javascripts['This will delete the specific price. Do you wish to proceed?'], null, {
      onContinue: function() {
        $.ajax({
          type: 'GET',
          url: elem.attr('href'),
          beforeSend: function() {
            elem.attr('disabled', 'disabled');
          },
          success: function(response) {
            getAll();
            resetForm();
            showSuccessMessage(response);
          },
          error: function(response) {
            showErrorMessage(response.responseJSON);
          },
          complete: function() {
            elem.removeAttr('disabled');
          }
        });
      }
    }).show();
  }

  /** refresh combinations list selector for specific price form */
  function refreshCombinationsList() {
    var elem = $('#form_step2_specific_price_sp_id_product_attribute');
    var url = elem.attr('data-action').replace(/product-combinations\/\d+/, 'product-combinations/' + id_product);

    $.ajax({
      type: 'GET',
      url: url,
      success: function(combinations) {
        /** remove all options except first one */
        elem.find('option:gt(0)').remove();

        $.each(combinations, function(key, combination) {
          elem.append('<option value="' + combination.id + '">' + combination.name + '</option>');
        });
      }
    });
  }

  /**
   * Because all "forms" are encapsulated in a global form, we just can't use reset button
   * Reset all subform inputs values
   */
  function resetForm() {
    $('#specific_price_form').find('input').each(function() {
        $(this).val(initSpecificPriceForm[$(this).attr('id')]);
    });
    $('#specific_price_form').find('select').each(function() {
        $(this).val(initSpecificPriceForm[$(this).attr('id')]).change();
    });
    $('#specific_price_form').find('input:checkbox').each(function() {
        $(this).prop("checked", true);
    });
  }

  return {
    'init': function() {
      this.getAll();

      $('#specific-price .add').click(function() {
        $(this).hide();
      });

      $('#specific_price_form .js-cancel').click(function() {
        resetForm();
        $('#specific-price > a').click();
        $('#specific-price .add').click().show();
        productPriceField.prop('disabled', true);
      });

      $('#specific_price_form .js-save').click(function() {
        add($(this));
      });

      $(document).on('click', '#js-specific-price-list .js-delete', function(e) {
        e.preventDefault();
        remove($(this));
      });

      $('#form_step2_specific_price_sp_reduction_type').change(function() {
        if ($(this).val() === 'percentage') {
          $('#form_step2_specific_price_sp_reduction_tax').hide();
        } else {
          $('#form_step2_specific_price_sp_reduction_tax').show();
        }
      });

      this.refreshCombinationsList();

      /* enable price field only when needed */
      leaveInitialPrice.on('click', function togglePriceField() {
        productPriceField.prop('disabled', $(this).is(':checked'))
          .val('')
        ;
      });

      /* enable tax type field only when reduction by amount is selected */
      discountTypeField.on('change', function toggleDiscountTaxField() {
        var uglySelect2Selector = $('#select2-form_step2_specific_price_sp_reduction_tax-container').parent().parent();
        if ($(this).val() === 'amount') {
          uglySelect2Selector.show();
        }else {
          uglySelect2Selector.hide();
        }
      });

      this.getInitSpecificPriceForm();

    },
    'getAll': function() {
      getAll();
    },
    'refreshCombinationsList': function() {
      refreshCombinationsList();
    },
    'getInitSpecificPriceForm' : function() {
      getInitSpecificPriceForm();
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
        checkboxes.prop('checked', checkboxes.filter(':checked').size() === 0);
      });
      // location disablation depending on 'stored' checkbox
      $(document).on('change', 'div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]', function() {
        var checked = $(this).prop('checked');
        var location = $(this).closest('div.form-group').find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');
        location.prop('disabled', !checked);
        if (!checked) {
          location.val('');
        }
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
        var url = collectionHolder.attr('data-url').replace(/\/\d+(?=\?.*)/, '/' + id_product);
        $.ajax({
          url: url,
          success: function(response) {
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

  function send(redirect, target, callBack) {
    // target value by default
    if (typeof(target) == 'undefined') {
      target = false;
    }
    seo.onSave();
    updateMissingTranslatedNames();

    var data = $('input, textarea, select', elem).not(':input[type=button], :input[type=submit], :input[type=reset]').serialize();

    if (target == '_blank' && redirect) {
      var openBlank = window.open('about:blank', target, '');
      openBlank.document.write(
        '<p style="text-align: center;">' +
        '<img src="' + document.location.origin + baseAdminDir + '/themes/default/img/spinner.gif">' +
        '</p>'
      );
    }

    $.ajax({
      type: 'POST',
      data: data,
      beforeSend: function() {
        $('#submit', elem).attr('disabled', 'disabled');
        $('.btn-submit', elem).attr('disabled', 'disabled');
        $('ul.text-danger').remove();
        $('*.has-danger').removeClass('has-danger');
        $('#form-nav li.has-error').removeClass('has-error');
      },
      success: function(response) {
        if (callBack) {
          callBack();
        }
        showSuccessMessage(translate_javascripts['Form update success']);
        //update the customization ids
        if (typeof response.customization_fields_ids != "undefined") {
          $.each(response.customization_fields_ids, function (k, v) {
              $("#form_step6_custom_fields_" + k + "_id_customization_field").val(v);
          });
        }

        $('.js-spinner').hide();

        if (!redirect) {
          return;
        }

        if (false === target) {
          window.location = redirect;

          return;
        }

        if ('_blank' !== target) {
          window.open(redirect, target);

          return;
        }

        openBlank.location = redirect;
      },
      error: function(response) {
        showErrorMessage(translate_javascripts['Form update errors']);

        if (target == '_blank' && redirect) {
          openBlank.close();
        }

        var tabsWithErrors = [];

        $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
          tabsWithErrors.push(key);

          var html = '<ul class="list-unstyled text-danger">';
          $.each(errors, function(key, error) {
            html += '<li>' + error + '</li>';
          });
          html += '</ul>';

          if (key.match(/^combination_.*/)) {
            $('#' + key).parent().addClass('has-danger').append(html);
          } else {
            $('#form_' + key).parent().addClass('has-danger').append(html);
          }

        });

        /** find first tab with error, then switch to it */
        tabsWithErrors.sort();
        $.each(tabsWithErrors, function(key, tabIndex) {
          if (0 === key) {
            $('#form-nav li a[href="#' + tabIndex.split('_')[0] + '"]').tab('show');
          }

          $('#form-nav li a[href="#' + tabIndex.split('_')[0] + '"]').parent().addClass('has-error');
        });

        if ($('div[class*="translation-label-"].has-danger').length > 0) {
          var regexLabel = 'translation-label-';

          var translationLabelClass = $.grep($('div[class*="translation-label-"].has-danger').first().attr('class').split(" "), function(v, i){
            return v.indexOf(regexLabel) === 0;
          }).join();

          if (translationLabelClass) {
            var selectValue = translationLabelClass.replace(regexLabel, '');

            if ($('#form_switch_language option[value="' + selectValue + '"]').length > 0) {
              $('#form_switch_language').val(selectValue).change();
            }
          }
        }

        /** scroll to 1st error */
        if ($('.has-danger').first().offset()) {
          $('html, body').animate({
            scrollTop: $('.has-danger').first().offset().top - $('nav.main-header').height()
          }, 500);
        }
      },
      complete: function() {
        $('#submit', elem).removeAttr('disabled');
        $('.btn-submit', elem).removeAttr('disabled');
      }
    });
  }

  function switchLanguage(iso_code) {
    $('div.translations.tabbable > div > div.tab-pane:not(.translation-label-' + iso_code + ')').removeClass('active');
    $('div.translations.tabbable > div > div.tab-pane.translation-label-' + iso_code).addClass('active');
  }

  function updateMissingTranslatedNames() {
      var namesDiv = $('#form_step1_names');
      var defaultLanguageValue = null;
      $("input[id^='form_step1_name_']", namesDiv).each(function(index) {
          var value = $(this).val();
          // The first language is ALWAYS the employee language
          if (0 === index) {
              defaultLanguageValue = value;
          } else if (0 === value.length) {
              $(this).val(defaultLanguageValue);
          }
      });
  }

  return {
    'init': function() {
      /** prevent form submit on ENTER keypress */
      jwerty.key('enter', function(e) {
        e.preventDefault();
      });

      /** create keyboard event for save */
      jwerty.key('alt+shift+S', function(e) {
        e.preventDefault();
        send();
      });

      /** create keyboard event for save & duplicate */
      jwerty.key('alt+shift+D', function(e) {
        e.preventDefault();
        send($('.product-footer .duplicate').attr('data-redirect'));
      });

      /** create keyboard event for save & new */
      jwerty.key('alt+shift+P', function(e) {
        e.preventDefault();
        send($('.product-footer .new-product').attr('data-redirect'));
      });

      /** create keyboard event for save & go catalog */
      jwerty.key('alt+shift+Q', function(e) {
        e.preventDefault();
        send($('.product-footer .go-catalog').attr('data-redirect'));
      });

      /** create keyboard event for save & go preview */
      jwerty.key('alt+shift+V', function(e) {
          e.preventDefault();
          var productFooter = $('.product-footer .preview');
          send(productFooter.attr('data-redirect'), productFooter.attr('target'));
      });

      /** create keyboard event for save & active or desactive product*/
      jwerty.key('alt+shift+O', function(e) {
        e.preventDefault();
        var step1CheckBox = $('#form_step1_active');
        step1CheckBox.prop('checked', !step1CheckBox.is(':checked'));
      });

      elem.submit(function(event) {
        event.preventDefault();
        send();
      });

      elem.find('#form_switch_language').change(function(event) {
        event.preventDefault();
        switchLanguage(event.target.value);
      });

      /** on save with duplicate|new|preview */
      $('.btn-submit', elem).click(function(event) {
        event.preventDefault();
        send($(this).attr('data-redirect'), $(this).attr('target'));
      });

      $('.js-btn-save').on('click', function (event) {
        event.preventDefault();
        $('.js-spinner').show();
        send($(this).attr('href'));
      });

      /** on active field change, send form */
      $('#form_step1_active', elem).on('change', function() {
        var active = $(this).prop('checked');
        $('.for-switch.online-title').toggle(active);
        $('.for-switch.offline-title').toggle(!active);
        // update link preview
        var urlActive = $('#product_form_preview_btn').attr('data-redirect');
        var urlDeactive = $('#product_form_preview_btn').attr('data-url_deactive');
        $('#product_form_preview_btn').attr('data-redirect', urlDeactive);
        $('#product_form_preview_btn').attr('data-url_deactive', urlActive);
        // update product
        send();
      });

      /** on delete product */
      $('.product-footer .delete', elem).click(function(e) {
        e.preventDefault();
        var _this = $(this);
        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function() {
            window.location = _this.attr('href');
          }
        }).show();
      });

      /** show rendered form after page load */
      $(window).load(function() {
        $('#form-loading').fadeIn(function() {
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

          /** init input typeahead */
          $('#form_step3_attributes').tokenfield({
            typeahead: [{
              hint: false,
              cache: false
            }, {
              source: function(query, syncResults) {
                engine.search(query, function(suggestions) {
                  syncResults(filter(suggestions));
                });
              },
              display: 'label'
            }],
            minWidth: '768px'
          });

          /** Filter suggestion with selected tokens */
          var filter = function(suggestions) {
            var selected = [];
            $('#attributes-generator input.attribute-generator').each(function() {
              selected.push($(this).val());
            });

            return $.grep(suggestions, function(suggestion) {
              return $.inArray(suggestion.value, selected) === -1 && $.inArray('group-' + suggestion.data.id_group, selected) === -1;
            });
          };

          /** On event "tokenfield:createtoken" : stop event if its not a typehead result */
          $('#form_step3_attributes').on('tokenfield:createtoken', function(e) {
            if (!e.attrs.data && e.handleObj.origType !== 'tokenfield:createtoken') {
              return false;
            }
          });

          /** On event "tokenfield:createdtoken" : store attributes in input when add a token */
          $('#form_step3_attributes').on('tokenfield:createdtoken', function(e) {
            if (e.attrs.data) {
              $('#attributes-generator').append('<input type="hidden" id="attribute-generator-' + e.attrs.value + '" class="attribute-generator" value="' + e.attrs.value + '" name="options[' + e.attrs.data.id_group + '][' + e.attrs.value + ']" />');
            } else if (e.handleObj.origType == 'tokenfield:createdtoken') {
              $('#attributes-generator').append('<input type="hidden" id="attribute-generator-' + $('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data('value') + '" class="attribute-generator" value="' + $('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data('value') + '" name="options[' + $('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data('group-id') + '][' + $('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data('value') + ']" />');
            }
          });

          /** On event "tokenfield:removedtoken" : remove stored attributes input when remove token */
          $('#form_step3_attributes').on('tokenfield:removedtoken', function(e) {
            $('#attribute-generator-' + e.attrs.value).remove();
          });
        });
        imagesProduct.initExpander();
      });
    },
    'send': function(redirect, target, callBack) {
      send(redirect, target, callBack);
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

  /** Add a custom field */
  function add() {
    var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, collectionHolder.children().length);
    collectionHolder.append('<li>' + newForm + '</li>');
  }

  return {
    'init': function() {
      /** Click event on the add button */
      $('#custom_fields a.add').on('click', function(e) {
        e.preventDefault();
        add();
      });

      /** Click event on the remove button */
      $(document).on('click', 'ul.customFieldCollection .delete', function(e) {
        e.preventDefault();
        var _this = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function() {
            _this.parent().parent().parent().remove();
          }
        }).show();
      });
    }
  };
})();

/**
 * virtual product management
 */
var virtualProduct = (function() {
  var id_product = $('#form_id_product').val();

  var getOnDeleteVirtualProductFileHandler = function ($deleteButton) {
    return $.ajax({
      type: 'GET',
      url: $deleteButton.attr('href').replace(/\/\d+(?=\?.*)/, '/' + id_product),
      success: function () {
        $('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
        $('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
      }
    })
  };

  return {
    'init': function() {
      $(document).on('change', 'input[name="form[step3][virtual_product][is_virtual_file]"]', function() {
        if ($(this).val() === '1') {
          $('#virtual_product_content').show();
        } else {
          $('#virtual_product_content').hide();

          var url = $('#virtual_product').attr('data-action-remove').replace(/remove\/\d+/, 'remove/' + id_product);
          //delete virtual product
          $.ajax({
            type: 'GET',
            url: url,
            success: function() {
              //empty form
              $('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
              $('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
              $('#form_step3_virtual_product_name').val('');
              $('#form_step3_virtual_product_nb_downloadable').val(0);
              $('#form_step3_virtual_product_expiration_date').val('');
              $('#form_step3_virtual_product_nb_days').val(0);
            }
          });
        }
      });

      $('#form_step3_virtual_product_file').change(function(e) {
        if ($(this)[0].files !== undefined) {
          var files = $(this)[0].files;
          var name  = '';

          $.each(files, function(index, value) {
            name += value.name + ', ';
          });
          $('#form_step3_virtual_product_name').val(name.slice(0, -2));
        } else {
          // Internet Explorer 9 Compatibility
          var name = $(this).val().split(/[\\/]/);
          $('#form_step3_virtual_product_name').val(name[name.length - 1]);
        }
      });

      if ($('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val() === '1') {
        $('#virtual_product_content').show();
      } else {
        $('#virtual_product_content').hide();
      }

      /** delete attached file */
      $('#form_step3_virtual_product_file_details .delete').click(function(e) {
        e.preventDefault();
        var $deleteButton = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function() {
            getOnDeleteVirtualProductFileHandler($deleteButton);
          }
        }).show();
      });

      /** save virtual product */
      $('#form_step3_virtual_product_save').click(function() {
        var _this = $(this);
        var data = new FormData();

        if ($('#form_step3_virtual_product_file')[0].files[0]) {
          data.append('product_virtual[file]', $('#form_step3_virtual_product_file')[0].files[0]);
        }
        data.append('product_virtual[is_virtual_file]', $('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val());
        data.append('product_virtual[name]', $('#form_step3_virtual_product_name').val());
        data.append('product_virtual[nb_downloadable]', $('#form_step3_virtual_product_nb_downloadable').val());
        data.append('product_virtual[expiration_date]', $('#form_step3_virtual_product_expiration_date').val());
        data.append('product_virtual[nb_days]', $('#form_step3_virtual_product_nb_days').val());

        $.ajax({
          type: 'POST',
          url: $('#virtual_product').attr('data-action').replace(/save\/\d+/, 'save/' + id_product),
          data: data,
          contentType: false,
          processData: false,
          beforeSend: function() {
            _this.prop('disabled', 'disabled');
            $('ul.text-danger').remove();
            $('*.has-danger').removeClass('has-danger');
          },
          success: function(response) {
            showSuccessMessage(translate_javascripts['Form update success']);
            if (response.file_download_link) {
              $('#form_step3_virtual_product_file_details a.download').attr('href', response.file_download_link);
              $('#form_step3_virtual_product_file_input').removeClass('show').addClass('hide');
              $('#form_step3_virtual_product_file_details').removeClass('hide').addClass('show');
            }
          },
          error: function(response) {
            $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
              var html = '<ul class="list-unstyled text-danger">';
              $.each(errors, function(key, error) {
                html += '<li>' + error + '</li>';
              });
              html += '</ul>';

              $('#form_step3_virtual_product_' + key).parent().append(html);
              $('#form_step3_virtual_product_' + key).parent().addClass('has-danger');
            });
          },
          complete: function() {
            _this.removeAttr('disabled');
          }
        });
      });
    },
    'destroy': function () {
      var fileDetailsSelector = '#form_step3_virtual_product_file_details';
      var fileAssociationExists = !$(fileDetailsSelector).hasClass('hide');

      if (fileAssociationExists) {
        var $deleteButton = $(fileDetailsSelector + ' .delete');
        getOnDeleteVirtualProductFileHandler($deleteButton);
      }

      var associatedFileCheckboxSelectorPrefix = '#form_step3_virtual_product_is_virtual_file_';
      $(associatedFileCheckboxSelectorPrefix + '0').prop('checked', false);
      $(associatedFileCheckboxSelectorPrefix + '1').prop('checked', true);

      $('#virtual_product_content input').val('');
    }
  }
})();

/**
 * attachment product management
 */
var attachmentProduct = (function() {
  var id_product = $('#form_id_product').val();

  return {
    'init': function() {
      var buttonSave = $('#form_step6_attachment_product_add');
      var buttonCancel = $('#form_step6_attachment_product_cancel');

      /** check all attachments files */
      $('#product-attachment-files-check').change(function() {
        if ($(this).is(":checked")) {
          $('#product-attachment-file input[type="checkbox"]').prop('checked', true);
        } else {
          $('#product-attachment-file input[type="checkbox"]').prop('checked', false);
        }
      });

      buttonCancel.click(function (){
        resetAttachmentForm();
      });

      function resetAttachmentForm() {
        $('#form_step6_attachment_product_file').val('');
        $('#form_step6_attachment_product_name').val('');
        $('#form_step6_attachment_product_description').val('');
      }

      /** add attachment */
      $('#form_step6_attachment_product_add').click(function() {
        var _this = $(this);
        var data = new FormData();

        if ($('#form_step6_attachment_product_file')[0].files[0]) {
          data.append('product_attachment[file]', $('#form_step6_attachment_product_file')[0].files[0]);
        }
        data.append('product_attachment[name]', $('#form_step6_attachment_product_name').val());
        data.append('product_attachment[description]', $('#form_step6_attachment_product_description').val());

        $.ajax({
          type: 'POST',
          url: $('#form_step6_attachment_product').attr('data-action').replace(/\/\d+(?=\?.*)/, '/' + id_product),
          data: data,
          contentType: false,
          processData: false,
          beforeSend: function() {
            buttonSave.prop('disabled', 'disabled');
            $('ul.text-danger').remove();
            $('*.has-danger').removeClass('has-danger');
          },
          success: function(response) {
            resetAttachmentForm();

            //inject new attachment in attachment list
            if (response.id) {
              var row = '<tr>\
                <td class="col-md-3"><input type="checkbox" name="form[step6][attachments][]" value="' + response.id + '" checked="checked"> ' + response.real_name + '</td>\
                <td class="col-md-6">' + response.file_name + '</td>\
                <td class="col-md-2">' + response.mime + '</td>\
              </tr>';

              $('#product-attachment-file tbody').append(row);
              $('.js-options-no-attachments').addClass('hide');
              $('.js-options-with-attachments').removeClass('hide');
            }
          },
          error: function(response) {
            $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
              var html = '<ul class="list-unstyled text-danger">';
              $.each(errors, function(key, error) {
                html += '<li>' + error + '</li>';
              });
              html += '</ul>';

              $('#form_step6_attachment_product_' + key).parent().append(html);
              $('#form_step6_attachment_product_' + key).parent().addClass('has-danger');
            });
          },
          complete: function() {
            buttonSave.removeAttr('disabled');
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
  var dropZoneElem = $('#product-images-dropzone');
  var expanderElem = $('#product-images-container .dropzone-expander');

  function checkDropzoneMode() {
      if (!dropZoneElem.find('.dz-preview:not(.openfilemanager)').length) {
        dropZoneElem.removeClass('dz-started');
        dropZoneElem.find('.dz-preview.openfilemanager').hide();
      }
      else {
          dropZoneElem.find('.dz-preview.openfilemanager').show();
      }
  };

  return {
    'toggleExpand': function() {
        if (expanderElem.hasClass('expand')) {
          dropZoneElem.css('height', 'auto');
          expanderElem.removeClass('expand').addClass('compress');
        } else {
          dropZoneElem.css('height', '');
          expanderElem.removeClass('compress').addClass('expand');
        }
    },
    'displayExpander': function() {
      expanderElem.show();
    },
    'hideExpander': function() {
      expanderElem.hide();
    },
    'shouldDisplayExpander': function () {
      var oldHeight = dropZoneElem.css('height');

      dropZoneElem.css('height', '');
      var closedHeight = dropZoneElem.outerHeight();
      var realHeight = dropZoneElem[0].scrollHeight;
      dropZoneElem.css('height', oldHeight);

      return (realHeight > closedHeight);
    },
    'updateExpander': function() {
      if (this.shouldDisplayExpander()) {
        this.displayExpander();
      }
    },
    'initExpander': function() {
      if (this.shouldDisplayExpander()) {
        this.displayExpander();
        expanderElem.addClass('expand');
      }

      var self = this;
      $(document).on('click', '#product-images-container .dropzone-expander', function() {
        self.toggleExpand();
      });
    },
    'init': function() {
      Dropzone.autoDiscover = false;
      var errorElem = $('#product-images-dropzone-error');

      //on click image, display custom form
      $(document).on('click', '#product-images-dropzone .dz-preview', function() {
        if (!$(this).attr('data-id')) {
          return;
        }
        formImagesProduct.form($(this).attr('data-id'));
      });

      var dropzoneOptions = {
        url: dropZoneElem.attr('url-upload'),
        paramName: 'form[file]',
        maxFilesize: dropZoneElem.attr('data-max-size'),
        addRemoveLinks: true,
        clickable: '.openfilemanager',
        thumbnailWidth: 250,
        thumbnailHeight: null,
        acceptedFiles: 'image/*',
        dictRemoveFile: translate_javascripts['Delete'],
        dictFileTooBig: translate_javascripts['ToLargeFile'],
        dictCancelUpload: translate_javascripts['Delete'],
        sending: function(file, response) {
          checkDropzoneMode();
          expanderElem.addClass('expand').click();
          errorElem.html('');
        },
        queuecomplete: function() {
          checkDropzoneMode();
          dropZoneElem.sortable('enable');
          imagesProduct.updateExpander();
        },
        processing: function() {
          dropZoneElem.sortable('disable');
        },
        success: function(file, response) {
          //manage error on uploaded file
          if (response.error !== 0) {
            errorElem.append('<p>' + file.name + ': ' + response.error + '</p>');
            this.removeFile(file);
            return;
          }

          //define id image to file preview
          $(file.previewElement).attr('data-id', response.id);
          $(file.previewElement).attr('url-update', response.url_update);
          $(file.previewElement).attr('url-delete', response.url_delete);
          $(file.previewElement).addClass('ui-sortable-handle');
          if (response.cover === 1) {
            imagesProduct.updateDisplayCover(response.id);
          }
        },
        error: function(file, response) {
          var message = '';
          if ($.type(response) === 'undefined') {
            return;
          } else if ($.type(response) === 'string') {
            message = response;
          } else if (response.message) {
            message = response.message;
          }

          if (message === '') {
            return;
          }

          //append new error
          errorElem.append('<p>' + file.name + ': ' + message + '</p>');

          //remove uploaded item
          this.removeFile(file);
        },
        init: function() {
          //if already images uploaded, mask drop file message
          if (dropZoneElem.find('.dz-preview:not(.openfilemanager)').length) {
            dropZoneElem.addClass('dz-started');
          } else {
            dropZoneElem.find('.dz-preview.openfilemanager').hide();
          }

          //init sortable
          dropZoneElem.sortable({
            items: "div.dz-preview:not(.disabled)",
            opacity: 0.9,
            containment: 'parent',
            distance: 32,
            tolerance: 'pointer',
            cursorAt: {
              left: 64,
              top: 64
            },
            cancel: '.disabled',
            stop: function(event, ui) {
              var sort = {};
              $.each(dropZoneElem.find('.dz-preview:not(.disabled)'), function(index, value) {
                if (!$(value).attr('data-id')) {
                  sort = false;
                  return;
                }
                sort[$(value).attr('data-id')] = index + 1;
              });

              //if sortable ok, update it
              if (sort) {
                $.ajax({
                  type: 'POST',
                  url: dropZoneElem.attr('url-position'),
                  data: {
                    json: JSON.stringify(sort)
                  }
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
    },
    'checkDropzoneMode': function() {
      checkDropzoneMode();
    },
    'getOlderImageId': function() {
      return Math.min.apply(Math,$('.dz-preview').map(function(){
        return $(this).data('id');
      }));
    }
  };
})();


var formImagesProduct = (function() {
  var dropZoneElem = $('#product-images-dropzone');
  var formZoneElem = $('#product-images-form-container');

  formZoneElem.magnificPopup({
    delegate: 'a.open-image',
    type: 'image'
  });

  function toggleColDropzone(enlarge) {
      var smallCol = "col-md-8";
      var largeCol = "col-md-12";
      if (true === enlarge) {
          dropZoneElem.removeClass(smallCol).addClass(largeCol);
      } else {
          dropZoneElem.removeClass(largeCol).addClass(smallCol);
      }
  }

  return {
    'form': function(id) {
      dropZoneElem.find(".dz-preview.active").removeClass("active");
      dropZoneElem.find(".dz-preview[data-id='"+id+"']").addClass("active");
      if(imagesProduct.shouldDisplayExpander() == false){
        dropZoneElem.css('height','auto');
      }
      $.ajax({
        url: dropZoneElem.find(".dz-preview[data-id='"+id+"']").attr('url-update'),
        success: function(response) {
          formZoneElem.find('#product-images-form').html(response);
          form.switchLanguage($('#form_switch_language').val());
        },
        complete: function() {
          toggleColDropzone(false);
          formZoneElem.show();
        }
      });
    },
    'send': function(id) {
      $.ajax({
        type: 'POST',
        url: dropZoneElem.find(".dz-preview[data-id='"+id+"']").attr('url-update'),
        data: formZoneElem.find('textarea, input').serialize(),
        beforeSend: function() {
          formZoneElem.find('.actions button').prop('disabled', 'disabled');
          formZoneElem.find('ul.text-danger').remove();
          formZoneElem.find('*.has-danger').removeClass('has-danger');
        },
        success: function() {
          if (formZoneElem.find('#form_image_cover:checked').length) {
            imagesProduct.updateDisplayCover(id);
          }
        },
        error: function(response) {
          if (response && response.responseText) {
            $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
              var html = '<ul class="list-unstyled text-danger">';
              $.each(errors, function(key, error) {
                html += '<li>' + error + '</li>';
              });
              html += '</ul>';

              $('#form_image_' + key).parent().append(html);
              $('#form_image_' + key).parent().addClass('has-danger');
            });
          }
        },
        complete: function() {
          formZoneElem.find('.actions button').removeAttr('disabled');
        }
      });
    },
    'delete': function(id) {
      modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
        onContinue: function() {
          $.ajax({
            url: dropZoneElem.find('.dz-preview[data-id="' + id + '"]').attr('url-delete'),
            complete: function() {
              formZoneElem.find('.close').click();
              var wasCover = !!dropZoneElem.find('.dz-preview[data-id="' + id + '"] .iscover').length;
              dropZoneElem.find('.dz-preview[data-id="' + id + '"]').remove();
              $('.images .product-combination-image [value=' + id + ']').parent().remove();
              imagesProduct.checkDropzoneMode();
              if (true === wasCover) {
                // The controller will choose the oldest image as the new cover.
                imagesProduct.updateDisplayCover(imagesProduct.getOlderImageId());
              }
            }
          });
        }
      }).show();
    },
    'close': function() {
      toggleColDropzone(true);
      dropZoneElem.css('height','');
      formZoneElem.find('#product-images-form').html('');
      formZoneElem.hide();
      dropZoneElem.find(".dz-preview.active").removeClass("active");
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
  var reTaxElem = $('#step2_id_tax_rules_group_rendered');
  var displayPricePrecision = priceHTElem.attr('data-display-price-precision');
  var ecoTaxRate = ecoTaxElem.attr('data-eco-tax-rate');

  /**
   * Add taxes to a price
   * @param {Number} price - Price without tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod The computation calculate method
   */
  function addTaxes(price, rates, computationMethod) {
    var price_with_taxes = price;

    var i = 0;
    if (computationMethod === '0') {
      for (i in rates) {
        price_with_taxes *= (1.00 + parseFloat(rates[i]) / 100.00);
        break;
      }
    } else if (computationMethod === '1') {
      var rate = 0;
      for (i in rates) {
        rate += rates[i];
      }
      price_with_taxes *= (1.00 + parseFloat(rate) / 100.00);
    } else if (computationMethod === '2') {
      for (i in rates) {
        price_with_taxes *= (1.00 + parseFloat(rates[i]) / 100.00);
      }
    }

    return price_with_taxes;
  }

  /**
   * Remove taxes from a price
   * @param {Number} price - Price with tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod - The computation method
   */
  function removeTaxes(price, rates, computationMethod) {
    var i = 0;
    if (computationMethod === '0') {
      for (i in rates) {
        price /= (1 + rates[i] / 100);
        break;
      }
    } else if (computationMethod === '1') {
      var rate = 0;
      for (i in rates) {
        rate += rates[i];
      }
      price /= (1 + rate / 100);
    } else if (computationMethod === '2') {
      for (i in rates) {
        price /= (1 + rates[i] / 100);
      }
    }

    return price;
  }

  /**
   *
   * @return {Number}
   */
  function getEcotaxTaxIncluded() {
    var displayPrecision = 6;
    var ecoTax = Tools.parseFloatFromString(ecoTaxElem.val());

    if (isNaN(ecoTax)) {
      ecoTax = 0;
    }

    if (ecoTax === 0) {
      return ecoTax;
    }
    var ecotaxTaxExcl = ecoTax / (1 + ecoTaxRate);

    return ps_round(ecotaxTaxExcl * (1 + ecoTaxRate), displayPrecision);
  }

  function getEcotaxTaxExcluded() {
    return Tools.parseFloatFromString(ecoTaxElem.val()) / (1 + ecoTaxRate);
  }

  return {

    init: function () {
      /** on update tax recalculate tax include price */
      taxElem.change(function() {
        if (reTaxElem.val() !== taxElem.val()) {
          reTaxElem.val(taxElem.val()).trigger('change');
        }

        priceCalculation.taxInclude();
        priceTTCElem.change();
      });

      reTaxElem.change(function() {
        taxElem.val(reTaxElem.val()).trigger('change');
      });

      /** update without tax price and shortcut price field on change */
      $('#form_step1_price_shortcut, #form_step2_price').keyup(function() {
        var price = priceCalculation.normalizePrice($(this).val());

        if ($(this).attr('id') === 'form_step1_price_shortcut') {
          $('#form_step2_price').val(price).change();
        } else {
          $('#form_step1_price_shortcut').val(price).change();
        }

        priceCalculation.taxInclude();
      });

      /** update HT price and shortcut price field on change */
      $('#form_step1_price_ttc_shortcut, #form_step2_price_ttc').keyup(function() {
        var price = priceCalculation.normalizePrice($(this).val());

        if ($(this).attr('id') === 'form_step1_price_ttc_shortcut') {
          $('#form_step2_price_ttc').val(price).change();
        } else {
          $('#form_step1_price_ttc_shortcut').val(price).change();
        }

        priceCalculation.taxExclude();
      });

      /** on price change, update final retails prices */
      $('#form_step2_price, #form_step2_price_ttc').change(function() {
        var taxExcludedPrice = priceCalculation.normalizePrice($('#form_step2_price').val());
        var taxIncludedPrice = priceCalculation.normalizePrice($('#form_step2_price_ttc').val());

        formatCurrencyCldr(taxExcludedPrice, function(result) {
          $('#final_retail_price_te').text(result);
        });
        formatCurrencyCldr(taxIncludedPrice, function(result) {
          $('#final_retail_price_ti').text(result);
        });
      });

      /** update HT price and shortcut price field on change */
      $('#form_step2_ecotax').keyup(function() {
        priceCalculation.taxExclude();
      });

      /** combinations : update TTC price field on change */
      $(document).on('keyup', '.combination-form .attribute_priceTE', function() {
        priceCalculation.impactTaxInclude($(this));
        priceCalculation.impactFinalPrice($(this));
      });
      /** combinations : update HT price field on change */
      $(document).on('keyup', '.combination-form .attribute_priceTI', function() {
        priceCalculation.impactTaxExclude($(this));
      });
      /** combinations : update wholesale price, unity and price TE field on blur */
      $(document).on('blur','.combination-form .attribute_wholesale_price,.combination-form .attribute_unity,.combination-form .attribute_priceTE', function(){
        $(this).val(priceCalculation.normalizePrice($(this).val()));
      });

      priceCalculation.taxInclude();

      $('#form_step2_price, #form_step2_price_ttc').change();
    },

    /**
     * Converts a price string into a number
     * @param {String} price
     * @return {Number}
     */
    normalizePrice: function (price) {
      return Tools.parseFloatFromString(price, true);
    },

    /**
     * Adds taxes to a price
     * @param {Number} price Price without taxes
     * @return {Number} Price with added taxes
     */
    addCurrentTax: function (price) {
      var rates = this.getRates();
      var computation_method = taxElem.find('option:selected').attr('data-computation-method');
      var priceWithTaxes = Number(ps_round(addTaxes(price, rates, computation_method), displayPricePrecision));
      var ecotaxIncluded = Number(getEcotaxTaxIncluded());

      return priceWithTaxes + ecotaxIncluded;
    },

    /**
     * Calculates the price with taxes and updates the elements containing it
     */
    taxInclude: function () {
      var newPrice = truncateDecimals(this.addCurrentTax(this.normalizePrice(priceHTElem.val())), 6);

      priceTTCElem.val(newPrice).change();
      priceTTCShorcutElem.val(newPrice).change();
    },

    /**
     * Removes taxes from a price
     * @param {Number} price Price with taxes
     * @return {Number} Price without taxes
     */
    removeCurrentTax: function (price) {
      var rates = this.getRates();
      var computation_method = taxElem.find('option:selected').attr('data-computation-method');

      return ps_round(removeTaxes(ps_round(price - getEcotaxTaxIncluded(), displayPricePrecision), rates, computation_method), displayPricePrecision);
    },

    /**
     * Calculates the price without taxes and updates the elements containing it
     */
    taxExclude: function () {
      var newPrice = truncateDecimals(this.removeCurrentTax(this.normalizePrice(priceTTCElem.val())), 6);

      priceHTElem.val(newPrice).change();
      priceHTShortcutElem.val(newPrice).change();
    },

    /**
     * Calculates and displays the impact on price (including tax) for a combination
     * @param {jQuery} obj
     */
    impactTaxInclude: function (obj) {
      var price = Tools.parseFloatFromString(obj.val());
      var targetInput = obj.closest('div[id^="combination_form_"]').find('input.attribute_priceTI');
      var newPrice = 0;

      if (!isNaN(price)) {
        var rates = this.getRates();
        var computation_method = taxElem.find('option:selected').attr('data-computation-method');
        newPrice = ps_round(addTaxes(price, rates, computation_method), 6);
        newPrice = truncateDecimals(newPrice, 6);
      }

      targetInput
        .val(newPrice)
        .trigger('change')
      ;
    },

    /**
     * Calculates and displays the final price for a combination
     * @param {jQuery} obj
     */
    impactFinalPrice: function (obj) {
      var price = this.normalizePrice(obj.val());
      var finalPrice = obj.closest('div[id^="combination_form_"]').find('.final-price');
      var defaultFinalPrice = finalPrice.attr('data-price');
      var priceToBeChanged = Number(price) + Number(defaultFinalPrice);
      priceToBeChanged = truncateDecimals(priceToBeChanged, 6);

      finalPrice.html(priceToBeChanged);
    },

    /**
     * Calculates and displays the impact on price (excluding tax) for a combination
     * @param {jQuery} obj
     */
    impactTaxExclude: function (obj) {
      var price = Tools.parseFloatFromString(obj.val());
      var targetInput = obj.closest('div[id^="combination_form_"]').find('input.attribute_priceTE');
      var newPrice = 0;

      if (!isNaN(price)) {
        var rates = this.getRates();
        var computation_method = taxElem.find('option:selected').attr('data-computation-method');
        newPrice = removeTaxes(ps_round(price, displayPricePrecision), rates, computation_method);
        newPrice = truncateDecimals(newPrice, 6);
      }

      targetInput
        .val(newPrice)
        .trigger('change')
      ;
    },

    /**
     * Returns the tax rates that apply
     * @return {Number[]}
     */
    getRates: function () {
      return taxElem
        .find('option:selected')
        .attr('data-rates')
        .split(',')
        .map(function(rate) {
          return Tools.parseFloatFromString(rate, true);
        });
    }
  };
})();

/**
 * Manage seo
 */
var seo = (function() {
  var redirectTypeElem = $('#form_step5_redirect_type');
  var productRedirect = $('#id-product-redirected');

  /** Hide or show the input product selector */
  function hideShowRedirectToProduct() {
    if ('404' === redirectTypeElem.val()) {
      $('#id-product-redirected').hide();
    } else {
      updateRemoteUrl();
      $('#id-product-redirected').show();
    }
  }

  function updateRemoteUrl() {
    switch(redirectTypeElem.val()) {
      case '301-category':
      case '302-category':
        productRedirect.find('label').html(redirectTypeElem.attr('data-labelcategory'));
        productRedirect.find('input').attr('placeholder', redirectTypeElem.attr('data-placeholdercategory'));
        break;
      default:
        productRedirect.find('label').html(redirectTypeElem.attr('data-labelproduct'));
        productRedirect.find('input').attr('placeholder', redirectTypeElem.attr('data-placeholderproduct'));
    }

    productRedirect.find('.autocomplete-search').attr('data-remoteurl', redirectTypeElem.find('option:selected').data('remoteurl'));
    productRedirect.find('.autocomplete-search').trigger('buildTypeahead');
  }

  /** Update friendly URL */
  var updateFriendlyUrl = function(elem) {
      /** Attr name equals "form[step1][name][1]".
       * We need in this string the second integer */
      var id_lang = elem.attr('name').match(/\d+/g)[1];
      $('#form_step5_link_rewrite_' + id_lang).val(str2url(elem.val(), 'UTF-8'));
  };

  return {
    'init': function() {

      hideShowRedirectToProduct();
      updateRemoteUrl();

      /** On redirect type select change */
      redirectTypeElem.change(function() {
        productRedirect.find('#form_step5_id_type_redirected-data').html('');
        hideShowRedirectToProduct();
      });

      /** On product title change, update friendly URL*/
      $('#form_step1_names.friendly-url-force-update input').keyup(function() {
        updateFriendlyUrl($(this));
      });

      /** Reset all languages title to friendly url*/
      $('#seo-url-regenerate').click(function() {
        $.each($('#form_step1_names input'), function() {
          updateFriendlyUrl($(this));
        });
      });
    },
    'onSave': function() {
        // check all friendly URLs have been filled. If not, fill them.
        $('input[id^="form_step5_link_rewrite_"]', "#form_step5_link_rewrite").each(function(){
            var elem = $(this);
            if (0 === elem.val().length) {
                var id_lang = elem.attr('name').match(/\d+/g)[1];
                updateFriendlyUrl($('#form_step1_name_' + id_lang));
            }
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
      $('#form_step6_tags .tokenfield').tokenfield({
        minWidth: '768px'
      });
    }
  };
})();

var recommendedModules = (function() {
  return {
    'init': function() {
      this.moduleActionMenuLinkSelectors = 'button.module_action_menu_install, button.module_action_menu_enable, ' +
        'button.module_action_menu_uninstall, button.module_action_menu_disable, button.module_action_menu_reset, button.module_action_menu_update';
      $(this.moduleActionMenuLinkSelectors).on('module_card_action_event', this.saveProduct);
    },
    'saveProduct': function(event, action) {
      form.send();
    }
  };
})();
