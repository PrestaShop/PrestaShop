/**
 * Related product management
 */
window.relatedProduct = (function () {
  return {
    init() {
      const addButton = $('#add-related-product-button');
      const resetButton = $('#reset_related_product');
      const relatedContent = $('#related-content');
      const productItems = $('#form_step1_related_products-data');
      const searchProductsBar = $('#form_step1_related_products');

      addButton.on('click', (e) => {
        e.preventDefault();
        relatedContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', (e) => {
        e.preventDefault();
        // eslint-disable-next-line
        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue: function onContinue() {
            const items = productItems.find('li').toArray();

            items.forEach((item) => {
              console.log(item);
              item.remove();
            });
            searchProductsBar.val('');

            relatedContent.addClass('hide');
            addButton.show();
          },
        }).show();
      });
    },
  };
}());

// eslint-disable-next-line
BOEvent.on('Product Related Management started', () => {
  relatedProduct.init();
}, 'Back office');
