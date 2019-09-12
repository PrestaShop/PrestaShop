const {Menu} = require('../../selectors/BO/menu.js');
const {Positions} = require('../../selectors/BO/design/positions');
const {AccessPageBO} = require('../../selectors/BO/access_page');

let promise = Promise.resolve();


module.exports = {

  searchHook(hookName) {
    scenario('Access to position menu', client => {
      test('should go to "Design > Positions" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.positions_submenu));
      test('should search for a hook', () => client.waitAndSetValue(Positions.search_hook, hookName));
    }, 'common_client');
  },
  changePositionDragDrop() {
    scenario('Change position by drag and drop', client => {
      test('should change the order', () => client.dragAndDrop(Positions.hook_element.replace("%I", 3), Positions.hook_element.replace("%I", 1)));
      test('should verify the appearance of the green validation', () => {
        return promise
          .then(() => client.checkTextValue(Positions.success_alert, 'Update successful'))
          .then(() => client.pause(3000));
      });
      test('should click on "View my shop" then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should verify the changed position in Front Office', () => {
        return promise
          .then(() => client.waitForVisible(Positions.footer_information.replace("%O", 1)))
          .then(() => client.switchWindow(0));
      });
    }, 'common_client');
  },
  changePositionArrow() {
    scenario('Change position using arrow down icon', client => {
      test('should click on "arrow down" icon 2 times', () => {
        return promise
          .then(() => client.waitForExistAndClick(Positions.arrow_down_icon.replace("%I", 1)))
          .then(() => client.waitForExistAndClick(Positions.arrow_down_icon.replace("%I", 2)));
      });
      test('should click on "View my shop" button then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClickJs(AccessPageBO.shopname))
          .then(() => client.switchWindow(2));
      });
      test('should verify the changed position in Front Office', () => client.waitForVisible(Positions.footer_information.replace("%O", 3)));
    }, 'common_client');
  },
  transplantModule(moduleName, hookName, hook) {
    scenario('Transplant "' + moduleName + '" module to "' + hook + '" hook', client => {
      test('should go to "Design > Positions" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.positions_submenu));
      test('should click on "Transplant a module"', () => client.waitForExistAndClick(Positions.transplant_button));
      test('should select the "' + moduleName.toUpperCase() + '" module from the list', () => client.showSelect(moduleName));
      test('should select the "' + hookName.toUpperCase() + '" hook from the list', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.waitAndSelectByVisibleText(Positions.transplant_to_list, hookName));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(Positions.save_button));
    }, 'design');
  },
  unhookModule(moduleName, hookName) {
    scenario('Remove "' + moduleName + '" module from "' + hookName + '" hook', client => {
      test('should go to "Design > Positions" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.positions_submenu));
      test('should search the module by name', () => client.showSelect(moduleName, Positions.search_module_list));
      test('should click on "Unhook" action', () => client.clickOnAction(Positions.unhook_action_button.replace("%B", moduleName), Positions.block_action.replace("%B", moduleName), "unhook"));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Positions.success_panel, "The module was successfully removed from the hook."));
    }, 'design');
  },
};
