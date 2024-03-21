// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import addressesPage from '@pages/BO/customers/addresses';
import addAddressPage from '@pages/BO/customers/addresses/add';
import dashboardPage from '@pages/BO/dashboard';

import {
  FakerAddress,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_addresses_CRUDAddressInBO';

// Create, Read, Update and Delete address in BO
describe('BO - Customers - Addresses : CRUD Address in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  const createAddressData: FakerAddress = new FakerAddress({email: 'pub@prestashop.com', country: 'France'});
  const editAddressData: FakerAddress = new FakerAddress({country: 'France'});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Customers > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.addressesLink,
    );
    await addressesPage.closeSfToolBar(page);

    const pageTitle = await addressesPage.getPageTitle(page);
    expect(pageTitle).to.contains(addressesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create address
  describe('Create address in BO', async () => {
    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await addressesPage.goToAddNewAddressPage(page);

      const pageTitle = await addAddressPage.getPageTitle(page);
      expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, createAddressData);
      expect(textResult).to.equal(addressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await addressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 2 : Update address
  describe('Update address', async () => {
    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await addressesPage.resetFilter(page);
      await addressesPage.filterAddresses(page, 'input', 'firstname', createAddressData.firstName);
      await addressesPage.filterAddresses(page, 'input', 'lastname', createAddressData.lastName);

      const firstName = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(createAddressData.firstName);

      const lastName = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.contains(createAddressData.lastName);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await addressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await addAddressPage.getPageTitle(page);
      expect(pageTitle).to.contains(addAddressPage.pageTitleEdit);
    });

    it('should update address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, editAddressData);
      expect(textResult).to.equal(addressesPage.successfulUpdateMessage);

      const numberOfAddressesAfterUpdate = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterUpdate).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 3 : Delete address from BO
  describe('Delete address', async () => {
    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await addressesPage.resetFilter(page);
      await addressesPage.filterAddresses(page, 'input', 'firstname', editAddressData.firstName);
      await addressesPage.filterAddresses(page, 'input', 'lastname', editAddressData.lastName);

      const firstName = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(editAddressData.firstName);

      const lastName = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.contains(editAddressData.lastName);
    });

    it('should delete address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await addressesPage.deleteAddress(page, 1);
      expect(textResult).to.equal(addressesPage.successfulDeleteMessage);

      const numberOfAddressesAfterDelete = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
    });
  });
});
