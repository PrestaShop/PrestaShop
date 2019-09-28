/**
 * This script is based on the scenario described in this test link
 * [id="PS-153"][Name="By IP Adress"]
 **/
const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Localization} = require('../../../../selectors/BO/international/localization');
const {Menu} = require('../../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let promise = Promise.resolve();

scenario('"Geolocation by IP Address"', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    welcomeScenarios.findAndCloseWelcomeModal();
    scenario('Change Geolocation by IP address', client => {
      test('should go to "International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Geolocation" subtab', () => client.waitForExistAndClick(Menu.Improve.International.geolocation_tab));
      test('should click on "this file" link', () => client.waitForExistAndClick(Localization.Geolocation.download_file_link));
      test('Download and dezip the folder', () => {
        return promise
          .then(() => client.getAttributeInVar(Localization.Geolocation.download_file_link, "href", "link"))
          .then(() => client.getFileName(tab["link"]))
          .then(() => client.unzipFile(global.downloadsFolderPath, global.downloadedFileName));
      });
      test('should Put the file in /app/Resources/geoip', () => client.moveFile(global.downloadsFolderPath, global.downloadedFileName, global.shopPath + "/app/Resources/geoip"));
      test('should change the value of the geolocation by IP Address to "Yes"', () => client.waitForExistAndClick(Localization.Geolocation.ip_address_yes_label));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Geolocation.save_geolocation_ip_address_button));
      test('should delete the file from /app/Resources/geoip', () => client.deleteFile(global.downloadedFileName, global.shopPath + "/app/Resources/geoip"));
      test('should change the value of the geolocation by IP Address to "No"', () => client.waitForExistAndClick(Localization.Geolocation.ip_address_no_label));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Geolocation.save_geolocation_ip_address_button));
    }, 'international');
    scenario('Logout from the Back Office', client => {
      test('should logout successfully from Back Office', () => client.signOutBO());
    }, 'common_client');
  },
  'common_client', true);
