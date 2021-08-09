/**
 * Combination bulk actions management
 */
export default function () {
  const bulkForm = $('#bulk-combinations-container');
  const deleteCombinationsBtn = $('#delete-combinations');
  const applyChangesBtn = $('#apply-on-combinations');
  const syncedCollection = $('[data-uniqid]');
  const finalPrice = $('#form_step2_price');
  const finalPriceIT = $('#form_step2_price_ttc');
  const finalPriceBasics = $('#form_step1_price_shortcut');
  const finalPriceBasicsIT = $('#form_step1_price_ttc_shortcut');
  const ecotaxTI = $('#form_step2_ecotax');

  return {
    init: function init() {
      const that = this;
      // stop propagation on buttons
      deleteCombinationsBtn.on('click', (event) => {
        event.preventDefault();
        that.deleteCombinations();
      });

      applyChangesBtn.on('click', (event) => {
        event.preventDefault();
        that.applyChangesOnCombinations()
          .hideForm()
          .resetForm()
          .unselectCombinations()
          .submitUpdate();
      });

      /* if final price change with user interaction, combinations should be impacted */
      finalPrice.on('blur', () => {
        this.syncToPricingTab();
      });

      finalPriceIT.on('blur', () => {
        this.syncToPricingTab();
      });

      /* if we use final price shortcuts, also combinations should be impacted */
      finalPriceBasics.on('blur', () => {
        this.syncToPricingTab();
      });

      finalPriceBasicsIT.on('blur', () => {
        this.syncToPricingTab();
      });

      ecotaxTI.on('blur', () => {
        this.syncToPricingTab();
      });

      syncedCollection.on('DOMSubtreeModified', (event) => {
        const uniqid = event.target.getAttribute('data-uniqid');
        const newValue = event.target.innerText;

        const spans = $(`[data-uniqid="${uniqid}"]`);

        spans.each(function () {
          if ($(this).text() !== newValue) {
            $(this).text(newValue);
          }
        });
      });

      // bulk select animation
      $('#toggle-all-combinations').on('change', (event) => {
        $('#accordion_combinations td:first-child input[type="checkbox"]').each(function () {
          $(this).prop('checked', $(event.currentTarget).prop('checked'));
        });
      });

      $(document).on('change', '.js-combination', () => {
        if ($('.bulk-action').attr('aria-expanded') === 'false' || !$('.js-combination').is(':checked')) {
          $('.js-collapse').collapse('toggle');
        }
        $('span.js-bulk-combinations').text($('input.js-combination:checked').length);
      });
    },
    getSelectedCombinations: function getSelectedCombinations() {
      const combinations = [];
      const selectedCombinations = Array.from(
        $('#accordion_combinations td:first-child input[type="checkbox"]:checked'),
      );
      selectedCombinations.forEach((combination) => {
        const combinationId = combination.getAttribute('data-id');
        const combinationIndex = combination.getAttribute('data-index');
        combinations.push(new Combination(combinationId, combinationIndex));
      });

      return combinations;
    },
    applyChangesOnCombinations: function applyChangesOnCombinations() {
      const values = this.getFormValues();
      const combinations = this.getSelectedCombinations();
      combinations.forEach((combination) => {
        combination.updateForm(values);
        combination.syncValues(values);
      });

      return this;
    },
    deleteCombinations: function deleteCombinations() {
      const combinations = this.getSelectedCombinations();
      const combinationsIds = [];
      combinations.forEach((combination) => {
        combinationsIds.push(combination.domId);
      });

      window.modalConfirmation.create(
        window.translate_javascripts['Are you sure you want to delete the selected item(s)?'],
        null,
        {
          onContinue() {
            const deletionURL = $(deleteCombinationsBtn).attr('data');
            $.ajax({
              type: 'DELETE',
              data: {
                'attribute-ids': combinationsIds,
              },
              url: deletionURL,
              beforeSend() {
                $('#create-combinations, #apply-on-combinations, #submit, .btn-submit').attr('disabled', 'disabled');
              },
              success(response) {
                window.showSuccessMessage(response.message);
                window.refreshTotalCombinations(-1, combinationsIds.length);
                $('span.js-bulk-combinations').text('0');
                combinationsIds.forEach((combinationId) => {
                  const combination = new Combination(combinationId);
                  combination.removeFromDOM();
                });
                window.displayFieldsManager.refresh();
              },
              error(response) {
                window.showErrorMessage(jQuery.parseJSON(response.responseText).message);
              },
              complete() {
                $('#create-combinations, #apply-on-combinations, #submit, .btn-submit').removeAttr('disabled');
              },
            });
          },
        }).show();
    },
    getFormValues: function getFormValues() {
      const values = [];
      $(bulkForm).find('input').each(function () {
        if ($(this).val() !== '' && $(this).attr('id') !== 'product_combination_bulk__token') {
          values.push({
            id: $(this).attr('id'),
            value: $(this).val(),
          });
        }
      });
      return values;
    },
    resetForm: function resetForm() {
      bulkForm.find('input').val('');

      return this;
    },
    unselectCombinations: function unselectCombinations() {
      // Use of the bulk action button. It has an event listener to unselect all the combinations
      $('#toggle-all-combinations').prop('checked', false);

      return this;
    },
    hideForm: function toggleForm() {
      bulkForm.collapse('hide');

      return this;
    },
    submitUpdate: function submitUpdate() {
      const globalProductSubmitButton = $('#form'); // @todo: choose a better identifier
      globalProductSubmitButton.submit();
    },
    syncToPricingTab: function syncToPricingTab() {
      $('tr.combination').toArray().forEach((item) => {
        const tableRow = $(`#${item.id}`);
        // We need this because there is a specific data="smthg" attribute so we can't use data() function
        const attributeId = tableRow.attr('data');

        // Get combination final price value from combination form
        const combinationFinalPrice = window.priceCalculation.getCombinationFinalPriceTaxExcludedById(attributeId);
        const combinationFinalPriceLabel = tableRow.find('.attribute-finalprice span.final-price');
        combinationFinalPriceLabel.html(combinationFinalPrice);

        // Update ecotax preview (tax included)
        let combinationEcotaxTI = window.priceCalculation.getCombinationEcotaxTaxIncludedById(attributeId);

        if (combinationEcotaxTI === 0) {
          combinationEcotaxTI = window.priceCalculation.getProductEcotaxTaxIncluded();
        }
        const ecoTaxLabel = tableRow.find('.attribute-finalprice span.attribute-ecotax');
        ecoTaxLabel.html(Number(window.ps_round(combinationEcotaxTI, 2)).toFixed(2)); // 2 digits for short
        const ecoTaxPreview = tableRow.find('.attribute-finalprice .attribute-ecotax-preview');
        ecoTaxPreview.toggleClass('d-none', Number(combinationEcotaxTI) === 0);
      });
    },
  };
}

class Combination {
  constructor(domId, index) {
    this.inputBulkPattern = 'product_combination_bulk_';
    this.inputPattern = `combination_${index}_`;
    this.domId = domId;
    this.appId = `attribute_${this.domId}`;
    this.element = $(`#${this.appId}`);
    this.form = $(`#combination_form_${this.domId}`);
  }

  isSelected() {
    return this.element.checked;
  }

  removeFromDOM() {
    $(this.element).remove();
  }

  updateForm(values) {
    values.forEach((valueObject) => {
      const valueId = valueObject.id.substr(this.inputBulkPattern.length);
      const $field = $(`#${this.convertInput(valueId)}`);

      if ($field.is(':checkbox')) {
        $field.prop('checked', !!valueObject.value);
      } else {
        $field.val(valueObject.value);
      }
    });
    return this.form;
  }

  /**
   * Returns the related input field in legacy form from
   * bulk form field
   *
   * @param bulkInput
   * @returns {string}
   */
  convertInput(bulkInput) {
    let convertedInput = '';

    switch (bulkInput) {
      case 'quantity':
      case 'reference':
      case 'minimal_quantity':
      case 'low_stock_threshold':
      case 'low_stock_alert':
        convertedInput = `${this.inputPattern}attribute_${bulkInput}`;
        break;
      case 'cost_price':
        convertedInput = `${this.inputPattern}attribute_wholesale_price`;
        break;
      case 'date_availability':
        convertedInput = `${this.inputPattern}available_date_attribute`;
        break;
      case 'impact_on_weight':
        convertedInput = `${this.inputPattern}attribute_weight`;
        break;
      case 'impact_on_price_te':
        convertedInput = `${this.inputPattern}attribute_price`;
        break;
      case 'impact_on_price_ti':
        convertedInput = `${this.inputPattern}attribute_priceTI`;
        break;
      default:
    }

    return convertedInput;
  }

  /**
   * Sync values with fast bulk edit form of each combination
   *
   * @param values
   * @returns {bool}
   */
  syncValues(values) {
    values.forEach((valueObject) => {
      let valueId = valueObject.id.substr(this.inputBulkPattern.length);
      const {value} = valueObject;

      const syncedProperties = [
        'quantity',
        'impact_on_price_te',
      ];

      if (syncedProperties.indexOf(valueId) !== -1) {
        valueId = valueId === 'quantity' ? 'quantity' : 'price';
        const input = $(`#attribute_${this.domId} .attribute-${valueId} input`);
        input.val(value);
        input.change();
      }
    });

    return true;
  }
}
