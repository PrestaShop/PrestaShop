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

const baseContext: string = 'functional_BO_customers_addresses_addressesBulkActions';

// Create addresses then delete with Bulk actions
describe('BO - Customers - Addresses : Addresses bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  const addressData: FakerAddress = new FakerAddress({address: 'todelete', email: 'pub@prestashop.com', country: 'France'});

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

  // 1 : Create 2 addresses in BO
  describe('Create 2 addresses in BO', async () => {
    [
      {args: {addressToCreate: addressData}},
      {args: {addressToCreate: addressData}},
    ].forEach((test, index: number) => {
      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAddressPage${index + 1}`, baseContext);

        await boAddressesPage.goToAddNewAddressPage(page);

        const pageTitle = await boAddressesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
      });

      it(`should create address n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index + 1}`, baseContext);

        const textResult = await boAddressesCreatePage.createEditAddress(page, test.args.addressToCreate);
        expect(textResult).to.equal(boAddressesPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await boAddressesPage.getNumberOfElementInGrid(page);
        expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + index + 1);
      });
    });
  });

  // 2 : Delete addresses created with bulk actions
  describe('Delete addresses with Bulk Actions', async () => {
    it(`should filter list by address ${addressData.address}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', addressData.address);

      const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      expect(address).to.contains(addressData.address);
    });

    it('should delete addresses with Bulk Actions and check addresses Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await boAddressesPage.deleteAddressesBulkActions(page);
      expect(deleteTextResult).to.be.equal(boAddressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
