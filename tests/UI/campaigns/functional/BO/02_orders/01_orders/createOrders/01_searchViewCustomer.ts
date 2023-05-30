// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import viewCustomerPage from '@pages/BO/customers/view';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';

// Import data
import Customers from '@data/demo/customers';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Frame, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_searchViewCustomer';

/*
Pre-condition:
- Create disabled customer
- Create customer with lastName 'DOE'
Scenario:
- Search for non existent customer and check error message
- Search for disabled customer and check error message
- Search for customers with lastName 'DOE' and check result number
- Check displayed customer card then click on choose
- Click on details button and check customer details
Pre-condition:
- Delete created customers
 */
describe('BO - Orders - Create order : Search and view customer details from new order page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let customerIframe: Frame|null;

  const nonExistentCustomer: CustomerData = new CustomerData();
  const disabledCustomer: CustomerData = new CustomerData({enabled: false});
  const newCustomer: CustomerData = new CustomerData({
    firstName: 'Jane',
    lastName: 'DOE',
    defaultCustomerGroup: 'Customer',
    enabled: true,
  });

  // Pre-condition: Create disabled customer
  createCustomerTest(disabledCustomer, `${baseContext}_preTest_1`);

  // Pre-condition: Create new customer
  createCustomerTest(newCustomer, `${baseContext}_preTest_2`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Search for customers
  describe('Search for customers', () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    [
      {
        testIdentifier: 'checkNonExistentCustomerError',
        customerType: 'non existent',
        customer: nonExistentCustomer,
      },
      {
        testIdentifier: 'checkDisabledCustomerError',
        customerType: 'disabled',
        customer: disabledCustomer,
      },
    ].forEach((step) => {
      it(`should search for ${step.customerType} customer and check error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', step.testIdentifier, baseContext);

        await addOrderPage.searchCustomer(page, step.customer.email);

        const errorDisplayed = await addOrderPage.getNoCustomerFoundError(page);
        await expect(errorDisplayed, 'Error is not correct').to.equal(addOrderPage.noCustomerFoundText);
      });
    });

    it('should search for the customer with lastName \'Doe\' and check result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.lastName);

      const searchResultNumber = await addOrderPage.getCustomersSearchNumber(page);
      await expect(searchResultNumber).to.be.equal(2);
    });

    it('should check that first customer card contain \'Name, Email, birthdate and groups\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstSearchResult', baseContext);

      const defaultCustomerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      await expect(defaultCustomerName).to.contains(
        `${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`,
      );

      const customerCardContent = await addOrderPage.getCustomerCardBody(page, 1);
      await expect(customerCardContent)
        .to.contains(Customers.johnDoe.email)
        .and.to.contains(Customers.johnDoe.birthDate.toJSON().slice(0, 10))
        .and.to.contains(Customers.johnDoe.defaultCustomerGroup);
    });

    it('should check that second customer card contain \'Name, Email, birthdate and groups\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondSearchResult', baseContext);

      const newCustomerName = await addOrderPage.getCustomerNameFromResult(page, 2);
      await expect(newCustomerName).to.contains(`${newCustomer.firstName} ${newCustomer.lastName}`);

      const customerCardContent = await addOrderPage.getCustomerCardBody(page, 2);
      await expect(customerCardContent)
        .to.contains(newCustomer.email)
        .and.to.contains(`${newCustomer.yearOfBirth}-${newCustomer.monthOfBirth}-${newCustomer.dayOfBirth}`)
        .and.to.contains(newCustomer.defaultCustomerGroup);
    });

    it(
      `should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

        await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

        const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
        await expect(isCartsTableVisible).to.be.true;
      },
    );
  });

  // 2 - View customer details
  describe('View customer details', async () => {
    it('should click on \'Details\' button from customer card', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailButton', baseContext);

      const isIframeVisible = await addOrderPage.clickOnDetailsButton(page);
      await expect(isIframeVisible).to.be.true;
    });

    it('should check the existence of personal information block in the iframe', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformation', baseContext);

      customerIframe = await addOrderPage.getCustomerIframe(page, Customers.johnDoe.id);
      await expect(customerIframe).to.be.not.null;

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(customerIframe!);
      await expect(cardHeaderText).to.contains(Customers.johnDoe.firstName);
      await expect(cardHeaderText).to.contains(Customers.johnDoe.lastName);
      await expect(cardHeaderText).to.contains(Customers.johnDoe.email);
    });

    [
      {args: {blockName: 'Orders', number: 5}},
      {args: {blockName: 'Carts', number: 6}},
      {args: {blockName: 'Purchased products', number: 6}},
      {args: {blockName: 'Messages', number: 0}},
      {args: {blockName: 'Vouchers', number: 0}},
      {args: {blockName: 'Last emails', number: 0}},
      {args: {blockName: 'Last connections', number: 0}},
      {args: {blockName: 'Groups', number: 1}},
      {args: {blockName: 'Addresses', number: 2}},
    ].forEach((test) => {
      it(`should check the ${test.args.blockName} number`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.blockName}Number`, baseContext);

        const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(customerIframe!, test.args.blockName);
        await expect(parseInt(cardHeaderText, 10)).to.be.at.least(test.args.number);
      });
    });

    it('should check the existence of add private note block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddPrivateNote', baseContext);

      const isVisible = await viewCustomerPage.isPrivateNoteBlockVisible(customerIframe!);
      await expect(isVisible).to.be.true;
    });
  });

  // Post-condition: Delete disabled customer
  deleteCustomerTest(disabledCustomer, `${baseContext}_postTest_1`);

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomer, `${baseContext}_postTest_2`);
});
