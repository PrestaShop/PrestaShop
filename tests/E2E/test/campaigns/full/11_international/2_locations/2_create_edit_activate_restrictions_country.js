/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-156"][Name="Add a country"]
 * [id="PS-157"][Name="Edit a country"]
 * [id="PS-158"][Name="Activate country restrictions"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const {Location} = require('../../../../selectors/BO/international/location');
const commonLocation = require('../../../common_scenarios/location');
const {accountPage} = require('../../../../selectors/FO/add_account_page');
const {productPage} = require('../../../../selectors/FO/product_page');
const commonCustomer = require('../../../common_scenarios/customer');
const commonAddress = require('../../../common_scenarios/address');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let promise = Promise.resolve();

let customerData = {
    first_name: 'test',
    last_name: 'test',
    email_address: 'test@prestashop.com',
    password: 'test123456',
    birthday: {
      day: '18',
      month: '12',
      year: '1991'
    }
  },
  firstCustomerData = {
    first_name: 'test',
    last_name: 'test',
    email: 'test@gmail.com',
    password: 'test123',
  },
  secondCustomerData = {
    first_name: 'test',
    last_name: 'test',
    email: 'testprestashop@gmail.com',
    password: 'test123',
  },
  thirdCustomerData = {
    first_name: 'test',
    last_name: 'test',
    email: 'prestaTest@gmail.com',
    password: 'test123',
  },
  forthCustomerData = {
    first_name: 'test',
    last_name: 'test',
    email: 'prestatest@prestashop.com',
    password: 'test123',
  };

scenario('Create, edit a country and activate country restrictions in the Back Office ', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Test 1: Create then check country in the Back Office', () => {
    commonLocation.createZone("Canaries", false);
    scenario('Create country in the Back Office', client => {
      test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
      test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
      test('should verify if all countries are listed', () => client.checkTextValue(Location.Country.number_country_span, 243, 'greaterThan'));
      test('should click on "Add new country" button', () => client.waitForExistAndClick(Location.Country.add_new_country_button));
      test('should set the "Country" input', () => client.waitAndSetValue(Location.Country.country_input, 'Corse'));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Location.Country.iso_code_input, 'CO'));
      test('should set the "Call prefix" input', () => client.waitAndSetValue(Location.Country.call_prefix_input, '+33'));
      test('should choose "Default store currency" from "Default currency" list', () => client.waitAndSelectByVisibleText(Location.Country.default_currency_select, 'Default store currency'));
      test('should choose "Canaries' + date_time + '" from "Zone" list', () => client.waitAndSelectByVisibleText(Location.Country.zone_select, 'Canaries' + date_time));
      test('should put "Does it need Zip/postal code?" button on "Yes"', () => client.waitForExistAndClick(Location.Country.need_zip_code_yes_label));
      test('should set the "Zip/postal code format" input', () => client.waitAndSetValue(Location.Country.zip_code_format_input, 'NNNNN'));
      test('should click on "Clear format" button', () => client.waitForExistAndClick(Location.Country.clear_format_button));
      test('should click on "OK" button', () => client.alertAccept());
      test('should verify "Address format" field is empty', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', ''));
      test('should click on "CUSTOMER" tab', () => client.waitForExistAndClick(Location.Country.address_format_tab.replace('%B', 'Customer')));
      test('should click on "birthday" button', () => client.waitForExistAndClick(Location.Country.customer_birthday_button));
      test('should verify "Customer:birthday" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'Customer:birthday', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "ADDRESS" tab', () => client.waitForExistAndClick(Location.Country.address_format_tab.replace('%B', 'Address')));
      test('should click on "lastname" button', () => client.waitForExistAndClick(Location.Country.address_last_name_button));
      test('should verify "lastname" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'lastname', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "firstname" button', () => client.waitForExistAndClick(Location.Country.address_first_name_button));
      test('should verify "firstname" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'firstname', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "address1" button', () => client.waitForExistAndClick(Location.Country.address_address1_button));
      test('should verify "address1" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'address1', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "address2" button', () => client.waitForExistAndClick(Location.Country.address_address2_button));
      test('should verify "address2" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'address2', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "dni" button', () => client.waitForExistAndClick(Location.Country.address_dni_button));
      test('should verify "dni" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'dni', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "postcode" button', () => client.waitForExistAndClick(Location.Country.address_postcode_button));
      test('should verify "postcode" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'postcode', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "city" button', () => client.waitForExistAndClick(Location.Country.address_city_button));
      test('should verify "city" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'city', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "phone" button', () => client.waitForExistAndClick(Location.Country.address_phone_button));
      test('should verify "phone" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'phone', 'contain'));
      test('should push "Enter"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.address_format_field_textarea))
          .then(() => client.keys('Enter'));
      });
      test('should click on "COUNTRY" tab', () => client.waitForExistAndClick(Location.Country.address_format_tab.replace('%B', 'Country')));
      test('should click on "name" button', () => client.waitForExistAndClick(Location.Country.country_name_button));
      test('should verify "Country:name" is added in the "Address format" field', () => client.checkAttributeValue(Location.Country.address_format_field_textarea, 'value', 'Country:name', 'contain'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
      test('should verify the appearance of the red error', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-danger'), '×\n2 errors\nThis ISO code already exists.You cannot create two countries with the same ISO code.\nThe call_prefix field is invalid.'));
      test('should set the "Call prefix" input', () => client.waitAndSetValue(Location.Country.call_prefix_input, '33'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
      test('should verify the appearance of the red error', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-danger'), '×\nThis ISO code already exists.You cannot create two countries with the same ISO code.'));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Location.Country.iso_code_input, 'COR'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-success'), '×\nSuccessful creation.'));
      test('should click on "States" subtab', () => client.waitForExistAndClick(Menu.Improve.International.states_tab));
      test('should verify the "Corse" country is not in the drop down list', () => client.isNotExisting(Location.State.search_country_option_list.replace('%B', 'Corse')));
      test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
      test('should set the "ISO code" search input to "COR"', () => client.waitAndSetValue(Location.Country.search_iso_code_input, 'COR'));
      test('should click on "Search" button', () => client.waitForExistAndClick(Location.Country.search_button));
      test('should verify that there is only 1 result', () => client.checkTextValue(Location.Country.number_country_span, '1'));
      test('should Verify the country is "Corse"', () => client.checkTextValue(Location.Country.element_country_table.replace('%B', 3).replace('%ID', 1), 'Corse'));
      test('should Verify the call prefix is "+33"', () => client.checkTextValue(Location.Country.element_country_table.replace('%B', 5).replace('%ID', 1), '+33'));
      test('should Verify the zone is "Canaries' + date_time + '"', () => client.checkTextValue(Location.Country.element_country_table.replace('%B', 6).replace('%ID', 1), 'Canaries' + date_time));
      test('should verify it is enabled', () => client.waitForExist(Location.Country.enabled_disabled_icon.replace('%ID', 1).replace('%ICON', 'icon-check')));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
      test('should enter "FR" in the "Country" field then click on "Search" button', () => client.searchByValue(Location.Country.search_country_input, Location.Country.search_button, 'FR'));
      test('should verify there are 6 results', () => client.checkTextValue(Location.Country.number_country_span, '6'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
      test('should enter "4" in the "ID" field then click on "Search" button', () => client.searchByValue(Location.Country.search_id_input, Location.Country.search_button, '4'));
      test('should get number of countries', () => client.getTextInVar(Location.Country.number_country_span, 'number_countries'));
      test('should check that all displayed countries contains a "4" in the id', () => {
        for (let j = 1; j <= (parseInt(tab['number_countries'])); j++) {
          promise.then(() => client.checkTextValue(Location.Country.element_country_table.replace('%ID', j).replace('%B', 2), '4', 'contain'));
        }
        return promise.then(() => client.pause(3000));
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
      test('should Select "Europe" for "Zone" list', () => client.waitAndSelectByVisibleText(Location.Country.search_zone_list, 'Europe'));
      test('should get number of countries', () => client.getTextInVar(Location.Country.number_country_span, 'number_countries'));
      test('should check that all the country listed are in Europe', async () => {
        for (let j = 1; j <= (parseInt(tab['number_countries'])); j++) {
          await client.checkTextValue(Location.Country.element_country_table.replace('%ID', j).replace('%B', 6), 'Europe')
        }
        return await client.pause(3000);
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
      test('should choose "Yes" from enabled filter list', () => client.waitAndSelectByVisibleText(Location.Country.search_enabled_list, 'Yes'));
      test('should get number of countries', () => client.getTextInVar(Location.Country.number_country_span, 'number_countries'));
      test('should check that all the listed countries are enabled', () => {
        for (let j = 1; j <= (parseInt(tab['number_countries'])); j++) {
          promise.then(() => client.waitForExist(Location.Country.enabled_disabled_icon.replace('%ID', j).replace('%ICON', 'icon-check')))
        }
        return promise.then(() => client.pause(3000));
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
      test('should choose "1000" from display result list', () => {
        return promise
          .then(() => client.waitForExistAndClick(Location.Country.pagination_button))
          .then(() => client.waitForExistAndClick(Location.Country.pagination_element.replace('%NUMBER','1000')));
      });
    }, 'common_client');
    commonLocation.sortCountry(Location.Country.element_country_table.replace('%B', 2), 'ID', true);
    commonLocation.sortCountry(Location.Country.element_country_table.replace('%B', 3), 'Country');
    commonLocation.sortCountry(Location.Country.element_country_table.replace('%B', 4), 'ISO code');
    commonLocation.sortCountry(Location.Country.element_country_table.replace('%B', 5), 'Call prefix', true);
    commonLocation.sortCountry(Location.Country.element_country_table.replace('%B', 6), 'Zone');
  }, 'common_client');

  scenario('Test 2: Edit a country', client => {
    test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
    test('should set the "ISO code" search input to "ES"', () => client.waitAndSetValue(Location.Country.search_iso_code_input, 'ES'));
    test('should click on "Search" button', () => client.waitForExistAndClick(Location.Country.search_button));
    test('should verify that there is only 1 result', () => client.checkTextValue(Location.Country.number_country_span, '1'));
    test('should Verify the country is "Spain"', () => client.checkTextValue(Location.Country.element_country_table.replace('%B', 3).replace('%ID', 1), 'Spain'));
    test('should click on "Edit" button', () => client.waitForExistAndClick(Location.Country.edit_button));
    test('should remove all fields from the address format except "phone"', () => client.clearAddressFormat('ordered_fields', 'phone'));
    test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-success'), '×\nSuccessful update.'));
    test('should click on "Edit" button', () => client.waitForExistAndClick(Location.Country.edit_button));
    test('should click on "CUSTOMER" tab', () => client.waitForExistAndClick(Location.Country.address_format_tab.replace('%B', 'Customer')));
    test('should click on "lastname" button', () => client.waitForExistAndClick(Location.Country.customer_lastname_button));
    test('should click on "CUSTOMER" tab', () => client.waitForExistAndClick(Location.Country.address_format_tab.replace('%B', 'Customer')));
    test('should click on "firstname" button', () => client.waitForExistAndClick(Location.Country.customer_firstname_button));
    test('should click on "Use the last registered format" button', () => client.waitForExistAndClick(Location.Country.use_last_registered_format_button));
    test('should click on "OK" button', () => client.alertAccept());
    test('should check that the address format is equal to "phone"', () => client.checkTextValue(Location.Country.address_format_field_textarea, 'phone\ndni', 'equal'));
    test('should click on "Use the default format" button', () => client.waitForExistAndClick(Location.Country.use_default_format_button));
    test('should click on "OK" button', () => client.alertAccept());
    test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
    test('should click on "Edit" button', () => client.waitForExistAndClick(Location.Country.edit_button));
    test('should check the address format value', () => client.checkTextValue(Location.Country.address_format_field_textarea, 'firstname lastname\ncompany\nvat_number\naddress1\naddress2\npostcode city\nCountry:name\nphone\ndni'));
    test('should change "Contains states" button on "Yes"', () => client.waitForExistAndClick(Location.Country.contain_states_yes_button));
    test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-success'), '×\nSuccessful update.'));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
    test('should click on "States" subtab', () => client.waitForExistAndClick(Menu.Improve.International.states_tab));
    test('should verify that "Spain" country is in the drop down list', () => client.isExisting(Location.State.search_country_option_list.replace('%B', 'Spain')));
  }, 'international');

  scenario('Test 3: Activate country restrictions', () => {
    scenario('Create new customer with two addresses', () => {
      commonCustomer.createCustomer(customerData);
      commonAddress.createCustomerAddress(customerData);
      commonAddress.createCustomerAddress(customerData);
    }, 'common_client');
    commonLocation.checkCountryAddress(customerData);
    commonCustomer.checkCustomerInfo('Corse');
    scenario('Sign out from Front Office then click on the first product', client => {
      test('should click on "Sign out" button', () => client.signOutWithoutCookiesFO(AccessPageFO));
      test('should click on "my store" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
      });
      test('should click on the first product', () => client.waitForExistAndClick(productPage.first_product));
    }, 'common_client');
    commonCustomer.fillCustomerInfoFromAGuest(firstCustomerData);
    commonCustomer.checkCustomerInfo('Corse');
    scenario('Verify tax product then sign out from Front Office', client => {
      test('should click on "my store" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000))
      });
      test('should click on the first product', () => client.waitForExistAndClick(productPage.first_product));
      test('should verify the existence of the "tax included" or "tax excluded" label', () => client.waitForVisible(productPage.product_tax_label));
      test('should click on "Sign out" button', () => client.signOutWithoutCookiesFO(AccessPageFO));
    }, 'common_client');
    commonCustomer.fillCustomerInfoFromAGuest(secondCustomerData, false);
    commonCustomer.checkCustomerInfo('Corse');
    commonCustomer.signInFromCheckout(customerData);
    commonCustomer.checkCustomerInfo('Corse');
    scenario('Sign out from Front office then go back to the back Office', client => {
      test('should go back to the Back office', () => client.switchWindow(0));
    }, 'common_client');
    commonAddress.createCustomerAddress(customerData);
    commonAddress.createCustomerAddress(customerData);
    commonLocation.checkCountryAddress(customerData, 'Yes', 2);
    scenario('Check the inexistence of "Corse" country', client => {
      test('should click on "Country" list', () => client.waitForExistAndClick(accountPage.country_list));
      test('should verify that "Corse" is not in the list of countries', () => client.isNotExisting(accountPage.country_option.replace('%B', 'Corse')));
      test('should click on "Sign out" button', () => client.signOutWithoutCookiesFO(AccessPageFO));
      test('should click on "my store" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
          .then(() => client.changeLanguage());
      });
      test('should click on the first product', () => client.waitForExistAndClick(productPage.first_product));
    }, 'customer');
    commonCustomer.fillCustomerInfoFromAGuest(thirdCustomerData);
    scenario('Verify tax product then sign out from Front Office', client => {
      test('should verify that "Corse" is not in the list of countries', () => client.isNotExisting(accountPage.country_option.replace('%B', 'Corse')));
      test('should click on "my store" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
          .then(() => client.changeLanguage());
      });
      test('should click on the first product', () => client.waitForExistAndClick(productPage.first_product));
      test('should verify the existence of the "tax included" or "tax excluded" label', () => client.waitForVisible(productPage.product_tax_label));
      test('should click on "Sign out" button', () => client.signOutWithoutCookiesFO(AccessPageFO));
    }, 'common_client');
    commonCustomer.fillCustomerInfoFromAGuest(forthCustomerData, false);
    scenario('Verify that Corse is not in the list', client => {
      test('should verify that "Corse" is not in the list of countries', () => client.isNotExisting(accountPage.country_option.replace('%B', 'Corse')));
    }, 'common_client');
    commonCustomer.signInFromCheckout(customerData);
    scenario('Verify that Corse is not in the list', client => {
      test('should verify that "Corse" is not in the list of countries', () => client.isNotExisting(accountPage.country_option.replace('%B', 'Corse')));
      test('should go back to the Back office', () => client.switchWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Delete the created country then back to default sort', client => {
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
    test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
    test('should set the "ISO code" search input to "COR"', () => client.waitAndSetValue(Location.Country.search_iso_code_input, 'COR'));
    test('should click on "Search" button', () => client.waitForExistAndClick(Location.Country.search_button));
    test('should click on "Select all" action from the "Bulk action" list', () => client.clickOnAction(Location.Country.action_group_button.replace("%ID", 1), Location.Country.bulk_action_button, "selectAll"));
    test('should click on "Delete selected" action from the "Bulk action" list', () => client.clickOnAction(Location.Country.action_group_button.replace("%ID", 5), Location.Country.bulk_action_button, "delete"));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Country.alert_panel.replace("%B", "success"), "×\nThe selection has been successfully deleted."));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
    test('should change "Restrict country selections in front office to those covered by active carriers" button on "No"', () => client.waitForExistAndClick(Location.Country.restrict_country_selections_button.replace('%B', 'off')));
    test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.country_option_save_button));
    test('should choose "50" from display result list', () => {
      return promise
        .then(() => client.waitForExistAndClick(Location.Country.pagination_button))
        .then(() => client.waitForExistAndClick(Location.Country.pagination_element.replace('%NUMBER','50')));
    });
  }, 'international');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
