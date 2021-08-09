/**
 * Default category management
 */
const defaultCategory = (function () {
  const defaultCategoryForm = $('#form_step1_id_category_default');

  return {
    init() {
      // Populate category tree with the default category
      const defaultCategoryId = defaultCategoryForm.find('input:checked').val();
      productCategoriesTags.checkDefaultCategory(defaultCategoryId);

      /** Hide the default form, if javascript disabled it will be visible and so we
       * still can select a default category using the form
       */
      defaultCategoryForm.hide();
    },

    /**
     * Check the radio bouton with the selected value
     */
    check(value) {
      defaultCategoryForm.find(`input[value="${value}"]`).prop('checked', true);
    },

    isChecked(value) {
      return defaultCategoryForm.find(`input[value="${value}"]`).is(':checked');
    },

    /**
     * When the category selected as a default is unselected
     * The default category MUST be a selected category
     */
    reset() {
      const firstInput = defaultCategoryForm.find('input:first-child');
      firstInput.prop('checked', true);
      const categoryId = firstInput.val();
      productCategoriesTags.checkDefaultCategory(categoryId);
    },
  };
}());

window.defaultCategory = defaultCategory;

BOEvent.on('Product Default category Management started', () => {
  defaultCategory.init();
}, 'Back office');
