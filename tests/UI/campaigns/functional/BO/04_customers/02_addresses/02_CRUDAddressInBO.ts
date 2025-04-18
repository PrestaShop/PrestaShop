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

  // 1 : Create address
  describe('Create address in BO', async () => {
    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await boAddressesPage.goToAddNewAddressPage(page);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await boAddressesCreatePage.createEditAddress(page, createAddressData);
      expect(textResult).to.equal(boAddressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 2 : Update address
  describe('Update address', async () => {
    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', createAddressData.firstName);
      await boAddressesPage.filterAddresses(page, 'input', 'lastname', createAddressData.lastName);

      const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(createAddressData.firstName);

      const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.contains(createAddressData.lastName);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boAddressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
    });

    it('should update address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await boAddressesCreatePage.createEditAddress(page, editAddressData);
      expect(textResult).to.equal(boAddressesPage.successfulUpdateMessage);

      const numberOfAddressesAfterUpdate = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterUpdate).to.be.equal(numberOfAddresses + 1);
    });
  });

  // 3 : Delete address from BO
  describe('Delete address', async () => {
    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', editAddressData.firstName);
      await boAddressesPage.filterAddresses(page, 'input', 'lastname', editAddressData.lastName);

      const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(editAddressData.firstName);

      const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.contains(editAddressData.lastName);
    });

    it('should delete address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await boAddressesPage.deleteAddress(page, 1);
      expect(textResult).to.equal(boAddressesPage.successfulDeleteMessage);

      const numberOfAddressesAfterDelete = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfAddressesAfterReset = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
    });
  });
});
