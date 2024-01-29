// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';
import viewCustomerPage from '@pages/BO/customers/view';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';

// Import data
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_CRUDCustomer';

// Create, Read, Update and Delete Customer in BO
describe('BO - Customers - Customers : CRUD Customer in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const createCustomerData: CustomerData = new CustomerData();
  const editCustomerData: CustomerData = new CustomerData({enabled: false});

  const createCustomerName: string = `${createCustomerData.firstName[0]}. ${createCustomerData.lastName}`;
  const editCustomerName: string = `${editCustomerData.firstName[0]}. ${editCustomerData.lastName}`;

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
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create customer and go to FO to check sign in is OK
  describe('Create customer in BO and check sign in in FO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
      expect(textResult).to.equal(customersPage.successfulCreationMessage);

      const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
      expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 2 : Check sign in FO
  describe('Check sign in in FO by new customer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);
      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should sign in by new customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInWithNewCustomer', baseContext);

      await foHomePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutFO', baseContext);

      // Logout in FO
      await foHomePage.logout(page);

      const isCustomerConnected = await foHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  // 3 : View customer and check data
  describe('View created customer', async () => {
    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(createCustomerData.firstName);
      expect(cardHeaderText).to.contains(createCustomerData.lastName);
      expect(cardHeaderText).to.contains(createCustomerData.email);

      const numOrders = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Orders');
      expect(parseInt(numOrders, 10)).equal(0);

      const numCarts = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Carts');
      expect(parseInt(numCarts, 10)).equal(0);
    });
  });

  // 4 : Update customer (enabled = false)
  describe('Update customer in BO', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPageToUpdate', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await customersPage.goToEditCustomerPage(page, 1);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleEdit);
    });

    it('should update customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, editCustomerData);
      expect(textResult).to.equal(customersPage.successfulUpdateMessage);

      const numberOfCustomersAfterUpdate = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterUpdate).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 5 : Check sign in FO (customer can't sign in FO)
  describe('Check sign in in FO by disabled customer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);
      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check sign in by edited account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFOSignInWithUpdatedCustomer', baseContext);

      // Try to log in
      await foHomePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, editCustomerData);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);

      const loginError = await foLoginPage.getLoginError(page);
      expect(loginError).to.contains(foLoginPage.disabledAccountErrorText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  // 6 : View updated customer and check data
  describe('View updated customer', async () => {
    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedCustomer', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(editCustomerName));
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(editCustomerData.firstName);
      expect(cardHeaderText).to.contains(editCustomerData.lastName);
      expect(cardHeaderText).to.contains(editCustomerData.email);

      const numOrders = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Orders');
      expect(parseInt(numOrders, 10)).equal(0);

      const numCarts = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Carts');
      expect(parseInt(numCarts, 10)).equal(0);
    });
  });

  // 7 : Delete customer from BO
  describe('Delete customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      expect(textResult).to.equal(customersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });
});
