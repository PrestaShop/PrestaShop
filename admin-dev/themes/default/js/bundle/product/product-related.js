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
      var searchProductsBar = $('#form_step1_related_products');

      addButton.on('click', function(e) {
        e.preventDefault();
        relatedContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', function(e) {
        e.preventDefault();
        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function onContinue(){
            var items = productItems.find('li').toArray();

            items.forEach(function removeItem(item) {
              console.log(item);
              item.remove();
            });
            searchProductsBar.val('');

            relatedContent.addClass('hide');
            addButton.show();
          }
        }).show();
      });
    }
  };
})();

BOEvent.on("Product Related Management started", function initRelatedProductManagement() {
  relatedProduct.init();
}, "Back office");

