import $ from 'jquery';

export default function() {
  $(document).ready(function() {
    let $jsCombinationsList = $('.js-combinations-list');
    let idsProductAttribute = $jsCombinationsList.data('ids-product-attribute').toString().split(',');
    let idsCount = idsProductAttribute.length;
    let currentCount = 0;
    let step = 5;

    $.get($jsCombinationsList.attr('data-action-refresh-images') + '/' + $jsCombinationsList.data('id-product'))
      .then(function(response) {
        if (idsProductAttribute[0] != '') {
          getCombinations(response);
        }
        $('#create-combinations').click(function() {
          generate();
        });
      });

    $(document).on('click', '#form .product-combination-image', function() {
      var input = $(this).find('input');
      var isChecked = input.prop('checked');
      input.prop('checked', isChecked ? false : true);

      if (isChecked) {
        $(this).removeClass('img-highlight');

      } else {
        $(this).addClass('img-highlight');
      }
      refreshDefaultImage();
    });

    /*
     * Retrieve URL to get a set of combination forms from data attribute
     * Concatenate ids_product_attribute to load from a slice of idsProductAttribute depending of step and last set
     */
    let combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');

    let getCombinations = (combinationsImages) => {
      let $jsCombinationsBulkForm = $('#combinations-bulk-form');
      $jsCombinationsBulkForm.toggleClass('inactive', !$jsCombinationsBulkForm.hasClass('inactive'));
      $.get(combinationUrl).then(function (resp) {
        $('#loading-attribute').before(resp);
        refreshImagesCombination(combinationsImages, idsProductAttribute.slice(currentCount, currentCount+step));
        currentCount += step;
        combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');
        if (currentCount < idsCount) {
          getCombinations(combinationsImages);
        } else {
          $jsCombinationsBulkForm.removeClass('inactive');
          $('#loading-attribute').fadeOut(1000).remove();
        }
      });
    };
  });

  let refreshImagesCombination = (combinationsImages, idsProductAttribute) => {
    $.each(idsProductAttribute, function (index, value) {
      var $combinationElem = $('.combination[data="' + value + '"]');
      var $imagesElem = $combinationElem.find('.images');
      var $index = $combinationElem.attr('data-index');

      $imagesElem.html('');

      $.each(combinationsImages[value], function(key, image) {
        $imagesElem.append(`<div class="product-combination-image ${(image.id_image_attr ? 'img-highlight' : '')}">
          <input type="checkbox" name="combination_${$index}[id_image_attr][]" value="${image.id}" ${(image.id_image_attr ? 'checked="checked"' : '')}>
          <img src="${image.base_image_url}-small_default.${image.format}" alt="" />
        </div>`);
      });

      $combinationElem.fadeIn(1000);
    });

    refreshDefaultImage();
  };

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
  };

  let generate = () => {
    $.ajax({
      type: 'POST',
      url: $('#form_step3_attributes').attr('data-action'),
      data: $('#attributes-generator input.attribute-generator, #form_id_product').serialize(),
      beforeSend: function() {
        $('#create-combinations').attr('disabled', 'disabled');
      },
      success: function(response) {
        $('#accordion_combinations').append(response.form);
        displayFieldsManager.refresh();
        $.get($('.js-combinations-list').attr('data-action-refresh-images') + '/' + $('.js-combinations-list').data('id-product'))
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
        $('#create-combinations').removeAttr('disabled');
        supplierCombinations.refresh();
        warehouseCombinations.refresh();
      }
    });
  };
}
