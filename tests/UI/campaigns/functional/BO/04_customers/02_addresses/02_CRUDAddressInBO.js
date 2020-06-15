/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddressesPage = require('@pages/BO/customers/addresses');
const AddAddressPage = require('@pages/BO/customers/addresses/add');

// Import data
const AddressFaker = require('@data/faker/address');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_addresses_CRUDAddressesInBO';


let browserContext;
let page;
let numberOfAddresses = 0;

const createAddressData = new AddressFaker({email: 'pub@prestashop.com', country: 'France'});
const editAddressData = new AddressFaker({country: 'France'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addressesPage: new AddressesPage(page),
    addAddressPage: new AddAddressPage(page),
  };
};

// Create, Read, Update and Delete address in BO
describe('Create, Read, Update and Delete address in BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to addresses page
  loginCommon.loginBO();

  it('should go to \'Customers>Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.addressesLink,
    );

    await this.pageObjects.addressesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });
  // 1 : Create address
  describe('Create address in BO', async () => {
    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await this.pageObjects.addressesPage.goToAddNewAddressPage();
      const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await this.pageObjects.addAddressPage.createEditAddress(createAddressData);
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await this.pageObjects.addressesPage.getNumberOfElementInGrid();
      await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 2 : Update address
  describe('Update address Created', async () => {
    it('should go to \'Customers>Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToUpdate', baseContext);

      await this.pageObjects.addressesPage.goToSubMenu(
        this.pageObjects.addressesPage.customersParentLink,
        this.pageObjects.addressesPage.addressesLink,
      );

      const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
    });

    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await this.pageObjects.addressesPage.resetFilter();

      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'firstname',
        createAddressData.firstName,
      );

      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'lastname',
        createAddressData.lastName,
      );

      const firstName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'firstname');
      await expect(firstName).to.contains(createAddressData.firstName);

      const lastName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'lastname');
      await expect(lastName).to.contains(createAddressData.lastName);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await this.pageObjects.addressesPage.goToEditAddressPage(1);
      const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleEdit);
    });

    it('should update address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await this.pageObjects.addAddressPage.createEditAddress(editAddressData);
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulUpdateMessage);

      const numberOfAddressesAfterUpdate = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
      await expect(numberOfAddressesAfterUpdate).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 3 : Delete address from BO
  describe('Delete address', async () => {
    it('should go to \'Customers>Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDelete', baseContext);

      await this.pageObjects.addressesPage.goToSubMenu(
        this.pageObjects.addressesPage.customersParentLink,
        this.pageObjects.addressesPage.addressesLink,
      );

      const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
    });

    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await this.pageObjects.addressesPage.resetFilter();

      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'firstname',
        editAddressData.firstName,
      );

      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'lastname',
        editAddressData.lastName,
      );

      const firstName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'firstname');
      await expect(firstName).to.contains(editAddressData.firstName);

      const lastName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'lastname');
      await expect(lastName).to.contains(editAddressData.lastName);
    });

    it('should delete address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await this.pageObjects.addressesPage.deleteAddress(1);
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulDeleteMessage);

      const numberOfAddressesAfterDelete = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
      await expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });
  });
});
