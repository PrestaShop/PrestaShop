import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomersPage,
  boCustomersCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCustomer,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_customers_editCustomerWithoutGroup';

// Check that editing a customer without any access group is blocked with an inline error
describe('BO - Customers - Customers : Edit customer without group access', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const createCustomerData: FakerCustomer = new FakerCustomer();

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

  // 1 : Create a fresh customer so the test is self-contained
  describe('Create customer in BO', async () => {
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

  // 2 : Edit the customer and try to save with no group access
  describe('Edit customer without group access', async () => {
    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEditCustomer', baseContext);

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

    it('should uncheck all group access and try to save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveWithoutGroup', baseContext);

      await boCustomersCreatePage.uncheckAllGroupAccess(page);
      await boCustomersCreatePage.clickOnSaveButton(page);

      // The save is blocked : the page does not navigate to the list, we stay on the edit page
      const pageTitle = await boCustomersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleEdit);

      const errorMessage = await boCustomersCreatePage.getGroupAccessErrorMessage(page);
      expect(errorMessage).to.contains('You must select at least one group.');
    });
  });

  // 3 : Delete the created customer
  describe('Delete customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await boCustomersCreatePage.goToSubMenu(
        page,
        boCustomersCreatePage.customersParentLink,
        boCustomersCreatePage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await boCustomersPage.deleteCustomer(page, 1);
      expect(textResult).to.equal(boCustomersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
