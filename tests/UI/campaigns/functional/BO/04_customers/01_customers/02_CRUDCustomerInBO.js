require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO ages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');

// Import data
const CustomerFaker = require('@data/faker/customer');

const baseContext = 'functional_BO_customers_customers_CRUDCustomerInBO';

let browserContext;
let page;
let numberOfCustomers = 0;

const createCustomerData = new CustomerFaker();
const editCustomerData = new CustomerFaker({enabled: false});

// Create, Read, Update and Delete Customer in BO
describe('BO - Customers - Customers : CRUD Customer in BO', async () => {
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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );

    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create customer and go to FO to check sign in is OK
  describe('Create customer in BO and check sign in in FO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);

      const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should login in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO_1', baseContext);

      // Login in FO
      await foHomePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logout_FO', baseContext);

      // Logout in FO
      await foHomePage.logout(page);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_1', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  // 2 : View Customer and check Creation data are correct
  describe('View customer', async () => {
    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);
      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });
  });

  // 3 : Update customer and check that customer can't sign in in BO (enabled = false)
  describe('Update customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPageToUpdate', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await customersPage.goToEditCustomerPage(page, 1);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleEdit);
    });

    it('should update customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, editCustomerData);
      await expect(textResult).to.equal(customersPage.successfulUpdateMessage);

      const numberOfCustomersAfterUpdate = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterUpdate).to.be.equal(numberOfCustomers + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should check sign in with edited account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSignIn', baseContext);

      // Try to login
      await foHomePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, editCustomerData);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_2', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  // 4 : View customer and check data are correct
  describe('View customer updated', async () => {
    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedCustomer', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);
      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
    });
  });

  // 5 : Delete customer from BO
  describe('Delete customer', async () => {
    it('should go to \'Customer > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email ${editCustomerData.email}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      await expect(textResult).to.equal(customersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterDelete).to.equal(numberOfCustomers);
    });
  });
});
