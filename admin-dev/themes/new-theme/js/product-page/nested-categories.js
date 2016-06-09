import $ from 'jquery';

/**
 * Nested categories management
 */
export default function() {
  var nestedCategoriesForm = $('#form_step1_categories');
  return {
    'init': function() {
      nestedCategoriesForm.categorytree();
      this.removeDefaultIfNeeded();

      // now we can select default category from nested Categories even if it's not related from a "code" point of view.
      nestedCategoriesForm.on('change', 'input.default-category', function updateDefaultCategory() {
        var categoryId = $(this).val();
        /* we can't select a default category if category is not selected
         * that's why we check category first instead of warn user.
         */
        var category = nestedCategoriesForm.find('input[value="' + categoryId + '"].category');
        var defaultcat = nestedCategoriesForm.find('input[value="' + categoryId + '"].default-category');

        if (false === category.is(':checked') && true === defaultcat.is(':checked')) {
          category.trigger('click');
        }
        defaultCategory.check(categoryId);
      });
    },
    'removeDefaultIfNeeded': function() {
      /**
       * What if we unselect category when it's a default category ?
       */
      nestedCategoriesForm.on('change', 'input.category', function removeDefaultCategoryIfNotSelected() {
        var categoryId = $(this).val();
        var category = nestedCategoriesForm.find('input[value="' + categoryId + '"].category');
        var defaultcat = nestedCategoriesForm.find('input[value="' + categoryId + '"].default-category');

        if (false === category.is(':checked') && true === defaultcat.is(':checked')) {
          /* default category is setup to the first one */
          defaultcat.prop('checked', false);
          defaultCategory.reset();
        }
      });
    }
  };
}
