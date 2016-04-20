/**
 * Default category management
 */
var defaultCategory = (function() {
  var defaultCategoryForm = $('#form_step1_id_category_default');
  return {
    'init': function () {
      /** Populate category tree with the default category **/
      var defaultCategoryId = defaultCategoryForm.find('input:checked').val();
      productCategoriesTags.checkDefaultCategory(defaultCategoryId);

      /** Hide the default form, if javascript disabled it will be visible and so we
       * still can select a default category using the form
       */
      defaultCategoryForm.hide();
    },
    /**
     * Check the radio bouton with the selected value
     */
    'check': function(value) {
      var defaultCategory = defaultCategoryForm.find('input[value="'+value+'"]');
      if (defaultCategory.is(':checked')) {
        console.log('already checked');
        return;
      }
      else {
        var previousDefault = defaultCategoryForm.find('input:checked');
        previousDefault.attr('checked', false);
        defaultCategory.attr('checked', 'checked');
      }
    }
  };
})();

BOEvent.on("Product Default category Management started", function initDefaultCategoryManagement() {
  defaultCategory.init();
}, "Back office");
