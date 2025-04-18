import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAddressesPage,
  boAddressesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAddress,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddresses).to.be.above(0);
    });

    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await boAddressesPage.goToAddNewAddressPage(page);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await boAddressesCreatePage.createEditAddress(page, addressData);
      expect(textResult).to.equal(boAddressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await boAddressesPage.getNumberOfElementInGrid(page);
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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddresses).to.be.above(0);
    });

    it(`should filter list by '${filterBy}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await boAddressesPage.filterAddresses(page, 'input', filterBy, value);

      const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, filterBy);
      expect(address).to.contains(value);
    });

    it('should delete addresses with bulk actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await boAddressesPage.deleteAddressesBulkActions(page);
      expect(deleteTextResult).to.be.equal(boAddressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.be.above(0);
    });
  });
}

export {createAddressTest, bulkDeleteAddressesTest};
