require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

const loginCommon = require('@commonTests/loginBO');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const FakerAddress = require('@data/faker/address');

const createAddressData = new FakerAddress({country: 'France'});
const editAddressData = new FakerAddress({country: 'France'});

// Import pages
// FO
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAddressesPage = require('@pages/FO/myAccount/addresses');
const foAddAddressesPage = require('@pages/FO/myAccount/addAddress');

// BO
const boDashboardPage = require('@pages/BO/dashboard');
const boAddressesPage = require('@pages/BO/customers/addresses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_CRUDAddress';

let browserContext;
let page;

/*
Create address in FO
Check creation in BO
Update the created address in FO
Check the Update in BO
Delete the address in FO
Check that the address is deleted
 */
describe('FO - Account : CRUD address', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foHomePage.goToFo(page);
    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

    await foHomePage.goToLoginPage(page);

    const pageHeaderTitle = await foLoginPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
  });

  it('Should sign in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

    await foLoginPage.customerLogin(page, DefaultCustomer);
    const isCustomerConnected = await foMyAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should go to addresses page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAddressesPage', baseContext);

    await foHomePage.goToMyAccountPage(page);
    await foMyAccountPage.goToAddressesPage(page);
    const pageHeaderTitle = await foAddressesPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(foAddressesPage.pageTitle);
  });

  describe('Create new address in FO', async () => {
    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foAddAddressesPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddAddressesPage.creationFormTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await foAddAddressesPage.setAddress(page, createAddressData);
      await expect(textResult).to.equal(foAddressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Go to BO and check the created address', async () => {
    before(async () => {
      page = await helper.newTab(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOAddressesPageAfterCreation', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should check the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedAddress', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', createAddressData.address);

      const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      await expect(textColumn).to.equal(createAddressData.address);
    });

    after(async () => {
      page = await boAddressesPage.closePage(browserContext, page, 0);
    });
  });

  describe('Update the created address on FO', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await foAddressesPage.goToEditAddressPage(page);

      const pageHeaderTitle = await foAddAddressesPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddAddressesPage.updateFormTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await foAddAddressesPage.setAddress(page, editAddressData);
      await expect(textResult).to.equal(foAddressesPage.updateAddressSuccessfulMessage);
    });
  });

  describe('Go to BO and check the updated address', async () => {
    before(async () => {
      page = await helper.newTab(browserContext);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOAddressesPageAfterUpdate', baseContext);

      await boDashboardPage.goTo(page, global.BO.URL);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      const pageTitle = await boAddressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should check the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedAddress', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', editAddressData.address);

      const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      await expect(textColumn).to.equal(editAddressData.address);
    });

    after(async () => {
      page = await boAddressesPage.closePage(browserContext, page, 0);
    });
  });

  describe('Update the created address on FO', async () => {
    it('should delete the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await foAddressesPage.deleteAddress(page);
      await expect(textResult).to.equal(foAddressesPage.deleteAddressSuccessfulMessage);
    });
  });

  describe('Go to BO and check the deleted address', async () => {
    before(async () => {
      page = await helper.newTab(browserContext);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOAddressesPageAfterDelete', baseContext);

      await boDashboardPage.goTo(page, global.BO.URL);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      const pageTitle = await boAddressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should check that the address is deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedAddress', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', editAddressData.address);

      const numberOfAddresses = await boAddressesPage.getNumberOfElementInGrid(page);

      // Expecting that there is no address after filter
      // or that the existing address has not the same value as the one deleted
      try {
        await expect(numberOfAddresses).to.equal(0);
      } catch (e) {
        for (let row = 1; row <= numberOfAddresses; row++) {
          const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, row, 'address1');
          await expect(textColumn).to.not.equal(editAddressData.address);
        }
      }
    });
  });
});
