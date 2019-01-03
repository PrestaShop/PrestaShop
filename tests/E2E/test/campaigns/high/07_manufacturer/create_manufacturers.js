/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Brands} = require('../../../selectors/BO/catalogpage/Manufacturers/brands');
const {BrandAddress} = require('../../../selectors/BO/catalogpage/Manufacturers/brands_address');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Create "Brand" - "Brand address"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'manufacturers');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Create a new "Brand"', client => {
    test('should go to "Brands & Suppliers" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu));
    test('should click on "Add new brand" button', () => client.waitForExistAndClick(Brands.new_brand_button));
    test('should set the "Name" input', () => client.waitAndSetValue(Brands.name_input, 'PrestaShop' + date_time));
    test('should set the "Short Description" input', () => client.setEditorText(Brands.short_description_input, 'short description'));
    test('should set the "Description" input', () => client.setEditorText(Brands.description_input, 'description'));
    test('should upload "Picture" to the brand', () => client.uploadPicture("prestashop.png", Brands.image_input, "logo"));
    test('should set the "Meta title" input', () => client.waitAndSetValue(Brands.meta_title_input, "meta title"));
    test('should set the "Meta description" input', () => client.waitAndSetValue(Brands.meta_description_input, "meta description"));
    test('should set the "Meta keywords" input', () => client.addMetaKeywords(Brands.meta_keywords_input));
    test('should click on "Activate" button', () => client.waitForExistAndClick(Brands.active_button));
    test('should click on "Save" button', () => client.waitForExistAndClick(Brands.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
  }, 'manufacturers');

  scenario('Create a new "Brand address"', client => {
    test('should click on "Add new brand address" button', () => client.waitForExistAndClick(BrandAddress.new_brand_address_button));
    test('should Choose the brand name', () => client.waitAndSelectByVisibleText(BrandAddress.branch_select, 'PrestaShop' + date_time));
    test('should set the "Last name" input', () => client.waitAndSetValue(BrandAddress.last_name_input, "Prestashop"));
    test('should set the "First name" input', () => client.waitAndSetValue(BrandAddress.first_name_input, "Prestashop"));
    test('should set the "Address" input', () => client.waitAndSetValue(BrandAddress.address_input, "12 rue d'amesterdam"));
    test('should set the "Second address" input', () => client.waitAndSetValue(BrandAddress.secondary_address, "RDC"));
    test('should set the "Zip code" input', () => client.waitAndSetValue(BrandAddress.postal_code_input, "75009"));
    test('should set the "City" input', () => client.waitAndSetValue(BrandAddress.city_input, "paris"));
    test('should choose the country', () => client.waitAndSelectByValue(BrandAddress.country, "8"));
    test('should set the "Phone" input', () => client.waitAndSetValue(BrandAddress.phone_input, "0140183004"));
    test('should set the "Other information" input', () => client.waitAndSetValue(BrandAddress.other_input, "azerty"));
    test('should click on "Save" button', () => client.waitForExistAndClick(BrandAddress.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
  }, 'manufacturers');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'manufacturers')

}, 'manufacturers', true);
