/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-159"][Name="Add a zone"]
 * [id="PS-160"][Name="Edit a zone"]
 * [id="PS-161"][Name="Delete a zone"]
 * [id="PS-162"][Name="Bulk actions"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const {Location} = require('../../../../selectors/BO/international/location');
const commonLocation = require('../../../common_scenarios/location');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let promise = Promise.resolve();

scenario('Add, edit, delete and bulk actions "Zone" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Test 1: Add zone in the Back Office', () => {
    commonLocation.createZone("", true);
    commonLocation.createZone("Canaries", false);
    scenario('Check results in the Back Office', client => {
      test('should search for the created zone by name', () => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, "Canaries" + date_time));
      test('should verify that the created zone is enabled', () => client.waitForExist(Location.Zone.enabled_disabled_icon.replace('%ID', 1).replace('%ICON', 'icon-check')));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
    }, 'common_client');
    commonLocation.sortZone(Location.Zone.element_zone_table.replace('%B', 2), 'ID', true);
    commonLocation.sortZone(Location.Zone.element_zone_table.replace('%B', 3), 'Zone');
    scenario('Search zones by status then by name', client => {
      test('should select "Yes" in enabled column', () => client.waitAndSelectByValue(Location.Zone.search_enabled_list, '1'));
      test('should get number of zones', () => client.getTextInVar(Location.Zone.number_zone_span, 'number_enabled__zones'));
      test('should check all displayed zones are enabled', () => {
        for (let j = 1; j <= (parseInt(tab['number_enabled__zones'])); j++) {
          promise.then(() => client.waitForExist(Location.Zone.enabled_disabled_icon.replace('%ID', j).replace('%ICON', 'icon-check')))
        }
        return promise;
      });
      test('should disable the first zone', () => client.waitForExistAndClick(Location.Zone.enabled_disabled_icon.replace('%ID', 1).replace('%ICON', 'icon-check')));
      test('should select "No" in enabled column', () => client.waitAndSelectByValue(Location.Zone.search_enabled_list, '0'));
      test('should get number of zones', () => client.getTextInVar(Location.Zone.number_zone_span, 'number_disabled_zones'));
      test('should check all displayed zones are enabled', () => {
        for (let j = 1; j <= (parseInt(tab['number_disabled_zones'])); j++) {
          promise.then(() => client.waitForExist(Location.Zone.enabled_disabled_icon.replace('%ID', j).replace('%ICON', 'icon-remove')))
        }
        return promise;
      });
      test('should enable the first zone', () => client.waitForExistAndClick(Location.Zone.enabled_disabled_icon.replace('%ID', 1).replace('%ICON', 'icon-remove')));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
      test('should verify all zones are displayed', () => client.checkTextValue(Location.Zone.number_zone_span, tab['number_zones']));
      test('should enter "America" in "Zone" field', () => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'America'));
      test('should verify that 3 results are displayed in the table', () => {
        return promise
          .then(() => client.getTextInVar(Location.Zone.number_zone_span, 'number_zones'))
          .then(() => client.checkTextValue(Location.Zone.number_zone_span, '3'));
      });
      test('should verify all results contains "America" in Zone name ', () => {
        for (let j = 1; j <= (parseInt(tab['number_zones'])); j++) {
          promise.then(() => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 3).replace('%ID', j), 'America', 'contain'))
        }
        return promise;
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
      test('should enter "6" in "ID" field', () => client.searchByValue(Location.Zone.search_id_input, Location.Zone.search_button, 6));
      test('should verify that 1 result that equal "6" in ID is displayed in the table', () => {
        return promise
          .then(() => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 2).replace('%ID', 1), '6'));
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
      test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
      test('should click on zone and select "Canaries"', () => client.waitAndSelectByVisibleText(Location.Country.search_zone_list, 'Canaries' + date_time));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
    }, 'common_client');
  }, 'common_client');

  scenario('Test 2: Edit zone in the Back Office', client => {
    test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should search for "Asia" zone', () => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'Asia'));
    test('should click on "Edit" action from the dropdown list', () => client.clickOnAction(Location.Zone.edit_delete_button.replace('%B', 'Edit')));
    test('should Change the "Name" input', () => client.waitAndSetValue(Location.Zone.name_input, 'Asiaaa'));
    test('should click on "Save" button', () => client.waitForExistAndClick(Location.Zone.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Zone.alert_panel.replace('%I', 'success'), '×\nSuccessful update.'));
    test('should search for "Asiaaa" zone', () => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'Asiaaa'));
    test('should verify that the edited zone is in the list', () => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 3).replace('%ID', 1), 'Asiaaa'));
    test('should verify that the edited zone is enabled', () => client.waitForExist(Location.Zone.enabled_disabled_icon.replace('%ID', 1).replace('%ICON', 'icon-check')));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
    test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
    test('should click on zone and select "Asiaaa"', () => client.waitAndSelectByVisibleText(Location.Country.search_zone_list, 'Asiaaa'));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Country.reset_button));
  }, 'international');

  scenario('Test 3: Delete zone in the Back Office', client => {
    test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should search for "Asiaaa" zone', () => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'Asiaaa'));
    test('should click on "Delete" action from the dropdown list then click on "OK" button', () => client.clickOnAction(Location.Zone.edit_delete_button.replace('%B', 'Delete'), Location.Zone.dropdown_button, 'delete'));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Zone.alert_panel.replace('%I', 'success'), '×\nSuccessful deletion.'));
    test('should verify the "Asiaaa" zone is not in the list anymore', () => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 1).replace('%ID', 1), 'No records found', 'contain'));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Location.Zone.reset_button));
    test('should click on "Countries" subtab', () => client.waitForExistAndClick(Menu.Improve.International.countries_tab));
    test('should verify the "Asiaaa" zone is not in the drop down list', () => client.isNotExisting(Location.Country.search_zone_option_list));
    test('should recreate the deleted zone', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu))
        .then(() => client.waitForExistAndClick(Location.Zone.add_new_zone_button))
        .then(() => client.waitAndSetValue(Location.Zone.name_input, 'Asia'))
        .then(() => client.waitForExistAndClick(Location.Zone.save_button));
    });
  }, 'international');

  scenario('Test 4: Bulk action', client => {
    test('should go to "International > Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should get the number of zones', () => client.getTextInVar(Location.Zone.number_zone_span, 'number_zones'));
    test('should click on "Select all" action from the "Bulk action" list', () => client.clickOnAction(Location.Zone.action_group_button.replace("%ID", 1), Location.Zone.bulk_action_button, 'selectAll'));
    test('should check that all checkbox are checked', () => {
      for (let j = 1; j <= (parseInt(tab['number_zones'])); j++) {
        promise.then(() => client.checkCheckboxStatus(Location.Zone.checkbox_input.replace('%B', j), true))
      }
      return promise.then(() => client.pause(4000));
    });
    test('should click on "Unselect all" action from the "Bulk action" list', () => client.clickOnAction(Location.Zone.action_group_button.replace('%ID', 2), Location.Zone.bulk_action_button, 'unselectAll'));
    test('should check that all checkbox are unchecked', () => {
      for (let j = 1; j <= (parseInt(tab['number_zones'])); j++) {
        promise.then(() => client.checkCheckboxStatus(Location.Zone.checkbox_input.replace('%B', j), false))
      }
      return promise.then(() => client.pause(4000));
    });
    test('should click on "Africa" and "Oceania" checkboxes', () => {
      return promise
        .then(() => client.waitForExistAndClick(Location.Zone.search_by_zone_checkbox.replace('%B', 'Africa')))
        .then(() => client.waitForExistAndClick(Location.Zone.search_by_zone_checkbox.replace('%B', 'Oceania')));
    });
    test('should click on "Disable selection" action from the "Bulk action" list', () => client.clickOnAction(Location.Zone.action_group_button.replace('%ID', 4), Location.Zone.bulk_action_button, 'DisableSelection'));
    test('should verify that "Africa" & "Oceania" zones are disabled', () => {
      return promise
        .then(() => client.waitForExist(Location.Zone.search_by_zone_icon.replace('%B', 'Africa').replace('%ICON', 'icon-remove')))
        .then(() => client.waitForExist(Location.Zone.search_by_zone_icon.replace('%B', 'Oceania').replace('%ICON', 'icon-remove')));
    });
    test('should click on "Enable selection" action from the "Bulk action" list', () => client.clickOnAction(Location.Zone.action_group_button.replace('%ID', 3), Location.Zone.bulk_action_button, 'EnableSelection'));
    test('should verify that "Africa" & "Oceania" zones are enabled', () => {
      return promise
        .then(() => client.waitForExist(Location.Zone.search_by_zone_icon.replace('%B', 'Africa').replace('%ICON', 'icon-check')))
        .then(() => client.waitForExist(Location.Zone.search_by_zone_icon.replace('%B', 'Oceania').replace('%ICON', 'icon-check')));
    });
    test('should click on "Delete selected" action from the "Bulk action" list', () => client.clickOnAction(Location.Zone.action_group_button.replace('%ID', 5), Location.Zone.bulk_action_button, 'delete'));
    test('should verify the appearance of the green validation', () => client.checkTextValue(Location.Zone.alert_panel.replace('%I', 'success'), '×\nThe selection has been successfully deleted.'));
    test('should verify that "Africa" & "Oceania" zones are not in the list', () => {
      return promise
        .then(() => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'Africa'))
        .then(() => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 1).replace('%ID', 1), 'No records found', 'contain'))
        .then(() => client.searchByValue(Location.Zone.search_zone_input, Location.Zone.search_button, 'Oceania'))
        .then(() => client.checkTextValue(Location.Zone.element_zone_table.replace('%B', 1).replace('%ID', 1), 'No records found', 'contain'))
        .then(() => client.waitForExistAndClick(Location.Zone.reset_button));
    });
    test('should recreate the deleted zones "Africa" and "Oceania"', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu))
        .then(() => client.waitForExistAndClick(Location.Zone.add_new_zone_button))
        .then(() => client.waitAndSetValue(Location.Zone.name_input, 'Africa'))
        .then(() => client.waitForExistAndClick(Location.Zone.save_button))
        .then(() => client.waitForExistAndClick(Location.Zone.add_new_zone_button))
        .then(() => client.waitAndSetValue(Location.Zone.name_input, 'Oceania'))
        .then(() => client.waitForExistAndClick(Location.Zone.save_button));
    });
  }, 'international');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
