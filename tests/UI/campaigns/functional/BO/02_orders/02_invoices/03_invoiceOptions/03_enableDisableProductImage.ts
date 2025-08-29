import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_enableDisableProductImage';

/*
Enable product image in invoice
Create order
Create invoice
Check that there is 2 images in the invoice (Logo and product image)
Disable product image in invoice
Create order
Create invoice
Check that there is 1 image in the invoice (Logo)
 */
describe('BO - Orders - Invoices : Enable/Disable product image in invoices', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

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

  [
    {args: {action: 'Enable', enable: true, imageNumber: global.URLHasPort ? 1 : 2}},
    {args: {action: 'Disable', enable: false, imageNumber: global.URLHasPort ? 0 : 1}},
  ].forEach((test, index: number) => {
    describe(`${test.args.action} product image in invoice then check the invoice file created`, async () => {
      describe(`${test.args.action} product image`, async () => {
        it('should go to \'Orders > Invoices\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToInvoicesPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.ordersParentLink,
            boDashboardPage.invoicesLink,
          );
          await boInvoicesPage.closeSfToolBar(page);

          const pageTitle = await boInvoicesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
        });

        it(`should ${test.args.action} product image`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);

          await boInvoicesPage.enableProductImage(page, test.args.enable);

          const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
          expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
        });
      });

      describe('Create new order in FO', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

          // Click on view my shop
          page = await boInvoicesPage.viewMyShop(page);
          // Change FO language
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${index}`, baseContext);

          await foClassicHomePage.goToLoginPage(page);

          const pageTitle = await foClassicLoginPage.getPageTitle(page);
          expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

          const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should add product to cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Go to home page
          await foClassicLoginPage.goToHomePage(page);
          // Go to the first product page
          await foClassicHomePage.goToProductPage(page, 1);
          // Add the product to the cart
          await foClassicProductPage.addProductToTheCart(page);

          const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
          expect(notificationsNumber).to.be.equal(1);
        });

        it('should go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          // Proceed to checkout the shopping cart
          await foClassicCartPage.clickOnProceedToCheckout(page);

          // Address step - Go to delivery step
          const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
          expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should go to payment step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
          expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

          // Payment step - Choose payment step
          await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
          expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

          await foClassicCheckoutOrderConfirmationPage.logout(page);

          const isCustomerConnected = await foClassicCheckoutOrderConfirmationPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is connected').to.eq(false);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          // Close page and init page objects
          page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

          const pageTitle = await boInvoicesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
        });
      });

      describe('Generate the invoice and check product image', async () => {
        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

          await boInvoicesPage.goToSubMenu(
            page,
            boInvoicesPage.ordersParentLink,
            boInvoicesPage.ordersLink,
          );

          const pageTitle = await boOrdersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrdersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCreatedOrderPage${index}`, baseContext);

          await boOrdersPage.goToOrder(page, 1);

          const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
        });

        it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

          const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
          expect(result).to.equal(dataOrderStatuses.shipped.name);
        });

        it('should download the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `downloadInvoice${index}`, baseContext);

          // Download invoice
          filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page);
          expect(filePath).to.not.eq(null);

          const exist = await utilsFile.doesFileExist(filePath);
          expect(exist).to.eq(true);
        });

        it('should check the product images in the PDF File', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${index}`, baseContext);

          const imageNumber = await utilsFile.getImageNumberInPDF(filePath);
          expect(imageNumber).to.be.equal(test.args.imageNumber);

          await utilsFile.deleteFile(filePath);
        });
      });
    });
  });
});
