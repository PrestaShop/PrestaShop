const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {CategoryPageFO} = require('../../selectors/FO/category_page');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {CategorySubMenu} = require('../../selectors/BO/catalogpage/category_submenu');
const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();

/**** Example of category data ****
 * let categoryData = {
 *  name: 'category name',
 *  description: 'description of category',
 *  picture: 'category picture file',
 *  thumb_picture: 'category thumb picture file',
 *  thumb_menu_picture: 'category thumb menu picture file',
 *  meta_title: 'meta title category',
 *  meta_description: 'meta description category',
 *  meta_keywords: {
 *    1: 'first key',
 *    2: 'second key'
 *  },
 *  friendly_url: 'friendly url'
 * };
 */

module.exports = {
  createCategory(categoryData) {
    scenario('Create a new "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should click on "Add new category" button', () => client.waitForExistAndClick(CategorySubMenu.new_category_button));
      test('should set the "Name" input', () => client.waitAndSetValue(CategorySubMenu.name_input, categoryData.name + date_time));
      test('should set the "Description" textarea', () => client.setTextToEditor(CategorySubMenu.description_textarea, categoryData.description + date_time));
      test('should upload the picture', () => client.uploadPicture(categoryData.picture, CategorySubMenu.picture, 'image'));
      test('should upload the thumb picture', () => client.uploadPicture(categoryData.thumb_picture, CategorySubMenu.thumb_picture, 'image'));
      test('should upload the menu thumbnails', () => client.AddFile(categoryData.thumb_menu_picture, CategorySubMenu.thumb_menu_picture));
      test('should click on "UPLOAD FILES" button', () => client.waitForExistAndClick(CategorySubMenu.upload_files_button));
      test('should check that the menu thumbnails picture is well uploaded', () => client.checkImage(CategorySubMenu.thumbnail_menu_img));
      test('should check that the green validation of menu thumbnails is well displayed', () => client.checkTextValue(CategorySubMenu.thumbnail_success_alert, categoryData.thumb_menu_picture, 'contain', 1000));
      test('should set the "Meta title" input', () => client.waitAndSetValue(CategorySubMenu.title, categoryData.meta_title));
      test('should set the "Meta description" input', () => client.waitAndSetValue(CategorySubMenu.meta_description, categoryData.meta_description));
      Object.keys(categoryData.meta_keywords).forEach(function (key) {
        test('should set the "Meta keywords" input', () => {
          return promise
            .then(() => client.waitAndSetValue(CategorySubMenu.keyswords, categoryData.meta_keywords[key]))
            .then(() => client.keys('Enter'));
        });
      });
      test('should set the "Friendly url" input', () => client.waitAndSetValue(CategorySubMenu.simplify_URL_input, categoryData.friendly_url + date_time));
      test('should click on "Save" button', () => {
        return promise
          .then(() => client.scrollWaitForExistAndClick(CategorySubMenu.save_button, 50))
          .then(() => client.getTextInVar(CategorySubMenu.category_number_span, "number_category"));
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'category');
  },
  editCategory(categoryData, editedCategoryData) {
    scenario('Update the created "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Edit" action', () => client.clickOnAction(CategorySubMenu.update_button));
      test('should set the "Name" input', () => client.waitAndSetValue(CategorySubMenu.name_input, editedCategoryData.name + date_time));
      test('should set the "Description" textarea', () => client.setTextToEditor(CategorySubMenu.description_textarea, editedCategoryData.description + date_time));
      test('should set the "Meta title" input', () => client.waitAndSetValue(CategorySubMenu.title, editedCategoryData.meta_title));
      test('should set the "Meta description" input', () => client.waitAndSetValue(CategorySubMenu.meta_description, editedCategoryData.meta_description));
      for (let j in categoryData.meta_keywords) {
        test('should delete the old "Meta Keywords - ' + categoryData.meta_keywords[j] + '" input', () => client.waitForExistAndClick(CategorySubMenu.delete_tag_button.replace("%POS", Number(j))));
      }
      Object.keys(editedCategoryData.meta_keywords).forEach(function (key) {
        test('should set the "Meta keywords" input', () => {
          return promise
            .then(() => client.waitAndSetValue(CategorySubMenu.keyswords, editedCategoryData.meta_keywords[key]))
            .then(() => client.keys('Enter'));
        });
      });
      test('should set the "Friendly url" input', () => client.waitAndSetValue(CategorySubMenu.simplify_URL_input, editedCategoryData.friendly_url + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(CategorySubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
    }, 'category');
  },
  checkCategoryBO(categoryData) {
    scenario('Check category in BO', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Edit" action', () => client.clickOnAction(CategorySubMenu.update_button));
      test('should check category image', () => client.checkImage(CategorySubMenu.image_link));
      test('should check category image thumb', () => client.checkImage(CategorySubMenu.thumb_link));
      test('should check category title', () => client.checkAttributeValue(CategorySubMenu.title, 'value', categoryData.meta_title));
      test('should check category meta description', () => client.checkAttributeValue(CategorySubMenu.meta_description, 'value', categoryData.meta_description));
      test('should check category friendly url', () => client.checkAttributeValue(CategorySubMenu.simplify_URL_input, 'value', categoryData.friendly_url + date_time));
    }, 'category');
  },
  deleteCategoryWithDeleteMode(categoryData, deleteMode = 'linkanddisable') {
    scenario('Delete the created "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Delete" action', () => client.clickOnAction(CategorySubMenu.delete_button, CategorySubMenu.action_button, 'delete'));
      if(deleteMode === 'delete') {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_delete_radio));
      } else if (deleteMode === 'link') {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_link_radio));
      } else {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_link_disable_radio));
      }
      test('should delete category', () => client.scrollWaitForExistAndClick(CategorySubMenu.second_delete_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'category');
  },
  checkCategoryFO(categoryData, id) {
    scenario('Check that the category is well displayed in the Front Office', client => {
      test('should click on "Shop name" then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(id));
      });
      test('should change front office language to english', () => client.changeLanguage('english'));
      test('should click on "All products" link', () => client.scrollWaitForExistAndClick(AccessPageFO.product_list_button, 50));
      test('should check the existence of the created category', () => {
        for (let i = 1; i < (parseInt(tab["number_category"]) + 1); i++) {
          promise = client.getCategoriesName(AccessPageFO.categories_list, i);
        }
        return promise.then(() => client.checkCategory(AccessPageFO.categories_list, categoryData.name + date_time));
      });
      test('should click on "' + categoryData.name + date_time + '" category name', () => client.waitForExistAndClick(CategoryPageFO.category_name.replace('%NAME', categoryData.name + date_time)));
      test('should check the category title', () => client.checkTextValue(CategoryPageFO.category_title, (categoryData.name + date_time).toUpperCase()));
      test('should check the category description', () => client.checkTextValue(CategoryPageFO.category_description, categoryData.description + date_time));
      test('should check the category picture', () => client.checkImage(CategoryPageFO.category_picture));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'category');
  }
};
