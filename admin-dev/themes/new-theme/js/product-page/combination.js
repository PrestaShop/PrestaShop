import $ from 'jquery';

export default function() {
  $(document).ready(function() {
    let $jsCombinationsList = $('.js-combinations-list');
    let idsProductAttribute = $jsCombinationsList.data('ids-product-attribute').split(',');
    let idsCount = idsProductAttribute.length;
    let currentCount = 0;
    let step = 5;

    $.get($jsCombinationsList.attr('data-action-refresh-images') + '/' + 2)
      .then(function(response) {
        getCombinations(response);
      });

    let combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');

    let getCombinations = (combinationsImages) => {
      $.get(combinationUrl).then(function (resp) {
        $jsCombinationsList.append(resp);
        refreshImagesCombination(combinationsImages, idsProductAttribute.slice(currentCount, currentCount+step));
        currentCount += step;
        combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');
        if (currentCount <= idsCount) {
          getCombinations(combinationsImages);
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
        $imagesElem.append('<div class="product-combination-image ' + (image.id_image_attr ? 'img-highlight' : '') + '">\
                 <input type="checkbox" name="form[step3][combinations][' + $index + '][id_image_attr][]" value="' + image.id + '" ' + (image.id_image_attr ? 'checked="checked"' : '') + '>\
                  <img src="' + image.base_image_url + '-small_default.' + image.format + '" alt="" />\
                </div>');
      });
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
}
