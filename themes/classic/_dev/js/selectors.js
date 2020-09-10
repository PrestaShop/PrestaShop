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
import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.themeSelectors = {
  product: {
    activeTabs: '.tabs .nav-link.active',
    imagesModal: '.js-product-images-modal',
    thumb: '.js-thumb',
    arrows: '.js-arrows',
    selected: '.selected',
    modalProductCover: '.js-modal-product-cover',
    cover: '.js-qv-product-cover',
  },
  listing: {
    searchFilterToggler: '#search_filter_toggler',
    searchFiltersWrapper: '#search_filters_wrapper',
    searchFilterControls: '#search_filter_controls',
    searchFilters: '#search_filters',
    activeSearchFilters: '#js-active-search-filters',
    listTop: '#js-product-list-top',
    list: '#js-product-list',
    listBottom: '#js-product-list-bottom',
    listHeader: '#js-product-list-header',
    searchFiltersClearAll: '.js-search-filters-clear-all',
    searchLink: '.js-search-link',
  },
  order: {
    returnForm: '#order-return-form',
  },
  arrowDown: '.arrow-down',
  arrowUp: '.arrow-up',
  clear: '.clear',
  fileInput: '.js-file-input',
  contentWrapper: '#content-wrapper',
  footer: '#footer',
  modalContent: '.js-modal-content',
  modal: '#modal',
  checkout: {
    termsLink: '.js-terms a',
    giftCheckbox: '.js-gift-checkbox',
    imagesLink: '.card-block .cart-summary-products p a',
    carrierExtraContent: '.carrier-extra-content',
  },
};

$(document).ready(() => {
  prestashop.emit('themeSelectorsInit');
});
