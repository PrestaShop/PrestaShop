/**
 * Manufacturer management
 */
window.manufacturer = (function () {
  return {
    init() {
      const addButton = $('#add_brand_button');
      const resetButton = $('#reset_brand_product');
      const manufacturerContent = $('#manufacturer-content');
      const selectManufacturer = $('#form_step1_id_manufacturer');

      /** Click event on the add button */
      addButton.on('click', (e) => {
        e.preventDefault();
        manufacturerContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', (e) => {
        e.preventDefault();
        // eslint-disable-next-line
        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue() {
            manufacturerContent.addClass('hide');
            selectManufacturer.val('').trigger('change');
            addButton.show();
          },
        }).show();
      });
    },
  };
}());

// eslint-disable-next-line
BOEvent.on('Product Manufacturer Management started', () => {
  manufacturer.init();
}, 'Back office');
