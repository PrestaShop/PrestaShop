const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
scenario('Install ZIP "Module"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  scenario('Install "Module"', client => {
    test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should click on "Upload a module" button', () => client.waitForExistAndClick(ModulePage.upload_button));
    test('should add zip file', () => client.addFile(ModulePage.zip_file_input,"v1.1.7-prestafraud.zip"));
    test('should verify that the module is installed', () => client.waitForVisibleAndCheckText(ModulePage,"Module installed!"));
    test('should click on close modal button', () => client.waitForExistAndClick(ModulePage.close_modal_button));
    test('should click on "Installed Modules"', () => client.goInstalledModule(ModulePage.installed_modules_tabs));
    test('should set the name of the module to search for it', () => client.searchModule(ModulePage, "prestafraud"));
    test('should check if the module "prestafraud" was installed', () => client.checkTextValue(ModulePage.built_in_module, "1", "contain"));


/*    test('should click on option button', () => client.waitForExistAndClick(ModulePage.option_button));
    test('should click on "Uninstall" button', () => client.waitForExistAndClick(ModulePage.uninstall_button));
    test('should check "Optional: delete module folder after uninstall." button', () => client.waitForExistAndClick(ModulePage.optional_button));
    test('should click on "Yes, uninstall it" button', () => client.waitForExistAndClick(ModulePage.uninstall_confirmation));*/
  }, 'module');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'module');
}, 'module');
