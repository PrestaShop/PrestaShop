/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

$(document).ready(() => {
  window.form.init();
  nav.init();
  featuresCollection.init();
  displayFormCategory.init();
  formCategory.init();
  stock.init();
  supplier.init();
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
  BOEvent.emitEvent('Product Categories Management started', 'CustomEvent');
  BOEvent.emitEvent('Product Default category Management started', 'CustomEvent');
  BOEvent.emitEvent('Product Manufacturer Management started', 'CustomEvent');
  BOEvent.emitEvent('Product Related Management started', 'CustomEvent');
  BOEvent.emitEvent('Modal confirmation started', 'CustomEvent');
  BOEvent.emitEvent('Product Combinations Management started', 'CustomEvent');

  /** Type product fields display management */
  $('#form_step1_type_product').change(() => {
    displayFieldsManager.refresh();
  });

  // Validate price fields on input change
  $(".money-type input[type='text']").change(function validate() {
    const inputValue = priceCalculation.normalizePrice($(this).val());
    const parsedValue = truncateDecimals(inputValue, 6);

    $(this).val(parsedValue);
  });

  /** tooltips should be hidden when we move to another tab */
  $('#form-nav').on('click', '.nav-item', () => {
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('[data-toggle="popover"]').popover('hide');
  });

  $('.summary-description-container a[data-toggle="tab"]').on('shown.bs.tab', resetEditor);
  form.switchLanguage($('#form_switch_language').val());
});

/**
 * Reset active tinyMce editor (triggered when switch language, or switching tabs)
 */
function resetEditor() {
  const languageEditorsSelector = '.summary-description-container div.translation-field.active textarea.autoload_rte';
  $(languageEditorsSelector).each((index, textarea) => {
    if (window.tinyMCE) {
      const editor = window.tinyMCE.get(textarea.id);

      if (editor) {
        // Reset content to force refresh of editor
        editor.setContent(editor.getContent());
      }
    }
  });
}

/**
 * Manage show or hide fields
 */
window.displayFieldsManager = (function () {
  const typeProduct = $('#form_step1_type_product');
  const showVariationsSelector = $('#show_variations_selector');
  const combinationsBlock = $('#combinations');
  let managedVirtualProduct;

  return {
    init(virtualProduct) {
      managedVirtualProduct = virtualProduct;

      /** Type product fields display management */
      $('#form_step1_type_product').change(() => {
        displayFieldsManager.refresh();
      });

      $('#form .form-input-title input').on('focus', function () {
        $(this).select();
      });

      this.initVisibilityRule();

      /** Tax rule dropdown shortcut */
      $('a#tax_rule_shortcut_opener').on('click', () => {
        // lazy instantiated
        let duplicate = $('#form_step2_id_tax_rules_group_shortcut');

        if (duplicate.length === 0) {
          const origin = $('select#form_step2_id_tax_rules_group');
          duplicate = origin.clone(false).attr('id', 'form_step2_id_tax_rules_group_shortcut');
          origin.on('change', () => {
            duplicate.val(origin.val()); // no change() here to avoid infinite loop.
          });
          duplicate.on('change', () => {
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
    initVisibilityRule() {
      const showPriceSelector = '.js-show-price';
      const availableForOrderSelector = '.js-available-for-order';

      const applyVisibilityRule = function applyVisibilityRule() {
        const $availableForOrder = $(`${availableForOrderSelector} input`);
        const $showPrice = $(`${showPriceSelector} input`);
        const $showPriceColumn = $(showPriceSelector);

        if ($availableForOrder.prop('checked')) {
          $showPrice.prop('checked', true);
          $showPriceColumn.addClass('hide');
        } else {
          $showPriceColumn.removeClass('hide');
        }
      };
      $(`${availableForOrderSelector} .checkbox`).on('click', applyVisibilityRule);
      applyVisibilityRule();
    },
    refresh() {
      this.checkAccessVariations();
      $('#virtual_product').hide();
      $('#form-nav a[href="#step3"]').text(translate_javascripts.Quantities);

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
          $('#form-nav a[href="#step3"]').text(translate_javascripts.Quantities);
        }
      }

      // Switching from a product type to another which is not "Virtual product",
      // triggers the destruction of pre-existing virtual product
      const shouldDestroyVirtualProduct = typeProduct.val() !== '2';

      if (shouldDestroyVirtualProduct && managedVirtualProduct !== undefined) {
        managedVirtualProduct.destroy();
      }

      /** check quantity / combinations display */
      if (
        showVariationsSelector.find('input:checked').val() === '1'
        || $('#accordion_combinations tr:not(#loading-attribute)').length > 0
      ) {
        combinationsBlock.show();

        $('#form-nav a[href="#step3"]').text(translate_javascripts.Combinations);
        $('#product_qty_0_shortcut_div, #quantities').hide();
      } else {
        combinationsBlock.hide();
        $('#product_qty_0_shortcut_div, #quantities').show();
      }

      /** Tooltip for product type combinations */
      if ($('input[name="show_variations"][value="1"]:checked').length >= 1) {
        $('#product_type_combinations_shortcut').show();
      } else {
        $('#product_type_combinations_shortcut').hide();
      }
    },
    getProductType() {
      /* eslint-disable */
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
      /* eslint-enable */
    },
    /**
     * Product pack or virtual can't have variations
     * Warn e-merchant.
     * @param errorMessage
     */
    checkAccessVariations() {
      if (
        (showVariationsSelector.find('input:checked').val() === '1'
        || $('#accordion_combinations tr:not(#loading-attribute)').length > 0)
        && (typeProduct.val() === '1'
        || typeProduct.val() === '2')
      ) {
        const typeOfProduct = this.getProductType();
        // eslint-disable-next-line
        const errorMessage = `You can't create ${typeOfProduct} product with variations. Are you sure to disable variations ? they will all be deleted.`;
        modalConfirmation.create(translate_javascripts[errorMessage], null, {
          onCancel() {
            typeProduct.val(0).change();
            /* else the radio bouton is not display even if checked attribute is true */
            $('#show_variations_selector input[value="1"]').click();
          },
          onContinue() {
            $.ajax({
              type: 'GET',
              // eslint-disable-next-line
              url: $('#accordion_combinations').attr('data-action-delete-all').replace(/delete-all\/\d+/, `delete-all/${$('#form_id_product').val()}`),
              success() {
                $('#accordion_combinations .combination').remove();
                displayFieldsManager.refresh();
              },
              error(response) {
                showErrorMessage(jQuery.parseJSON(response.responseText).message);
              },
            });
          },
        }).show();
      }
    },
  };
}());

/**
 * Display category form management
 */
const displayFormCategory = (function () {
  const parentElem = $('#add-categories');

  return {
    init() {
      /** Click event on the add button */
      parentElem.find('a.open').on('click', function (e) {
        e.preventDefault();
        parentElem.find('#add-categories-content').removeClass('hide');
        $(this).hide();
      });
    },
  };
}());

/**
 * Form category management
 */
const formCategory = (function () {
  const elem = $('#form_step1_new_category');

  /** Send category form and it to nested categories */
  function send(form) {
    $.ajax({
      type: 'POST',
      url: elem.attr('data-action'),
      data: {
        'form[category][name]': $('#form_step1_new_category_name').val(),
        'form[category][id_parent]': $('#form_step1_new_category_id_parent').val(),
        'form[_token]': $('#form #form__token').val(),
      },
      beforeSend() {
        $('button.submit', elem).attr('disabled', 'disabled');
        $('ul.text-danger', elem).remove();
        $('*.has-danger', elem).removeClass('has-danger');
        $('*.has-danger').removeClass('has-danger');
      },
      success(response) {
        // inject new category into category tree
        let html = `<li>
          <div class="checkbox js-checkbox">
            <label>
              <input type="checkbox" name="form[step1][categories][tree][]" checked value="${response.category.id}">
              ${response.category.name[1]}
              <input type="radio" value="${response.category.id}" name="ignore" class="default-category">
            </label>
          </div>
          </li>`;

        const parentElement = $(`#form_step1_categories input[value=${response.category.id_parent}]`).parent().parent();

        if (parentElement.next('ul').length === 0) {
          html = `<ul>${html}</ul>`;
          parentElement.append(html);
        } else {
          parentElement.next('ul').append(html);
        }

        // inject new category in parent category selector
        // eslint-disable-next-line
        $('#form_step1_new_category_id_parent').append(`<option value="${response.category.id}">${response.category.name[1]}</option>`);

        // create label
        const tag = {
          name: response.category.name[1],
          id: response.category.id,
          breadcrumb: '',
        };
        productCategoriesTags.createTag(tag);

        // hide the form
        form.hideBlock();
      },
      error(response) {
        $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
          let html = '<ul class="list-unstyled text-danger">';
          $.each(errors, (errorsKey, error) => {
            html += `<li>${error}</li>`;
          });
          html += '</ul>';

          $(`#form_step1_new_${key}`).parent().append(html);
          $(`#form_step1_new_${key}`).parent().addClass('has-danger');
        });
      },
      complete() {
        $('#form_step1_new_category button.submit').removeAttr('disabled');
      },
    });
  }

  return {
    init() {
      const that = this;
      /** remove all categories from selector, except pre defined */
      $('#add-categories button.save').click(() => {
        send(that);
      });
      $('#add-categories button[type="reset"]').click(() => {
        that.hideBlock();
      });
    },
    hideBlock() {
      $('#form_step1_new_category_name').val('');
      $('#add-category-button').show();
      $('#add-categories-content').addClass('hide');
    },
  };
}());

/**
 * Feature collection management
 */
const featuresCollection = (function () {
  const collectionHolder = $('.feature-collection');
  let maxCollectionChildren = collectionHolder.children('.row').length;

  /** Add a feature */
  function add() {
    const newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, maxCollectionChildren);
    collectionHolder.append(newForm);
    maxCollectionChildren += 1;
    prestaShopUiKit.initSelects();
  }

  return {
    init() {
      /** Click event on the add button */
      $('#features .add').on('click', (e) => {
        e.preventDefault();
        add();
        $('#features-content').removeClass('hide');
      });

      /** Click event on the remove button */
      $(document).on('click', '.feature-collection .delete', function (e) {
        e.preventDefault();
        const that = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue() {
            that.closest('.product-feature').remove();
          },
        }).show();
      });

      function replaceEndingIdFromUrl(url, newId) {
        return url.replace(/\/\d+(?!.*\/\d+)((?=\?.*))?/, `/${newId}`);
      }

      /** On feature selector event change, refresh possible values list */
      $(document).on('change', '.feature-collection select.feature-selector', function (event) {
        const that = event.currentTarget;
        const $row = $($(that).parents('.row')[0]);
        const $selector = $row.find('.feature-value-selector');

        if ($(this).val() !== '') {
          $.ajax({
            url: replaceEndingIdFromUrl($(this).attr('data-action'), $(this).val()),
            success(response) {
              $selector.prop('disabled', response.length === 0);
              $selector.empty();
              $.each(response, (index, elt) => {
                // the placeholder shouldn't be posted.
                if (elt.id === '0') {
                  elt.id = '';
                }
                $selector.append($('<option></option>').attr('value', elt.id).text(elt.value));
              });
            },
          });
        }
      });

      const $featuresContainer = $('#features-content');

      $featuresContainer.on('change', '.row select, .row input[type="text"]', (event) => {
        const that = event.currentTarget;
        const $row = $($(that).parents('.row')[0]);
        const $definedValueSelector = $row.find('.feature-value-selector');
        const $customValueSelector = $row.find('input[type=text]');

        // if feature has changed we need to reset values
        if ($(that).hasClass('feature-selector')) {
          $customValueSelector.val('');
          $definedValueSelector.val('');
        }
      });
    },
  };
}());

/**
 * Suppliers management
 */
const supplier = (function () {
  const supplierInputManage = function (input) {
    // eslint-disable-next-line
    const supplierDefaultInput = $(`#form_step6_suppliers input[name="form[step6][default_supplier]"][value=${$(input).val()}]`);

    if ($(input).is(':checked')) {
      supplierDefaultInput.prop('disabled', false).show();
    } else {
      supplierDefaultInput.prop('disabled', true).hide();
    }
  };

  return {
    init() {
      /** On supplier select, hide or show the default supplier selector */
      const supplierInput = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]');
      supplierInput.change(function () {
        supplierInputManage($(this));
        supplierCombinations.refresh();
      });

      // default display
      $('#form_step6_suppliers input[name="form[step6][suppliers][]"]').each(function () {
        supplierInputManage($(this));
      });
    },
  };
}());

/**
 * Supplier combination collection management
 */
window.supplierCombinations = (function () {
  const idProduct = $('#form_id_product').val();
  const collectionHolder = $('#supplier_combination_collection');

  return {
    refresh() {
      const suppliers = $('#form_step6_suppliers input[name="form[step6][suppliers][]"]:checked').map(function () {
        return $(this).val();
      }).get();
      const url = collectionHolder.attr('data-url')
        .replace(
          /refresh-product-supplier-combination-form\/\d+\/\d+/,
          // eslint-disable-next-line
          `refresh-product-supplier-combination-form/${idProduct}${suppliers.length > 0 ? `/${suppliers.join('-')}` : ''}`,
        );
      $.ajax({
        url,
        success(response) {
          collectionHolder.empty().append(response);
        },
      });
    },
  };
}());

/**
 * Quantities management
 */
window.stock = (function () {
  return {
    init() {
      /** Update qty_0 and shortcut qty_0 field on change */
      $('#form_step1_qty_0_shortcut, #form_step3_qty_0').keyup(function () {
        if ($(this).attr('id') === 'form_step1_qty_0_shortcut') {
          $('#form_step3_qty_0').val($(this).val());
        } else {
          $('#form_step1_qty_0_shortcut').val($(this).val());
        }
      });

      /** if GSA : Show depends_on_stock choice only if advanced_stock_management checked */
      $('#form_step3_advanced_stock_management').on('change', (e) => {
        if (e.target.checked) {
          $('#depends_on_stock_div').show();
        } else {
          $('#depends_on_stock_div').hide();
        }
        warehouseCombinations.refresh();
      });

      /** if GSA activation change on 'depend on stock', update quantities fields */
      // eslint-disable-next-line
      $('#form_step3_depends_on_stock_0, #form_step3_depends_on_stock_1, #form_step3_advanced_stock_management').on('change', (e) => {
        displayFieldsManager.refresh();
        warehouseCombinations.refresh();
      });
      displayFieldsManager.refresh();
    },
  };
}());

/**
 * Navigation management
 */
window.nav = (function () {
  return {
    init() {
      /** Manage tabls hash routes */
      const {hash} = document.location;
      const formNav = $('#form-nav');
      const prefix = 'tab-';

      if (hash) {
        formNav.find(`a[href='${hash.replace(prefix, '')}']`).tab('show');
      }

      formNav.find('a').on('shown.bs.tab', (e) => {
        if (e.target.hash) {
          window.location.hash = e.target.hash.replace('#', `#${prefix}`);
        }
      });
    },
  };
}());

/**
 * Warehouse combination collection management (ASM only)
 */
window.warehouseCombinations = (function () {
  const idProduct = $('#form_id_product').val();
  const collectionHolder = $('#warehouse_combination_collection');

  return {
    init() {
      // toggle all button action
      $(document).on('click', 'div[id^="warehouse_combination_"] button.check_all_warehouse', function () {
        const checkboxes = $(this).closest('div[id^="warehouse_combination_"]')
          .find('input[type="checkbox"][id$="_activated"]');
        checkboxes.prop('checked', checkboxes.filter(':checked').length === 0);
      });
      // location disablation depending on 'stored' checkbox
      // eslint-disable-next-line
      $(document).on('change', 'div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]', function () {
        const checked = $(this).prop('checked');
        const location = $(this).closest('div.form-group')
          .find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');
        location.prop('disabled', !checked);
        if (!checked) {
          location.val('');
        }
      });
      this.locationDisabler();
    },
    locationDisabler() {
      // eslint-disable-next-line
      $('div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]', collectionHolder).each(function () {
        const checked = $(this).prop('checked');
        const location = $(this).closest('div.form-group')
          .find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');
        location.prop('disabled', !checked);
      });
    },
    refresh() {
      const show = $('input#form_step3_advanced_stock_management:checked').length > 0;

      if (show) {
        const url = collectionHolder.attr('data-url').replace(/\/\d+(?=\?.*)/, `/${idProduct}`);
        $.ajax({
          url,
          success(response) {
            collectionHolder.empty().append(response);
            collectionHolder.show();
            warehouseCombinations.locationDisabler();
          },
        });
      } else {
        collectionHolder.hide();
      }
    },
  };
}());

/**
 * Form management
 */
window.form = (function () {
  const elem = $('#form');

  function send(redirect, target, callBack) {
    // target value by default
    if (typeof (target) === 'undefined') {
      // eslint-disable-next-line
      target = false;
    }
    seo.onSave();
    updateMissingTranslatedNames();

    const data = $('input, textarea, select', elem)
      .not(':input[type=button], :input[type=submit], :input[type=reset]')
      .serialize();
    let openBlank;

    if (target === '_blank' && redirect) {
      openBlank = window.open('about:blank', target, '');
      openBlank.document.write(
        `<p style="text-align: center;">
          <img src="${document.location.origin}${baseAdminDir}/themes/default/img/spinner.gif">
         </p>`,
      );
    }

    $.ajax({
      type: 'POST',
      data,
      beforeSend() {
        $('#submit', elem).attr('disabled', 'disabled');
        $('.btn-submit', elem).attr('disabled', 'disabled');
        $('ul.text-danger').remove();
        $('*.has-danger').removeClass('has-danger');
        $('#form-nav li.has-error').removeClass('has-error');
        updateDisplayGlobalErrors(null);
      },
      success(response) {
        if (callBack) {
          callBack();
        }
        showSuccessMessage(translate_javascripts['Form update success']);
        // update the customization ids
        if (typeof response.customization_fields_ids !== 'undefined') {
          $.each(response.customization_fields_ids, (k, v) => {
            $(`#form_step6_custom_fields_${k}_id_customization_field`).val(v);
          });
        }

        $('.js-spinner').hide();

        if (!redirect) {
          return;
        }

        if (target === false) {
          window.location = redirect;

          return;
        }

        if (target !== '_blank') {
          window.open(redirect, target);

          return;
        }

        openBlank.location = redirect;
      },
      error(response) {
        showErrorMessage(translate_javascripts['Form update errors']);

        if (target === '_blank' && redirect) {
          openBlank.close();
        }

        const tabsWithErrors = [];

        $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
          tabsWithErrors.push(key);

          let html = '<ul class="list-unstyled text-danger">';
          $.each(errors, (unusedKey, error) => {
            html += `<li>${error}</li>`;
          });
          html += '</ul>';

          if (key.localeCompare('error') === 0) {
            updateDisplayGlobalErrors(html);
          } else if (key.match(/^combination_.*/)) {
            $(`#${key}`).parent().addClass('has-danger').append(html);
          } else {
            $(`#form_${key}`).parent().addClass('has-danger').append(html);
          }
        });

        /** find first tab with error, then switch to it */
        tabsWithErrors.sort();
        $.each(tabsWithErrors, (key, tabIndex) => {
          if (key === 0) {
            $(`#form-nav li a[href="#${tabIndex.split('_')[0]}"]`).tab('show');
          }

          $(`#form-nav li a[href="#${tabIndex.split('_')[0]}"]`).parent().addClass('has-error');
        });

        if ($('div[class*="translation-label-"].has-danger').length > 0) {
          const regexLabel = 'translation-label-';

          const translationLabelClass = $.grep(
            $('div[class*="translation-label-"].has-danger')
              .first()
              .attr('class')
              .split(' '),
            (v) => v.indexOf(regexLabel) === 0,
          )
            .join();

          if (translationLabelClass) {
            const selectValue = translationLabelClass.replace(regexLabel, '');

            if ($(`#form_switch_language option[value="${selectValue}"]`).length > 0) {
              $('#form_switch_language').val(selectValue).change();
            }
          }
        }

        /** scroll to 1st error */
        if ($('.has-danger').first().offset()) {
          $('html, body').animate({
            scrollTop: $('.has-danger').first().offset().top - $('nav.main-header').height(),
          }, 500);
        }
      },
      complete() {
        $('#submit', elem).removeAttr('disabled');
        $('.btn-submit', elem).removeAttr('disabled');
      },
    });
  }

  function switchLanguage(isoCode) {
    $(`div.translations.tabbable > div > div.translation-field:not(.translation-label-${isoCode})`)
      .removeClass('show active');

    $(`div.translations.tabbable > div > div.translation-field.translation-label-${isoCode}`).addClass('show active');
    resetEditor();
  }

  function updateMissingTranslatedNames() {
    const namesDiv = $('#form_step1_names');
    let defaultLanguageValue = null;
    $("input[id^='form_step1_name_']", namesDiv).each(function (index) {
      const value = $(this).val();

      // The first language is ALWAYS the employee language
      if (index === 0) {
        defaultLanguageValue = value;
      } else if (value.length === 0) {
        $(this).val(defaultLanguageValue);
      }
    });
  }

  /**
   * Depending on the provided params, this method displays or hides
   * an error panel with the form errors not linked to a specific field.
   *
   * @param {string} content The HTML content to display
   */
  function updateDisplayGlobalErrors(content) {
    const target = $('#form_bubbling_errors');
    target.html('');
    if (content) {
      target.html(`<div class="alert alert-danger">${content}</div>`);
    }
  }
  return {
    init() {
      /** prevent form submit on ENTER keypress */
      jwerty.key('enter', (e) => {
        e.preventDefault();
      });

      /** create keyboard event for save */
      jwerty.key('alt+shift+S', (e) => {
        e.preventDefault();
        send();
      });

      /** create keyboard event for save & duplicate */
      jwerty.key('alt+shift+D', (e) => {
        e.preventDefault();
        send($('.product-footer .duplicate').attr('data-redirect'));
      });

      /** create keyboard event for save & new */
      jwerty.key('alt+shift+P', (e) => {
        e.preventDefault();
        send($('.product-footer .new-product').attr('data-redirect'));
      });

      /** create keyboard event for save & go catalog */
      jwerty.key('alt+shift+Q', (e) => {
        e.preventDefault();
        send($('.product-footer .go-catalog').attr('data-redirect'));
      });

      /** create keyboard event for save & go preview */
      jwerty.key('alt+shift+V', (e) => {
        e.preventDefault();
        const productFooter = $('.product-footer .preview');
        send(productFooter.attr('data-redirect'), productFooter.attr('target'));
      });

      /** create keyboard event for save & active or desactive product */
      jwerty.key('alt+shift+O', (e) => {
        e.preventDefault();
        const step1CheckBox = $('#form_step1_active');
        step1CheckBox.prop('checked', !step1CheckBox.is(':checked'));
      });

      elem.submit((event) => {
        replaceBadLocaleCharacters();
        event.preventDefault();
        send();
      });

      elem.find('#form_switch_language').change((event) => {
        event.preventDefault();
        switchLanguage(event.target.value);
      });

      /** on save with duplicate|new|preview */
      $('.btn-submit, .preview', elem).click(function (event) {
        event.preventDefault();
        send($(this).attr('data-redirect'), $(this).attr('target'));
      });

      $('.js-btn-save').on('click', function (event) {
        replaceBadLocaleCharacters();
        event.preventDefault();
        $('.js-spinner').css('display', 'inline-block');
        send($(this).attr('href'));
      });

      /** on active field change, send form */
      $('#form_step1_active', elem).on('change', function () {
        const active = $(this).prop('checked');
        $('.for-switch.online-title').toggle(active);
        $('.for-switch.offline-title').toggle(!active);
        // update link preview
        const previewButton = $('#product_form_preview_btn');
        const urlActive = previewButton.attr('data-redirect');
        const urlDeactive = previewButton.attr('data-url-deactive');
        previewButton.attr('data-redirect', urlDeactive);
        previewButton.attr('data-url-deactive', urlActive);
        // update product
        send();
      });

      /** on delete product */
      $('.product-footer .delete', elem).click(function (e) {
        e.preventDefault();
        const that = $(this);
        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue() {
            window.location = that.attr('href');
          },
        }).show();
      });

      $('#form-loading').fadeIn(() => {
      /** Create Bloodhound engine */
        const engine = new Bloodhound({
          datumTokenizer(d) {
            return Bloodhound.tokenizers.whitespace(d.label);
          },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: {
            url: $('#form_step3_attributes').attr('data-prefetch'),
            cache: false,
          },
        });

        /** init input typeahead */
        $('#form_step3_attributes').tokenfield({
          typeahead: [{
            hint: false,
            cache: false,
          }, {
            source(query, syncResults) {
              engine.search(query, (suggestions) => {
                syncResults(filter(suggestions));
              });
            },
            display: 'label',
          }],
          minWidth: '768px',
        });

        /** Filter suggestion with selected tokens */
        const filter = function (suggestions) {
          const selected = [];
          $('#attributes-generator input.attribute-generator').each(function () {
            selected.push($(this).val());
          });

          // eslint-disable-next-line
          return $.grep(suggestions, (suggestion) => $.inArray(suggestion.value, selected) === -1 && $.inArray(`group-${suggestion.data.id_group}`, selected) === -1);
        };

        /** On event "tokenfield:createtoken" : check values are valid if its not a typehead result */
        // eslint-disable-next-line
        $('#form_step3_attributes').on('tokenfield:createtoken', (e) => {
          if (!e.attrs.data) {
            if (e.handleObj.origType !== 'tokenfield:createtoken') {
              return false;
            }

            const orgLabel = e.attrs.label;

            if (e.attrs.label === e.attrs.value) {
              engine.search(e.attrs.label, (result) => {
                if (result.length >= 1) {
                  e.attrs.label = result[0].label;
                  e.attrs.value = result[0].value;
                  e.attrs.data = [];
                  e.attrs.data.id_group = result[0].data.id_group;
                }
              });
            } else {
              const attr = $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`);

              if (attr) {
                e.attrs.label = attr.data('label');
                e.attrs.value = attr.data('value');
                e.attrs.data = [];
                e.attrs.data.id_group = attr.data('group-id');
              }
            }

            if (e.attrs.data && filter([e.attrs]).length === 0) {
              $('#form_step3_attributes-tokenfield').val((i, value) => value.replace(orgLabel, ''));
              return false;
            }
          }
        });

        /** On event "tokenfield:createdtoken" : store attributes in input when add a token */
        $('#form_step3_attributes').on('tokenfield:createdtoken', (e) => {
          if (e.attrs.data) {
            // eslint-disable-next-line
            $('#attributes-generator').append(`<input type="hidden" id="attribute-generator-${e.attrs.value}" class="attribute-generator" value="${e.attrs.value}" name="options[${e.attrs.data.id_group}][${e.attrs.value}]" />`);
          } else {
            $(e.relatedTarget).addClass('invalid');
          }
        });

        /** On event "tokenfield:removedtoken" : remove stored attributes input when remove token */
        $('#form_step3_attributes').on('tokenfield:removedtoken', (e) => {
          if (!$(e.relatedTarget).hasClass('invalid')) {
            $(`#attribute-generator-${e.attrs.value}`).remove();
          }
        });
      });
    },
    send(redirect, target, callBack) {
      send(redirect, target, callBack);
    },
    switchLanguage(isoCode) {
      switchLanguage(isoCode);
    },
  };
}());

/**
 * Custom field collection management
 */
window.customFieldCollection = (function () {
  const collectionHolder = $('ul.customFieldCollection');
  let maxCollectionChildren = collectionHolder.children().length;

  /** Add a custom field */
  function add() {
    const newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, maxCollectionChildren);
    maxCollectionChildren += 1;

    collectionHolder.append(`<li>${newForm}</li>`);
    window.prestaShopUiKit.init();
  }

  return {
    init() {
      /** Click event on the add button */
      $('#custom_fields a.add').on('click', (e) => {
        e.preventDefault();
        add();
      });

      /** Click event on the remove button */
      $(document).on('click', 'ul.customFieldCollection .delete', function (e) {
        e.preventDefault();
        const that = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue() {
            that.parent().parent().parent().remove();
          },
        }).show();
      });
    },
  };
}());

/**
 * virtual product management
 */
window.virtualProduct = (function () {
  const idProduct = $('#form_id_product').val();

  const getOnDeleteVirtualProductFileHandler = function ($deleteButton) {
    return $.ajax({
      type: 'GET',
      url: $deleteButton.attr('href').replace(/\/\d+(?=\?.*)/, `/${idProduct}`),
      success() {
        $('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
        $('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
      },
    });
  };

  return {
    init() {
      $(document).on('change', 'input[name="form[step3][virtual_product][is_virtual_file]"]', function () {
        if ($(this).val() === '1') {
          $('#virtual_product_content').show();
        } else {
          $('#virtual_product_content').hide();

          const url = $('#virtual_product').attr('data-action-remove').replace(/remove\/\d+/, `remove/${idProduct}`);
          // delete virtual product
          $.ajax({
            type: 'GET',
            url,
            success() {
              // empty form
              $('#form_step3_virtual_product_file_input').removeClass('hide').addClass('show');
              $('#form_step3_virtual_product_file_details').removeClass('show').addClass('hide');
              $('#form_step3_virtual_product_name').val('');
              $('#form_step3_virtual_product_nb_downloadable').val(0);
              $('#form_step3_virtual_product_expiration_date').val('');
              $('#form_step3_virtual_product_nb_days').val(0);
            },
          });
        }
      });

      $('#form_step3_virtual_product_file').change(function () {
        if ($(this)[0].files !== undefined) {
          const {files} = $(this)[0];
          let name = '';

          $.each(files, (index, value) => {
            name += `${value.name}, `;
          });
          $('#form_step3_virtual_product_name').val(name.slice(0, -2));
        } else {
          // Internet Explorer 9 Compatibility
          const name = $(this).val().split(/[\\/]/);
          $('#form_step3_virtual_product_name').val(name[name.length - 1]);
        }
      });

      if ($('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val() === '1') {
        $('#virtual_product_content').show();
      } else {
        $('#virtual_product_content').hide();
      }

      /** delete attached file */
      $('#form_step3_virtual_product_file_details .delete').click(function (e) {
        e.preventDefault();
        const $deleteButton = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue() {
            getOnDeleteVirtualProductFileHandler($deleteButton);
          },
        }).show();
      });

      /** save virtual product */
      $('#form_step3_virtual_product_save').click(function () {
        const that = $(this);
        const data = new FormData();

        if ($('#form_step3_virtual_product_file')[0].files[0]) {
          data.append('product_virtual[file]', $('#form_step3_virtual_product_file')[0].files[0]);
        }
        data.append('product_virtual[is_virtual_file]',
          $('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val(),
        );
        data.append('product_virtual[name]', $('#form_step3_virtual_product_name').val());
        data.append('product_virtual[nb_downloadable]', $('#form_step3_virtual_product_nb_downloadable').val());
        data.append('product_virtual[expiration_date]', $('#form_step3_virtual_product_expiration_date').val());
        data.append('product_virtual[nb_days]', $('#form_step3_virtual_product_nb_days').val());

        $.ajax({
          type: 'POST',
          url: $('#virtual_product').attr('data-action').replace(/save\/\d+/, `save/${idProduct}`),
          data,
          contentType: false,
          processData: false,
          beforeSend() {
            that.prop('disabled', 'disabled');
            $('ul.text-danger').remove();
            $('*.has-danger').removeClass('has-danger');
          },
          success(response) {
            showSuccessMessage(translate_javascripts['Form update success']);
            if (response.file_download_link) {
              $('#form_step3_virtual_product_file_details a.download').attr('href', response.file_download_link);
              $('#form_step3_virtual_product_file_input').removeClass('show').addClass('hide');
              $('#form_step3_virtual_product_file_details').removeClass('hide').addClass('show');
            }
          },
          error(response) {
            $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
              let html = '<ul class="list-unstyled text-danger">';
              $.each(errors, (errorsKey, error) => {
                html += `<li>${error}</li>`;
              });
              html += '</ul>';

              $(`#form_step3_virtual_product_${key}`).parent().append(html);
              $(`#form_step3_virtual_product_${key}`).parent().addClass('has-danger');
            });
          },
          complete() {
            that.removeAttr('disabled');
          },
        });
      });
    },
    destroy() {
      const fileDetailsSelector = '#form_step3_virtual_product_file_details';
      const fileAssociationExists = !$(fileDetailsSelector).hasClass('hide');

      if (fileAssociationExists) {
        const $deleteButton = $(`${fileDetailsSelector} .delete`);
        getOnDeleteVirtualProductFileHandler($deleteButton);
      }

      const associatedFileCheckboxSelectorPrefix = '#form_step3_virtual_product_is_virtual_file_';
      $(`${associatedFileCheckboxSelectorPrefix}0`).prop('checked', false);
      $(`${associatedFileCheckboxSelectorPrefix}1`).prop('checked', true);

      $('#virtual_product_content input').val('');
    },
  };
}());

/**
 * attachment product management
 */
window.attachmentProduct = (function () {
  const idProduct = $('#form_id_product').val();

  return {
    init() {
      const buttonSave = $('#form_step6_attachment_product_add');
      const buttonCancel = $('#form_step6_attachment_product_cancel');

      buttonCancel.click(() => {
        resetAttachmentForm();
      });

      function resetAttachmentForm() {
        $('#form_step6_attachment_product_file').val('');
        $('#form_step6_attachment_product_name').val('');
        $('#form_step6_attachment_product_description').val('');
      }

      function replaceEndingIdFromUrl(url, newId) {
        return url.replace(/\/\d+(?!.*\/\d+)((?=\?.*))?/, `/${newId}`);
      }

      /** add attachment */
      // eslint-disable-next-line
      $('#form_step6_attachment_product_add').click(function () {
        const data = new FormData();

        if ($('#form_step6_attachment_product_file')[0].files[0]) {
          data.append('product_attachment[file]', $('#form_step6_attachment_product_file')[0].files[0]);
        }
        data.append('product_attachment[name]', $('#form_step6_attachment_product_name').val());
        data.append('product_attachment[description]', $('#form_step6_attachment_product_description').val());

        $.ajax({
          type: 'POST',
          url: replaceEndingIdFromUrl($('#form_step6_attachment_product').attr('data-action'), idProduct),
          data,
          contentType: false,
          processData: false,
          beforeSend() {
            buttonSave.prop('disabled', 'disabled');
            $('ul.text-danger').remove();
            $('*.has-danger').removeClass('has-danger');
          },
          success(response) {
            resetAttachmentForm();

            // inject new attachment in attachment list
            if (response.id) {
              /* eslint-disable */
              const row = `<tr>\
                <td class="col-md-3"><input type="checkbox" name="form[step6][attachments][]" value="${response.id}" checked="checked"> ${response.real_name}</td>\
                <td class="col-md-6">${response.file_name}</td>\
                <td class="col-md-2">${response.mime}</td>\
              </tr>`;
              /* eslint-enable */

              $('#product-attachment-file tbody').append(row);
              $('.js-options-no-attachments').addClass('hide');
              $('.js-options-with-attachments').removeClass('hide');
            }
          },
          error(response) {
            $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
              let html = '<ul class="list-unstyled text-danger">';
              $.each(errors, (errorsKey, error) => {
                html += `<li>${error}</li>`;
              });
              html += '</ul>';

              $(`#form_step6_attachment_product_${key}`).parent().append(html);
              $(`#form_step6_attachment_product_${key}`).parent().addClass('has-danger');
            });
          },
          complete() {
            buttonSave.removeAttr('disabled');
          },
        });
      });
    },
  };
}());

/**
 * images product management
 */
window.imagesProduct = (function () {
  const dropZoneElem = $('#product-images-dropzone');
  const expanderElem = $('#product-images-container .dropzone-expander');

  function checkDropzoneMode() {
    if (!dropZoneElem.find('.dz-preview:not(.openfilemanager)').length) {
      dropZoneElem.removeClass('dz-started');
      dropZoneElem.find('.dz-preview.openfilemanager').hide();
    } else {
      dropZoneElem.find('.dz-preview.openfilemanager').show();
    }
  }

  return {
    toggleExpand() {
      if (expanderElem.hasClass('expand')) {
        dropZoneElem.css('height', 'auto');
        expanderElem.removeClass('expand').addClass('compress');
      } else {
        dropZoneElem.css('height', '');
        expanderElem.removeClass('compress').addClass('expand');
      }
    },
    displayExpander() {
      expanderElem.show();
    },
    hideExpander() {
      expanderElem.hide();
    },
    shouldDisplayExpander() {
      const oldHeight = dropZoneElem.css('height');

      dropZoneElem.css('height', '');
      const closedHeight = dropZoneElem.outerHeight();
      const realHeight = dropZoneElem[0].scrollHeight;
      dropZoneElem.css('height', oldHeight);

      return (realHeight > closedHeight);
    },
    updateExpander() {
      if (this.shouldDisplayExpander()) {
        this.displayExpander();
      }
    },
    initExpander() {
      if (this.shouldDisplayExpander()) {
        this.displayExpander();
        expanderElem.addClass('expand');
      }

      const self = this;
      $(document).on('click', '#product-images-container .dropzone-expander', () => {
        self.toggleExpand();
      });
    },
    init() {
      Dropzone.autoDiscover = false;
      const errorElem = $('#product-images-dropzone-error');

      // on click image, display custom form
      $(document).on('click', '#product-images-dropzone .dz-preview', function () {
        if (!$(this).attr('data-id')) {
          return;
        }
        formImagesProduct.form($(this).attr('data-id'));
      });

      const dropzoneOptions = {
        url: dropZoneElem.attr('url-upload'),
        paramName: 'form[file]',
        maxFilesize: dropZoneElem.attr('data-max-size'),
        addRemoveLinks: true,
        clickable: '.openfilemanager',
        thumbnailWidth: 250,
        thumbnailHeight: null,
        acceptedFiles: 'image/*',
        timeout: 0,
        dictRemoveFile: translate_javascripts.Delete,
        dictFileTooBig: translate_javascripts.ToLargeFile,
        dictCancelUpload: translate_javascripts.Delete,
        sending() {
          checkDropzoneMode();
          expanderElem.addClass('expand').click();
          errorElem.html('');
        },
        queuecomplete() {
          checkDropzoneMode();
          dropZoneElem.sortable('enable');
          imagesProduct.updateExpander();
        },
        processing() {
          dropZoneElem.sortable('disable');
        },
        success(file, response) {
          // manage error on uploaded file
          if (response.error !== 0) {
            errorElem.append($('<p></p>').text(`${file.name}: ${response.error}`));
            this.removeFile(file);
            return;
          }

          // define id image to file preview
          $(file.previewElement).attr('data-id', response.id);
          $(file.previewElement).attr('url-update', response.url_update);
          $(file.previewElement).attr('url-delete', response.url_delete);
          $(file.previewElement).addClass('ui-sortable-handle');
          if (response.cover === 1) {
            imagesProduct.updateDisplayCover(response.id);
          }
        },
        error(file, response) {
          let message = '';

          if ($.type(response) === 'undefined') {
            return;
          } if ($.type(response) === 'string') {
            message = response;
          } else if (response.message) {
            // eslint-disable-next-line
            message = response.message;
          }

          if (message === '') {
            return;
          }

          // append new error
          errorElem.append($('<p></p>').text(`${file.name}: ${message}`));

          // remove uploaded item
          this.removeFile(file);
        },
        init() {
          // if already images uploaded, mask drop file message
          if (dropZoneElem.find('.dz-preview:not(.openfilemanager)').length) {
            dropZoneElem.addClass('dz-started');
          } else {
            dropZoneElem.find('.dz-preview.openfilemanager').hide();
          }

          // init sortable
          dropZoneElem.sortable({
            items: 'div.dz-preview:not(.disabled)',
            opacity: 0.9,
            containment: 'parent',
            distance: 32,
            tolerance: 'pointer',
            cursorAt: {
              left: 64,
              top: 64,
            },
            cancel: '.disabled',
            stop() {
              let sort = {};
              $.each(dropZoneElem.find('.dz-preview:not(.disabled)'), (index, value) => {
                if (!$(value).attr('data-id')) {
                  sort = false;
                  return;
                }
                sort[$(value).attr('data-id')] = index + 1;
              });

              // if sortable ok, update it
              if (sort) {
                $.ajax({
                  type: 'POST',
                  url: dropZoneElem.attr('url-position'),
                  data: {
                    json: JSON.stringify(sort),
                  },
                });
              }
            },
            start(event, ui) {
              // init zindex
              dropZoneElem.find('.dz-preview').css('zIndex', 1);
              ui.item.css('zIndex', 10);
            },
          });

          dropZoneElem.disableSelection();
          imagesProduct.initExpander();
        },
      };

      dropZoneElem.dropzone(jQuery.extend(dropzoneOptions));
    },
    updateDisplayCover(idImage) {
      $('#product-images-dropzone .dz-preview .iscover').remove();
      $(`#product-images-dropzone .dz-preview[data-id="${idImage}"]`)
        .append(`<div class="iscover">${translate_javascripts.Cover}</div>`);
    },
    checkDropzoneMode() {
      checkDropzoneMode();
    },
    getOlderImageId() {
      // eslint-disable-next-line
      return Math.min.apply(Math, $('.dz-preview').map(function () {
        return $(this).data('id');
      }));
    },
  };
}());

window.formImagesProduct = (function () {
  const dropZoneElem = $('#product-images-dropzone');
  const formZoneElem = $('#product-images-form-container');

  // default state
  formZoneElem.hide();

  formZoneElem.magnificPopup({
    delegate: 'a.open-image',
    type: 'image',
  });

  function toggleColDropzone(enlarge) {
    const smallCol = 'col-md-8';
    const largeCol = 'col-md-12';

    if (enlarge === true) {
      dropZoneElem.removeClass(smallCol).addClass(largeCol);
    } else {
      dropZoneElem.removeClass(largeCol).addClass(smallCol);
    }
  }

  return {
    form(id) {
      dropZoneElem.find('.dz-preview.active').removeClass('active');
      dropZoneElem.find(`.dz-preview[data-id='${id}']`).addClass('active');
      if (!imagesProduct.shouldDisplayExpander()) {
        dropZoneElem.css('height', 'auto');
      }
      $.ajax({
        url: dropZoneElem.find(`.dz-preview[data-id='${id}']`).attr('url-update'),
        success(response) {
          formZoneElem.find('#product-images-form').html(response);
          form.switchLanguage($('#form_switch_language').val());
        },
        complete() {
          toggleColDropzone(false);
          formZoneElem.show();
          dropZoneElem.addClass('d-none d-md-block');
        },
      });
    },
    send(id) {
      $.ajax({
        type: 'POST',
        url: dropZoneElem.find(`.dz-preview[data-id='${id}']`).attr('url-update'),
        data: formZoneElem.find('textarea, input').serialize(),
        beforeSend() {
          formZoneElem.find('.actions button').prop('disabled', 'disabled');
          formZoneElem.find('ul.text-danger').remove();
          formZoneElem.find('*.has-danger').removeClass('has-danger');
        },
        success() {
          if (formZoneElem.find('#form_image_cover:checked').length) {
            imagesProduct.updateDisplayCover(id);
          }
        },
        error(response) {
          if (response && response.responseText) {
            $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
              let html = '<ul class="list-unstyled text-danger">';
              $.each(errors, (errorsKey, error) => {
                html += `<li>${error}</li>`;
              });
              html += '</ul>';

              $(`#form_image_${key}`).parent().append(html);
              $(`#form_image_${key}`).parent().addClass('has-danger');
            });
          }
        },
        complete() {
          formZoneElem.find('.actions button').removeAttr('disabled');
        },
      });
    },
    delete(id) {
      modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
        onContinue() {
          $.ajax({
            url: dropZoneElem.find(`.dz-preview[data-id="${id}"]`).attr('url-delete'),
            complete() {
              formZoneElem.find('.close').click();
              const wasCover = !!dropZoneElem.find(`.dz-preview[data-id="${id}"] .iscover`).length;
              dropZoneElem.find(`.dz-preview[data-id="${id}"]`).remove();
              $(`.images .product-combination-image [value=${id}]`).parent().remove();
              imagesProduct.checkDropzoneMode();
              if (wasCover === true) {
                // The controller will choose the oldest image as the new cover.
                imagesProduct.updateDisplayCover(imagesProduct.getOlderImageId());
              }
            },
          });
        },
      }).show();
    },
    close() {
      toggleColDropzone(true);
      dropZoneElem.removeClass('d-none d-md-block');
      dropZoneElem.css('height', '');
      formZoneElem.find('#product-images-form').html('');
      formZoneElem.hide();
      dropZoneElem.find('.dz-preview.active').removeClass('active');
    },
  };
}());

/**
 * Price calculation
 */
window.priceCalculation = (function () {
  const priceHTElem = $('#form_step2_price');
  const priceHTShortcutElem = $('#form_step1_price_shortcut');
  const priceTTCElem = $('#form_step2_price_ttc');
  const priceTTCShorcutElem = $('#form_step1_price_ttc_shortcut');
  const ecoTaxElem = $('#form_step2_ecotax');
  const taxElem = $('#form_step2_id_tax_rules_group');
  const reTaxElem = $('#step2_id_tax_rules_group_rendered');
  const displayPricePrecision = priceHTElem.attr('data-display-price-precision');
  const ecoTaxRate = ecoTaxElem.attr('data-eco-tax-rate');

  /**
   * Add taxes to a price
   * @param {Number} price - Price without tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod The computation calculate method
   */
  function addTaxes(price, rates, computationMethod) {
    let priceWithTaxes = price;

    let i = 0;

    if (computationMethod === '0') {
      // eslint-disable-next-line
      for (i in rates) {
        priceWithTaxes *= (1.00 + parseFloat(rates[i]) / 100.00);
        break;
      }
    } else if (computationMethod === '1') {
      let rate = 0;

      // eslint-disable-next-line
      for (i in rates) {
        rate += rates[i];
      }
      priceWithTaxes *= (1.00 + parseFloat(rate) / 100.00);
    } else if (computationMethod === '2') {
      // eslint-disable-next-line
      for (i in rates) {
        priceWithTaxes *= (1.00 + parseFloat(rates[i]) / 100.00);
      }
    }

    return priceWithTaxes;
  }

  /**
   * Remove taxes from a price
   * @param {Number} price - Price with tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod - The computation method
   */
  function removeTaxes(price, rates, computationMethod) {
    let i = 0;

    /* eslint-disable */
    if (computationMethod === '0') {
      for (i in rates) {
        price /= (1 + rates[i] / 100);
        break;
      }
    } else if (computationMethod === '1') {
      let rate = 0;

      for (i in rates) {
        rate += rates[i];
      }
      price /= (1 + rate / 100);
    } else if (computationMethod === '2') {
      for (i in rates) {
        price /= (1 + rates[i] / 100);
      }
    }
    /* eslint-enable */

    return price;
  }

  /**
   *
   * @return {Number}
   */
  function getEcotaxTaxIncluded() {
    const displayPrecision = 6;
    let ecoTax = Tools.parseFloatFromString(ecoTaxElem.val());

    if (isNaN(ecoTax)) {
      ecoTax = 0;
    }

    if (ecoTax === 0) {
      return ecoTax;
    }
    const ecotaxTaxExcl = ecoTax / (1 + ecoTaxRate);

    return ps_round(ecotaxTaxExcl * (1 + ecoTaxRate), displayPrecision);
  }

  return {

    init() {
      /** on update tax recalculate tax include price */
      taxElem.change(() => {
        if (reTaxElem.val() !== taxElem.val()) {
          reTaxElem.val(taxElem.val()).trigger('change');
        }

        priceCalculation.taxInclude();
        priceTTCElem.change();
      });

      reTaxElem.change(() => {
        taxElem.val(reTaxElem.val()).trigger('change');
      });

      /** update without tax price and shortcut price field on change */
      $('#form_step1_price_shortcut, #form_step2_price').keyup(function () {
        const price = priceCalculation.normalizePrice($(this).val());

        if ($(this).attr('id') === 'form_step1_price_shortcut') {
          $('#form_step2_price').val(price).change();
        } else {
          $('#form_step1_price_shortcut').val(price).change();
        }

        priceCalculation.taxInclude();
      });

      /** update HT price and shortcut price field on change */
      $('#form_step1_price_ttc_shortcut, #form_step2_price_ttc').keyup(function () {
        const price = priceCalculation.normalizePrice($(this).val());

        if ($(this).attr('id') === 'form_step1_price_ttc_shortcut') {
          $('#form_step2_price_ttc').val(price).change();
        } else {
          $('#form_step1_price_ttc_shortcut').val(price).change();
        }

        priceCalculation.taxExclude();
      });

      /** on price change, update final retails prices */
      $('#form_step2_price, #form_step2_price_ttc').change(() => {
        const taxExcludedPrice = priceCalculation.normalizePrice($('#form_step2_price').val());
        const taxIncludedPrice = priceCalculation.normalizePrice($('#form_step2_price_ttc').val());

        formatCurrencyCldr(taxExcludedPrice, (result) => {
          $('#final_retail_price_te').text(result);
        });
        formatCurrencyCldr(taxIncludedPrice, (result) => {
          $('#final_retail_price_ti').text(result);
        });
      });

      /** update HT price and shortcut price field on change */
      $('#form_step2_ecotax').keyup(() => {
        priceCalculation.taxExclude();
      });

      /** combinations : update TTC price field on change */
      $(document).on('keyup', '.combination-form .attribute_priceTE', function () {
        priceCalculation.impactTaxInclude($(this));
        priceCalculation.impactFinalPrice($(this));
      });
      /** combinations : update HT price field on change */
      $(document).on('keyup', '.combination-form .attribute_priceTI', function () {
        priceCalculation.impactTaxExclude($(this));
      });
      /** combinations : update wholesale price, unity and price TE field on blur */
      // eslint-disable-next-line
      $(document).on('blur', '.combination-form .attribute_wholesale_price,.combination-form .attribute_unity,.combination-form .attribute_priceTE', function () {
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
    normalizePrice(price) {
      return Tools.parseFloatFromString(price, true);
    },

    /**
     * Adds taxes to a price
     * @param {Number} price Price without taxes
     * @return {Number} Price with added taxes
     */
    addCurrentTax(price) {
      const rates = this.getRates();
      const computationMethod = taxElem.find('option:selected').attr('data-computation-method');
      const priceWithTaxes = Number(ps_round(addTaxes(price, rates, computationMethod), displayPricePrecision));
      const ecotaxIncluded = Number(getEcotaxTaxIncluded());

      return priceWithTaxes + ecotaxIncluded;
    },

    /**
     * Calculates the price with taxes and updates the elements containing it
     */
    taxInclude() {
      const newPrice = truncateDecimals(this.addCurrentTax(this.normalizePrice(priceHTElem.val())), 6);

      priceTTCElem.val(newPrice).change();
      priceTTCShorcutElem.val(newPrice).change();
    },

    /**
     * Removes taxes from a price
     * @param {Number} price Price with taxes
     * @return {Number} Price without taxes
     */
    removeCurrentTax(price) {
      const rates = this.getRates();
      const computationMethod = taxElem.find('option:selected').attr('data-computation-method');

      return ps_round(
        removeTaxes(
          ps_round(price - getEcotaxTaxIncluded(),
            displayPricePrecision,
          ),
          rates, computationMethod),
        displayPricePrecision,
      );
    },

    /**
     * Calculates the price without taxes and updates the elements containing it
     */
    taxExclude() {
      const newPrice = truncateDecimals(this.removeCurrentTax(this.normalizePrice(priceTTCElem.val())), 6);

      priceHTElem.val(newPrice).change();
      priceHTShortcutElem.val(newPrice).change();
    },

    /**
     * Calculates and displays the impact on price (including tax) for a combination
     * @param {jQuery} obj
     */
    impactTaxInclude(obj) {
      const price = Tools.parseFloatFromString(obj.val());
      const targetInput = obj.closest('div[id^="combination_form_"]').find('input.attribute_priceTI');
      let newPrice = 0;

      if (!isNaN(price)) {
        const rates = this.getRates();
        const computationMethod = taxElem.find('option:selected').attr('data-computation-method');
        newPrice = ps_round(addTaxes(price, rates, computationMethod), 6);
        newPrice = truncateDecimals(newPrice, 6);
      }

      targetInput
        .val(newPrice)
        .trigger('change');
    },

    /**
     * Calculates and displays the final price for a combination
     * @param {jQuery} obj
     */
    impactFinalPrice(obj) {
      const price = this.normalizePrice(obj.val());
      const finalPrice = obj.closest('div[id^="combination_form_"]').find('.final-price');
      const defaultFinalPrice = finalPrice.attr('data-price');
      let priceToBeChanged = Number(price) + Number(defaultFinalPrice);
      priceToBeChanged = truncateDecimals(priceToBeChanged, 6);

      finalPrice.html(priceToBeChanged);
    },

    /**
     * Calculates and displays the impact on price (excluding tax) for a combination
     * @param {jQuery} obj
     */
    impactTaxExclude(obj) {
      const price = Tools.parseFloatFromString(obj.val());
      const targetInput = obj.closest('div[id^="combination_form_"]').find('input.attribute_priceTE');
      let newPrice = 0;

      if (!isNaN(price)) {
        const rates = this.getRates();
        const computationMethod = taxElem.find('option:selected').attr('data-computation-method');
        newPrice = removeTaxes(ps_round(price, displayPricePrecision), rates, computationMethod);
        newPrice = truncateDecimals(newPrice, 6);
      }

      targetInput
        .val(newPrice)
        .trigger('change');
    },

    /**
     * Returns the tax rates that apply
     * @return {Number[]}
     */
    getRates() {
      return taxElem
        .find('option:selected')
        .attr('data-rates')
        .split(',')
        .map((rate) => Tools.parseFloatFromString(rate, true));
    },
  };
}());

/**
 * Manage seo
 */
window.seo = (function () {
  const redirectTypeElem = $('#form_step5_redirect_type');
  const productRedirect = $('#id-product-redirected');

  /** Hide or show the input product selector */
  function hideShowRedirectToProduct() {
    if (redirectTypeElem.val() === '404') {
      $('#id-product-redirected').hide();
    } else {
      updateRemoteUrl();
      $('#id-product-redirected').show();
    }
  }

  function updateRemoteUrl() {
    switch (redirectTypeElem.val()) {
      case '301-category':
      case '302-category':
        productRedirect.find('label').html(redirectTypeElem.attr('data-labelcategory'));
        productRedirect.find('input').attr('placeholder', redirectTypeElem.attr('data-placeholdercategory'));
        productRedirect.find('.typeahead-hint').text(redirectTypeElem.attr('data-hintcategory'));
        break;
      default:
        productRedirect.find('label').html(redirectTypeElem.attr('data-labelproduct'));
        productRedirect.find('input').attr('placeholder', redirectTypeElem.attr('data-placeholderproduct'));
        productRedirect.find('.typeahead-hint').text('');
    }

    productRedirect.find('.autocomplete-search').attr(
      'data-remoteurl',
      redirectTypeElem.find('option:selected').data('remoteurl'),
    );
    productRedirect.find('.autocomplete-search').trigger('buildTypeahead');
  }

  /** Update friendly URL */
  const updateFriendlyUrl = function (elem) {
    /** Attr name equals "form[step1][name][1]".
       * We need in this string the second integer */
    const idLang = elem.attr('name').match(/\d+/g)[1];
    $(`#form_step5_link_rewrite_${idLang}`).val(str2url(elem.val(), 'UTF-8'));
  };

  return {
    init() {
      hideShowRedirectToProduct();
      updateRemoteUrl();

      /** On redirect type select change */
      redirectTypeElem.change(() => {
        productRedirect.find('#form_step5_id_type_redirected-data').html('');
        hideShowRedirectToProduct();
      });

      /** On product title change, update friendly URL */
      $('#form_step1_names.friendly-url-force-update input').keyup(function () {
        updateFriendlyUrl($(this));
      });

      /** Reset all languages title to friendly url */
      $('#seo-url-regenerate').click(() => {
        $.each($('#form_step1_names input'), function () {
          updateFriendlyUrl($(this));
        });
      });
    },
    onSave() {
      // check all friendly URLs have been filled. If not, fill them.
      $('input[id^="form_step5_link_rewrite_"]', '#form_step5_link_rewrite').each(function () {
        const elem = $(this);

        if (elem.val().length === 0) {
          const idLang = elem.attr('name').match(/\d+/g)[1];
          updateFriendlyUrl($(`#form_step1_name_${idLang}`));
        }
      });
    },
  };
}());

/**
 * Tags management
 */
window.tags = (function () {
  return {
    init() {
      $('#form_step6_tags .tokenfield').tokenfield({
        minWidth: '768px',
      });
    },
  };
}());

window.recommendedModules = (function () {
  return {
    init() {
      this.moduleActionMenuLinkSelectors = 'button.module_action_menu_install, button.module_action_menu_enable, '
        // eslint-disable-next-line
        + 'button.module_action_menu_uninstall, button.module_action_menu_disable, button.module_action_menu_reset, button.module_action_menu_update';
      $(this.moduleActionMenuLinkSelectors).on('module_card_action_event', this.saveProduct);
    },
    saveProduct() {
      form.send();
    },
  };
}());
