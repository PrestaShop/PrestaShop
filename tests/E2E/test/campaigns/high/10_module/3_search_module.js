const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
let promise = Promise.resolve();

scenario('Search "Contact form Modules"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  scenario('Check that the result of search modules is correct', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, "contact form"));
    test('should click on "Search" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(ModulePage.selection_search_button))
        .then(() => client.getTextInVar(ModulePage.modules_number, "modules_number"))
    });
    test('should check the results of the module after search', () => {
      let length = parseInt((tab["modules_number"].match(/[0-9]+/g)[0]));
      for (let i = 1; i <= length; i++) {
        moduleLists[i-1] = {};
        for (let attr in moduleObject) {
          promise = client.getModuleAttributes(ModulePage.list_module, attr, i)
        }
      }
      return promise
        .then(() => client.checkModuleData(length));
    });
  }, 'module');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'module');
}, 'module', true);