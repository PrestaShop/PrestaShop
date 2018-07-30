var $ = window.$;

export default function() {
  $(document).ready(function() {
    const $jsCombinationsList = $('.js-combinations-list');
    // If we are not on the product page, return
    if (0 === $jsCombinationsList.length) {
        return;
    }

    const idsProductAttribute = $jsCombinationsList.data('ids-product-attribute').toString().split(',');
    const refreshImagesUrl = $jsCombinationsList
      .attr('data-action-refresh-images')
      .replace(/product-form-images\/\d+/, 'product-form-images/' + $jsCombinationsList.data('id-product'));
    const idsCount = idsProductAttribute.length;
    const step = 50;
    let currentCount = 0;


    $.get(refreshImagesUrl).then(function(response) {
      if (idsCount !== 0) {
        getCombinations(response);
      }
    });

    $('#create-combinations').click(function(event) {
      event.preventDefault();
      form.send(false, false, generate);
    });

    const productDropzone = Dropzone.forElement('#product-images-dropzone');
    const updateCombinationImages = function() {
      const productAttributeIds = $.map(
        $('.js-combinations-list .combination'),
        (combination) => {
          return $(combination).data('index');
        }
      );

      $.get(refreshImagesUrl).then((response) => {
        refreshImagesCombination(response, productAttributeIds);
      });
    };
    productDropzone.on('success', updateCombinationImages);

    $(document).on('click', '#form .product-combination-image', function() {
      const input = $(this).find('input');
      const isChecked = input.prop('checked');
      input.prop('checked', !isChecked);
      $(this).toggleClass('img-highlight', !isChecked);
      refreshDefaultImage();
    });

    $('#product_combination_bulk_impact_on_price_ti, #product_combination_bulk_impact_on_price_te').keyup(function() {
      const self = $(this);
      const price = priceCalculation.normalizePrice(self.val());

      if ('product_combination_bulk_impact_on_price_ti' === self.attr('id')) {
        $('#product_combination_bulk_impact_on_price_te').val(priceCalculation.removeCurrentTax(price)).change();
      } else {
        $('#product_combination_bulk_impact_on_price_ti').val(priceCalculation.addCurrentTax(price)).change();
      }
    });

    const getCombinations = (combinationsImages) => {
      let $jsCombinationsBulkForm = $('#combinations-bulk-form');
      if (!$jsCombinationsBulkForm.hasClass('inactive')) {
        $jsCombinationsBulkForm.addClass('inactive');
      }

      $.get(getCombinationsUrl()).then(function (resp) {
        $('#loading-attribute').before(resp);
        refreshImagesCombination(combinationsImages, idsProductAttribute.slice(currentCount, currentCount+step));
        currentCount += step;
        if (currentCount < idsCount) {
          getCombinations(combinationsImages);
        } else {
          $jsCombinationsBulkForm.removeClass('inactive');
          $('#loading-attribute').fadeOut(1000).remove();
          $('[data-toggle="popover"]').popover();
        }
      });
    };

    /*
     * Retrieve URL to get a set of combination forms from data attribute
     * Concatenate ids_product_attribute to load from a slice of idsProductAttribute depending of step and last set
     */
    const getCombinationsUrl = () => {
      return $jsCombinationsList
        .data('combinations-url')
        .replace(
          ':numbers',
          idsProductAttribute.slice(currentCount, currentCount+step).join('-')
        );
    };
  });

  const refreshImagesCombination = (combinationsImages, idsProductAttribute) => {
    $.each(idsProductAttribute, function (index, value) {
      const $combinationElem = $('.combination[data="' + value + '"]');
      const $index = $combinationElem.attr('data-index');
      let $imagesElem = $combinationElem.find('.images');
      let html = '';

      if (0 === $imagesElem.length) {
        $imagesElem = $('#combination_' + $index + '_id_image_attr');
      }

      $.each(combinationsImages[value], function(key, image) {
        html += `<div class="product-combination-image ${(image.id_image_attr ? 'img-highlight' : '')}">
          <input type="checkbox" name="combination_${$index}[id_image_attr][]" value="${image.id}" ${(image.id_image_attr ? 'checked="checked"' : '')}>
          <img src="${image.base_image_url}-small_default.${image.format}" alt="" />
        </div>`;
      });
      $imagesElem.html(html);
      $combinationElem.fadeIn(1000);
    });

    refreshDefaultImage();
  };

  const refreshDefaultImage = () => {
    const productCoverImageElem = $('#product-images-dropzone').find('.iscover');
    let productDefaultImageUrl = null;

    /** get product cover image */
    if (productCoverImageElem.length === 1) {
      let imgElem = productCoverImageElem.parent().find('.dz-image');

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
      let defaultImageUrl = productDefaultImageUrl;

      /** get first selected image */
      const defaultImageElem = $(elem).find('.product-combination-image input:checked:first');
      if (defaultImageElem.length === 1) {
        defaultImageUrl = defaultImageElem.parent().find('img').attr('src');
      }

      if (defaultImageUrl) {
        var img = '<img src="' + defaultImageUrl + '" class="img-responsive" />';
        $('#attribute_' + $(elem).attr('data')).find('td.img').html(img);
      }
    });
  };

  const generate = () => {
    $.ajax({
      type: 'POST',
      url: $('#form_step3_attributes').attr('data-action'),
      data: $('#attributes-generator input.attribute-generator, #form_id_product').serialize(),
      beforeSend: function() {
        $('#create-combinations, #submit, .btn-submit').attr('disabled', 'disabled');
      },
      success: function(response) {
        refreshTotalCombinations(1, $(response.form).filter('.combination.loaded').length);
        $('#accordion_combinations').append(response.form);
        displayFieldsManager.refresh();
        const url = $('.js-combinations-list').attr('data-action-refresh-images').replace(/product-form-images\/\d+/, 'product-form-images/' + $('.js-combinations-list').data('id-product'));
        $.get(url)
          .then(function(combinationsImages) {
            refreshImagesCombination(combinationsImages, response.ids_product_attribute);
          });


        /** initialize form */
        $('input.attribute-generator').remove();
        $('#attributes-generator div.token').remove();
        $('.js-attribute-checkbox:checked').each(function() {
          $(this).prop('checked', false);
        });
        $('#combinations_thead').fadeIn();
      },
      complete: function() {
        $('#create-combinations, #submit, .btn-submit').removeAttr('disabled');
        supplierCombinations.refresh();
        warehouseCombinations.refresh();
      }
    });
  };
}
