const {Location} = require('../../selectors/BO/international/location');
const {Menu} = require('../../selectors/BO/menu.js');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {accountPage} = require('../../selectors/FO/add_account_page');
let promise = Promise.resolve();

module.exports = {

  createZone: function (name = "", cancel = false) {
    scenario('Create new zone', client => {
      test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
      test('should click on "Add new zone" button', () => client.waitForExistAndClick(Location.Zone.add_new_zone_button));
      if (cancel === true) {
        test('should click on "Cancel" button', () => client.waitForExistAndClick(Location.Zone.cancel_button));
        test('should check the appearance of zones table', () => client.waitForExist(Location.Zone.zones_table));
      } else {
        test('should set the "Name" input', () => client.waitAndSetValue(Location.Zone.name_input, name + date_time));
        test('should click on "Save" button', () => client.waitForExistAndClick(Location.Zone.save_button));
        test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Zone.alert_panel.replace("%I", "success"), "×\nSuccessful creation."));
      }
    }, 'international');
  },

  sortZone: async function (selector, sortBy, isNumber = false) {
    global.elementsSortedTable = [];
    global.elementsTable = [];
    scenario('Check the sort of zones by "' + sortBy.toUpperCase() + '"', client => {
      test('should get the number of zones', () => client.getTextInVar(Location.Zone.number_zone_span, 'number_zones'));
      test('should click on "Sort by ASC" icon', async () => {
        for (let j = 0; j < (parseInt(tab['number_zones'])); j++) {
          await client.getTableField(selector, j);
        }
        await client.waitForExistAndClick(Location.Zone.sort_icon.replace('%B', sortBy).replace('%W', 2));
      });
      test('should check that the zones are well sorted by ASC', async () => {
        for (let j = 0; j < (parseInt(tab['number_zones'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber);
      });
      test('should click on "Sort by DESC" icon', () => client.waitForExistAndClick(Location.Zone.sort_icon.replace('%B', sortBy).replace('%W', 1)));
      test('should check that the zones are well sorted by DESC', async () => {
        for (let j = 0; j < (parseInt(tab['number_zones'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber, 'DESC');
      });
    }, 'international');
  },
  sortCountry: async function (selector, sortBy, isNumber = false) {
    global.elementsSortedTable = [];
    global.elementsTable = [];
    scenario('Check the sort of countries by "' + sortBy.toUpperCase() + '"', client => {
      test('should get the number of countries', () => client.getTextInVar(Location.Country.number_country_span, 'number_countries'));
      test('should click on "Sort by ASC" icon', async () => {
        for (let j = 0; j < (parseInt(tab['number_countries'])); j++) {
          if (sortBy === 'Call prefix') {
            await client.getCallPrefixField(selector, j);
          } else {
            await client.getTableField(selector, j);
          }
        }
        await client.waitForExistAndClick(Location.Country.sort_icon.replace('%B', sortBy).replace('%W', 2));
      });
      test('should check that the countries are well sorted by ASC', async () => {
        for (let j = 0; j < (parseInt(tab['number_countries'])); j++) {
          if (sortBy === 'Call prefix') {
            await client.getCallPrefixField(selector, j, true);
          } else {
            await client.getTableField(selector, j, true);
          }
        }
        await client.checkSortTable(isNumber);
      });
      test('should click on "Sort by DESC" icon', () => client.waitForExistAndClick(Location.Country.sort_icon.replace('%B', sortBy).replace('%W', 1)));
      test('should check that the countries are well sorted by DESC', async () => {
        for (let j = 0; j < (parseInt(tab['number_countries'])); j++) {
          if (sortBy === 'Call prefix') {
            await client.getCallPrefixField(selector, j, true);
          } else {
            await client.getTableField(selector, j, true);
          }
        }
        await client.checkSortTable(isNumber, 'DESC');
      });
    }, 'international');
  },
  checkCountryAddress: function (customerData, restrictCountry = 'No', window = 1) {
    scenario('Delete customer addresses then check country address', client => {
      test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
      test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
      if (restrictCountry === "No") {
        test('should change "Restrict country selections in front office to those covered by active carriers" button on "No"', () => client.waitForExistAndClick(Location.Country.restrict_country_selections_button.replace('%B', 'off')));
      } else {
        test('should change "Restrict country selections in front office to those covered by active carriers" button on "Yes"', () => client.waitForExistAndClick(Location.Country.restrict_country_selections_button.replace('%B', 'on')));
      }
      test('should click on "Save" button', () => client.waitForExistAndClick(Location.Country.country_option_save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Country.alert_panel.replace('%B', 'alert-success'), 'The settings have been successfully updated.'));
      test('should click on "View my shop" then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(window))
          .then(() => client.changeLanguage());
      });
      if (window === 2) {
        test('should sign out from the Front office', () => client.signOutFO(AccessPageFO));
      }
      test('should click on "Sign in" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
      test('should set the "Email" input', () => client.waitAndSetValue(AccessPageFO.login_input, date_time + customerData.email_address));
      test('should set the "Password" input', () => client.waitAndSetValue(AccessPageFO.password_inputFO, customerData.password));
      test('should click on "Sign In" button', () => client.waitForExistAndClick(AccessPageFO.login_button));
      test('should click on "Addresses" button', () => client.waitForExistAndClick(AccessPageFO.address_information_link));
      test('should get the number of addresses', () => client.getAddressNumberInVar(accountPage.address_block, 'address_number'));
      test('should delete all existing addresses', () => client.deleteAddresses(accountPage.delete_address_button));
      test('should click on "Create new address" button', () => client.waitForExistAndClick(accountPage.create_new_address_button));
    }, 'customer');
  }
};
