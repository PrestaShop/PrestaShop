/**
 * Function for removing bad characters from localization formating.
 */
function replaceBadLocaleCharacters() {
  // eslint-disable-next-line
  $.each($('input.attribute_wholesale_price, input.attribute_priceTE, input.attribute_priceTI, input.attribute_unity, input.attribute_weight'), function () {
    $(this).val($(this).val().replace('−', '-')); // replace U+002D with U+2212
  });
}
/**
 * Combination management
 */
window.combinations = (function () {
  /**
   * Remove a combination
   * @param {object} elem - The clicked link
   */
  function remove(elem) {
    const combinationElem = $(`#attribute_${elem.attr('data')}`);

    // eslint-disable-next-line
    window.modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
      onContinue() {
        const attributeId = elem.attr('data');
        $.ajax({
          type: 'DELETE',
          data: {'attribute-ids': [attributeId]},
          url: elem.attr('href'),
          beforeSend() {
            elem.attr('disabled', 'disabled');
            $('#create-combinations, #apply-on-combinations, #submit, .btn-submit').attr('disabled', 'disabled');
          },
          success(response) {
            refreshTotalCombinations(-1, 1);
            combinationElem.remove();
            showSuccessMessage(response.message);
            displayFieldsManager.refresh();
          },
          error(response) {
            showErrorMessage(jQuery.parseJSON(response.responseText).message);
          },
          complete() {
            elem.removeAttr('disabled');
            $('#create-combinations, #apply-on-combinations, #submit, .btn-submit').removeAttr('disabled');
            supplierCombinations.refresh();
            warehouseCombinations.refresh();
            if ($('.js-combinations-list .combination').length <= 0) {
              $('#combinations_thead').fadeOut();
            }
          },
        });
      },
    }).show();
  }

  /**
   * Update final price, regarding the impact on price in combinations table
   * @param {jQuery} tableRow - Table row that contains the combination
   */
  function updateFinalPrice(tableRow) {
    if (!tableRow.is('tr')) {
      throw new Error('Structure of table has changed, this function needs to be updated.');
    }
    const priceImpactInput = tableRow.find('.attribute_priceTE').first();
    const finalPriceLabel = tableRow.find('.attribute-finalprice span');

    const impactOnPrice = Tools.parseFloatFromString(priceImpactInput.val());
    const previousImpactOnPrice = Tools.parseFloatFromString(priceImpactInput.attr('value'));

    const currentFinalPrice = Tools.parseFloatFromString(finalPriceLabel.data('price'), true);
    const finalPrice = currentFinalPrice - previousImpactOnPrice + impactOnPrice;

    finalPriceLabel.html(Number(ps_round(finalPrice, 6)).toFixed(6));
  }

  /**
   * Returns a reference to the form for a specific combination
   * @param {String} attributeId
   * @return {jQuery}
   */
  function getCombinationForm(attributeId) {
    return $(`#combination_form_${attributeId}`);
  }

  /**
   * Returns a reference to the row of a specific combination
   * @param {String} attributeId
   * @return {jQuery}
   */
  function getCombinationRow(attributeId) {
    return $(`#accordion_combinations #attribute_${attributeId}`);
  }

  return {
    init() {
      const showVariationsSelector = '#show_variations_selector input';
      const productTypeSelector = $('#form_step1_type_product');
      const combinationsListSelector = '#accordion_combinations .combination';
      let combinationsList = $(combinationsListSelector);

      if (combinationsList.length > 0) {
        productTypeSelector.prop('disabled', true);
      }

      $(document)
        // delete combination
        .on('click', '#accordion_combinations .delete', function (e) {
          e.preventDefault();
          remove($(this));
        })

        // when typing a new quantity on the form, update it on the row
        .on('keyup', 'input[id^="combination"][id$="_attribute_quantity"]', function () {
          const attributeId = $(this).closest('.combination-form').attr('data');
          const input = getCombinationRow(attributeId).find('.attribute-quantity input');

          input.val($(this).val());
        })

        // when typing a new quantity on the row, update it on the form
        .on('keyup', '.attribute-quantity input', function () {
          const attributeId = $(this).closest('.combination').attr('data');
          const input = getCombinationForm(attributeId).find('input[id^="combination"][id$="_attribute_quantity"]');

          input.val($(this).val());
        })

        .on({
          // when typing a new impact on price on the form, update it on the row
          keyup() {
            const attributeId = $(this).closest('.combination-form').attr('data');
            const input = getCombinationRow(attributeId).find('.attribute-price input');

            input.val($(this).val());
          },
          // when impact on price on the form is changed, update final price
          change() {
            const attributeId = $(this).closest('.combination-form').attr('data');
            const input = getCombinationRow(attributeId).find('.attribute-price input');

            input.val($(this).val());

            updateFinalPrice($(input.parents('tr')[0]));
          },
        }, 'input[id^="combination"][id$="_attribute_price"]')

        // when price impact is changed on the row, update it on the form
        .on('change', '.attribute-price input', function () {
          const attributeId = $(this).closest('.combination').attr('data');
          const input = getCombinationForm(attributeId).find('input[id^="combination"][id$="_attribute_price"]');

          input.val($(this).val());

          updateFinalPrice($(this).parent().parent().parent());
        })

        // on change default attribute, update which combination is the new default
        .on('click', 'input.attribute-default', function () {
          const selectedCombination = $(this);
          const combinationRadioButtons = $('input.attribute-default');
          const attributeId = $(this).closest('.combination').attr('data');

          combinationRadioButtons.each(function unselect() {
            const combination = $(this);

            if (combination.data('id') !== selectedCombination.data('id')) {
              combination.prop('checked', false);
            }
          });

          $('.attribute_default_checkbox').prop('checked', false);
          getCombinationForm(attributeId)
            .find('input[id^="combination"][id$="_attribute_default"]')
            .prop('checked', true);
        })

        // Combinations fields display management
        .on('change', showVariationsSelector, function () {
          displayFieldsManager.refresh();
          combinationsList = $(combinationsListSelector);

          if ($(this).val() === '0') {
            // if combination(s) exists, alert user for deleting it
            if (combinationsList.length > 0) {
              window.modalConfirmation.create(
                translate_javascripts['Are you sure to disable variations ? they will all be deleted'], null, {
                  onCancel() {
                    $('#show_variations_selector input[value="1"]').prop('checked', true);
                    displayFieldsManager.refresh();
                  },
                  onContinue() {
                    $.ajax({
                      type: 'GET',
                      // eslint-disable-next-line
                      url: $('#accordion_combinations').attr('data-action-delete-all').replace(/\/\d+(?=\?.*)?/, `/${$('#form_id_product').val()}`),
                      success() {
                        combinationsList.remove();
                        displayFieldsManager.refresh();
                      },
                      error(response) {
                        showErrorMessage(jQuery.parseJSON(response.responseText).message);
                      },
                    });
                    // enable the top header selector
                    // we want to use a "Simple product" without any combinations
                    productTypeSelector.prop('disabled', false);
                  },
                }).show();
            } else {
              // enable the top header selector if no combination(s) exists
              productTypeSelector.prop('disabled', false);
            }
          } else {
            // this means we have or we want to have combinations
            // disable the product type selector
            productTypeSelector.prop('disabled', true);
          }
        })

        // open combination form
        .on('click', '#accordion_combinations .btn-open', function (e) {
          e.preventDefault();
          const contentElem = $($(this).attr('href'));

          /** create combinations navigation */
          const navElem = contentElem.find('.nav');
          const idAttribute = contentElem.attr('data');
          const prevCombinationId = $(`#accordion_combinations tr[data="${idAttribute}"]`).prev().attr('data');
          const nextCombinationId = $(`#accordion_combinations tr[data="${idAttribute}"]`).next().attr('data');
          navElem.find('.prev, .next').hide();
          if (prevCombinationId) {
            navElem.find('.prev').attr('data', prevCombinationId).show();
          }
          if (nextCombinationId) {
            navElem.find('.next').attr('data', nextCombinationId).show();
          }

          /** init combination tax include price */
          replaceBadLocaleCharacters();
          priceCalculation.impactTaxInclude(contentElem.find('.attribute_priceTE'));

          contentElem.insertBefore('#form-nav').removeClass('hide').show();

          contentElem.find('.datepicker input[type="text"]').datetimepicker({
            locale: iso_user,
            format: 'YYYY-MM-DD',
          });

          function countSelectedProducts() {
            return $(`#combination_form_${contentElem.attr('data')} .img-highlight`).length;
          }

          const number = $(`#combination_form_${contentElem.attr('data')} .number-of-images`);
          // eslint-disable-next-line
          const allProductCombination = $(`#combination_form_${contentElem.attr('data')} .product-combination-image`).length;

          number.text(`${countSelectedProducts()}/${allProductCombination}`);

          $(document).on('click', '.tabs .product-combination-image', () => {
            number.text(`${countSelectedProducts()}/${allProductCombination}`);
          });

          /** Add title on product's combination image */
          $(() => {
            $(`#combination_form_${contentElem.attr('data')}`).find('img').each(function () {
              title = $(this).attr('src').split('/').pop();
              $(this).attr('title', title);
            });
          });

          $('#form-nav, #form_content').hide();
        })

        // close combination form
        .on('click', '#form .combination-form .btn-back', function (e) {
          e.preventDefault();
          $(this).closest('.combination-form').hide();
          $('#form-nav, #form_content').show();
        })

        // switch combination form
        .on('click', '#form .combination-form .nav a', function (e) {
          e.preventDefault();
          $('.combination-form').hide();
          $(`#accordion_combinations .combination[data="${$(this).attr('data')}"] .btn-open`).click();
        });
    },
  };
}());

BOEvent.on('Product Combinations Management started', () => {
  combinations.init();
}, 'Back office');

/**
 * Refresh bulk actions combination number after creating or deleting combinations
 *
 * @param {number} sign
 * @param {number} number
 */
window.refreshTotalCombinations = function (sign, number) {
  const $bulkCombinationsTotal = $('#js-bulk-combinations-total');
  const currentnumber = parseInt($bulkCombinationsTotal.text(), 10) + (sign * number);
  $bulkCombinationsTotal.text(currentnumber);
};
