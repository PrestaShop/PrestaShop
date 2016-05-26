import $ from 'jquery';

/**
 * Combination bulk actions management
 */
export default function() {

  var bulkForm = $('#bulk-combinations-container');
  var combinationsTable = $('#accordion_combinations');
  var deleteCombinationsBtn = $('#delete-combinations');
  var applyChangesBtn = $('#apply-on-combinations');

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
        that.applyChangesOnCombinations();
      });

      // bulk select animation
      $('#toggle-all-combinations').on('change', (event) => {
        $('#accordion_combinations td:first-child input[type="checkbox"]').each(function () {
          $(this).prop('checked', $(event.currentTarget).prop('checked'));
        });
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
            data: {'attribute-ids': combinationsIds},
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
        if($(this).val() !== '' && $(this).attr('id') !== 'product_combination_bulk__token') {
          values.push({'id' : $(this).attr('id'), 'value': $(this).val()});
        }
      });
      return values;
    }
  };
}

class Combination {
  constructor(domId, index) {
    this.inputBulkPattern = "product_combination_bulk_";
    this.inputPattern = "form_step3_combinations_"+index+"_";
    this.domId = domId;
    this.appId = 'attribute_'+this.domId;
    this.element = $('#'+this.appId);
    this.form = $('#combination_form_'+this.domId);
  }

  isSelected() {
    return this.element.checked;
  }

  removeFromDOM() {
    $(this.element).remove();
  }

  updateForm(values) {
    values.forEach((valueObject) => {
      var valueId = valueObject.id.substr(this.inputBulkPattern.length);console.log('#'+this.convertInput(valueId));
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
    switch(bulkInput) {
      case "quantity":
      case "reference":
      case "minimal_quantity":
        convertedInput = this.inputPattern+'attribute_'+bulkInput;
        break;
      case "cost_price":
        convertedInput = this.inputPattern+'attribute_wholesale_price';
        break;
      case "date_availability":
        convertedInput = this.inputPattern+'available_date_attribute';
        break;
      case "impact_on_weight":
        convertedInput = this.inputPattern+'attribute_weight';
        break;
      case "impact_on_price_te":
        convertedInput = this.inputPattern+'attribute_price';
        break;
      case "impact_on_price_ti":
        convertedInput = this.inputPattern+'attribute_priceTI';
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

      if (syncedProperties.indexOf(valueId) !== -1){
        valueId = valueId === 'quantity' ? 'quantity' : 'price';
        $(`#attribute_${this.domId} .attribute-${valueId} input`).val(value);
      }
    });
  }
}
