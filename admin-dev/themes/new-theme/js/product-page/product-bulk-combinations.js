import $ from 'jquery';

/**
 * Combination bulk actions management
 */
export default function() {

  var bulkForm = $('#bulk-combinations-container');
  var deleteCombinationsBtn = $('#delete-combinations');
  var applyChangesBtn = $('#apply-on-combinations');
  var syncedCollection = $('[data-uniqid]');
  var finalPrice = $('#form_step2_price');
  var finalPriceIT = $('#form_step2_price_ttc');
  var impactOnPriceSelector = 'input.attribute_priceTE';
  var finalPriceSelector = '.attribute-finalprice span';

  return {
    'init': function init() {
      var that = this;
      // stop propagation on buttons
      deleteCombinationsBtn.on('click', (event) => {
        event.preventDefault();
        that.deleteCombinations();
      });

      applyChangesBtn.on('click', (event) => {
        event.preventDefault();
        that.applyChangesOnCombinations()
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

      syncedCollection.on('DOMSubtreeModified', (event) => {

        var uniqid = event.target.getAttribute('data-uniqid');
        var newValue = event.target.innerText;

        var spans = $('[data-uniqid="'+uniqid+'"]');

        spans.each( function( index, element ){
          if ($(this).text() !== newValue) {
            $(this).text(newValue);
          }
        });
      });

      // bulk select animation
      $('#toggle-all-combinations').on('change', (event) => {
        $('#accordion_combinations td:first-child input[type="checkbox"]').each(function() {
          $(this).prop('checked', $(event.currentTarget).prop('checked'));
        });
      });

      $('.js-combination').on('change', () => {
        if ($('.bulk-action').attr('aria-expanded') === "false" || !$('.js-combination').is(':checked')) {
          $('.js-collapse').collapse('toggle');
        }
        $('span.js-bulk-combinations').text($('input.js-combination:checked').length);
      });
    },
    'getSelectedCombinations': function getSelectedCombinations() {
      var combinations = [];
      var selectedCombinations = Array.from($('#accordion_combinations td:first-child input[type="checkbox"]:checked'));
      selectedCombinations.forEach((combination) => {
        var combinationId = combination.getAttribute('data-id');
        var combinationIndex = combination.getAttribute('data-index');
        combinations.push(new Combination(combinationId, combinationIndex));
      });

      return combinations;
    },
    'applyChangesOnCombinations': function applyChangesOnCombinations() {
      var values = this.getFormValues();
      var combinations = this.getSelectedCombinations();
      combinations.forEach((combination) => {
        combination.updateForm(values);
        combination.syncValues(values);
      });

      return this;
    },
    'deleteCombinations': function deleteCombinations() {
      var combinations = this.getSelectedCombinations();
      var combinationsIds = [];
      combinations.forEach((combination) => {
        combinationsIds.push(combination.domId);
      });

      modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
        onContinue: function() {
          var deletionURL = $(deleteCombinationsBtn).attr('data');
          $.ajax({
            type: 'DELETE',
            data: {
              'attribute-ids': combinationsIds
            },
            url: deletionURL,
            success: function(response) {
              showSuccessMessage(response.message);
              combinationsIds.forEach((combinationId) => {
                var combination = new Combination(combinationId);
                combination.removeFromDOM();
              });
              displayFieldsManager.refresh();
            },
            error: function(response) {
              showErrorMessage(jQuery.parseJSON(response.responseText).message);
            },
          });
        }
      }).show();
    },
    'getFormValues': function getFormValues() {
      var values = [];
      $(bulkForm).find('input').each(function() {
        if ($(this).val() !== '' && $(this).attr('id') !== 'product_combination_bulk__token') {
          values.push({
            'id': $(this).attr('id'),
            'value': $(this).val()
          });
        }
      });
      return values;
    },
    'resetForm': function resetForm() {
      bulkForm.find('input').val('');

      return this;
    },
    'unselectCombinations': function unselectCombinations() {
      $('input.js-combination').prop('checked', false);

      return this;
    },
    'hideForm': function toggleForm() {
      bulkForm.collapse('hide');

      return this;
    },
    'submitUpdate': function submitUpdate() {
      var globalProductSubmitButton = $('#form'); // @todo: choose a better identifier
      globalProductSubmitButton.submit();
    },
    'syncToPricingTab': function syncToPricingTab() {
      var newPrice = finalPrice.val();
      $('tr.combination').toArray().forEach((item) => {
        var jQueryRow = $('#'+item.id);
        var jQueryFinalPriceEl = jQueryRow.find(finalPriceSelector);
        var impactOnPriceEl = jQueryRow.find(impactOnPriceSelector);
        var impactOnPrice = impactOnPriceEl.val();

        jQueryFinalPriceEl.data('price', newPrice);
        // calculate new price
        var newFinalPrice = new Number(newPrice) + new Number(impactOnPrice);
        jQueryFinalPriceEl.text(newFinalPrice.toFixed(2));
      });
    }
  };
}

class Combination {
  constructor(domId, index) {
    this.inputBulkPattern = "product_combination_bulk_";
    this.inputPattern = "form_step3_combinations_" + index + "_";
    this.domId = domId;
    this.appId = 'attribute_' + this.domId;
    this.element = $('#' + this.appId);
    this.form = $('#combination_form_' + this.domId);
  }

  isSelected() {
    return this.element.checked;
  }

  removeFromDOM() {
    $(this.element).remove();
  }

  updateForm(values) {
    values.forEach((valueObject) => {
      var valueId = valueObject.id.substr(this.inputBulkPattern.length);
      $('#'+this.convertInput(valueId)).val(valueObject.value);
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

    var convertedInput = '';
    switch (bulkInput) {
      case "quantity":
      case "reference":
      case "minimal_quantity":
        convertedInput = this.inputPattern + 'attribute_' + bulkInput;
        break;
      case "cost_price":
        convertedInput = this.inputPattern + 'attribute_wholesale_price';
        break;
      case "date_availability":
        convertedInput = this.inputPattern + 'available_date_attribute';
        break;
      case "impact_on_weight":
        convertedInput = this.inputPattern + 'attribute_weight';
        break;
      case "impact_on_price_te":
        convertedInput = this.inputPattern + 'attribute_price';
        break;
      case "impact_on_price_ti":
        convertedInput = this.inputPattern + 'attribute_priceTI';
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
      var valueId = valueObject.id.substr(this.inputBulkPattern.length);
      var value = valueObject.value;

      var syncedProperties = [
        'quantity',
        'impact_on_price_te'
      ];

      if (syncedProperties.indexOf(valueId) !== -1) {
        valueId = valueId === 'quantity' ? 'quantity' : 'price';
        var input = $(`#attribute_${this.domId} .attribute-${valueId} input`);
        input.val(value);
        input.change();
      }
    });

    return true;
  }
}
