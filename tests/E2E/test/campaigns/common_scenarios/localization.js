const {HomePage} = require('../../selectors/FO/home_page');
const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const {Localization} = require('../../selectors/BO/international/localization');
const {InternationalPage} = require('../../selectors/BO/international/index');
const {Menu} = require('../../selectors/BO/menu.js');
const {ThemeAndLogo} = require('../../selectors/BO/design/theme_and_logo');
const Design = require('../../selectors/BO/design/index');
const {languageFO} = require('../../selectors/FO/index');
const {AddProductPage} = require('../../selectors/BO/add_product_page')
let promise = Promise.resolve();

/**** Example of advanced data ****
 * let advancedData = {
 *  languageIdentifier: 'language',
 *  countryIdentifier: 'country',
 * }
 */

module.exports = {
  importLocalization(language, localization, downloadPackData = false, contentImport = false) {
    scenario('Import a localization pack', client => {
      test('should go to "International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Localization pack you want to import" select', () => client.waitForExistAndClick(Localization.Localization.pack_select));
      test('should search for the localization from the select', () => client.waitAndSetValue(Localization.Localization.pack_search_input, language));
      test('should click on "' + localization.toUpperCase() + '" option', () => client.waitForExistAndClick(Localization.Localization.pack_option.replace('%B', localization)));
      if (contentImport === true) {
        test('should uncheck "States"', () => client.waitForExistAndClick(Localization.Localization.content_import_checkbox.replace('%B', 0)));
        test('should uncheck "Taxes"', () => client.waitForExistAndClick(Localization.Localization.content_import_checkbox.replace('%B', 1)));
        test('should uncheck "Units"', () => client.waitForExistAndClick(Localization.Localization.content_import_checkbox.replace('%B', 4)));
      }
      if (downloadPackData === true) {
        test('should put "Download pack data" toggle button on "No"', () => client.waitForExistAndClick(Localization.Localization.download_pack_data_toggle_button));
      }
      test('should click on "Import" button', () => client.waitForExistAndClick(Localization.Localization.import_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Localization.success_alert_panel.replace('%B', 'alert-success'), 'Localization pack imported successfully.'));
    }, 'common_client');
  },
  getDefaultConfiguration() {
    scenario('Get the default configuration values', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should get the default language', () => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_language'), 'value', 'firstDefaultLanguageValue'));
      test('should get the default country', () => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_country'), 'value', 'firstDefaultCountryValue'));
      test('should get the default currency', () => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_currency'), 'value', 'firstDefaultCurrencyValue'));
      test('should get the time zone', () => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_timezone'), 'value', 'firstDefaultTimezoneValue'));
    }, 'common_client');
  },
  checkUnchangedDefaultConfiguration() {
    scenario('Check the unchanged default configuration', client => {
      test('should Verify if the default language has not changed', () => {
        return promise
          .then(() => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_language'), 'value', 'secondDefaultLanguageValue'))
          .then(() => expect(tab['secondDefaultLanguageValue']).to.be.equal(tab['firstDefaultLanguageValue']));
      });
      test('should Verify if the default country has not changed', () => {
        return promise
          .then(() => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_country'), 'value', 'secondDefaultCountryValue'))
          .then(() => expect(tab['secondDefaultCountryValue']).to.be.equal(tab['firstDefaultCountryValue']));
      });
      test('should Verify if the default currency has not changed', () => {
        return promise
          .then(() => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_currency'), 'value', 'secondDefaultCurrencyValue'))
          .then(() => expect(tab['secondDefaultCurrencyValue']).to.be.equal(tab['firstDefaultCurrencyValue']));
      });
      test('should Verify if the time zone has not changed', () => {
        return promise
          .then(() => client.getAttributeInVar(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_timezone'), 'value', 'secondDefaultTimezoneValue'))
          .then(() => expect(tab['secondDefaultTimezoneValue']).to.be.equal(tab['firstDefaultTimezoneValue']));
      });
    }, 'common_client');
  },
  checkExistenceLanguage(language) {
    scenario('Check the existence language in the Back Office', client => {
      test('should click on "Languages" subtab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for "' + language.toUpperCase() + '" language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, language));
      test('should check  "' + language.toUpperCase() + '" language exists', () => client.checkTextValue(Localization.languages.language_column.replace("%ID", 4), language, 'contain'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Localization.languages.reset_button));
    }, 'common_client');
  },
  createLanguage: function (languageData) {
    scenario('Create a new "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should click on "Add new language" button', () => client.waitForExistAndClick(Localization.languages.add_new_language_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Localization.languages.name_input, languageData.name + date_time));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Localization.languages.iso_code_input, languageData.iso_code));
      test('should set the "Language code" input', () => client.waitAndSetValue(Localization.languages.language_code_input, languageData.language_code));
      test('should set the "Date format" input', () => client.waitAndSetValue(Localization.languages.date_format_input, languageData.date_format));
      test('should set the "Date format (full)" input', () => client.waitAndSetValue(Localization.languages.date_format_full_input, languageData.date_format_full));
      test('should upload the "Flag" picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should upload the "No-picture" image', () => client.uploadPicture(languageData.no_picture_file, Localization.languages.no_picture_file, 'no_picture'));
      test('should switch the "Is RTL language"', () => client.waitForExistAndClick(Localization.languages.is_rtl_button.replace('%S', languageData.is_rtl)));
      test('should switch the "Status"', () => client.waitForExistAndClick(Localization.languages.status_button.replace('%S', languageData.status)));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  editLanguage: function (name, languageData) {
    scenario('Edit the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Localization.languages.edit_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Localization.languages.name_input, languageData.name + date_time));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Localization.languages.iso_code_input, languageData.iso_code));
      test('should set the "Language code" input', () => client.waitAndSetValue(Localization.languages.language_code_input, languageData.language_code));
      test('should set the "Date format" input', () => client.waitAndSetValue(Localization.languages.date_format_input, languageData.date_format));
      test('should set the "Date format (full)" input', () => client.waitAndSetValue(Localization.languages.date_format_full_input, languageData.date_format_full));
      test('should upload the "Flag" picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should upload the "No-picture" image', () => client.uploadPicture(languageData.no_picture_file, Localization.languages.no_picture_file, 'no_picture'));
      if (languageData.hasOwnProperty('is_rtl')) {
        test('should switch the "Is RTL language"', () => client.waitForExistAndClick(Localization.languages.is_rtl_button.replace('%S', languageData.is_rtl)));
      }
      if (languageData.hasOwnProperty('status')) {
        test('should switch the "Status"', () => client.waitForExistAndClick(Localization.languages.status_button.replace('%S', languageData.status)));
      }
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkLanguageBO: function (languageData) {
    scenario('Check the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, languageData.name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Localization.languages.edit_button));
      test('should check that the "Name" is equal to "' + languageData.name + date_time + '"', () => client.checkAttributeValue(Localization.languages.name_input, 'value', languageData.name + date_time));
      test('should check that the "ISO code" is equal to "' + languageData.iso_code.toLowerCase() + '"', () => client.checkAttributeValue(Localization.languages.iso_code_input, 'value', languageData.iso_code.toLowerCase()));
      test('should check that the "Language code" is equal to "' + languageData.language_code + '"', () => client.checkAttributeValue(Localization.languages.language_code_input, 'value', languageData.language_code));
      test('should check that the "Date format" is equal to "' + languageData.date_format + '"', () => client.checkAttributeValue(Localization.languages.date_format_input, 'value', languageData.date_format));
      test('should check that the "Date format (full)" is equal to "' + languageData.date_format_full + '"', () => client.checkAttributeValue(Localization.languages.date_format_full_input, 'value', languageData.date_format_full));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Localization.languages.reset_button));
    }, 'common_client');
  },
  checkLanguageFO: function (languageData, isDeleted = false) {
    scenario('Check the created "Language" in the Front Office', client => {
      if (isDeleted) {
        test('should click on "Language" select', () => client.waitForExistAndClick(languageFO.language_selector));
        test('should check that the "' + languageData.name + '" doesn\'t appear', () => client.checkIsNotVisible(languageFO.language_option.replace('%LANG', languageData.iso_code.toLowerCase())));
      } else {
        test('should set the shop language to "' + languageData.name + '"', () => client.changeLanguage(languageData.iso_code.toLowerCase()));
        test('should check that the "' + languageData.name + '" language is well selected', () => client.checkTextValue(languageFO.selected_language_button, languageData.name + date_time, 'equal', 3000));
        if (languageData.hasOwnProperty('is_rtl') && languageData.is_rtl === 'on') {
          test('should check that the "Home" page is well displayed in RTL mode', () => client.checkCssPropertyValue(HomePage.home_page, 'direction', 'rtl', 'equal', 2000));
          test('should check that the "Contact us" is well reversed', () => client.checkCssPropertyValue(HomePage.contact_us_link, 'float', 'right'));
          test('should check that the "Logo" is well reversed', () => client.checkCssPropertyValue(HomePage.logo_home_page, 'float', 'right'));
          test('should check that the "Top menu" is well reversed', () => client.checkCssPropertyValue(HomePage.top_menu_page, 'float', 'right'));
          test('should check that the "Search element" is well reversed', () => client.checkCssPropertyValue(HomePage.search_widget, 'float', 'left'));
          test('should check that the "All products" is well reversed', () => client.checkCssPropertyValue(HomePage.all_product_link, 'float', 'left', 'contain'));
          test('should check that the "Products block" is well reversed', () => client.checkCssPropertyValue(HomePage.products_block, 'float', 'right'));
          test('should check that the "Newsletter block" is well reversed', () => client.checkCssPropertyValue(HomePage.newsletter_block, 'float', 'right'));
          test('should check that the "Our campany block" is well reversed', () => client.checkCssPropertyValue(HomePage.our_campany_block, 'float', 'right'));
          test('should check that the "Your account block" is well reversed', () => client.checkCssPropertyValue(HomePage.your_account_block, 'float', 'right'));
          test('should check that the "Store information block" is well reversed', () => client.checkCssPropertyValue(HomePage.store_information_block, 'float', 'right'));
          test('should go to the product page', () => client.waitForExistAndClick(productPage.first_product));
          test('should check that the "Product" page is well displayed in RTL mode', () => client.checkCssPropertyValue(productPage.product_page, 'direction', 'rtl', 'equal', 2000));
          test('should check that the "Breadcrumb list" is well reversed', () => client.checkCssPropertyValue(productPage.breadcrumb_nav, 'direction', 'rtl'));
          test('should check that the "Product pictures" is well reversed', () => client.checkCssPropertyValue(productPage.product_section.replace('%I', 1), 'float', 'right'));
          test('should check that the "Product name" is well reversed', () => client.checkCssPropertyValue(productPage.product_name, 'direction', 'rtl'));
          test('should check that the "Product price" is well reversed', () => client.checkCssPropertyValue(productPage.product_price, 'direction', 'rtl'));
          test('should check that the "Product discount" is well reversed', () => client.checkCssPropertyValue(productPage.product_discount_details, 'direction', 'rtl'));
          test('should check that the "Product size" select is well reversed', () => client.checkCssPropertyValue(productPage.product_size, 'direction', 'rtl'));
          test('should check that the "Product color" radio button is well reversed', () => client.checkCssPropertyValue(productPage.product_color, 'direction', 'rtl'));
          test('should check that the "Product quantity" input is well reversed', () => client.checkCssPropertyValue(productPage.first_product_quantity, 'direction', 'rtl'));
          test('should check that the "ADD TO CART" button is well reversed', () => client.checkCssPropertyValue(CheckoutOrderPage.add_to_cart_button, 'direction', 'rtl'));
          test('should check that the "Description" is well reversed', () => client.checkCssPropertyValue(productPage.product_description, 'direction', 'rtl'));
          test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
          test('should check that the "Product manufacturer" is well reversed', () => client.checkCssPropertyValue(productPage.product_manufacturer, 'direction', 'rtl'));
          test('should check that the "Product reference" is well reversed', () => client.checkCssPropertyValue(productPage.product_reference, 'direction', 'rtl'));
          test('should check that the "Product quantities in stock" is well reversed', () => client.checkCssPropertyValue(productPage.product_quantity, 'direction', 'rtl'));
          test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
          test('should check that the "Modal content" is well reversed', () => client.checkCssPropertyValue(CheckoutOrderPage.modal_content, 'direction', 'rtl'));
          test('should click on "PROCEED TO CHECKOUT" modal button', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
          test('should check that the "Cart" page is well displayed in RTL mode', () => client.checkCssPropertyValue(CheckoutOrderPage.cart_page, 'direction', 'rtl', 'equal', 2000));
          test('should check that the "Cart body" is well displayed in RTL mode', () => client.checkCssPropertyValue(CheckoutOrderPage.cart_body, 'direction', 'rtl'));
          test('should check that the "Cart total" is well reversed', () => client.checkCssPropertyValue(CheckoutOrderPage.cart_total, 'direction', 'rtl'));
          test('should check that the "Cart subtotal" is well reversed', () => client.checkCssPropertyValue(CheckoutOrderPage.cart_subtotal_products, 'direction', 'rtl'));
          test('should go to the "Home" page', () => client.waitForExistAndClick(HomePage.logo_home_page));
          test('should set the shop language to "' + languageData.name + '"', () => client.changeLanguage(languageData.iso_code.toLowerCase()));
          test('should click on "All product" page', () => client.scrollWaitForExistAndClick(HomePage.all_product_link));
          test('should check that the "Category" page is well displayed in RTL mode', () => client.checkCssPropertyValue(productPage.category_page, 'direction', 'rtl'));
          test('should check that the "Left column" is well reversed', () => client.checkCssPropertyValue(productPage.left_column_block, 'direction', 'rtl'));
          test('should check that the "Pagination" block is well reversed', () => client.checkCssPropertyValue(productPage.pagination_block, 'direction', 'rtl'));
        }
      }
    }, 'common_client');
  },
  deleteLanguage: function (name, dateTime = true) {
    scenario('Delete the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      if (dateTime === false) {
        test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name));
      } else {
        test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name + date_time));
      }
      test('should click on "dropdown toggle" button', () => client.waitForExistAndClick(Localization.languages.dropdown_button));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Localization.languages.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful deletion.'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Localization.languages.reset_button));
    }, 'common_client');
  },
  generateRtlStylesheet: function () {
    scenario('Generate RTL stylesheet', client => {
      test('should go to "Theme & logo" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu));
      test('should switch the "Generate RTL stylesheet" to "YES"', () => client.scrollWaitForExistAndClick(ThemeAndLogo.generate_rtl_stylesheet_button.replace('%S', 'on')));
      test('should click on "Save" button', () => client.waitForExistAndClick(ThemeAndLogo.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Design.success_panel, 'Your RTL stylesheets has been generated successfully'));
    }, 'common_client');
  },
  updateAdvancedData(advancedData) {
    scenario('Update advanced data', client => {
      test('should set "Language identifier" input', () => client.waitAndSetValue(Localization.Localization.advanced_language_identifier_input, advancedData.languageIdentifier));
      test('should set "Country identifier" input', () => client.waitAndSetValue(Localization.Localization.advanced_country_identifier_input, advancedData.countryIdentifier));
      test('should close symfony toolbar', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar, 3000))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          });
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Localization.advanced_save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Localization.alert_panel.replace("%B", "alert-text"), 'Update successful'));
    }, 'common_client');
  }
};
