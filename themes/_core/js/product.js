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

// Used to be able to abort request if user modify something
var currentRequest = null;

// Used to clearTimeout if user flood the product quantity input
var currentRequestDelayedId = null;

$(document).ready(function () {
    // Listen on all form elements + those who have a data-product-attribute
    $('body').on(
        'change touchspin.on.startspin',
        '.product-variants *[name]',
        function(e) {
            prestashop.emit('updateProduct', {
                eventType: 'updatedProductCombination',
                event: e
            });
        }
    );

    $('body').on(
        'click',
        '.product-refresh',
        function (e, extraParameters) {
            e.preventDefault();
            let eventType = 'updatedProductCombination';

            if (typeof extraParameters !== 'undefined'
                && extraParameters.eventType
            ) {
                eventType = extraParameters.eventType;
            }
            prestashop.emit('updateProduct', {
                eventType: eventType,
                event: e
            })
        }
    );

    // Refresh all the product content
    prestashop.on('updateProduct', function (args) {
        const eventType = args.eventType;
        const event = args.event;

        getProductUpdateUrl().done(function(productUpdateUrl) {
            return updateProduct(event, eventType, productUpdateUrl);
        }).fail(function() {
            if ($('section#main > .ajax-error').length === 0) {
                showError($('#product-availability'), 'An error occurred while processing your request');
            }
        });
    });
});

/**
 * Get product update URL from different
 * sources if needed (for compatibility)
 *
 * @return {Promise}
 */
function getProductUpdateUrl()
{
    let dfd = $.Deferred();
    const $productActions = $('.product-actions');
    const $quantityWantedInput = $productActions.find('#quantity_wanted:first');
    let updateUrl = null;

    if (prestashop != null
        && prestashop.page != null
        && prestashop.page.canonical != ''
    ) {
        updateUrl = prestashop.page.canonical;
    }

    if (updateUrl == null) {
        let formData = {};

        $($productActions.find('form:first').serializeArray()).each(function(k, v) {
            formData[v.name] = v.value;
        });

        $.ajax({
            url: $productActions.find('form:first').attr('action'),
            method: 'POST',
            data: Object.assign(
                {
                    ajax: 1,
                    action: 'productrefresh',
                    quantity_wanted: $quantityWantedInput.val()
                },
                formData
            ),
            dataType: 'json',
            success: function(data, textStatus, errorThrown) {
                let productUpdateUrl = data.productUrl;
                prestashop.page.canonical = productUpdateUrl;
                dfd.resolve(productUpdateUrl);
            }
        });
    } else {
        dfd.resolve(updateUrl);
    }

    return dfd.promise();
}

/**
 * Update the product html
 *
 * @param {string} event
 * @param {string} eventType
 * @param {string} updateUrl
 */
function updateProduct(event, eventType, updateUrl)
{
    const $productActions = $('.product-actions');
    const $quantityWantedInput = $productActions.find('#quantity_wanted:first');
    const formSerialized = $productActions.find('form:first').serialize();
    let preview = psGetRequestParameter('preview');

    if (preview !== null) {
        preview = '&preview=' + preview;
    } else {
        preview = '';
    }

    // Can not get product ajax url
    if (updateUrl == null) {
        showError($('#product-availability'), 'An error occurred while processing your request');

        return;
    }

    // New request only if new value
    if (event != null
        && event.type === 'keyup'
        && $quantityWantedInput.val() === $quantityWantedInput.data('old-value')
    ) {
        return;
    }
    $quantityWantedInput.data('old-value', $quantityWantedInput.val());

    if (currentRequestDelayedId) {
        clearTimeout(currentRequestDelayedId);
    }

    currentRequestDelayedId = setTimeout(function() {
        currentRequest = $.ajax({
            url: updateUrl + '?' + formSerialized + preview,
            method: 'POST',
            data: {
                ajax: 1,
                action: 'refresh',
                quantity_wanted: $quantityWantedInput.val()
            },
            dataType: 'json',
            beforeSend: function() {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus !== 'abort'
                    && $('section#main > .ajax-error').length === 0
                ) {
                    showError($('#product-availability'), 'An error occurred while processing your request');
                }
            },
            success: function(data, textStatus, errorThrown) {
                // Avoid image to blink each time we modify the product quantity
                // Can not compare directly cause of HTML comments in data.
                const $newImagesContainer = $('<div>').append(data.product_cover_thumbnails);

                // Used to avoid image blinking if same image = epileptic friendly
                if ($('.images-container').html() !== $newImagesContainer.find('.images-container').html()) {
                    $('.images-container').replaceWith(data.product_cover_thumbnails);
                }
                $('.product-prices').replaceWith(data.product_prices);
                $('.product-customization').replaceWith(data.product_customization);
                $('.product-variants').replaceWith(data.product_variants);
                $('.product-discounts').replaceWith(data.product_discounts);
                $('.product-additional-info').replaceWith(data.product_additional_info);
                $('#product-details').replaceWith(data.product_details);
                replaceAddToCartSections(data);
                const minimalProductQuantity = parseInt(data.product_minimal_quantity, 10);

                // Prevent quantity input from blinking with classic theme.
                if (!isNaN(minimalProductQuantity)
                    && $quantityWantedInput.val() < minimalProductQuantity
                    && eventType !== 'updatedProductQuantity'
                ) {
                    $quantityWantedInput.attr('min', minimalProductQuantity);
                    $quantityWantedInput.val(minimalProductQuantity);
                }
                prestashop.emit('updatedProduct', data);
            },
            complete: function(jqXHR, textStatus) {
                currentRequest = null;
                currentRequestDelayedId = null;
            }
        });
    }.bind(currentRequest, currentRequestDelayedId), 250);
}

// Replace all "add to cart" sections but the quantity input
// in order to keep quantity field intact i.e.
function replaceAddToCartSections(data)
{
    let $productAddToCart = null;

    $(data.product_add_to_cart).each(function(index, value) {
        if ($(value).hasClass('product-add-to-cart')) {
            $productAddToCart = $(value);

            return false;
        }
    });

    if ($productAddToCart === null) {
        showError($('#product-availability'), 'An error occurred while processing your request');
    }
    const $addProductToCart = $('.product-add-to-cart');
    const productAvailabilitySelector = '.add';
    const productAvailabilityMessageSelector = '#product-availability';
    const productMinimalQuantitySelector = '.product-minimal-quantity';

    replaceAddToCartSection({
        $addToCartSnippet: $productAddToCart,
        $targetParent: $addProductToCart,
        targetSelector: productAvailabilitySelector
    });

    replaceAddToCartSection({
        $addToCartSnippet: $productAddToCart,
        $targetParent: $addProductToCart,
        targetSelector: productAvailabilityMessageSelector
    });

    replaceAddToCartSection({
        $addToCartSnippet: $productAddToCart,
        $targetParent: $addProductToCart,
        targetSelector: productMinimalQuantitySelector
    });
}

function replaceAddToCartSection(replacement)
{
    if ($(replacement.$targetParent.find(replacement.targetSelector)).length <= 0) {
        return;
    }
    const replace = replacement.$addToCartSnippet.find(replacement.targetSelector);

    if (replace.length > 0) {
        $(replacement.$targetParent.find(replacement.targetSelector)).replaceWith(replace[0].outerHTML);
    } else {
        $(replacement.$targetParent.find(replacement.targetSelector)).html('');
    }
}

function showError($container, textError)
{
    const $error = $(`<div class="alert alert-danger ajax-error" role="alert">${textError}</div>`);
    $container.replaceWith($error);
}
