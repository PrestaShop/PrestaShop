/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import {psGetRequestParameter} from './common';

$(document).ready(function () {
  $('body').on('change', '.product-variants [data-product-attribute]', function () {
    $("input[name$='refresh']").click();
  });

  $('body').on(
    'click',
    '.product-refresh',
    function (event, extraParameters) {
      var $productRefresh = $(this);
      event.preventDefault();

      let eventType = 'updatedProductCombination';
      if (typeof extraParameters !== 'undefined' && extraParameters.eventType) {
        eventType = extraParameters.eventType;
      }

      var preview = psGetRequestParameter('preview');
      if (preview !== null) {
        preview = '&preview=' + preview;
      } else {
        preview = '';
      }

      var query = $(event.target.form).serialize() + '&ajax=1&action=productrefresh' + preview;
      var actionURL = $(event.target.form).attr('action');

      $.post(actionURL, query, null, 'json').then(function(resp) {
        prestashop.emit('updateProduct', {
          reason: {
            productUrl: resp.productUrl
          },
          refreshUrl: $productRefresh.data('url-update'),
          eventType: eventType,
          resp: resp
        });
      });
    }
  );

  prestashop.on('updateProduct', function (event) {
    if (typeof event.refreshUrl == "undefined") {
        event.refreshUrl = true;
    }

    var eventType = event.eventType;

    let replaceAddToCartSections = ((addCartHtml) => {
      let $addToCartSnippet = $(addCartHtml);
      let $addProductToCart = $('.product-add-to-cart');

      function replaceAddToCartSection(replacement) {
        let replace = replacement.$addToCartSnippet.find(replacement.targetSelector);

        if ($(replacement.$targetParent.find(replacement.targetSelector)).length > 0) {
          if (replace.length > 0) {
            $(replacement.$targetParent.find(replacement.targetSelector)).replaceWith(replace[0].outerHTML);
          } else {
            $(replacement.$targetParent.find(replacement.targetSelector)).html('');
          }
        }
      }

      const productAvailabilitySelector = '.add';
      replaceAddToCartSection({
        $addToCartSnippet: $addToCartSnippet,
        $targetParent: $addProductToCart,
        targetSelector: productAvailabilitySelector
      });

      const productAvailabilityMessageSelector = '#product-availability';
      replaceAddToCartSection({
        $addToCartSnippet: $addToCartSnippet,
        $targetParent: $addProductToCart,
        targetSelector: productAvailabilityMessageSelector
      });

      const productMinimalQuantitySelector = '.product-minimal-quantity';
      replaceAddToCartSection({
        $addToCartSnippet: $addToCartSnippet,
        $targetParent: $addProductToCart,
        targetSelector: productMinimalQuantitySelector
      });
    });

    $.post(event.reason.productUrl, {ajax: '1', action: 'refresh'}, null, 'json').then(function(resp) {
      $('.product-prices').replaceWith(resp.product_prices);
      $('.product-customization').replaceWith(resp.product_customization);
      $('.product-variants').replaceWith(resp.product_variants);
      $('.product-discounts').replaceWith(resp.product_discounts);
      $('.images-container').replaceWith(resp.product_cover_thumbnails);
      $('.product-additional-info').replaceWith(resp.product_additional_info);
      $('#product-details').replaceWith(resp.product_details);

      // Replace all "add to cart" sections but the quantity input in order to keep quantity field intact i.e.
      // Prevent quantity input from blinking with classic theme.
      let $productAddToCart;
      $(resp.product_add_to_cart).each(function(index, value) {
          if ($(value).hasClass('product-add-to-cart')) {
            $productAddToCart = $(value);
          }
      });
      replaceAddToCartSections($productAddToCart);

      const minimalProductQuantity = parseInt(resp.product_minimal_quantity, 10);
      const quantityInputSelector = '#quantity_wanted';
      let quantityInput = $(quantityInputSelector);
      const quantity_wanted = quantityInput.val();

      if (!isNaN(minimalProductQuantity) && quantity_wanted < minimalProductQuantity && eventType !== 'updatedProductQuantity') {
        quantityInput.attr('min', minimalProductQuantity);
        quantityInput.val(minimalProductQuantity);
      }

      if (event.refreshUrl) {
        window.history.pushState({id_product_attribute: resp.id_product_attribute}, undefined, resp.product_url);
      }

      prestashop.emit('updatedProduct', resp);
    });
  });
});

