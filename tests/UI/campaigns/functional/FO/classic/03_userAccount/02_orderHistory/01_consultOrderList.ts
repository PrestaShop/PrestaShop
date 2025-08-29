import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  type BrowserContext,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderHistoryPage,
  type OrderHistory,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_consultOrderList';

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

  const customerData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const today: string = utilsDate.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create new account
  createAccountTest(customerData, `${baseContext}_enableNewProduct`);

  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check that no order has been placed in order history', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage: boolean = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, customerData);

      const isCustomerConnected: boolean = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should check number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders1', baseContext);

      const numberOfOrders: number = await foClassicMyOrderHistoryPage.getNumberOfOrders(page);
      expect(numberOfOrders).to.equal(0);
    });
  });

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  describe('Check that one order has been placed in order history', async () => {
    it('should reload the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      await foClassicMyOrderHistoryPage.reloadPage(page);

      const pageHeaderTitle: string = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should check the number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders2', baseContext);

      const numberOfOrders: number = await foClassicMyOrderHistoryPage.getNumberOfOrders(page);
      expect(numberOfOrders).to.equal(1);
    });

    it('should check the order information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformation', baseContext);

      const result: OrderHistory = await foClassicMyOrderHistoryPage.getOrderHistoryDetails(page);
      await Promise.all([
        expect(result.reference).not.null,
        expect(result.date).to.equal(today),
        expect(result.price).to.equal(`€${dataProducts.demo_1.finalPrice}`),
        expect(result.paymentType).to.equal(dataPaymentMethods.wirePayment.displayName),
        expect(result.status).to.equal(dataOrderStatuses.awaitingBankWire.name),
        expect(result.invoice).to.equal('-'),
      ]);
    });

    it('should click on \'Back to you account\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToYourAccount', baseContext);

      await foClassicMyOrderHistoryPage.clickOnBackToYourAccountLink(page);

      const pageTitle: string = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
    });

    it('should go back to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should click on \'Home\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

      await foClassicMyOrderHistoryPage.clickOnHomeLink(page);

      const pageTitle: string = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });
  });

  // Post-condition : Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postText`);
});
