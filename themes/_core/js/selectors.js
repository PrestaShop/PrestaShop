import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.selectors = {
  quantityWanted: '#quantity_wanted',
  product: {
    imageContainer: '.image-container',
    container: '.product-container',
    availability: '#product-availability',
    actions: '.product-actions',
    variants: '.product-variants',
    refresh: '.product-refresh',
    miniature: '.js-product-miniature',
    minimalQuantity: '.product-minimal-quantity',
  },
  listing: {
    quickview: '.quick-view',
  },
  checkout: {
    form: '.checkout-step form',
  },
};

$(document).ready(() => {
  prestashop.emit('selectorsInit');
});
