require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const ordersPage = require('@pages/BO/orders');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const foLoginPage = require('@pages/FO/login');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {DefaultFrTax} = require('@data/demo/tax');
const {PaymentMethods} = require('@data/demo/paymentMethods');

const baseContext = 'functional_BO_shopParameters_orderSettings_giftOptions';

let browserContext;
let page;

/*
Scenario:
- Update gift options
- Go to FO, login by default customer and check gift in every option
- Go to BO and check gift message
Post-condition:
- Disable 'Offer gift wrapping'
 */
describe('BO - Shop Parameters - Order Settings : Update gift options ', async () => {
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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.orderSettingsLink,
    );

    await orderSettingsPage.closeSfToolBar(page);

    const pageTitle = await orderSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'GiftEnabledNoPriceNoTax',
          action: 'enable',
          wantedStatus: true,
          price: 0,
          tax: 'None',
          isRecyclablePackage: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithPriceNoTax',
          action: 'enable',
          wantedStatus: true,
          price: 1,
          tax: 'None',
          isRecyclablePackage: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithPriceAndTax',
          action: 'enable',
          wantedStatus: true,
          price: 1,
          tax: 'FR Taux standard (20%)',
          taxValue: DefaultFrTax.rate / 100,
          isRecyclablePackage: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithRecyclablePackage',
          action: 'enable',
          wantedStatus: true,
          price: 0,
          tax: 'None',
          isRecyclablePackage: true,
        },
    },
  ];

  tests.forEach((test, index) => {
    describe(`Set gift option with status: '${test.args.wantedStatus}', price: '${test.args.price}', `
      + `tax: '${test.args.tax}', recyclable package: '${test.args.isRecyclablePackage}'`, async () => {
      it(
        `should ${test.args.action} gift options with price '€${test.args.price} and tax '${test.args.tax}`,
        async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `setOptions${test.args.testIdentifier}`,
            baseContext,
          );

          const result = await orderSettingsPage.setGiftOptions(
            page,
            test.args.wantedStatus,
            test.args.price,
            test.args.tax,
            test.args.isRecyclablePackage,
          );

          await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
        },
      );

      it('should view my shop', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `viewMyShopToCheck${test.args.testIdentifier}`,
          baseContext,
        );

        // Go to FO
        page = await orderSettingsPage.viewMyShop(page);

        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToLoginPageFOToCheck${test.args.testIdentifier}`,
          baseContext,
        );

        await homePage.goToLoginPage(page);

        const pageTitle = await foLoginPage.getPageTitle(page);
        await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `sighInFOToCheck${test.args.testIdentifier}`,
          baseContext,
        );

        await foLoginPage.customerLogin(page, DefaultCustomer);
        const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToShippingStep${test.args.testIdentifier}`,
          baseContext,
        );

        await foLoginPage.goToHomePage(page);

        // Go to the fourth product page
        await homePage.goToProductPage(page, 4);

        // Add the product to the cart
        await productPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it(`should check that gift checkbox visibility is '${test.args.wantedStatus}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkGiftVisibility${test.args.testIdentifier}`,
          baseContext,
        );

        const isGiftCheckboxVisible = await checkoutPage.isGiftCheckboxVisible(page);

        await expect(
          isGiftCheckboxVisible,
          'Gift checkbox has not the correct status',
        ).to.equal(test.args.wantedStatus);
      });

      if (test.args.testIdentifier === 'GiftEnabledNoPriceNoTax') {
        it('should check the gift checkbox and check that the gift message textarea is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkGiftTextarea', baseContext);

          await checkoutPage.setGiftCheckBox(page);

          const isVisible = await checkoutPage.isGiftMessageTextareaVisible(page);
          await expect(isVisible, 'Gift message textarea is not visible!').to.be.true;
        });

        it('should set a gift message and continue to payment', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'setGiftMessage', baseContext);

          await checkoutPage.setGiftMessage(page, 'This is your gift');

          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
        });
      }

      if (test.args.wantedStatus) {
        it('should check gift price and tax', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkGiftPrice${test.args.testIdentifier}`,
            baseContext,
          );

          const giftPrice = await checkoutPage.getGiftPrice(page);

          await expect(giftPrice, 'Gift price is incorrect').to.equal(
            test.args.price === 0 ? 'Free'
              : `€${parseFloat(
                test.args.price * (test.args.tax === 'None' ? 1 : (1 + test.args.taxValue)),
              ).toFixed(2)}`,
          );
        });
      }

      it(
        `should check that recyclable package checkbox visibility is '${test.args.isRecyclablePackage}'`,
        async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkRecyclableVisibility${test.args.testIdentifier}`,
            baseContext,
          );

          const isRecyclableCheckboxVisible = await checkoutPage.isRecyclableCheckboxVisible(page);

          await expect(
            isRecyclableCheckboxVisible,
            'Gift checkbox has not the correct status',
          ).to.equal(test.args.isRecyclablePackage);
        });

      if (index === 4) {
        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.checkPayment.moduleName);

          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });
      }
      it('should sign out from FO', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `sighOutFOAfterCheck${test.args.testIdentifier}`,
          baseContext,
        );

        await checkoutPage.goToHomePage(page);
        await homePage.logout(page);

        const isCustomerConnected = await homePage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer should be disconnected').to.be.false;
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goBackToBoAfterCheck${test.args.testIdentifier}`,
          baseContext,
        );

        page = await checkoutPage.closePage(browserContext, page, 0);

        const pageTitle = await orderSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
      });
    });
  });

  describe('Check the gift message from \'BO > Orders > view order page\'', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.ordersParentLink, dashboardPage.ordersLink);

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should view the first order in the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check the gift message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftMessage', baseContext);

      const giftMessageText = await orderPageTabListBlock.getGiftMessage(page);
      await expect(giftMessageText).to.be.equal('This is your gift');
    });
  });

  describe('POST-TEST: Disable \'Offer gift wrapping\'', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.orderSettingsLink);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should disable \'Offer gift wrapping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableGiftWrapping', baseContext);

      const result = await orderSettingsPage.setGiftOptions(page, false, 0, 'None', false);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });
});
