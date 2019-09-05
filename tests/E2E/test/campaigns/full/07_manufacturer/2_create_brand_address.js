/**
 * This script is based on the scenario described in this test link
 * [id="PS-48"][Name="Add a new manufacturer address"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Brands} = require('../../../selectors/BO/catalogpage/Manufacturers/brands');
const commonManufacturers = require('../../common_scenarios/manufacturers');
const welcomeScenarios = require('../../common_scenarios/welcome');
let brandData = {
  name: 'PrestaShopBrand',
  shortDescription: 'short description',
  description: 'description',
  picture: 'prestashop.png',
  metaTitle: 'meta title',
  metaDescription: 'meta description',
  metaKeywords: {
    1: 'first key',
    2: 'second key'
  },
};
let brandAddressData = {
  brand: 'PrestaShopBrand',
  lastName: 'Prestashop',
  firstName: 'Prestashop',
  address: '12 rue Amesterdam',
  secondAddress: 'RDC',
  postalCode: '75009',
  city: 'paris',
  country: 'France',
  homePhone: '0140183004',
  mobilePhone: '0160183004',
  other: 'other',
};

scenario('Create "Brand address"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();

  commonManufacturers.createBrand(brandData);
  commonManufacturers.createBrandAddress(brandAddressData);
  scenario('Check the created brand', client => {
    test('should search for the created brand', () => client.searchByValue(Brands.filter_name_input, Brands.brand_search_button, brandData.name + date_time));
    test('should verify that addresses is equal to "1"', () => client.checkTextValue(Brands.brand_column.replace('%TR', 1).replace('%COL', 5), '1'));
    test('should verify that the table contains only one address', () => client.waitForExistAndClick(Brands.brand_reset_button));
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');

}, 'common_client', true);
