// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import addressesPage from '@pages/BO/customers/addresses';
import addAddressPage from '@pages/BO/customers/addresses/add';
import dashboardPage from '@pages/BO/dashboard';

import {
  // Import data
  FakerAddress,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;
let numberOfAddresses: number = 0;

/**
 * Function to create address
 * @param addressData {AddressData} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createAddressTest(addressData: FakerAddress, baseContext: string = 'commonTests-createAddressTest'): void {
  describe('PRE-TEST: Create address', async () => {
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

    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await addressesPage.goToAddNewAddressPage(page);

      const pageTitle = await addAddressPage.getPageTitle(page);
      expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, addressData);
      expect(textResult).to.equal(addressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await addressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });
}

/**
 * Function to bulk delete addresses
 * @param filterBy {string} Value to filter by
 * @param value {string} Value to set in filter input to delete
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteAddressesTest(
  filterBy: string,
  value: string,
  baseContext: string = 'commonTests-deleteAddressesByBulkActionsTest',
): void {
  describe('POST-TEST: Delete addresses by bulk actions', async () => {
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

    it(`should filter list by '${filterBy}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await addressesPage.filterAddresses(page, 'input', filterBy, value);

      const address = await addressesPage.getTextColumnFromTableAddresses(page, 1, filterBy);
      expect(address).to.contains(value);
    });

    it('should delete addresses with bulk actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await addressesPage.deleteAddressesBulkActions(page);
      expect(deleteTextResult).to.be.equal(addressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.be.above(0);
    });
  });
}

export {createAddressTest, bulkDeleteAddressesTest};
