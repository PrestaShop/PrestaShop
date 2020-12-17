require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

const loginCommon = require('@commonTests/loginBO');

// Import data
const FakerCustomer = require('@data/faker/customer');

const createCustomerData = new FakerCustomer();

// Importing pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foCreateAccountPage = require('@pages/FO/myAccount/add');
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

describe('Create an account in FO and edit its information', async () => {
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
});
