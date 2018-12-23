const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();

module.exports = {
  checkConfigPage: function (client, ModulePage, moduleTechName) {
    test('should click on "see more" link if existing', () => {
      return promise
        .then(() => client.isVisible(ModulePage.see_more_link, 3000))
        .then(() => {
          if (global.isVisible) {
            client.scrollWaitForVisibleAndClick(ModulePage.see_more_link);
          }
        })
        .then(() => client.pause(1000));
    });
    test('should click on "Configure" button', () => {
      return promise
        .then(() => client.getModuleButtonName(ModulePage, moduleTechName))
        .then(() => client.clickOnConfigureModuleButton(ModulePage, moduleTechName));
    });
    test('should check the configuration page', () => client.checkTextValue(ModulePage.config_legend.replace("%moduleTechName", moduleTechName), moduleTechName));
  },
  installModule: function (client, ModulePage, AddProductPage, moduleTechName) {
    test('should go to "Module Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should click on "Modules Catalog" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_catalog_submenu));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button, 2000));
    test('should click on "Install" button', () => client.waitForExistAndClick(ModulePage.install_button.replace("%moduleTechName", moduleTechName)));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should go to "Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Modules" tab', () => client.waitForExistAndClick(ModulePage.modules_tab));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should click on "see more" link if existing', () => {
      return promise
        .then(() => client.isVisible(ModulePage.see_more_link, 3000))
        .then(() => {
          if (global.isVisible) {
            client.scrollWaitForVisibleAndClick(ModulePage.see_more_link);
          }
        })
        .then(() => client.pause(1000));
    });
    test('should check if the module ' + moduleTechName + ' was installed', () => client.isExisting(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName)));
  },
  uninstallModule: function (client, ModulePage, AddProductPage, moduleTechName) {
    test('should go to "Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs));
    test('should search for ' + moduleTechName + ' module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on "see more" link if existing', () => {
      return promise
        .then(() => client.isVisible(ModulePage.see_more_link, 3000))
        .then(() => {
          if (global.isVisible) {
            client.scrollWaitForVisibleAndClick(ModulePage.see_more_link);
          }
        })
        .then(() => client.pause(1000));
    });
    test('should click on module dropdown', () => client.scrollWaitForVisibleAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName)));
    test('should click on "Uninstall" button', () => client.scrollWaitForVisibleAndClick(ModulePage.uninstall_button.split('%moduleTechName').join(moduleTechName)));
    test('should click on "Yes, uninstall it" button', () => client.waitForVisibleAndClick(ModulePage.uninstall_module_modal));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should check that the backdrop is hidden', () => client.checkIsNotVisible(ModulePage.backdrop_modale));
    test('should check if the module ' + moduleTechName + ' was uninstalled', () => client.isNotExisting(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName)));
  },
  disableModule: function (client, ModulePage, AddProductPage, moduleTechName) {
    test('should go to "Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs));
    test('should search for ' + moduleTechName + ' module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on "see more" link if existing', () => {
      return promise
        .then(() => client.isVisible(ModulePage.see_more_link, 3000))
        .then(() => {
          if (global.isVisible) {
            client.scrollWaitForVisibleAndClick(ModulePage.see_more_link);
          }
        })
        .then(() => client.pause(1000));
    });
    test('should click on "Disable" button', () => {
      return promise
        .then(() => client.getModuleButtonName(ModulePage, moduleTechName))
        .then(() => client.clickOnDisableModuleButton(ModulePage, moduleTechName));
    });
    test('should click on "Yes, disable it" button', () => client.waitForVisibleAndClick(ModulePage.confirmation_disable_module));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
  },
  enableModule: function (client, ModulePage, AddProductPage, moduleTechName) {
    test('should go to "Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs));
    test('should search for ' + moduleTechName + ' module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on "see more" link if existing', () => {
      return promise
        .then(() => client.isVisible(ModulePage.see_more_link, 3000))
        .then(() => {
          if (global.isVisible) {
            client.scrollWaitForVisibleAndClick(ModulePage.see_more_link);
          }
        })
        .then(() => client.pause(1000));
    });
    test('should click on "Enable" button', () => {
      return promise
        .then(() => client.getModuleButtonName(ModulePage, moduleTechName))
        .then(() => client.clickOnEnableModuleButton(ModulePage, moduleTechName));
    });
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
  },
  resetModule: function (client, ModulePage, AddProductPage, moduleName, moduleTechName) {
    test('should go to "Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs));
    test('should search for "' + moduleName + '" module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on module dropdown', () => client.waitForVisibleAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName)));
    test('should click on "Reset" action', () => client.waitForExistAndClick(ModulePage.reset_module.split('%moduleTechName').join(moduleTechName)));
    test('should click on "Yes, reset it" button', () => client.waitForVisibleAndClick(ModulePage.reset_button_modal.replace('%moduleTechName', moduleTechName)));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should go to "Dashboard" page', () => client.waitForExistAndClick(Menu.dashboard_menu));
  },
  sortModule: function (client, ModulePage, sortType, attribute) {
    test('should select sort by "' + sortType + '"', () => client.waitAndSelectByValue(ModulePage.sort_select, sortType));
    test('should check sort modules by "' + sortType + '"', () => {
      for (let i = 0; i < (parseInt((tab["modules_number"].match(/[0-9]+/g)[0]))); i++) {
        promise = client.getModuleAttr(ModulePage.module_list, attribute, i)
      }
      if (sortType === "name") {
        return promise
          .then(() => client.checkSortByName((parseInt((tab["modules_number"].match(/[0-9]+/g)[0])))))
      } else if (sortType === "price") {
        return promise
          .then(() => client.checkSortByIncPrice((parseInt((tab["modules_number"].match(/[0-9]+/g)[0])))))
      } else {
        return promise
          .then(() => client.checkSortDesc((parseInt((tab["modules_number"].match(/[0-9]+/g)[0])))))
      }
    });
  },
  clickOnReadMore: function (ModulePage, moduleName, moduleTechName) {
    //BOOM: 9722
    scenario('Check that the click on "Read more" button is working well', client => {
      test('should go to "Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
      test('should search for the module "' + moduleName + '"', () => {
        return promise
          .then(() => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName))
          .then(() => client.waitForExistAndClick(ModulePage.selection_search_button));
      });
      test('should click on "Read more" link', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.read_more_link.replace("%moduleTechName", moduleTechName)));
      test('should check that the modal is well opened', () => client.waitForVisible(ModulePage.ReadMoreModal.overview_content.replace("%moduleTechName", moduleTechName)));
      /**
       * This scenario is based on the bug described in this ticket
       * http://forge.prestashop.com/browse/BOOM-4951
       */
      test('should check that the content of "Overview" tab is not empty', () => client.checkTextValue(ModulePage.ReadMoreModal.overview_content.replace("%moduleTechName", moduleTechName), '', 'notequal', 1000));
      /**
       * End
       */
      test('should click on "Additional information" tab in the modal', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.module_readmore_tabs.replace("%moduleTechName", moduleTechName).replace('%NAME', 'Additional information')));
      test('should check that the content of "Additional information" tab is not empty', () => client.checkTextValue(ModulePage.ReadMoreModal.additional_content.replace("%moduleTechName", moduleTechName), '', 'notequal', 1000));
      test('should click on "Overview" tab in the modal', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.module_readmore_tabs.replace("%moduleTechName", moduleTechName).replace('%NAME', 'Overview')));
      test('should check that the content of "Overview" tab is not empty', () => client.checkTextValue(ModulePage.ReadMoreModal.overview_content.replace("%moduleTechName", moduleTechName), '', 'notequal', 1000));
      test('should click on "Features" tab in the modal', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.module_readmore_tabs.replace("%moduleTechName", moduleTechName).replace('%NAME', 'Features')));
      test('should check that the content of "Features" tab is not empty', () => client.checkTextValue(ModulePage.ReadMoreModal.features_content.replace("%moduleTechName", moduleTechName), '', 'notequal', 1000));
      test('should click on "Changelog" tab in the modal', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.module_readmore_tabs.replace("%moduleTechName", moduleTechName).replace('%NAME', 'Changelog')));
      test('should check that the content of "Changelog" tab is not empty', () => client.checkTextValue(ModulePage.ReadMoreModal.changelog_content.replace("%moduleTechName", moduleTechName), '', 'notequal', 1000));
      test('should click on "Close" button in the modal', () => client.waitForExistAndClick(ModulePage.ReadMoreModal.close_modal_button.replace("%moduleTechName", moduleTechName), 2000));
    }, 'common_client');
  }
};
