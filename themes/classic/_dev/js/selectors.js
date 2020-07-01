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
};

$(document).ready(() => {
  prestashop.emit('selectorsInit');
});
