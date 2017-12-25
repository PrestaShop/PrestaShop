const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {CategorySubMenu} = require('../../../selectors/BO/catalogpage/category_submenu');

scenario('Delete "Category"', () => {
    scenario('Login in the Back Office', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'category');
    scenario('Delete the created "Category"', client => {
        test('should go to "Category" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, CategorySubMenu.submenu));
        test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, 'category' + date_time));
        test('should click on "Delete" action', () => client.clickOnAction(CategorySubMenu.delete_button, CategorySubMenu.action_button, 'delete'));
        test('should delete category', () => client.waitForExistAndClick(CategorySubMenu.second_delete_button));
        test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'category');
    scenario('Logout from the Back Office', client => {
        test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'category');
}, 'category', true);

scenario('Create "Category"', () => {
    scenario('Login in the Back Office', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'category');
    scenario('Create a new "Category"', client => {
        test('should go to "Category" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, CategorySubMenu.submenu));
        test('should click on "Add new category" button', () => client.waitForExistAndClick(CategorySubMenu.new_category_button));
        test('should set the "Name" input', () => client.waitAndSetValue(CategorySubMenu.name_input, 'category' + date_time));
        test('should upload the picture', () => client.uploadPicture('category_image.png', CategorySubMenu.picture, 'image'));
        test('should upload the thumb picture', () => client.uploadPicture('category_miniature.png', CategorySubMenu.thumb_picture, 'image'));
        test('should set the "Title" input', () => client.waitAndSetValue(CategorySubMenu.title, 'test category'));
        test('should set the "Meta desciption" input', () => client.waitAndSetValue(CategorySubMenu.meta_description, 'this is the meta description'));
        test('should set the "Meta keywords" input', () => client.waitAndSetValue(CategorySubMenu.keyswords, 'keyswords'));
        test('should set the "Simplify url" input', () => client.waitAndSetValue(CategorySubMenu.simplify_URL_input, 'category' + date_time));
        test('should click on "Save" button', () => {
            let promise = Promise.resolve();
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

scenario('Delete "Category"', () => {
    scenario('Login in the Back Office', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'category');
    scenario('Delete category with action group', client => {
        test('should go to "Category" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, CategorySubMenu.submenu));
        test('should search for category ', () => client.searchByValue(CategorySubMenu.search_input, CategorySubMenu.search_button, 'category' + date_time));
        test('should select the category that we will deleted', () => client.waitForExistAndClick(CategorySubMenu.select_category));
        test('should click on "Delete selected" action', () => client.clickOnAction(CategorySubMenu.delete_action_group_button, CategorySubMenu.action_group_button, 'delete', true));
        test('should click on "Delete" button', () => client.waitForExistAndClick(CategorySubMenu.second_delete_button));
        test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'category');
    scenario('Logout from the Back Office', client => {
        test('should logout successfully from Back Office', () => client.signOutBO());
    }, 'category');
}, 'category', true);