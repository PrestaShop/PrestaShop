// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import boMerchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';
import foMerchandiseReturnsPage from '@pages/FO/myAccount/merchandiseReturns';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';
import orderHistoryPage from '@pages/FO/myAccount/orderHistory';
import productPage from '@pages/FO/product';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customerService_merchandiseReturns_merchandiseReturnOptions';

/*
Pre-condition:
- Create order in FO
Scenario:
- Activate/Deactivate merchandise return
- Update returns prefix
- Change the first order status in the list to shipped
- Check the existence of the button return products
- Go to FO>My account>Order history> first order detail in the list
- Check the existence of product return form
- Create a merchandise returns then check the file prefix
 */
describe('BO - Customer Service - Merchandise Returns : Merchandise return (RMA) options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

      await orderConfirmationPage.logout(page);

      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('BO : Enable/Disable and update the prefix', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    [
      {args: {action: 'activate', enable: true, prefix: '#NE'}},
      {args: {action: 'deactivate', enable: false, prefix: '#RE'}},
    ].forEach((test, index: number) => {
      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.merchandiseReturnsLink,
        );
        await boMerchandiseReturnsPage.closeSfToolBar(page);

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it(`should ${test.args.action} merchandise returns`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Returns`, baseContext);

        const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, test.args.enable);
        await expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
      });

      it('should update Returns prefix', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateReturnPrefix${index}`, baseContext);

        const result = await boMerchandiseReturnsPage.setReturnsPrefix(page, test.args.prefix);
        await expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterOrder${index}`, baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        await expect(textColumn).to.contains(Customers.johnDoe.lastName);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

        // View order
        await ordersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderBasePage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
      });

      it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

        const result = await viewOrderBasePage.modifyOrderStatus(page, OrderStatuses.shipped.name);
        await expect(result).to.equal(OrderStatuses.shipped.name);
      });

      it('should check if the button \'Return products\' is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkReturnProductsButton${index}`, baseContext);

        const result = await viewOrderBasePage.isReturnProductsButtonVisible(page);
        await expect(result).to.equal(test.args.enable);
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

        // Click on view my shop
        page = await viewOrderBasePage.viewMyShop(page);
        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      // Go to My account page by login the first time and click on account link the second time
      if (index === 0) {
        it('should login', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

          await homePage.goToLoginPage(page);
          await foLoginPage.customerLogin(page, Customers.johnDoe);

          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected).to.be.true;

          await homePage.goToMyAccountPage(page);

          const pageTitle = await myAccountPage.getPageTitle(page);
          await expect(pageTitle).to.contains(myAccountPage.pageTitle);
        });
      } else {
        it('should go to account page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

          await homePage.goToMyAccountPage(page);

          const pageTitle = await myAccountPage.getPageTitle(page);
          await expect(pageTitle).to.contains(myAccountPage.pageTitle);
        });
      }

      it('should go to \'Order history and details\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderHistoryPage${index}`, baseContext);

        await myAccountPage.goToHistoryAndDetailsPage(page);

        const pageTitle = await orderHistoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
      });

      it('should go to the first order in the list and check the existence of order return form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `isOrderReturnFormVisible${index}`, baseContext);

        await orderHistoryPage.goToDetailsPage(page, 1);

        const result = await orderDetailsPage.isOrderReturnFormVisible(page);
        await expect(result).to.equal(test.args.enable);
      });
      if (test.args.enable) {
        it('should create a merchandise return', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

          await orderDetailsPage.requestMerchandiseReturn(page, 'test');

          const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
        });

        it('should verify order return prefix', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnPrefix', baseContext);

          const fileName = await foMerchandiseReturnsPage.getTextColumn(page, 'fileName');
          await expect(fileName).to.contains(test.args.prefix);
        });
      }

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

        page = await orderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await viewOrderBasePage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
      });
    });
  });
});
