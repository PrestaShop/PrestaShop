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
      defaultCategoryForm.find('input[value="'+value+'"]').prop('checked', true);
    },

    'isChecked': function(value) {
      return defaultCategoryForm.find('input[value="'+value+'"]').is(':checked');
    },

    /**
     * When the category selected as a default is unselected
     * The default category MUST be a selected category
     */
    'reset': function() {
      var firstInput = defaultCategoryForm.find('input:first-child');
      firstInput.prop('checked', true);
      var categoryId = firstInput.val();
      productCategoriesTags.checkDefaultCategory(categoryId);
    }
  };
})();

BOEvent.on("Product Default category Management started", function initDefaultCategoryManagement() {
  defaultCategory.init();
}, "Back office");
