const {Menu} = require('../../selectors/BO/menu.js');
const shopParameters = require('../common_scenarios/shop_parameters');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
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
  sortModule: function (client, ModulePage, installedMbo, byCategory, sortBy, sortType, attribute, isNumber, increasing) {
    test('should select sort by "' + sortBy + '"', async () => {
      global.moduleInfo = [];
      global.moduleSort = [];
      for (let i = 0; i < (parseInt(tab['modules_number'])); i++) {
        if (installedMbo && attribute === 'data-price') {
          if (byCategory === 1) {
            await client.getModulePrice(ModulePage.category_price_module_span.replace('%IND', global.tab['categoryRef']), attribute, i, false);
          } else {
            await client.getModulePrice(ModulePage.price_module_div, attribute, i, false);
          }
        } else {
          if (byCategory === 1) {
            await client.getModuleField(ModulePage.category_module_list.replace('%IND', global.tab['categoryRef']), attribute, i, false);
          } else {
            await client.getModuleField(ModulePage.module_list, attribute, i, false);
          }
        }
      }
      await client.waitAndSelectByValue(ModulePage.sort_select, sortType);
    });
    test('should check that modules are well sorted by "' + sortBy + '"', async () => {
      for (let i = 0; i < (parseInt(tab['modules_number'])); i++) {
        if (installedMbo && attribute === 'data-price') {
          if (byCategory === 1) {
            await client.getModulePrice(ModulePage.category_price_module_span.replace('%IND', tab['categoryRef']), attribute, i, true);
          } else {
            await client.getModulePrice(ModulePage.price_module_div, attribute, i, true);
          }
        } else {
          if (byCategory === 1) {
            await client.getModuleField(ModulePage.category_module_list.replace('%IND', tab['categoryRef']), attribute, i, true);
          } else {
            await client.getModuleField(ModulePage.module_list, attribute, i, true);
          }
        }
      }
      await client.checkSortModule(isNumber, increasing);
    });
  },
  searchModuleCategory: function (client, ModulePage, moduleTechName, page = 'manager', exist = true) {
    if (page === 'catalog') {
      test('should click on "Modules Catalog" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.catalog_tab));
    } else {
      test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
    }
    test('should select "Administration" from categories list', () => {
      return promise
        .then(() => client.waitForExistAndClick(ModulePage.categories_list))
        .then(() => client.waitForExistAndClick(ModulePage.categories_option_link.replace('%CAT', 'Administration')));
    });
    test('should search for the module "' + moduleTechName + '"', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName))
        .then(() => client.waitForExistAndClick(ModulePage.selection_search_button));
    });
    if (exist) {
      test('should check if the module "' + moduleTechName + '" is displayed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName), 2000));
    } else {
      test('should check if the module "' + moduleTechName + '" is not displayed', () => client.checkIsNotVisible(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName), 2000));
    }
  },
  filterCategory: async function (client, ModulePage) {
    test('should filter by categories one by one then verify if the modules displayed are only modules of the selected category', async () => {
      for (let i = 0; i < global.moduleCategoryNumber; i++) {
        await client.waitForExistAndClick(ModulePage.categories_list);
        await client.getTextInVar(ModulePage.categories_element_number_span.replace('%ID', i + 3), 'modules_number_by_category');
        await client.getAttributeInVar(ModulePage.categories_element_option.replace('%ID', i + 3), 'data-category-ref', 'categoryRef');
        await client.waitForExistAndClick(ModulePage.categories_element_option.replace('%ID', i + 3));
        await client.waitForSymfonyToolbar(AddProductPage, 3000);
        await client.isVisible(ModulePage.see_more_by_category_link.replace('%ID', tab['categoryRef']), 3000);
        if (global.isVisible) {
          await client.scrollWaitForVisibleAndClick(ModulePage.see_more_by_category_link.replace('%ID', tab['categoryRef']));
          await client.scrollTo(ModulePage.module_selection_input);
        }
        if (tab['modules_number_by_category'] > 0) {
          await client.checkModuleNumberByCategory(ModulePage.module_list_container_bloc.replace('%ID', tab['categoryRef']), tab['modules_number_by_category']);
        } else {
          await client.waitForVisible(ModulePage.module_list_container_empty_bloc.replace('%ID', tab['categoryRef']));
        }
      }
    });
  },
  DisableEnableModule: async function (client, ModulePage) {
    test('should click on "Disable" button for the first module', async () => {
      await client.getAttributeInVar(ModulePage.first_module_bloc, 'data-tech-name', 'moduleTechName');
      await client.getModuleButtonName(ModulePage, global.tab['moduleTechName'], ModulePage.module_action_link);
      await client.clickOnDisableModuleButton(ModulePage, global.tab['moduleTechName']);
      await client.waitForVisibleAndClick(ModulePage.confirmation_disable_module);
      await client.waitForExistAndClick(AddProductPage.close_validation_button);
      await client.refresh(); //To verify
    });
    test('should select "Disabled Modules" from "Status" list', async () => {
      await client.waitForExistAndClick(ModulePage.status_list);
      await client.waitForExistAndClick(ModulePage.status_option_link.replace('%ID', 0));
    });
    test('should search for the module "' + global.tab['moduleTechName'] + '"', async () => {
      await client.waitAndSetValue(ModulePage.module_selection_input, global.tab['moduleTechName']);
      await client.waitForExistAndClick(ModulePage.selection_search_button);
    });
    test('should check if the disabled  module is displayed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', global.tab['moduleTechName']), 2000));
    test('should click on "Enable" button', async () => {
      await client.getModuleButtonName(ModulePage, global.tab['moduleTechName']);
      await client.clickOnEnableModuleButton(ModulePage, global.tab['moduleTechName']);
      await client.waitForExistAndClick(AddProductPage.close_validation_button);
      await client.refresh(); //To verify
    });
    test('should select "Enabled Modules" from "Status" list', async () => {
      await client.waitForExistAndClick(ModulePage.status_list);
      await client.waitForExistAndClick(ModulePage.status_option_link.replace('%ID', 1));
    });
    test('should search for the module "' + global.tab['moduleTechName'] + '"', async () => {
      await client.waitAndSetValue(ModulePage.module_selection_input, tab['moduleTechName']);
      await client.waitForExistAndClick(ModulePage.selection_search_button);
    });
    test('should check if the enabled module is displayed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', tab['moduleTechName']), 2000));
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
  },
  installUninstallMboModule: async function (client, ModulePage, AddProductPage, moduleTechName, action) {
    if (action === 'Uninstall') {
      test('should go to "Module Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
      test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
      test('should click on "Installed Modules" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.installed_modules_tabs));
      test('should search for ' + moduleTechName + ' module in the installed module tab', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName, 2000));
      test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
      test('should check if the module ' + moduleTechName + ' was installed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName), 2000));
      test('should click on "Uninstall" button', async () => {
        if (global.isVisible) {
          await client.scrollTo(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName));
          await client.waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName));
          await client.scrollTo(ModulePage.uninstall_button.split('%moduleTechName').join(moduleTechName));
          await client.waitForExistAndClick(ModulePage.uninstall_button.split('%moduleTechName').join(moduleTechName));
          await client.waitForExistAndClick(ModulePage.uninstall_module_modal, 2000);
          await client.waitForExistAndClick(AddProductPage.close_validation_button);
          await client.checkIsNotVisible(ModulePage.backdrop_modale);
          await client.isNotExisting(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName));
          global.mboModule = await false;
        }
      });
      test('should go to the "Dashboard" page', () => client.waitForExistAndClick(Menu.dashboard_menu));
    }
    else {
      test('should go to the "Dashboard" page', () => shopParameters.checkMboModule(client));
      test('should install the "MBO Module" if not installed', async () => {
        if (mboModule === false) {
          await client.waitForExistAndClick(Menu.Improve.Modules.modules_menu);
          await client.waitForExistAndClick(Menu.Improve.Modules.modules_catalog_submenu);
          await client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName, 2000);
          await client.waitForExistAndClick(ModulePage.modules_search_button, 2000);
          await client.isVisible(ModulePage.install_button.replace("%moduleTechName", moduleTechName), 2000);
          if (global.isVisible) {
            await client.waitForExistAndClick(ModulePage.install_button.replace("%moduleTechName", moduleTechName), 2000);
            await client.waitForExistAndClick(AddProductPage.close_validation_button);
            await shopParameters.checkMboModule(client);
          }
        }
        await client.waitForExistAndClick(Menu.dashboard_menu);
      });
    }
  },
  checkExistenceModuleCatalog: function (client, moduleTechName, ModulePage) {
    test('should go to "Modules > Module Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should search for "' + moduleTechName + '" module in the module Catalog tab', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName, 2000));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should check if the module "' + moduleTechName + '" is displayed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', moduleTechName), 2000));
  },
  checkExistenceModuleManager: function (client, moduleTechName, ModulePage) {
    test('should go to "Modules > Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should search for "' + moduleTechName + '" module in the module Modules tab', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName, 2000));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    if (moduleTechName === 'check') {
      test('should check if the module "Payments by check" is displayed', () => client.isVisible(ModulePage.installed_module_div.replace('%moduleTechName', 'ps_checkpayment'), 2000));
    }
  },
  checkAmazonMarketPlace: async function (client, ModulePage, id) {
    test('should go to "Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should search for the module "Amazon Market Place"', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulePage.module_selection_input, 'amazon', 2000))
        .then(() => client.waitForExistAndClick(ModulePage.selection_search_button));
    });
    test('should click on "Discover" button', () => client.waitForExistAndClick(ModulePage.discover_amazon_module_button));
    test('should verify it opens the addons Amazon market place product page in a new tab', () => {
      return promise
        .then(() => client.switchWindow(id, 1000))
        .then(() => client.refresh()) /**Adding refreshing page because sometimes is not well opened we have to refresh it before */
        .then(() => client.checkTextValue(ModulePage.module_name, "Amazon Market Place", 'contain'))
        .then(() => client.switchWindow(0));
    });
  },
  configureModule: function (client, moduleTechName, ModulePage) {
    test('should go to "Modules > Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Alerts" tab', () => {
      return promise
        .then(() => client.waitForVisible(ModulePage.notification_number))
        .then(() => client.getTextInVar(ModulePage.notification_number, 'notification'))
        .then(() => client.waitForExistAndClick(Menu.Improve.Modules.alerts_subTab))
    });
    test('should click on "Configure" button for "' + moduleTechName + '"', () => client.waitForExistAndClick(ModulePage.configure_link.replace('%moduleTechName', moduleTechName), 2000));
    test('should set the "Account owner" input', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_owner_input, 'Demo'));
    test('should set the "Account details" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_details_textarea, 'Check notification module'));
    test('should set the "Bank address" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.bank_address_textarea, 'Boulvard street n°9 - 70501'));
    test('should click on "Save" button', () => client.waitForExistAndClick(ModulePage.ModuleBankTransferPage.save_button));
    test('should go to "Modules > Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Alerts" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.alerts_subTab));
    test('should check that the "Alerts number" is decremented with 1', () => {
      return promise
        .then(() => client.waitForVisible(ModulePage.notification_number))
        .then(() => client.checkTextValue(ModulePage.notification_number, (tab['notification'] - 1).toString(), 'equal'));
    });
    test('should check that the configured module is not visible in the "Alerts" tab', () => client.checkIsNotVisible(ModulePage.configure_module.replace('%moduleTechName', moduleTechName)));
  },
  upgradeModule: function (client, ModulePage) {
    test('should go to "Modules > Module Manager" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Updates" tab', () => {
      return promise
        .then(() => client.waitForVisible(ModulePage.update_notification_number_span))
        .then(() => client.getTextInVar(ModulePage.update_notification_number_span, 'notification_update'))
        .then(() => client.waitForExistAndClick(Menu.Improve.Modules.updates_subTab))
    });
    test('should click on "Upgrade" button for the first module if there is at least one module to update', async () => {
      if (tab['notification_update'] > 0) {
        await client.getAttributeInVar(ModulePage.module_bloc, 'data-tech-name', 'dataTechNameModule');
        await client.waitForExistAndClick(ModulePage.upgrade_module_button.replace('%moduleTechName', tab['dataTechNameModule']), 2000);
        await client.pause(2000);
      } else {
        await client.pause(0);
      }
    });
    test('should check that the updated module is not visible in the "Updates" tab if there is at least one module to update', async () => {
      if (tab['notification_update'] > 0) {
        await client.checkIsNotVisible(ModulePage.upgrade_module_button.replace('%moduleTechName', tab['dataTechNameModule']));
        await client.refresh();
      } else {
        await client.pause(0);
      }
    });
  },
  installAndCheckAbondonedCartProModule: function (ModulePage) {
    scenario('Install "abondoned cart pro" module by uploading a ZIP file', client => {
      test('should go to "Module" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
      test('should click on "Upload a module" button', () => client.waitForExistAndClick(ModulePage.upload_button));
      test('should add zip file', () => client.addFile(ModulePage.zip_file_input, "abandoned-cart-pro.zip"));
      test('should verify that the module is installed', () => {
        return promise
          .then(() => client.waitForVisible(ModulePage.success_install_message))
          .then(() => client.checkTextValue(ModulePage.module_import_success, "Module installed!"));
      });
      test('should click on close modal button', () => client.waitForExistAndClick(ModulePage.close_modal_button));
      test('should search for "abandoned cart pro" module in the installed module tab', () => {
        return promise
          .then(() => client.refresh())
          .then(() => client.waitAndSetValue(ModulePage.modules_search_input, "cartabandonmentpro"));
      });
      test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button, 1000));
      test('should click on "Configure" button', () => {
        return promise
          .then(() => client.getModuleButtonName(ModulePage, 'cartabandonmentpro'))
          .then(() => client.clickOnConfigureModuleButton(ModulePage, 'cartabandonmentpro'));
      });
      test('should verify you are on the configuration page of the module ', () => client.checkTextValue(ModulePage.check_configure, 'Thanks to Cart Abandonment Pro module', 'equal', 1000));
    }, 'module');
  },
  checkModuleExistence: function (client, ModulePage, moduleTechName) {
    test('should go to "Module Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should click on "Modules Catalog" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_catalog_submenu));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, moduleTechName));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button, 2000));
    test('should check that the ' + moduleTechName + ' module exist on the "Catalog" page', () => client.isExisting(ModulePage.installed_module_div.replace("%moduleTechName", moduleTechName)));
  }
};
