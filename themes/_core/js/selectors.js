import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.selectors = {
  quantityWanted: '#quantity_wanted',
  product: {
    imageContainer: '.image-container',
    container: '.product-container',
    availability: '#product-availability',
    actions: '.product-actions',
    minimalQuantity: '.product-minimal-quantity',
  },
};

$(document).ready(() => {
  prestashop.emit('selectorsInit');
});
