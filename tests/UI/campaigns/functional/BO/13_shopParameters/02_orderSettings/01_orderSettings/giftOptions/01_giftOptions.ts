// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  boOrderSettingsPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataTaxes,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_giftOptions_giftOptions';

/*
Scenario:
- Update gift options
- Go to FO, create an order and check gift in every option
- Go to BO > Orders > view created order and check gift configuration
Post-condition:
- Go back to default configuration
 */
describe('BO - Shop Parameters - Order Settings : Update gift options ', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    // Case 1 : Enable gift wrapping
    {
      args:
        {
          testIdentifier: 'GiftEnabledNoPriceNoTax',
          isGiftWrapping: true,
          giftWrappingPrice: 0,
          isGiftWrappingTax: 'None',
          taxValue: 0,
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
          taxValue: 0,
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
          taxValue: parseInt(dataTaxes.DefaultFrTax.rate, 10) / 100,
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
          taxValue: 0,
          isRecycledPackaging: true,
        },
    },
  ];

  tests.forEach((test, index: number) => {
    describe(`Set gift option with status: '${test.args.isGiftWrapping}', price: '${test.args.giftWrappingPrice}', `
      + `tax: '${test.args.isGiftWrappingTax}', recyclable packaging: '${test.args.isRecycledPackaging}'`, async () => {
      describe('Set gift options in BO', async () => {
        it('should go to \'Shop Parameters > Order Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderSettingsPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.orderSettingsLink,
          );
          await boOrderSettingsPage.closeSfToolBar(page);

          const pageTitle = await boOrderSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
        });

        it('should set gift options', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `setOptions${index}`, baseContext);

          const result = await boOrderSettingsPage.setGiftOptions(
            page,
            test.args.isGiftWrapping,
            test.args.giftWrappingPrice,
            test.args.isGiftWrappingTax,
            test.args.isRecycledPackaging,
          );
          expect(result, 'Success message is not displayed!')
            .to.contains(boOrderSettingsPage.successfulUpdateMessage);
        });
      });

      describe('Create an order and check the configuration in FO', async () => {
        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await boOrderSettingsPage.viewMyShop(page);
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
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

          const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should go to home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToHomePage${index}`, baseContext);

          await foClassicLoginPage.goToHomePage(page);

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open home page!').to.eq(true);
        });

        it('should add product to the cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Go to the fourth product page
          await foClassicHomePage.goToProductPage(page, 4);
          await foClassicProductPage.addProductToTheCart(page);

          const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
          expect(notificationsNumber).to.be.equal(1);
        });

        it('should click on proceed to checkout and go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          await foClassicCartPage.clickOnProceedToCheckout(page);

          const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
          expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
        });

        it(`should check that gift checkbox visibility is '${test.args.isGiftWrapping}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkGiftVisibility${index}`, baseContext);

          const isGiftCheckboxVisible = await foClassicCheckoutPage.isGiftCheckboxVisible(page);
          expect(
            isGiftCheckboxVisible,
            'Gift checkbox has not the correct status',
          ).to.equal(test.args.isGiftWrapping);
        });

        if (test.args.isGiftWrapping) {
          it('should check the gift checkbox and set a gift message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `setGiftMessage${index}`, baseContext);

            await foClassicCheckoutPage.setGiftCheckBox(page);

            const isVisible = await foClassicCheckoutPage.isGiftMessageTextareaVisible(page);
            expect(isVisible, 'Gift message textarea is not visible!').to.eq(true);

            if (isVisible) {
              await foClassicCheckoutPage.setGiftMessage(page, 'This is your gift');
            }
          });
        }

        if (test.args.giftWrappingPrice !== 0) {
          it('should check gift price and tax', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftPrice${index}`, baseContext);

            const giftPrice = await foClassicCheckoutPage.getGiftPrice(page);
            expect(giftPrice, 'Gift price is incorrect').to.equal(
              test.args.giftWrappingPrice === 0
                ? 'Free'
                : `€${(
                  test.args.giftWrappingPrice * (test.args.isGiftWrappingTax === 'None' ? 1 : (1 + test.args.taxValue))
                ).toFixed(2)}`,
            );
          });
        }

        it(
          `should check that recycled packaging checkbox visibility is '${test.args.isRecycledPackaging}'`
          + 'and check it if true',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkRecycleDVisibility${index}`, baseContext);

            const isRecycledPackagingCheckboxVisible = await foClassicCheckoutPage.isRecycledPackagingCheckboxVisible(page);
            expect(
              isRecycledPackagingCheckboxVisible,
              'Recycled packaging checkbox has not the correct status',
            ).to.equal(test.args.isRecycledPackaging);

            if (test.args.isRecycledPackaging) {
              await foClassicCheckoutPage.setRecycledPackagingCheckbox(page);
            }
          });

        it('should continue to payment', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
          expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

          await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

          const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
          expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
        });

        if (test.args.giftWrappingPrice !== 0) {
          it('should check the gift wrapping price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftWrappingPrice${index}`, baseContext);

            const giftWrappingValue = await foClassicCheckoutOrderConfirmationPage.getGiftWrappingValue(page);
            expect(giftWrappingValue).to.equal(
              test.args.giftWrappingPrice * (test.args.isGiftWrappingTax === 'None' ? 1 : (1 + test.args.taxValue)),
            );
          });
        }

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFOAfterCheck${index}`, baseContext);

          await foClassicCheckoutPage.goToHomePage(page);
          await foClassicHomePage.logout(page);

          const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer should be disconnected').to.eq(false);
        });
      });

      describe('Check the configuration in BO', async () => {
        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBoAfterCheck${index}`, baseContext);

          page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

          const pageTitle = await boOrderSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
        });

        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(page, boDashboardPage.ordersParentLink, boDashboardPage.ordersLink);
          await boOrdersPage.closeSfToolBar(page);

          const pageTitle = await boOrdersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boOrdersPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `resetOrderTableFilters${index}`, baseContext);

          const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
          expect(numberOfOrders).to.be.above(5);
        });

        it('should view the first order in the list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewFirstOrder${index}`, baseContext);

          await boOrdersPage.goToOrder(page, 1);

          const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
          expect(pageTitle, 'View order page is not visible!').to.contains(boOrdersViewBlockTabListPage.pageTitle);
        });

        if (test.args.isGiftWrapping && !test.args.isRecycledPackaging) {
          it('should check \'gift wrapping\' badge on status tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnStatusTab${index}`, baseContext);

            const isGiftWrapping = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 1);
            expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check \'gift wrapping\' badge on documents tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnDocTab${index}`, baseContext);

            const isTabOpened = await boOrdersViewBlockTabListPage.goToDocumentsTab(page);
            expect(isTabOpened, 'Documents tab is not opened!').to.eq(true);

            const isGiftWrapping = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 1);
            expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check \'gift wrapping\' badge on carriers tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftOnCarriersTab${index}`, baseContext);

            const isTabOpened = await boOrdersViewBlockTabListPage.goToCarriersTab(page);
            expect(isTabOpened, 'Carriers tab is not opened!').to.eq(true);

            const isGiftWrapping = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 1);
            expect(isGiftWrapping).to.be.equal('Gift wrapping');
          });

          it('should check the gift message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkGiftMessage${index}`, baseContext);

            const giftMessageText = await boOrdersViewBlockTabListPage.getGiftMessage(page);
            expect(giftMessageText).to.be.equal('This is your gift');
          });
        }

        if (test.args.giftWrappingPrice !== 0) {
          it('should check the \'Wrapping\' amount on products block', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkWrappingAmount${index}`, baseContext);

            const wrappingAmount = await boOrdersViewBlockProductsPage.getOrderWrappingTotal(page);
            expect(wrappingAmount).to.be.equal(
              test.args.giftWrappingPrice * (test.args.isGiftWrappingTax === 'None' ? 1 : (1 + test.args.taxValue)));
          });
        }

        if (test.args.isRecycledPackaging) {
          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on status tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnStatusTab${index}`, baseContext);

            const isRecycledPackaging = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 2);
            expect(isRecycledPackaging).to.contain('Recycled packaging')
              .and.to.contain('Gift wrapping');
          });

          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on documents tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnDocTab${index}`, baseContext);

            const isTabOpened = await boOrdersViewBlockTabListPage.goToDocumentsTab(page);
            expect(isTabOpened, 'Documents tab is not opened!').to.eq(true);

            const isRecycledPackaging = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 2);
            expect(isRecycledPackaging).to.contain('Recycled packaging')
              .and.to.contain('Gift wrapping');
          });

          it('should check \'Recycled packaging\' and \'gift wrapping\' badges on carriers tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBadgesOnCarriersTab${index}`, baseContext);

            const isTabOpened = await boOrdersViewBlockTabListPage.goToCarriersTab(page);
            expect(isTabOpened, 'Carriers tab is not opened!').to.eq(true);

            const isRecycledPackaging = await boOrdersViewBlockTabListPage.getSuccessBadge(page, 2);
            expect(isRecycledPackaging).to.be.contain('Recycled packaging')
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

      await boDashboardPage.goToSubMenu(page, boDashboardPage.shopParametersParentLink, boDashboardPage.orderSettingsLink);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });

    it('should go back to the default configuration', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultConfig', baseContext);

      const result = await boOrderSettingsPage.setGiftOptions(page, false, 0, 'None', false);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });
  });
});
