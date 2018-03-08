const {Menu} = require('../../../../selectors/BO/menu.js');
const {Category} = require('../../../../selectors/BO/desgin/pages');

let promise = Promise.resolve();

/****Example of category data ****
 * let categoryData = {
 *  name: 'Category',
 *  parent_category: 'demo',
 *  description: 'Category description',
 *  meta_title: 'Category meta title',
 *  meta_description: 'Category meta description',
 *  meta_keywords: 'Category meta keywords',
 * };
 */

module.exports = {
  createCategory: function (categoryData) {
    scenario('Create page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should click on "Add new page category" button', () => client.waitForExistAndClick(Category.add_category_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Category.name_input, categoryData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Category.enable_display_option));
      test('should select the "Parent category - home" option ', () => client.waitAndSelectByValue(Category.parent_category_select, 1));
      test('should set the "Description" text area ', () => client.waitAndSetValue(Category.description_textarea, categoryData.description));
      test('should set the "Meta title" input ', () => client.waitAndSetValue(Category.meta_title_input, categoryData.meta_title));
      test('should set the "Meta description" input ', () => client.waitAndSetValue(Category.meta_description_input, categoryData.meta_description));
      test('should set the "Meta keywords" input ', () => client.waitAndSetValue(Category.meta_keywords_input, categoryData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Category.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Category.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  checkCategoryBO: function (categoryName) {
    scenario('check page category existence in the Back Office', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should check the existence of the created page category', () => {
        return promise
          .then(() => client.isVisible(Category.name_filter))
          .then(() => client.search(Category.name_filter, categoryName + date_time))
          .then(() => client.checkExistence(Category.search_name_result, categoryName + date_time, 3))
      });
    }, 'common_client');
  },
  editCategory: function (categoryName, categoryData) {
    scenario('Edit page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Category.name_filter))
          .then(() => client.search(Category.name_filter, categoryName + date_time))
      });
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Category.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Category.edit_button))
      });
      test('should set the new "Name" input', () => client.waitAndSetValue(Category.name_input, categoryData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Category.enable_display_option));
      test('should select the "Parent category - home" option ', () => client.waitAndSelectByValue(Category.parent_category_select, 1));
      test('should set the new "Description" text area ', () => client.waitAndSetValue(Category.description_textarea, categoryData.description));
      test('should set the new "Meta title" input ', () => client.waitAndSetValue(Category.meta_title_input, categoryData.meta_title));
      test('should set the new "Meta description" input ', () => client.waitAndSetValue(Category.meta_description_input, categoryData.meta_description));
      test('should set the new "Meta keywords" input ', () => client.waitAndSetValue(Category.meta_keywords_input, categoryData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Category.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Category.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  deleteCategory: function (categoryName) {
    scenario('Delete page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Category.name_filter))
          .then(() => client.search(Category.name_filter, categoryName + date_time))
      });
      test('should click on "Delete" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Category.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Category.delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Category.success_panel, '×\nSuccessful deletion.'));
    }, 'common_client');
  },
  deleteCategoryWithBulkActions: function (categoryName) {
    scenario('Delete page category with bulk actions', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Category.name_filter))
          .then(() => client.search(Category.name_filter, categoryName))
      });
      test('should click on the "Bulk actions - Select all" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Category.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Category.bulk_actions_select_all_button))
      });
      test('should click on the "Bulk actions - Delete selected" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Category.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Category.bulk_actions_delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Category.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'common_client');
  }
};