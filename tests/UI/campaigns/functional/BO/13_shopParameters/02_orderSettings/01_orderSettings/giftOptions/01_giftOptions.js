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
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');

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
- Go to FO, create an order and check gift in every option
- Go to BO > Orders > view created order and check gift configuration
Post-condition:
- Go back to default configuration
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

  const tests = [
    // Case 1 : Enable gift wrapping
    {
      args:
        {
          testIdentifier: 'GiftEnabledNoPriceNoTax',
          isGiftWrapping: true,
          giftWrappingPrice: 0,
          isGiftWrappingTax: 'None',
          isRecycledPackaging: false,
        },
    },
    // Case 2 : Add gift wrapping price
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithPriceNoTax',
          isGiftWrapping: true,
          giftWrappingPrice: 1,
          isGiftWrappingTax: 'None',
          isRecycledPackaging: false,
        },
    },
    // Case 3 : Add gift wrapping tax
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithPriceAndTax',
          isGiftWrapping: true,
          giftWrappingPrice: 1,
          isGiftWrappingTax: 'FR Taux standard (20%)',
          taxValue: DefaultFrTax.rate / 100,
          isRecycledPackaging: false,
        },
    },
    // Case 4 : Enable offer recycled packaging
    {
      args:
        {
          testIdentifier: 'GiftEnabledWithRecyclablePackage',
          isGiftWrapping: true,
          giftWrappingPrice: 0,
          isGiftWrappingTax: 'None',
          isRecycledPackaging: true,
        },
    },
  ];

  tests.forEach((test, index) => {
    describe(`Set gift option with status: '${test.args.isGiftWrapping}', price: '${test.args.giftWrappingPrice}', `
      + `tax: '${test.args.isGiftWrappingTax}', recyclable packaging: '${test.args.isRecycledPackaging}'`, async () => {
      describe('Set gift options in BO', async () => {
        it('should go to \'Shop Parameters > Order Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderSettingsPage${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.orderSettingsLink,
          );

          await orderSettingsPage.closeSfToolBar(page);

          const pageTitle = await orderSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
        });

        it('should set gift options', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `setOptions${index}`, baseContext);

          const result = await orderSettingsPage.setGiftOptions(
            page,
            test.args.isGiftWrapping,
            test.args.giftWrappingPrice,
            test.args.isGiftWrappingTax,
            test.args.isRecycledPackaging,
          );

          await expect(result, 'Success message is not displayed!')
            .to.contains(orderSettingsPage.successfulUpdateMessage);
        });
      });

      describe('Create an order and check the configuration in FO', async () => {
        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await orderSettingsPage.viewMyShop(page);

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
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foLoginPage.customerLogin(page, DefaultCustomer);
          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should go to home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToHomePage${index}`, baseContext);

          await foLoginPage.goToHomePage(page);

          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage, 'Fail to open home page!').to.be.true;
        });

        it('should add product to the cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Go to the fourth product page
          await homePage.goToProductPage(page, 4);

          await productPage.addProductToTheCart(page);

          const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
          await expect(notificationsNumber).to.be.equal(1);
        });

        it('should click on proceed to checkout and go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          await cartPage.clickOnProceedToCheckout(page);

          const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
          await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
        });

        it(`should check that gift checkbox visibility is '${test.args.isGiftWrapping}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkGiftVisibility${index}`, baseContext);

          const isGiftCheckboxVisible = await checkoutPage.isGiftCheckboxVisible(page);

          await expect(
            isGiftCheckboxVisible,
            'Gift checkbox has not the correct status',
          ).to.equal(test.args.isGiftWrapping);
        });

        if (test.args.isGiftWrapping) {
          it('should check the gift checkbox and set a gift message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `setGiftMessage${index}`, baseContext);

            await checkoutPage.setGiftCheckBox(page);

            const isVisible = await checkoutPage.isGiftMessageTextareaVisible(page);
            await expect(isVisible, 'Gift message textarea is not visible!').to.be.true;

            if (isVisible) {
              await checkoutPage.setGiftMessage(page, 'This is your gift');
            }
          });
        }

        if (test.args.giftWrappingPrice !== 0) {
          it('should check gift price and tax', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftPrice${index}`, baseContext);

            const giftPrice = await checkoutPage.getGiftPrice(page);

            await expect(giftPrice, 'Gift price is incorrect').to.equal(
              test.args.giftWrappingPrice === 0 ? 'Free'
                : `€${parseFloat(
                  test.args.giftWrappingPrice * (test.args.isGiftWrappingTax === 'None' ? 1 : (1 + test.args.taxValue)),
                ).toFixed(2)}`,
            );
          });
        }

        it(
          `should check that recycled packaging checkbox visibility is '${test.args.isRecycledPackaging}'`
          + 'and check it if true',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkRecycleDVisibility${index}`, baseContext);

            const isRecycledPackagingCheckboxVisible = await checkoutPage.isRecycledPackagingCheckboxVisible(page);

            await expect(
              isRecycledPackagingCheckboxVisible,
              'Recycled packaging checkbox has not the correct status',
            ).to.equal(test.args.isRecycledPackaging);

            if (test.args.isRecycledPackaging) {
              await checkoutPage.setRecycledPackagingCheckbox(page);
            }
          });

        it('should continue to payment', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
        });

        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        if (test.args.giftWrappingPrice !== 0) {
          it('should check the gift wrapping price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftWrappingPrice${index}`, baseContext);

            const giftWrappingValue = await orderConfirmationPage.getGiftWrappingValue(page);
            await expect(giftWrappingValue).to.equal(test.args.giftWrappingPrice);
          });
        }

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFOAfterCheck${index}`, baseContext);

          await checkoutPage.goToHomePage(page);
          await homePage.logout(page);

          const isCustomerConnected = await homePage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer should be disconnected').to.be.false;
        });
      });

      describe('Check the configuration in BO', async () => {
        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBoAfterCheck${index}`, baseContext);

          page = await checkoutPage.closePage(browserContext, page, 0);

          const pageTitle = await orderSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
        });

        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

          await dashboardPage.goToSubMenu(page, dashboardPage.ordersParentLink, dashboardPage.ordersLink);

          await ordersPage.closeSfToolBar(page);

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `resetOrderTableFilters${index}`, baseContext);

          const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
          await expect(numberOfOrders).to.be.above(5);
        });

        it('should view the first order in the list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewFirstOrder${index}`, baseContext);

          await ordersPage.goToOrder(page, 1);

          const pageTitle = await orderPageTabListBlock.getPageTitle(page);
          await expect(pageTitle, 'View order page is not visible!').to.contains(orderPageTabListBlock.pageTitle);
        });

        if (test.args.isGiftWrapping && !test.args.isRecycledPackaging) {
          it('should check \'gift wrapping\' badge on status tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnStatusTab${index}`, baseContext);

            const isGiftWrapping = await orderPageTabListBlock.getSuccessBadge(page, 1);
            await expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check \'gift wrapping\' badge on documents tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnDocTab${index}`, baseContext);

            const isTabOpened = await orderPageTabListBlock.goToDocumentsTab(page);
            await expect(isTabOpened, 'Documents tab is not opened!').to.be.true;

            const isGiftWrapping = await orderPageTabListBlock.getSuccessBadge(page, 1);
            await expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check \'gift wrapping\' badge on carriers tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnCarriersTab${index}`, baseContext);

            const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
            await expect(isTabOpened, 'Carriers tab is not opened!').to.be.true;

            const isGiftWrapping = await orderPageTabListBlock.getSuccessBadge(page, 1);
            await expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check the gift message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftMessage${index}`, baseContext);

            const giftMessageText = await orderPageTabListBlock.getGiftMessage(page);
            await expect(giftMessageText).to.be.equal('This is your gift');
          });
        }

        if (test.args.giftWrappingPrice !== 0) {
          it('should check the \'Wrapping\' amount on products block', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkWrappingAmount${index}`, baseContext);

            const wrappingAmount = await orderPageProductsBlock.getOrderWrappingTotal(page);
            await expect(wrappingAmount).to.be.equal(
              test.args.giftWrappingPrice * (test.args.isGiftWrappingTax === 'None' ? 1 : (1 + test.args.taxValue)));
          });
        }

        if (test.args.isRecycledPackaging) {
          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on status tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnStatusTab${index}`, baseContext);

            const isRecycledPackaging = await orderPageTabListBlock.getSuccessBadge(page, 2);
            await expect(isRecycledPackaging).to.contain('Recycled packaging')
              .and.to.contain('Gift wrapping');
          });

          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on documents tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnDocTab${index}`, baseContext);

            const isTabOpened = await orderPageTabListBlock.goToDocumentsTab(page);
            await expect(isTabOpened, 'Documents tab is not opened!').to.be.true;

            const isRecycledPackaging = await orderPageTabListBlock.getSuccessBadge(page, 2);
            await expect(isRecycledPackaging).to.contain('Recycled packaging')
              .and.to.contain('Gift wrapping');
          });

          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on carriers tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnCarriersTab${index}`, baseContext);

            const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
            await expect(isTabOpened, 'Carriers tab is not opened!').to.be.true;

            const isRecycledPackaging = await orderPageTabListBlock.getSuccessBadge(page, 2);
            await expect(isRecycledPackaging).to.be.contain('Recycled packaging')
              .and.to.contain('Gift wrapping');
          });
        }
      });
    });
  });

  // Post-condition: Go back to default configuration
  describe('POST-TEST: Go back to the default configuration', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPagePostCondition', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.orderSettingsLink);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should go back to the default configuration', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultConfig', baseContext);

      const result = await orderSettingsPage.setGiftOptions(page, false, 0, 'None', false);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });
});
