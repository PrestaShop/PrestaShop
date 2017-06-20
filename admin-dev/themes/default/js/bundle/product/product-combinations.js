/**
 * Combination management
 */
var combinations = (function() {
  var id_product = $('#form_id_product').val();

  /**
   * Remove a combination
   * @param {object} elem - The clicked link
   */
  function remove(elem) {
    var combinationElem = $('#attribute_' + elem.attr('data'));

    modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
      onContinue: function() {

        var attributeId = elem.attr('data');
        $.ajax({
          type: 'DELETE',
          data: {'attribute-ids': [attributeId]},
          url: elem.attr('href'),
          beforeSend: function() {
            elem.attr('disabled', 'disabled');
          },
          success: function(response) {
            combinationElem.remove();
            showSuccessMessage(response.message);
            displayFieldsManager.refresh();
          },
          error: function(response) {
            showErrorMessage(jQuery.parseJSON(response.responseText).message);
          },
          complete: function() {
            elem.removeAttr('disabled');
            supplierCombinations.refresh();
            warehouseCombinations.refresh();
            if ($('.js-combinations-list .combination').length <= 0) {
              $('#combinations_thead').fadeOut();
            }
          }
        });
      }
    }).show();
  }

  /**
   * Update final price, regarding the impact on price in combinations table
   * @param {object} elem - The tableau row parent
   */
  function updateFinalPrice(tableRow) {
      if (!tableRow.is('tr')) {
          throw new Error('Structure of table has changed, this function need to be updated.');
      }
      var priceImpactInput = tableRow.find('.attribute_priceTE');
      var impactOnPrice = priceImpactInput.val() - priceImpactInput.attr('value');
      var actualFinalPriceInput = tableRow.find('.attribute-finalprice span');
      var actualFinalPrice = actualFinalPriceInput.data('price');

      var finalPrice = new Number(actualFinalPrice) + new Number(impactOnPrice);
      actualFinalPriceInput.html(ps_round(finalPrice, 6));
  }

  return {
    'init': function() {
      var showVariationsSelector = $('#show_variations_selector input');
      var productTypeSelector = $('#form_step1_type_product');
      var combinationsListSelector = '#accordion_combinations .combination';
      var combinationsList = $(combinationsListSelector);

      if (combinationsList.length > 0) {
        productTypeSelector.prop('disabled', true);
      }

      /** delete combination */
      $(document).on('click', '#accordion_combinations .delete', function(e) {
        e.preventDefault();
        remove($(this));
      });

      /** on change quantity, update field quantity row */
      $(document).on('keyup', 'input[id^="combination"][id$="_attribute_quantity"]', function() {
        var id_attribute = $(this).closest('.combination-form').attr('data');
        $('#accordion_combinations #attribute_' + id_attribute).find('.attribute-quantity input').val($(this).val());
      });

      /** on change shortcut quantity, update form field quantity */
      $(document).on('keyup', '.attribute-quantity input', function() {
        var id_attribute = $(this).closest('.combination').attr('data');
        $('#combination_form_' + id_attribute).find('input[id^="combination"][id$="_attribute_quantity"]').val($(this).val());
      });

      /** on change shortcut impact on price, update form field impact on price */
      $(document).on('keyup', 'input[id^="combination"][id$="_attribute_price"]', function () {
        var id_attribute = $(this).closest('.combination-form').attr('data');
        var input = $('#accordion_combinations #attribute_' + id_attribute).find('.attribute-price input');

        input.val($(this).val());

        /* force the update of final price */
        updateFinalPrice($(input.parents('tr')[0]));
      });

      /** on change default attribute, update which combination is the new default */
      $(document).on('click', 'input.attribute-default', function() {
        var selectedCombination = $(this);
        var combinationRadioButtons = $('input.attribute-default');
        var id_attribute = $(this).closest('.combination').attr('data');

        combinationRadioButtons.each(function unselect(index) {
          var combination = $(this);
          if(combination.data('id') !== selectedCombination.data('id')) {
            combination.prop("checked", false);
          }
        });


        $('.attribute_default_checkbox').removeAttr('checked');
        $('#combination_form_' + id_attribute).find('input[id^="combination"][id$="_attribute_default"]').prop("checked", true);
      });


      /** on change price on impact, update price on impact form field */
      $(document).on('change', '.attribute-price input', function() {
        var id_attribute = $(this).closest('.combination').attr('data');
        $('#combination_form_' + id_attribute).find('input[id^="combination"][id$="_attribute_price"]').val($(this).val());
        updateFinalPrice($(this).parent().parent().parent());
      });

      /** on change price, update price row */
      $(document).on('keyup', 'input[id^="combination"][id$="_attribute_price"]', function() {
        var id_attribute = $(this).closest('.combination-form').attr('data');
        var attributePrice = $('#accordion_combinations #attribute_' + id_attribute).find('.attribute-price-display');
        formatCurrencyCldr(parseFloat($(this).val()), function(result) {
          attributePrice.html(result);
        });
      });

      /** Combinations fields display management */
      showVariationsSelector.change(function() {
        displayFieldsManager.refresh();
        combinationsList = $(combinationsListSelector);

        if ($(this).val() === '0') {
          //if combination(s) exists, alert user for deleting it
          if (combinationsList.length > 0) {
            modalConfirmation.create(translate_javascripts['Are you sure to disable variations ? they will all be deleted'], null, {
              onCancel: function() {
                $('#show_variations_selector input[value="1"]').prop('checked', true);
                displayFieldsManager.refresh();
              },
              onContinue: function() {
                $.ajax({
                  type: 'GET',
                  url: $('#accordion_combinations').attr('data-action-delete-all').replace(/\/\d+(?=\?.*)/, '/' + $('#form_id_product').val()),
                  success: function(response) {
                    combinationsList.remove();
                    displayFieldsManager.refresh();
                  },
                  error: function(response) {
                    showErrorMessage(jQuery.parseJSON(response.responseText).message);
                  },
                });
                // enable the top header selector
                // we want to use a "Simple product" without any combinations
                productTypeSelector.prop('disabled', false);
              }
            }).show();
          } else {
            // enable the top header selector if no combination(s) exists
            productTypeSelector.prop('disabled', false);
          }
        }else {
          // this means we have or we want to have combinations
          // disable the product type selector
          productTypeSelector.prop('disabled', true);
        }
      });

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
        if (prevCombinationId) {
          navElem.find('.prev').attr('data', prevCombinationId).show();
        }
        if (nextCombinationId) {
          navElem.find('.next').attr('data', nextCombinationId).show();
        }

        /** init combination tax include price */
        priceCalculation.impactTaxInclude(contentElem.find('.attribute_priceTE'));

        contentElem.insertBefore('#form-nav').removeClass('hide').show();

        contentElem.find('.datepicker').datetimepicker({
          locale: iso_user,
          format: 'YYYY-MM-DD'
        });

        function countSelectedProducts() {
          return $('#combination_form_' + contentElem.attr('data') + ' .img-highlight').length;
        }

        var number = $('#combination_form_' + contentElem.attr('data') + ' .number-of-images'),
            allProductCombination = $('#combination_form_' + contentElem.attr('data') + ' .product-combination-image').length;

        number.text(countSelectedProducts() + '/' + allProductCombination);

        $(document).on('click','.tabs .product-combination-image', function () {
          number.text(countSelectedProducts() + '/' + allProductCombination);
        });

        $('#form-nav, #form_content').hide();
      });

      /** close combination form */
      $(document).on('click', '#form .combination-form .btn-back', function(e) {
        e.preventDefault();
        $(this).closest('.combination-form').hide();
        $('#form-nav, #form_content').show();
      });

      /** switch combination form */
      $(document).on('click', '#form .combination-form .nav a', function(e) {
        e.preventDefault();
        $('.combination-form').hide();
        $('#accordion_combinations .combination[data="' + $(this).attr('data') + '"] .btn-open').click();
      });
    }
  };
})();

BOEvent.on("Product Combinations Management started", function initCombinationsManagement() {
  combinations.init();
}, "Back office");
