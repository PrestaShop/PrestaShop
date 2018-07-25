const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {CategoryPageFO} = require('../../selectors/FO/category_page');
const {CategorySubMenu} = require('../../selectors/BO/catalogpage/category_submenu');
const {ProductList} = require('../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
const {Menu} = require('../../selectors/BO/menu.js');
const {ModulePage} = require('../../selectors/BO/module_page');
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
      test('should set the "Description" textarea', () => client.setEditorText(CategorySubMenu.description_textarea, categoryData.description + date_time));
      test('should upload the picture', () => client.uploadPicture(categoryData.picture, CategorySubMenu.picture, 'image'));
      test('should upload the thumb picture', () => client.uploadPicture(categoryData.thumb_picture, CategorySubMenu.thumb_picture, 'image'));
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
  configureMainMenu() {
    scenario('Add the created category to the top menu link', client => {
      test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_services_submenu));
      test('should set the module name in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, "ps_mainmenu"));
      test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
      test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.configure_module_theme_button));
      test('should choose the created category from the available items', () => client.waitForExistAndClick(ModulePage.MainMenuPage.available_item_list.replace('%ID', param['id_category'])));
      test('should click on "Add item" button', () => client.waitForExistAndClick(ModulePage.MainMenuPage.add_item_button));
      test('should check that the menu is well added to the selected items list', () => client.isExisting(ModulePage.MainMenuPage.selected_item_list.replace('%ID', param['id_category']), 1000));
      test('should click on "Save" button', () => client.waitForExistAndClick(ModulePage.MainMenuPage.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe settings have been updated.'));
    }, 'category');
  },
  editCategory(categoryData, editedCategoryData) {
    scenario('Update the created "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Edit" action', () => client.clickOnAction(CategorySubMenu.update_button));
      test('should set the "Name" input', () => client.waitAndSetValue(CategorySubMenu.name_input, editedCategoryData.name + date_time));
      test('should set the "Description" textarea', () => client.setEditorText(CategorySubMenu.description_textarea, editedCategoryData.description + date_time));
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
      test('should click on "Reset" button', () => client.waitForExistAndClick(CategorySubMenu.reset_button));
    }, 'category');
  },
  editParentCategory(categoryData, parentCategory) {
    scenario('Update the parent of the created "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Edit" action', () => client.clickOnAction(CategorySubMenu.update_button));
      test('should open all categories', () => client.waitForExistAndClick(CategorySubMenu.expand_all_button));
      test('should choose "Accessories" from the list', () => {
        return promise
          .then(() => client.waitForExistAndClick(CategorySubMenu.parent_category.replace('%NAME', parentCategory), 2000))
          .then(() => client.getAttributeInVar(CategorySubMenu.parent_category.replace('%NAME', parentCategory), 'value', "parent_category_id"))
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(CategorySubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(CategorySubMenu.reset_button));
    }, 'category');
  },
  checkCategoryBO(categoryData) {
    scenario('Check category in BO', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Edit" action', () => {
        return promise
          .then(() => client.clickOnAction(CategorySubMenu.update_button))
          .then(() => client.getParamFromURL('id_category', 2000));
      });
      test('should check the category name', () => client.checkAttributeValue(CategorySubMenu.name_input, 'value', categoryData.name + date_time));
      test('should check that the image is well displayed', () => client.checkImage(CategorySubMenu.image_link));
      test('should check that the image thumb is well displayed', () => client.checkImage(CategorySubMenu.thumb_link));
      test('should check the category title', () => client.checkAttributeValue(CategorySubMenu.title, 'value', categoryData.meta_title));
      test('should check the category meta description', () => client.checkAttributeValue(CategorySubMenu.meta_description, 'value', categoryData.meta_description));
      test('should check the category friendly url', () => client.checkAttributeValue(CategorySubMenu.simplify_URL_input, 'value', categoryData.friendly_url + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(CategorySubMenu.save_button));
      test('should click on "Reset" button', () => client.waitForExistAndClick(CategorySubMenu.reset_button));
    }, 'category');
  },
  deleteCategoryWithDeleteMode(categoryData, parentCategory = '', deleteMode = 'linkanddisable') {
    scenario('Delete the created "Category"', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      if(parentCategory !== '') {
        test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, parentCategory));
        test('should click on "View" action', () => {
          return promise
            .then(() => client.waitForExistAndClick(CategorySubMenu.view_button))
            .then(() => client.pause(3000));
        });
      }
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(CategorySubMenu.action_button, 1000));
      test('should click on "Delete" action', () => client.waitForExistAndClick(CategorySubMenu.delete_button));
      if (deleteMode === 'delete') {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_delete_radio));
      } else if (deleteMode === 'link') {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_link_radio));
      } else {
        test('should choose the delete mode radio button', () => client.scrollWaitForExistAndClick(CategorySubMenu.mode_link_disable_radio));
      }
      test('should delete category', () => client.scrollWaitForExistAndClick(CategorySubMenu.second_delete_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should check that the product is not existing', () => client.checkTextValue(CategorySubMenu.search_no_results, 'No records found', 'contain'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(CategorySubMenu.reset_button));
    }, 'category');
  },
  deleteCategoryWithBulkAction(categoryData) {
    scenario('Delete category with action group', client => {
      test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should select the category to delete', () => client.waitForExistAndClick(CategorySubMenu.select_category, 2000));
      test('should click on "Delete selected" action', () => client.clickOnAction(CategorySubMenu.delete_action_group_button, CategorySubMenu.action_group_button, 'delete', true));
      test('should click on "Delete" button', () => client.waitForExistAndClick(CategorySubMenu.second_delete_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully deleted.'));
      test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, categoryData.name + date_time));
      test('should check that the product does not exist', () => client.checkTextValue(CategorySubMenu.search_no_results, 'No records found', 'contain'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(CategorySubMenu.reset_button));
    }, 'category');
  },
  checkCategoryFO(categoryData, id, parentCategory = false) {
    scenario('Check that the category is well displayed in the Front Office', client => {
      test('should click on "Shop name" then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(id));
      });
      test('should change front office language to english', () => client.changeLanguage('english'));
      test('should click on "All products" link', () => client.scrollWaitForExistAndClick(AccessPageFO.product_list_button, 50));
      if(parentCategory) {
        test('should click on "' + categoryData.name + date_time + '" category name', () => client.waitForExistAndClick(CategoryPageFO.category_top_menu.replace('%ID', param['id_category']).replace('%POS', '2'), 2000));
        test('should check the existence of the created category inside the parent', () => client.checkCategoryInsideParent(CategoryPageFO.category_top_menu.replace('%ID', global.tab['parent_category_id']).replace('%POS', '1'), CategoryPageFO.category_top_menu.replace('%ID', param['id_category']).replace('%POS', '1')));
        test('should check the breadcrumb of the created category', () => client.checkBreadcrumbInFo(CategoryPageFO.breadcrumb_path, "Accessories", categoryData.name));
      } else {
        test('should check the existence of the created category', () => {
          for (let i = 1; i < (parseInt(tab["number_category"]) + 1); i++) {
            promise = client.getCategoriesName(AccessPageFO.categories_list, i);
          }
          return promise.then(() => client.checkCategory(AccessPageFO.categories_list, categoryData.name + date_time));
        });
        test('should click on "' + categoryData.name + date_time + '" category name', () => client.waitForExistAndClick(CategoryPageFO.category_name.replace('%NAME', categoryData.name + date_time)));
        test('should check the existence of the created category in the top menu', () => client.isExisting(CategoryPageFO.category_top_menu.replace('%ID', param['id_category']).replace('%POS', '1')));
        test('should check the category title', () => client.checkTextValue(CategoryPageFO.category_title, (categoryData.name + date_time).toUpperCase()));
        test('should check the category description', () => client.checkTextValue(CategoryPageFO.category_description, categoryData.description + date_time));
        test('should check the category picture', () => client.checkImage(CategoryPageFO.category_picture));
      }
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'category');
  },

  checkDeleteModeBO(productData, deleteMode, deletedProduct = false) {
    scenario('Check the delete mode in the Back Office', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      if (deletedProduct) {
        test('should search for the created product', () => client.searchProductByName(productData.name + date_time));
        test('should check that the product does not exist', () => client.checkTextValue(ProductList.search_no_results, 'There is no result for this search. Update your filters to view other products.'));
        test('should click on "Reset" button', () => client.waitForVisibleAndClick(ProductList.reset_button, 1000));
        test('should check that the first product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 1).replace('%ACTION', 'enable'), 'check'));
        test('should check that the first product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 1), 'Home'));
        test('should check that the second product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 2).replace('%ACTION', 'enable'), 'check'));
        test('should check that the second product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 2), 'Home'));
      } else {
        if (deleteMode === 'linkanddisable') {
          test('should check that the first product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 1).replace('%ACTION', 'enable'), 'check'));
          test('should check that the first product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 1), 'Home'));
          test('should check that the second product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 2).replace('%ACTION', 'enable'), 'check'));
          test('should check that the second product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 2), 'Home'));
          test('should check that the third product status is disabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 3).replace('%ACTION', 'disable'), 'clear'));
          test('should check that the third product category is equal to "Accessories"', () => client.checkTextValue(ProductList.product_category.replace('%I', 3), 'Accessories'));
        } else if (deleteMode === 'link') {
          test('should check that the first product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 1).replace('%ACTION', 'enable'), 'check'));
          test('should check that the first product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 1), 'Home'));
          test('should check that the second product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 2).replace('%ACTION', 'enable'), 'check'));
          test('should check that the second product category is equal to "Home"', () => client.checkTextValue(ProductList.product_category.replace('%I', 2), 'Home'));
          test('should check that the third product status is enabled', () => client.checkTextValue(ProductList.product_status.replace('%I', 3).replace('%ACTION', 'enable'), 'check'));
          test('should check that the third product category is equal to "Accessories"', () => client.checkTextValue(ProductList.product_category.replace('%I', 3), 'Accessories'));
        }
      }
    }, 'product/check_product');
  },

  checkDeleteModeFO(productData, deleteMode = 'link', closedWindow = true) {
    scenario('Check the delete mode in the Front Office', client => {
      if (closedWindow) {
        test('should click on "Shop name" then go to the Front Office', () => {
          return promise
            .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
            .then(() => client.switchWindow(1));
        });
      } else {
        test('should go to the Front Office', () => client.switchWindow(1));
      }
      Object.keys(productData).forEach(function (keys) {
        test('should search for the created product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button ,productData[keys].name + date_time));
        if (keys.toString() === '0' && deleteMode !== 'link') {
          test('should check that the product does not exist', () => client.isNotExisting(SearchProductPage.product_result_name, 2000));
        } else {
          test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
          test('should set the shop language to "English"', () => client.changeLanguage('english'));
          test('should check that the product category is equal to "Home"', () => client.checkTextValue('(//*[@id="wrapper"]//nav[contains(@class, "breadcrumb")]//span)[1]', 'Home'));
        }
      });
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'product/product');
  }
};
