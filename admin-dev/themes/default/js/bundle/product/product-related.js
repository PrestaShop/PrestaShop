/**
 * Related product management
 */
var relatedProduct = (function() {
  return {
    'init': function() {
      var addButton = $('#add-related-product-button');
      var resetButton = $('#reset_related_product');
      var relatedContent = $('#related-content');
      var productItems = $('#form_step1_related_products-data');
      addButton.on('click', function(e) {
        e.preventDefault();
        relatedContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', function(e) {
        e.preventDefault();
        productItems.remove();
        relatedContent.addClass('hide');
        addButton.show();
      });
    }
  };
})();

BOEvent.on("Product Related Management started", function initRelatedProductManagement() {
  relatedProduct.init();
}, "Back office");

