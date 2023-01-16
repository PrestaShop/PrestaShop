// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import FO pages
import cartPage from '@pages/FO/cart';
import homePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import productPage from '@pages/FO/product';

require('module-alias/register');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

const baseContext = 'functional_BO_orders_deliverySlips_deliverySlipOptions_enableDisableProductImage';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let filePath;

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

  tests.forEach((test, index) => {
    describe(`${test.args.action} product image in delivery slip then check the file created`, async () => {
      it('should go to \'Orders > Delivery slips\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliverySlipsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.deliverySlipslink,
        );

        await deliverySlipsPage.closeSfToolBar(page);

        const pageTitle = await deliverySlipsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
      });

      it(`should ${test.args.action} product image`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);

        await deliverySlipsPage.setEnableProductImage(page, test.args.enable);
        const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
        await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
      });

      describe('Create new order in FO', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

          // Click on view my shop
          page = await deliverySlipsPage.viewMyShop(page);

          // Change FO language
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage, 'Fail to open FO home page').to.be.true;
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginPageFO${index}`, baseContext);

          await homePage.goToLoginPage(page);
          const pageTitle = await foLoginPage.getPageTitle(page);
          await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signInFO${index}`, baseContext);

          await foLoginPage.customerLogin(page, DefaultCustomer);
          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should add product to cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

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
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);

          // Address step - Go to delivery step
          const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
          await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
        });

        it('should go to payment step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
        });

        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

          // Payment step - Choose payment step
          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signOutFO${index}`, baseContext);

          await orderConfirmationPage.logout(page);

          const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is connected').to.be.false;
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          // Close tab and init other page objects with new current tab
          page = await orderConfirmationPage.closePage(browserContext, page, 0);

          const pageTitle = await deliverySlipsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
        });
      });

      describe(`Generate the delivery slip and check that there is ${test.args.isProductImageDisplayed}`, async () => {
        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

          await deliverySlipsPage.goToSubMenu(
            page,
            deliverySlipsPage.ordersParentLink,
            deliverySlipsPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCreatedOrderPage${index}`, baseContext);

          await ordersPage.goToOrder(page, 1);
          const pageTitle = await orderPageTabListBlock.getPageTitle(page);
          await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
        });

        it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

          const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
          await expect(result).to.equal(Statuses.shipped.status);
        });

        it('should download the delivery slip', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `downloadDeliverySlips${index}`, baseContext);

          filePath = await orderPageTabListBlock.downloadDeliverySlip(page);

          const exist = await files.doesFileExist(filePath);
          await expect(exist).to.be.true;
        });

        it(`should check that there is ${test.args.isProductImageDisplayed} in the PDF File`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductImage${index}`, baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber).to.be.equal(test.args.imageNumber);
        });
      });
    });
  });
});
