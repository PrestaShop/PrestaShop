/**
 * Nested categories management
 */
export default function() {
  var nestedCategoriesForm = $('#form_step1_categories');
  return {
    'init': function() {

      if (0 === nestedCategoriesForm.length) {
        return;
      }

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
        const findClosestCheckedCategory = function(category) {
          const parent = category.closest('ul').parent();
          const parentCategory = parent.find('> div > label > input');

          if (!parent.is('li')) {
            return nestedCategoriesForm.find('input[type="checkbox"]:checked').first()
          } else if (parentCategory.prop('checked')) {
            return parentCategory;
          }
          return findClosestCheckedCategory(parent);
        };

        var categoryId = $(this).val();
        var category = nestedCategoriesForm.find('input[value="' + categoryId + '"].category');
        var defaultcat = nestedCategoriesForm.find('input[value="' + categoryId + '"].default-category');

        if (1 > nestedCategoriesForm.find('input[type="checkbox"]').filter(':checked').length) {
          category.prop('checked', true);
        } else if (false === category.is(':checked') && true === defaultcat.is(':checked')) {
          const newCategory = findClosestCheckedCategory(category);

          defaultCategory.check(newCategory.val());
          productCategoriesTags.checkDefaultCategory(newCategory.val());
        }
      });
    }
  };
}
