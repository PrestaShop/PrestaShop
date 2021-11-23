/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
import $ from 'jquery';
import prestashop from 'prestashop';

function setUpCheckout() {
  $(prestashop.themeSelectors.checkout.termsLink).on('click', (event) => {
    event.preventDefault();
    let url = $(event.target).attr('href');

    if (url) {
      // TODO: Handle request if no pretty URL
      url += '?content_only=1';
      $.get(url, (content) => {
        $(prestashop.themeSelectors.modal)
          .find(prestashop.themeSelectors.modalContent)
          .html($(content).find('.page-cms').contents());
      }).fail((resp) => {
        prestashop.emit('handleError', {eventType: 'clickTerms', resp});
      });
    }

    $(prestashop.themeSelectors.modal).modal('show');
  });

  $(prestashop.themeSelectors.checkout.giftCheckbox).on('click', () => {
    $('#gift').slideToggle();
  });
}

function toggleImage() {
  // Arrow show/hide details Checkout page
  $(prestashop.themeSelectors.checkout.imagesLink).on('click', function () {
    const icon = $(this).find('i.material-icons');

    if (icon.text() === 'expand_more') {
      icon.text('expand_less');
    } else {
      icon.text('expand_more');
    }
  });
}

$(document).ready(() => {
  if ($('body#checkout').length === 1) {
    setUpCheckout();
    toggleImage();
  }

  prestashop.on('updatedDeliveryForm', (params) => {
    if (typeof params.deliveryOption === 'undefined' || params.deliveryOption.length === 0) {
      return;
    }
    // Hide all carrier extra content ...
    $(prestashop.themeSelectors.checkout.carrierExtraContent).hide();
    // and show the one related to the selected carrier
    params.deliveryOption.next(prestashop.themeSelectors.checkout.carrierExtraContent).slideDown();
  });
});
