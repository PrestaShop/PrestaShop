// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';
import MailDevEmail from '@data/types/maildevEmail';

import {expect} from 'chai';
import MailDev from 'maildev';
import mailHelper from '@utils/mailHelper';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_returnOrder';

/*
Pre-condition:
- Setup SMTP parameters
- Create order in FO
- Enable merchandise returns
Scenario:
- Change order status to 'Delivered'
- Check 'Return products' button
- Return product
- Check email
Post-condition:
- Disable merchandise returns
- Reset SMTP parameters
 */
describe('BO - Orders - View and edit order : Return an order', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;
  const creditSlipMailSubject: string = 'New credit slip regarding your order';

  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_2`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe('Return an order', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await ordersPage.setOrderStatus(page, 1, OrderStatuses.delivered);
      expect(result).to.equal(ordersPage.successfulUpdateMessage);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on return products button and type the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts', baseContext);

      await orderPageTabListBlock.clickOnReturnProductsButton(page);
      await orderPageProductsBlock.setReturnedProductQuantity(page, 1, 1);

      const errorMessage = await orderPageProductsBlock.clickOnReturnProducts(page);
      expect(errorMessage).to.eq('Please select at least one product.');
    });

    it('should click on return products button and check quantity checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts2', baseContext);

      await orderPageTabListBlock.clickOnReturnProductsButton(page);
      await orderPageProductsBlock.checkReturnedQuantity(page);

      const successMessage = await orderPageProductsBlock.clickOnReturnProducts(page);
      expect(successMessage).to.eq('The product was successfully returned.');
    });

    it('should check that return products button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isButtonDisabled', baseContext);

      const isDisabled = await orderPageTabListBlock.isReturnProductsButtonDisabled(page);
      expect(isDisabled).to.eq(true);
    });

    it('should check that the new column refunded is visible in products table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isNewColumnVisible', baseContext);

      const isColumnVisible = await orderPageProductsBlock.isRefundedColumnVisible(page);
      expect(isColumnVisible).to.eq(true);
    });

    it('should check if the return product mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains(creditSlipMailSubject);
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
