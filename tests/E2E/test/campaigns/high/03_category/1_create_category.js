const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {CategorySubMenu} = require('../../../selectors/BO/catalogpage/category_submenu');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

scenario('Create "Category"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  scenario('Create a new "Category"', client => {
    test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
    test('should click on "Add new category" button', () => client.waitForExistAndClick(CategorySubMenu.new_category_button));
    test('should set the "Name" input', () => client.waitAndSetValue(CategorySubMenu.name_input, 'category' + date_time));
    test('should set the "Description" input', () => client.setEditorText(CategorySubMenu.category_description, 'category description'));
    test('should upload the picture', () => client.uploadPicture('category_image.png', CategorySubMenu.picture, 'image'));
    test('should upload the thumb picture', () => client.uploadPicture('category_miniature.png', CategorySubMenu.thumb_picture, 'image'));
    test('should set the "Title" input', () => client.waitAndSetValue(CategorySubMenu.title, 'test category'));
    test('should set the "Meta desciption" input', () => client.waitAndSetValue(CategorySubMenu.meta_description, 'this is the meta description'));
    test('should set the "Meta keywords" input', () => client.waitAndSetValue(CategorySubMenu.keyswords, 'keyswords'));
    test('should set the "Simplify url" input', () => client.waitAndSetValue(CategorySubMenu.simplify_URL_input, 'category' + date_time));
    test('should click on "Save" button', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(CategorySubMenu.save_button, 50))
        .then(() => client.getTextInVar(CategorySubMenu.category_number_span, "number_category"))
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
  }, 'category');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);

scenario('Check "Category" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  scenario('Check that the category is well created in the Back Office', client => {
    test('should go to "Category" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
    test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, 'category' + date_time));
    test('should check the description is equal a "category description"', () => client.checkTextValue(CategorySubMenu.description, 'category description'));
    test('should click on "Edit" action', () => client.clickOnAction(CategorySubMenu.update_button));
    test('should check category image', () => client.checkImage(CategorySubMenu.image_link));
    test('should check category image thumb', () => client.checkImage(CategorySubMenu.thumb_link));
    test('should check category title', () => client.checkAttributeValue(CategorySubMenu.title, 'value', 'test category'));
    test('should check category meta description', () => client.checkAttributeValue(CategorySubMenu.meta_description, 'value', 'this is the meta description'));
    test('should check category simplify url', () => client.checkAttributeValue(CategorySubMenu.simplify_URL_input, 'value', 'category' + date_time));
  }, 'category');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);

scenario('Check "Category" in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'category');
  scenario('Check that the category is well displayed in the Front Office', client => {
    test('should change front office language to english', () => client.changeLanguage('english'));
    test('should click on "All products" link', () => client.scrollWaitForExistAndClick(AccessPageFO.product_list_button, 50));
    test('should check the existence of the created category', () => {
      for (let i = 1; i < (parseInt(tab["number_category"]) + 1); i++) {
        promise = client.getCategoriesName(AccessPageFO.categories_list, i);
      }
      return promise.then(() => client.checkCategory(AccessPageFO.categories_list, 'category' + date_time));
    });
  }, 'category');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'category');
}, 'category', true);
