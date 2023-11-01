// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByGuestTest} from '@commonTests/FO/order';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customersPage from '@pages/BO/customers';
import viewCustomerPage from '@pages/BO/customers/view';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext = 'functional_BO_customers_customers_transformGuestToCustomer';

describe('BO - Customers _ Customers : Transform guest to customer account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number;

  const customerData: CustomerData = new CustomerData({password: ''});
  const addressData: AddressData = new AddressData({country: 'France'});

  const orderData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO by guest
  createOrderByGuestTest(orderData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Transform a guest to customer account', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(1);
    });

    it('should filter customers group by guest', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'default_group', 'Guest');

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'default_group');
      expect(textEmail).to.eq('Guest');
    });

    it('should go to view customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewPage', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(`${customerData.firstName[0]}. ${customerData.lastName}`));
    });

    it('should click on transform to customer account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnTransferToCustomerAccount', baseContext);

      const successMessage = await viewCustomerPage.clickOnTransformToCustomerAccount(page);
      expect(successMessage).to.contains(viewCustomerPage.successfulCreationMessage);
    });

    it('should check the transform to customer account button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isButtonVisible', baseContext);

      const isButtonVisible = await viewCustomerPage.isTransformToCustomerAccountButtonVisible(page);
      expect(isButtonVisible).to.eq(false);
    });

    it('should go back to Customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should check that the customers table is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoRecordFound', baseContext);

      const noRecordsFoundText = await customersPage.getTextWhenTableIsEmpty(page);
      expect(noRecordsFoundText).to.contains('No records found');
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await customersPage.resetFilter(page);

      const numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.at.least(0);
    });
  });

  // Post-condition: Delete customers
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
