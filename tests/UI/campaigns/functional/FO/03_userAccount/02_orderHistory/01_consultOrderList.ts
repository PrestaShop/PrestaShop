// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import date from '@utils/date';

// Import common tests
import {createAddressTest} from '@commonTests/BO/customers/createDeleteAddress';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/account';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import FO pages
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import foMyAccountPage from '@pages/FO/myAccount';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import {PaymentMethods} from '@data/demo/paymentMethods';
import AddressFaker from '@data/faker/address';
import CustomerFaker from '@data/faker/customer';
import {Order, OrderHistory} from '@data/types/order';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_userAccount_orderHistory_consultOrderList';

/*
Pre-condition:
- Create customer
- Create address
Scenario:
- Go to orders history page
- Check that number of orders is 0
- Create order
- Check that number of orders is 1
- Check the link of My account page
- Check the link of Home page
Post-condition
- Delete customer
 */

describe('FO - Account - Order history : Consult order list', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: CustomerFaker = new CustomerFaker();
  const addressData: AddressFaker = new AddressFaker({
    email: customerData.email,
    country: 'France',
  });
  const orderData: Order = {
    customer: customerData,
    productId: 1,
    productQuantity: 1,
    paymentMethod: PaymentMethods.wirePayment.moduleName,
  };
  const today: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create new account
  createAccountTest(customerData, `${baseContext}_enableNewProduct`);

  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check that no order has been placed in order history', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);

      const isHomePage: boolean = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, customerData);

      const isCustomerConnected: boolean = await foMyAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should check number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders1', baseContext);

      const numberOfOrders: number = await foOrderHistoryPage.getNumberOfOrders(page);
      await expect(numberOfOrders).to.equal(0);
    });
  });

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  describe('Check that one order has been placed in order history', async () => {
    it('should reload the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      await foOrderHistoryPage.reloadPage(page);

      const pageHeaderTitle: string = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should check the number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders2', baseContext);

      const numberOfOrders: number = await foOrderHistoryPage.getNumberOfOrders(page);
      await expect(numberOfOrders).to.equal(1);
    });

    it('should check the order information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformation', baseContext);

      const result: OrderHistory = await foOrderHistoryPage.getOrderHistoryDetails(page);
      await Promise.all([
        await expect(result.reference).not.null,
        await expect(result.date).to.equal(today),
        await expect(result.price).to.equal(`€${Products.demo_1.finalPrice}`),
        await expect(result.paymentType).to.equal(PaymentMethods.wirePayment.displayName),
        await expect(result.status).to.equal(OrderStatuses.awaitingBankWire.name),
        await expect(result.invoice).to.equal('-'),
      ]);
    });

    it('should click on \'Back to you account\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToYourAccount', baseContext);

      await foOrderHistoryPage.clickOnBackToYourAccountLink(page);

      const pageTitle: string = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go back to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should click on \'Home\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

      await foOrderHistoryPage.clickOnHomeLink(page);

      const pageTitle: string = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.equal(foHomePage.pageTitle);
    });
  });

  // Post-condition : Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postText`);
});
