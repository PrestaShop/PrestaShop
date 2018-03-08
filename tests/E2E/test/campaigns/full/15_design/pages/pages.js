const {Menu} = require('../../../../selectors/BO/menu.js');
const {PageCategory} = require('../../../../selectors/BO/desgin/pages');

let promise = Promise.resolve();

/****Example of pageCategory data ****
 * let pageCategoryData = {
 *  name: 'Category',
 *  parent_category: 'demo',
 *  description: 'Category description',
 *  meta_title: 'Category meta title',
 *  meta_description: 'Category meta description',
 *  meta_keywords: 'Category meta keywords',
 * };
 */

module.exports = {
  createPageCategory: function (pageCategoryData) {
    scenario('Create page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should click on "Add new page category" button', () => client.waitForExistAndClick(PageCategory.add_category_button));
      test('should set the "Name" input', () => client.waitAndSetValue(PageCategory.name_input, pageCategoryData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(PageCategory.enable_display_option));
      test('should select the "Parent category - home" option ', () => client.waitAndSelectByValue(PageCategory.parent_category_select, 1));
      test('should set the "Description" text area ', () => client.waitAndSetValue(PageCategory.description_textarea, pageCategoryData.description));
      test('should set the "Meta title" input ', () => client.waitAndSetValue(PageCategory.meta_title_input, pageCategoryData.meta_title));
      test('should set the "Meta description" input ', () => client.waitAndSetValue(PageCategory.meta_description_input, pageCategoryData.meta_description));
      test('should set the "Meta keywords" input ', () => client.waitAndSetValue(PageCategory.meta_keywords_input, pageCategoryData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(PageCategory.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(PageCategory.success_panel, '×\nSuccessful creation.'));
    }, 'design');
  },
  checkPageCategoryBO: function (categoryName) {
    scenario('check page category existence in the Back Office', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should check the existence of the created page category', () => {
        return promise
          .then(() => client.isVisible(PageCategory.name_filter))
          .then(() => client.search(PageCategory.name_filter, categoryName + date_time))
          .then(() => client.checkExistence(PageCategory.search_name_result, categoryName + date_time, 3))
      });
    }, 'design');
  },
  editPageCategory: function (categoryName, pageCategoryData) {
    scenario('Edit page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the customer email in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(PageCategory.name_filter))
          .then(() => client.search(PageCategory.name_filter, categoryName + date_time))
      });
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(PageCategory.dropdown_toggle))
          .then(() => client.waitForExistAndClick(PageCategory.edit_button))
      });
      test('should set the new "Name" input', () => client.waitAndSetValue(PageCategory.name_input, pageCategoryData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(PageCategory.enable_display_option));
      test('should select the "Parent category - home" option ', () => client.waitAndSelectByValue(PageCategory.parent_category_select, 1));
      test('should set the new "Description" text area ', () => client.waitAndSetValue(PageCategory.description_textarea, pageCategoryData.description));
      test('should set the new "Meta title" input ', () => client.waitAndSetValue(PageCategory.meta_title_input, pageCategoryData.meta_title));
      test('should set the new "Meta description" input ', () => client.waitAndSetValue(PageCategory.meta_description_input, pageCategoryData.meta_description));
      test('should set the new "Meta keywords" input ', () => client.waitAndSetValue(PageCategory.meta_keywords_input, pageCategoryData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(PageCategory.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(PageCategory.success_panel, '×\nSuccessful update.'));
    }, 'design');
  },
  deletePageCategory: function (categoryName) {
    scenario('Delete page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the customer email in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(PageCategory.name_filter))
          .then(() => client.search(PageCategory.name_filter, categoryName + date_time))
      });
      test('should click on "Delete" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(PageCategory.dropdown_toggle))
          .then(() => client.waitForExistAndClick(PageCategory.delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(PageCategory.success_panel, '×\nSuccessful deletion.'));
    }, 'design');
  }
};