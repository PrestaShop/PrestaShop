/**
 * Combination bulk actions management
 */
var bulkCombinations = (function () {

  var bulkForm = document.querySelector('#bulk-combinations-container');
  var combinationsTable = document.querySelector('#accordion_combinations');
  var deleteCombinationsBtn = document.querySelector('#delete-combinations');
  var applyChangesBtn = document.querySelector('#apply-on-combinations');

  return {
    'init': function init() {
      var that = this;
      // stop propagation on buttons
      deleteCombinationsBtn.addEventListener('click', function () {
        event.preventDefault();
        that.deleteCombinations();
      });

      applyChangesBtn.addEventListener('click', function () {
        event.preventDefault();
        that.applyChangesOnCombinations();
      });
    },
    'getSelectedCombinations': function getSelectedCombinations() {
      var combinations = [];
      var selectedCombinations = Array.from(document.querySelectorAll('#accordion_combinations td:first-child input[type="checkbox"]:checked'));
      selectedCombinations.forEach((combination) => {
        var combinationId = combination.getAttribute('data-id');
        combinations.push(new Combination(combinationId));
      });

      return combinations;
    },
    'applyChangesOnCombinations': function applyChangesOnCombinations() {
      values = this.getFormValues();
      combinations = this.getSelectedCombinations();
      combinations.forEach((combination) => {
        combination.updateForm(values);
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
          var deletionURL = deleteCombinationsBtn.getAttribute('data');
          $.ajax({
            type: 'DELETE',
            data: {'attribute-ids': combinationsIds},
            url: deletionURL,
            success: function(response) {
              showSuccessMessage(response.message);
              combinationsIds.forEach((combinationId) => {
                combination = new Combination(combinationId);
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
      var inputs = Array.from(bulkForm.getElementsByTagName('input'));
      var values = [];
      inputs.forEach((input) => {
        if(input.value !== '' && input.id !== 'product_combination_bulk__token') {
          values.push({'id' : input.id, 'value': input.value});
        }
      });

      return values;
    }
  };
})();

class Combination {
  constructor(domId) {
    this.inputBulkPattern = "product_combination_bulk_";
    this.inputPattern = "form_step3_combinations_"+(domId -1)+"_";
    this.domId = domId;
    this.appId = 'attribute_'+domId;
    this.element = document.querySelector('#'+this.appId);
    this.form = document.querySelector('#combination_form_'+this.domId);
  }

  isSelected() {
    return this.element.checked;
  }

  removeFromDOM() {
    this.element.parentNode.removeChild(this.element);
  }

  updateForm(values) {

    values.forEach((valueObject) => {
      var valueId = valueObject.id.substr(this.inputBulkPattern.length);
      var value = valueObject.value;

      var inputId = '#'+this.convertInput(valueId);
      var formInput = this.form.querySelector(inputId);
      formInput.value = value;
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
}

BOEvent.on("Product Bulk Combinations Management started", function initBulkCombinationsManagement() {
  bulkCombinations.init();
}, "Back office");
