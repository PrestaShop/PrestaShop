require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

const loginCommon = require('@commonTests/loginBO');

// Import data
const FakerCustomer = require('@data/faker/customer');

const createCustomerData = new FakerCustomer();
const editCustomerData = new FakerCustomer();

// Import pages
// FO
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foCreateAccountPage = require('@pages/FO/myAccount/add');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAccountIdentityPage = require('@pages/FO/myAccount/identity');

// BO
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_editInformation';

let browserContext;
let page;

/*
Go to FO and create account
Check created account in BO
Go back to FO and edit account information
Check new account information on BO
Delete the created account on BO
 */

describe('FO - Account : Create an account and edit its information', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create new account in FO', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToCreateAccount', baseContext);

      await foHomePage.goToFo(page);
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foHomePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, createCustomerData);

      const isCustomerConnected = await foHomePage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });
  });

  describe('Go to BO and check the created account', async () => {
    before(async () => {
      page = await helper.newTab(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageAfterCreation', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should check the created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textColumn = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textColumn).to.equal(createCustomerData.email);
    });

    after(async () => {
      page = await customersPage.closePage(browserContext, page, 0);
    });
  });

  describe('Edit the created account in FO', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      const pageTitle = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await foMyAccountPage.goToInformationPage(page);

      const pageTitle = await foAccountIdentityPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foAccountIdentityPage.pageTitle);
    });

    it('should edit the account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAccount', baseContext);

      const textResult = await foAccountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData);
      await expect(textResult).to.be.equal(foAccountIdentityPage.successfulUpdateMessage);
    });

    it('should check that the account is still connected after update', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'connectedUpdatedAccount', baseContext);

      const isCustomerConnected = await foAccountIdentityPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });
  });

  describe('Go to BO and check the account after update', async () => {
    before(async () => {
      page = await helper.newTab(browserContext);
    });

    it('should go to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBoAfterUpdate');

      await dashboardPage.goTo(page, global.BO.URL);
      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageAfterUpdate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should check that the updated customer exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textColumn = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textColumn).to.equal(editCustomerData.email);
    });

    it('should delete the account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAccount', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      await expect(textResult).to.equal(customersPage.successfulDeleteMessage);
    });

    after(async () => {
      page = await customersPage.closePage(browserContext, page, 0);
    });
  });
});
