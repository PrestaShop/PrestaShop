// Import utils
import testContext from '@utils/testContext';

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

import {expect} from 'chai';

const baseContext: string = 'functional_BO_customers_customers_customersBulkActions';

/*
Create Customer
Enable/Disable/Delete by Bulk actions
*/
describe('BO - Customers - Customers : Customers bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const firstCustomerData: FakerCustomer = new FakerCustomer({firstName: 'todelete'});
  const secondCustomerData: FakerCustomer = new FakerCustomer({firstName: 'todelete'});

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

  // 1 : Create 2 customers In BO
  describe('Create 2 customers in BO', async () => {
    [
      {args: {customerToCreate: firstCustomerData}},
      {args: {customerToCreate: secondCustomerData}},
    ].forEach((test, index: number) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCustomerPage${index + 1}`, baseContext);

        await boCustomersPage.goToAddNewCustomerPage(page);

        const pageTitle = await boCustomersCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index + 1}`, baseContext);

        const textResult = await boCustomersCreatePage.createEditCustomer(page, test.args.customerToCreate);
        expect(textResult).to.equal(boCustomersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });

  // 2 : Enable/Disable customers by bulk actions
  describe('Enable/Disable customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await boCustomersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} customers with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Customers`, baseContext);

        const textResult = await boCustomersPage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.be.equal(boCustomersPage.successfulUpdateMessage);

        const numberOfCustomersInGrid = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersInGrid).to.be.at.least(2);

        for (let i = 1; i <= numberOfCustomersInGrid; i++) {
          const customerStatus = await boCustomersPage.getCustomerStatus(page, i);
          expect(customerStatus).to.equals(test.args.enabledValue);
        }
      });
    });
  });

  // 3 : Delete Customers created with bulk actions
  describe('Delete customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boCustomersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      expect(textResult).to.contains('todelete');
    });

    it('should delete customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await boCustomersPage.deleteCustomersBulkActions(page);
      expect(deleteTextResult).to.be.equal(boCustomersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
