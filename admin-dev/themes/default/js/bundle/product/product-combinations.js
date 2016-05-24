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
          }
        });
      }
    }).show();
  }

  /**
   * Update final price, regarding the impact on price
   * @param {object} elem - The tableau row parent
   */
  function updateFinalPrice(tableRow) {
      if (!tableRow.is('tr')) {
          throw new Error('Structure of table has changed, this function need to be updated.');
      }
      var priceImpactInput = tableRow.find('.attribute_priceTE');
      var impactOnPrice = priceImpactInput.val() - priceImpactInput.attr('value');
      var actualFinalPriceInput = tableRow.find('.attribute-finalprice');
      var actualFinalPrice = actualFinalPriceInput.data('price');

      var finalPrice = actualFinalPrice + impactOnPrice;
      actualFinalPriceInput.html(finalPrice.toFixed(2));
  }

  return {
    'init': function() {
      var _this = this;

      /** delete combination */
      $(document).on('click', '#accordion_combinations .delete', function(e) {
        e.preventDefault();
        remove($(this));
      });

      /** on change quantity, update field quantity row */
      $(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_quantity"]', function() {
        var id_attribute = $(this).closest('.combination-form').attr('data');
        $('#accordion_combinations #attribute_' + id_attribute).find('.attribute-quantity input').val($(this).val());
      });

      /** on change shortcut quantity, update form field quantity */
      $(document).on('keyup', '.attribute-quantity input', function() {
        var id_attribute = $(this).closest('.combination').attr('data');
        $('#combination_form_' + id_attribute).find('input[id^="form_step3_combinations_"][id$="_attribute_quantity"]').val($(this).val());
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
        $('#combination_form_' + id_attribute).find('input[id^="form_step3_combinations_"][id$="_attribute_default"]').prop("checked", true);
      });


      /** on change price on impact, update price on impact form field */
      $(document).on('change', '.attribute-price input', function() {
        var id_attribute = $(this).closest('.combination').attr('data');
        $('#combination_form_' + id_attribute).find('input[id^="form_step3_combinations_"][id$="_attribute_price"]').val($(this).val());
        updateFinalPrice($(this).parent().parent().parent());
      });

      /** on change price, update price row */
      $(document).on('keyup', 'input[id^="form_step3_combinations_"][id$="_attribute_price"]', function() {
        var id_attribute = $(this).closest('.combination-form').attr('data');
        $('#accordion_combinations #attribute_' + id_attribute).find('.attribute-price-display').html(formatCurrency(parseFloat($(this).val())));
      });

      /** on change images selection */
      $(document).on('click', '#form .product-combination-image', function() {
        var input = $(this).find('input');
        var isChecked = input.prop('checked');
        input.prop('checked', isChecked ? false : true);

        if (isChecked) {
          $(this).removeClass('img-highlight');

        } else {
          $(this).addClass('img-highlight');
        }

        _this.refreshDefaultImage();
      });

      /** Combinations fields display management */
      $('#show_variations_selector input').change(function() {
        displayFieldsManager.refresh();

        if ($(this).val() === '0') {
          //if combination(s) exists, alert user for deleting it
          if ($('#accordion_combinations .combination').length > 0) {
            modalConfirmation.create(translate_javascripts['Are you sure to disable variations ? they will all be deleted'], null, {
              onCancel: function() {
                $('#show_variations_selector input[value="1"]').attr('checked', true);
                displayFieldsManager.refresh();
              },
              onContinue: function() {
                $.ajax({
                  type: 'GET',
                  url: $('#accordion_combinations').attr('data-action-delete-all') + '/' + $('#form_id_product').val(),
                  success: function(response) {
                    $('#accordion_combinations .combination').remove();
                    displayFieldsManager.refresh();
                  },
                  error: function(response) {
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
        if (prevCombinationId) {
          navElem.find('.prev').attr('data', prevCombinationId).show();
        }
        if (nextCombinationId) {
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
      $(document).on('click', '#form .combination-form .nav a', function(e) {
        e.preventDefault();
        $('.combination-form').hide();
        $('#accordion_combinations .combination[data="' + $(this).attr('data') + '"] .btn-open').click();
      });
    },
    'refreshDefaultImage': function() {
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

      $.each($('#form .combination-form'), function(key, elem) {
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
    },
    'refreshImagesCombination': function() {
      var _this = this;
      var target = $('#accordion_combinations');
      if (target.find('.combination').length === 0) {
        return;
      }

      $.ajax({
        type: 'GET',
        url: target.attr('data-action-refresh-images') + '/' + id_product,
        success: function(response) {
          $.each(response, function(id, combinationImages) {
            var combinationElem = target.find('.combination[data="' + id + '"]');
            var imagesElem = combinationElem.find('.images');
            var index = combinationElem.attr('data-index');

            imagesElem.html('');
            $.each(combinationImages, function(key, image) {
              var row = '<div class="product-combination-image ' + (image.id_image_attr ? 'img-highlight' : '') + '">\
                 <input type="checkbox" name="form[step3][combinations][' + index + '][id_image_attr][]" value="' + image.id + '" ' + (image.id_image_attr ? 'checked="checked"' : '') + '>\
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

BOEvent.on("Product Combinations Management started", function initCombinationsManagement() {
  combinations.init();
}, "Back office");
