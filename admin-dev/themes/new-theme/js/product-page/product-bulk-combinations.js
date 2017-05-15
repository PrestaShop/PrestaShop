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
  var finalPriceBasics = $('#form_step1_price_shortcut');
  var finalPriceBasicsIT = $('#form_step1_price_ttc_shortcut');
  var impactOnPriceSelector = 'input.attribute_priceTE';
  var finalPriceSelector = '.attribute-finalprice span';

  let refreshDefaultImage = () => {
    var productDefaultImageUrl = null;
    var productCoverImageElem = $('#product-images-dropzone').find('.iscover');

    /** get product cover image */
    if (productCoverImageElem.length === 1) {
      var imgElem = productCoverImageElem.parent().find('.dz-image');

      /** Dropzone.js workaround : If this is a fresh upload image, look up for an img, else find a background url */
      if (imgElem.find('img').length) {
        productDefaultImageUrl = imgElem.find('img').attr('src');
      } else {
        productDefaultImageUrl = imgElem.css('background-image')
          .replace(/^url\(["']?/, '')
          .replace(/["']?\)$/, '');
      }
    }

    $.each($('#form .combination-form'), function (key, elem) {
      var defaultImageUrl = productDefaultImageUrl;

      /** get first selected image */
      var defaultImageElem = $(elem).find('.product-combination-image input:checked:first');
      if (defaultImageElem.length === 1) {
        defaultImageUrl = defaultImageElem.parent().find('img').attr('src');
      }

      if (defaultImageUrl) {
        var img = '<img src="' + defaultImageUrl + '" class="img-responsive" />';
        $('#accordion_combinations #attribute_' + $(elem).attr('data')).find('td.img').html(img);
      }
    });
  };

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
            .hideForm()
            .resetForm()
            .unselectCombinations()
            .submitUpdate();
        refreshDefaultImage();
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

      $(document).on('change', '.js-combination', () => {
        if ($('.bulk-action').attr('aria-expanded') === "false" || !$('.js-combination').is(':checked')) {
          $('.js-collapse').collapse('toggle');
          that.fillBulkActionImages();
        }
        $('span.js-bulk-combinations').text($('input.js-combination:checked').length);
      });

      /** product combination bulk images */
      $('#tab_step3').on('click', function () {
        that.getBulkActionImages();
      });
      $('#product_combination_bulk_images').on("select2:select", function () {
        $('#product_combination_bulk_images').select2('open');
        that.setBulkActionImagesCheckboxStatus();
      });
      $('#product_combination_bulk_images').on("select2:unselect", function () {
        $('#product_combination_bulk_images').select2('open');
        that.setBulkActionImagesCheckboxStatus();
      });
      $('body').on('DOMNodeInserted', function () {
        that.setBulkActionImagesCheckboxStatus();
      });

      $('.bulk-action').on('click', function () {
        that.fillBulkActionImages();
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
      $(bulkForm).find('select').each(function () {
        if ($(this).val() !== '') {
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
      $('#product_combination_bulk_images').select2('val', 'ALL');

      return this;
    },
    'unselectCombinations': function unselectCombinations() {
      // Use of the bulk action button. It has an event listener to unselect all the combinations
      $('#toggle-all-combinations').prop('checked', false);

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
        jQueryFinalPriceEl.text(ps_round(newFinalPrice, 6));
      });
    },
    'getBulkActionImages': function getBulkActionImages() {
      let productImages = [];
      $.ajax({
        type: 'GET',
        url: $('#images-combinations').attr('data'),
        success: function (response) {
          response.forEach(function (element) {
            productImages.push({id: element.id, image: element.image, text: element.label});
          });
          $('#product_combination_bulk_images').select2({
            data: productImages,
            templateResult: function (element) {
              return $("<input type='checkbox' id='product_combination_bulk_images_checkbox_" + element.id + "'><img src='" + element.image + "' class='combination-bulk-images'><span class='combination-bulk-images-label'>" + element.text + "</span></img>");
            },
            minimumResultsForSearch: Infinity,
            tags: false,
          });
        }
      });
    },
    'setBulkActionImagesCheckboxStatus': function setBulkActionImagesCheckboxStatus() {
      let select = $('#product_combination_bulk_images');
      if (select.data('select2')) {
        let selectedIndex = select.select2('val');
        if (selectedIndex != null) {
          selectedIndex.forEach(function (item) {
            $('#product_combination_bulk_images_checkbox_' + item).prop('checked', "checked");
          });
        }
      }
    },
    'fillBulkActionImages': function fillBulkActionImages() {
      if ($('#product_combination_bulk_images option').length === 0) {
        this.getBulkActionImages();
      }
    }
  };
}

class Combination {
  constructor(domId, index) {
    this.inputBulkPattern = "product_combination_bulk_";
    this.inputPattern = "combination_" + index + "_";
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
      if (this.convertInput(valueId) == this.inputPattern + 'id_image_attr') {
        if (valueObject.value != null) {
          var combinationId = this.domId
          valueObject.value.forEach(function (element) {
            $('#combination_form_' + combinationId + ' .product-combination-image').each(function () {
              if ($(this).find('input').val() == element) {
                $(this).find('input').prop('checked', true);
                $(this).addClass('img-highlight');
              }
            });
          });
        }
      } else {
        $('#' + this.convertInput(valueId)).val(valueObject.value);
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
      case "images":
        convertedInput = this.inputPattern + 'id_image_attr';
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
