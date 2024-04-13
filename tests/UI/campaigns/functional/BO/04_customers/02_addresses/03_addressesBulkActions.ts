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

const baseContext: string = 'functional_BO_customers_addresses_addressesBulkActions';

// Create addresses then delete with Bulk actions
describe('BO - Customers - Addresses : Addresses bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  const addressData: FakerAddress = new FakerAddress({address: 'todelete', email: 'pub@prestashop.com', country: 'France'});

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

  // 1 : Create 2 addresses in BO
  describe('Create 2 addresses in BO', async () => {
    [
      {args: {addressToCreate: addressData}},
      {args: {addressToCreate: addressData}},
    ].forEach((test, index: number) => {
      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAddressPage${index + 1}`, baseContext);

        await addressesPage.goToAddNewAddressPage(page);

        const pageTitle = await addAddressPage.getPageTitle(page);
        expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
      });

      it(`should create address n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index + 1}`, baseContext);

        const textResult = await addAddressPage.createEditAddress(page, test.args.addressToCreate);
        expect(textResult).to.equal(addressesPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await addressesPage.getNumberOfElementInGrid(page);
        expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + index + 1);
      });
    });
  });

  // 2 : Delete addresses created with bulk actions
  describe('Delete addresses with Bulk Actions', async () => {
    it(`should filter list by address ${addressData.address}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await addressesPage.resetFilter(page);
      await addressesPage.filterAddresses(page, 'input', 'address1', addressData.address);

      const address = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      expect(address).to.contains(addressData.address);
    });

    it('should delete addresses with Bulk Actions and check addresses Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await addressesPage.deleteAddressesBulkActions(page);
      expect(deleteTextResult).to.be.equal(addressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
