import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boDeliverySlipsPage,
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

const baseContext: string = 'functional_BO_orders_deliverySlips_deliverySlipOptions_enableDisableProductImage';

/*
Enable product image in delivery slip
Create order
Create delivery slip
Check that there is 2 images in the delivery slip (Logo and product image)
Disable product image in delivery slip
Create order
Create delivery slip
Check that there is 1 image in the delivery slip (Logo)
 */
describe('BO - Orders - Delivery slips : Enable/Disable product image', async () => {
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

  const tests = [
    {
      args: {
        action: 'Enable',
        enable: true,
        imageNumber: global.URLHasPort ? 1 : 2,
        isProductImageDisplayed: 'a product image displayed',
      },
    },
    {
      args: {
        action: 'Disable',
        enable: false,
        imageNumber: global.URLHasPort ? 0 : 1,
        isProductImageDisplayed: 'no product image displayed',
      },
    },
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} product image in delivery slip then check the file created`, async () => {
      it('should go to \'Orders > Delivery slips\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliverySlipsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.deliverySlipslink,
        );
        await boDeliverySlipsPage.closeSfToolBar(page);

        const pageTitle = await boDeliverySlipsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDeliverySlipsPage.pageTitle);
      });

      it(`should ${test.args.action} product image`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);

        await boDeliverySlipsPage.setEnableProductImage(page, test.args.enable);

        const textMessage = await boDeliverySlipsPage.saveDeliverySlipOptions(page);
        expect(textMessage).to.contains(boDeliverySlipsPage.successfulUpdateMessage);
      });

      describe('Create new order in FO', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

          // Click on view my shop
          page = await boDeliverySlipsPage.viewMyShop(page);
          // Change FO language
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginPageFO${index}`, baseContext);

          await foClassicHomePage.goToLoginPage(page);

          const pageTitle = await foClassicLoginPage.getPageTitle(page);
          expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signInFO${index}`, baseContext);

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
          await testContext.addContextItem(this, 'testIdentifier', `signOutFO${index}`, baseContext);

          await foClassicCheckoutOrderConfirmationPage.logout(page);

          const isCustomerConnected = await foClassicCheckoutOrderConfirmationPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is connected').to.eq(false);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          // Close tab and init other page objects with new current tab
          page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

          const pageTitle = await boDeliverySlipsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDeliverySlipsPage.pageTitle);
        });
      });

      describe(`Generate the delivery slip and check that there is ${test.args.isProductImageDisplayed}`, async () => {
        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

          await boDeliverySlipsPage.goToSubMenu(
            page,
            boDashboardPage.ordersParentLink,
            boDashboardPage.ordersLink,
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

        it('should download the delivery slip', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `downloadDeliverySlips${index}`, baseContext);

          filePath = await boOrdersViewBlockTabListPage.downloadDeliverySlip(page);

          const exist = await utilsFile.doesFileExist(filePath);
          expect(exist).to.eq(true);
        });

        it(`should check that there is ${test.args.isProductImageDisplayed} in the PDF File`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductImage${index}`, baseContext);

          const imageNumber = await utilsFile.getImageNumberInPDF(filePath);
          expect(imageNumber).to.be.equal(test.args.imageNumber);
        });
      });
    });
  });
});
