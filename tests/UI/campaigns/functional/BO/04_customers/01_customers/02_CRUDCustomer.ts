import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomersPage,
  boCustomersCreatePage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCustomer,
  foClassicHomePage,
  foClassicLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_customers_CRUDCustomer';

// Create, Read, Update and Delete Customer in BO
describe('BO - Customers - Customers : CRUD Customer in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const createCustomerData: FakerCustomer = new FakerCustomer();
  const editCustomerData: FakerCustomer = new FakerCustomer({enabled: false});

  const createCustomerName: string = `${createCustomerData.firstName[0]}. ${createCustomerData.lastName}`;
  const editCustomerName: string = `${editCustomerData.firstName[0]}. ${editCustomerData.lastName}`;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );
    await boCustomersPage.closeSfToolBar(page);

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create customer and go to FO to check sign in is OK
  describe('Create customer in BO and check sign in in FO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await boCustomersPage.goToAddNewCustomerPage(page);

      const pageTitle = await boCustomersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await boCustomersCreatePage.createEditCustomer(page, createCustomerData);
      expect(textResult).to.equal(boCustomersPage.successfulCreationMessage);

      const numberOfCustomersAfterCreation = await boCustomersPage.getNumberOfElementInGrid(page);
      expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 2 : Check sign in FO
  describe('Check sign in in FO by new customer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      // View shop
      page = await boCustomersPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should sign in by new customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInWithNewCustomer', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutFO', baseContext);

      // Logout in FO
      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });
  });

  // 3 : View customer and check data
  describe('View created customer', async () => {
    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

      await boCustomersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(createCustomerName));
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);

      const cardHeaderText = await boCustomersViewPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(createCustomerData.firstName);
      expect(cardHeaderText).to.contains(createCustomerData.lastName);
      expect(cardHeaderText).to.contains(createCustomerData.email);

      const numOrders = await boCustomersViewPage.getNumberOfElementFromTitle(page, 'Orders');
      expect(parseInt(numOrders, 10)).equal(0);

      const numCarts = await boCustomersViewPage.getNumberOfElementFromTitle(page, 'Carts');
      expect(parseInt(numCarts, 10)).equal(0);
    });
  });

  // 4 : Update customer (enabled = false)
  describe('Update customer in BO', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPageToUpdate', baseContext);

      await boCustomersViewPage.goToSubMenu(
        page,
        boCustomersViewPage.customersParentLink,
        boCustomersViewPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateCustomer', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boCustomersPage.goToEditCustomerPage(page, 1);

      const pageTitle = await boCustomersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleEdit);
    });

    it('should update customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);

      const textResult = await boCustomersCreatePage.createEditCustomer(page, editCustomerData);
      expect(textResult).to.equal(boCustomersPage.successfulUpdateMessage);

      const numberOfCustomersAfterUpdate = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterUpdate).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 5 : Check sign in FO (customer can't sign in FO)
  describe('Check sign in in FO by disabled customer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      // View shop
      page = await boCustomersPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check sign in by edited account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFOSignInWithUpdatedCustomer', baseContext);

      // Try to log in
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, editCustomerData);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);

      const loginError = await foClassicLoginPage.getLoginError(page);
      expect(loginError).to.contains(foClassicLoginPage.disabledAccountErrorText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });
  });

  // 6 : View updated customer and check data
  describe('View updated customer', async () => {
    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedCustomer', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedCustomer', baseContext);

      await boCustomersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(editCustomerName));
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);

      const cardHeaderText = await boCustomersViewPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(editCustomerData.firstName);
      expect(cardHeaderText).to.contains(editCustomerData.lastName);
      expect(cardHeaderText).to.contains(editCustomerData.email);

      const numOrders = await boCustomersViewPage.getNumberOfElementFromTitle(page, 'Orders');
      expect(parseInt(numOrders, 10)).equal(0);

      const numCarts = await boCustomersViewPage.getNumberOfElementFromTitle(page, 'Carts');
      expect(parseInt(numCarts, 10)).equal(0);
    });
  });

  // 7 : Delete customer from BO
  describe('Delete customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await boCustomersViewPage.goToSubMenu(
        page,
        boCustomersViewPage.customersParentLink,
        boCustomersViewPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await boCustomersPage.deleteCustomer(page, 1);
      expect(textResult).to.equal(boCustomersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCustomersAfterReset = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });
});
