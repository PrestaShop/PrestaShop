/**
 * Manufacturer management
 */
var manufacturer = (function() {
  return {
    'init': function() {
      var addButton = $('#add_brand_button');
      var resetButton = $('#reset_brand_product');
      var manufacturerContent = $('#manufacturer-content');
      var selectManufacturer = $('#form_step1_id_manufacturer');

      /** Click event on the add button */
      addButton.on('click', function(e) {
        e.preventDefault();
        manufacturerContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', function(e) {
        e.preventDefault();
        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
          onContinue: function(){
            manufacturerContent.addClass('hide');
            selectManufacturer.val('').trigger('change');
            addButton.show();
          }
        }).show();
      });
    }
  };
})();

BOEvent.on("Product Manufacturer Management started", function initManufacturerManagement() {
  manufacturer.init();
}, "Back office");
