// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';
import dashboardPage from '@pages/BO/dashboard';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  // 1 : Create 2 customers In BO
  describe('Create 2 customers in BO', async () => {
    [
      {args: {customerToCreate: firstCustomerData}},
      {args: {customerToCreate: secondCustomerData}},
    ].forEach((test, index: number) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCustomerPage${index + 1}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);

        const pageTitle = await addCustomerPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index + 1}`, baseContext);

        const textResult = await addCustomerPage.createEditCustomer(page, test.args.customerToCreate);
        expect(textResult).to.equal(customersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });

  // 2 : Enable/Disable customers by bulk actions
  describe('Enable/Disable customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} customers with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Customers`, baseContext);

        const textResult = await customersPage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.be.equal(customersPage.successfulUpdateMessage);

        const numberOfCustomersInGrid = await customersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersInGrid).to.be.at.least(2);

        for (let i = 1; i <= numberOfCustomersInGrid; i++) {
          const customerStatus = await customersPage.getCustomerStatus(page, i);
          expect(customerStatus).to.equals(test.args.enabledValue);
        }
      });
    });
  });

  // 3 : Delete Customers created with bulk actions
  describe('Delete customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      expect(textResult).to.contains('todelete');
    });

    it('should delete customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
