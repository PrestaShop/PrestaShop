// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;
  const creditSlipMailSubject: string = `[${global.INSTALL.SHOP_NAME}] New credit slip regarding your order`;

  // New order by customer data
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_2`);

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Return an order', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(result).to.equal(boOrdersPage.successfulUpdateMessage);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage1', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on return products button and type the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts', baseContext);

      await boOrdersViewBlockTabListPage.clickOnReturnProductsButton(page);
      await boOrdersViewBlockProductsPage.setReturnedProductQuantity(page, 1, 1);

      const errorMessage = await boOrdersViewBlockProductsPage.clickOnReturnProducts(page);
      expect(errorMessage).to.eq('Please select at least one product.');
    });

    it('should click on return products button and check quantity checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProducts2', baseContext);

      await boOrdersViewBlockTabListPage.clickOnReturnProductsButton(page);
      await boOrdersViewBlockProductsPage.checkReturnedQuantity(page);
    });

    it('should check generate a voucher checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGenerateVoucher', baseContext);

      await boOrdersViewBlockProductsPage.checkGenerateVoucher(page, true);

      const successMessage = await boOrdersViewBlockProductsPage.clickOnReturnProducts(page);
      expect(successMessage).to.eq('The product was successfully returned.');
    });

    it('should check that return products button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isButtonDisabled', baseContext);

      const isDisabled = await boOrdersViewBlockTabListPage.isReturnProductsButtonDisabled(page);
      expect(isDisabled).to.eq(true);
    });

    it('should check that the new column refunded is visible in products table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isNewColumnVisible', baseContext);

      const isColumnVisible = await boOrdersViewBlockProductsPage.isRefundedColumnVisible(page);
      expect(isColumnVisible).to.eq(true);
    });

    it('should check the voucher email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkConfirmationEmail', baseContext);

      const orderReference = await boOrdersViewBlockTabListPage.getOrderReference(page);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject)
        .to.equal(`[${global.INSTALL.SHOP_NAME}] New voucher for your order #${orderReference}`);
    });

    it('should check if the return product mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(allEmails[numberOfEmails - 2].subject).to.contains(creditSlipMailSubject);
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
