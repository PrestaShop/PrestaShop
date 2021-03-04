/**
 * Nested categories management
 */
export default function () {
  const nestedCategoriesForm = $('#form_step1_categories');

  return {
    init() {
      if (nestedCategoriesForm.length === 0) {
        return;
      }

      nestedCategoriesForm.categorytree();
      this.removeDefaultIfNeeded();

      // now we can select default category from nested Categories even if it's not related from a "code" point of view.
      nestedCategoriesForm.on('change', 'input.default-category', function updateDefaultCategory() {
        const categoryId = $(this).val();
        /* we can't select a default category if category is not selected
         * that's why we check category first instead of warn user.
         */
        const category = nestedCategoriesForm.find(`input[value="${categoryId}"].category`);
        const defaultcat = nestedCategoriesForm.find(`input[value="${categoryId}"].default-category`);

        if (category.is(':checked') === false && defaultcat.is(':checked') === true) {
          category.trigger('click');
        }
        window.defaultCategory.check(categoryId);
      });
    },
    removeDefaultIfNeeded() {
      /**
       * What if we unselect category when it's a default category ?
       */
      nestedCategoriesForm.on('change', 'input.category', function removeDefaultCategoryIfNotSelected() {
        const findClosestCheckedCategory = function (category) {
          const parent = category.closest('ul').parent();
          const parentCategory = parent.find('> div > label > input');

          if (!parent.is('li')) {
            return nestedCategoriesForm.find('input[type="checkbox"]:checked').first();
          } if (parentCategory.prop('checked')) {
            return parentCategory;
          }
          return findClosestCheckedCategory(parent);
        };

        const categoryId = $(this).val();
        const category = nestedCategoriesForm.find(`input[value="${categoryId}"].category`);
        const defaultcat = nestedCategoriesForm.find(`input[value="${categoryId}"].default-category`);

        if (nestedCategoriesForm.find('input[type="checkbox"]').filter(':checked').length < 1) {
          category.prop('checked', true);
        } else if (category.is(':checked') === false && defaultcat.is(':checked') === true) {
          const newCategory = findClosestCheckedCategory(category);

          window.defaultCategory.check(newCategory.val());
          window.productCategoriesTags.checkDefaultCategory(newCategory.val());
        }
      });
    },
  };
}
