require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orderSettings_giftOptions';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const FOLoginPage = require('@pages/FO/login');
// Importing data
const {DefaultAccount} = require('@data/demo/customer');
const {DefaultFrTax} = require('@data/demo/tax');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    orderSettingsPage: new OrderSettingsPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    foLoginPage: new FOLoginPage(page),
  };
};

describe('Update gift options ', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Shop Parameters > Order Settings page
  loginCommon.loginBO();

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.orderSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
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
    {
      args:
        {
          testIdentifier: 'GiftDisabled',
          action: 'disable',
          wantedStatus: false,
          price: 0,
          tax: 'None',
          isRecyclablePackage: false,
        },
    },
  ];
  tests.forEach((test) => {
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
          const result = await this.pageObjects.orderSettingsPage.setGiftOptions(
            test.args.wantedStatus,
            test.args.price,
            test.args.tax,
            test.args.isRecyclablePackage,
          );
          await expect(result).to.contains(this.pageObjects.orderSettingsPage.successfulUpdateMessage);
        },
      );

      it('should view my shop', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `viewMyShopToCheck${test.args.testIdentifier}`,
          baseContext,
        );
        page = await this.pageObjects.boBasePage.viewMyShop();
        this.pageObjects = await init();
        await this.pageObjects.homePage.changeLanguage('en');
        const isHomePage = await this.pageObjects.homePage.isHomePage();
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToLoginPageFOToCheck${test.args.testIdentifier}`,
          baseContext,
        );
        await this.pageObjects.homePage.goToLoginPage();
        const pageTitle = await this.pageObjects.foLoginPage.getPageTitle();
        await expect(pageTitle, 'Fail to open FO login page').to.contains(this.pageObjects.foLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `sighInFOToCheck${test.args.testIdentifier}`,
          baseContext,
        );
        await this.pageObjects.foLoginPage.customerLogin(DefaultAccount);
        const isCustomerConnected = await this.pageObjects.foLoginPage.isCustomerConnected();
        await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToShippingStep${test.args.testIdentifier}`,
          baseContext,
        );
        await this.pageObjects.foLoginPage.goToHomePage();
        // Go to the first product page
        await this.pageObjects.homePage.goToProductPage(4);
        // Add the product to the cart
        await this.pageObjects.productPage.addProductToTheCart();
        // Proceed to checkout the shopping cart
        await this.pageObjects.cartPage.clickOnProceedToCheckout();
        // Address step - Go to delivery step
        const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it(`should check that gift checkbox visibility is '${test.args.wantedStatus}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkGiftVisibility${test.args.testIdentifier}`,
          baseContext,
        );
        const isGiftCheckboxVisible = await this.pageObjects.checkoutPage.isGiftCheckboxVisible();
        await expect(
          isGiftCheckboxVisible,
          'Gift checkbox has not the correct status',
        ).to.equal(test.args.wantedStatus);
      });

      if (test.args.wantedStatus) {
        it('should check gift price and tax', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkGiftPrice${test.args.testIdentifier}`,
            baseContext,
          );
          const giftPrice = await this.pageObjects.checkoutPage.getGiftPrice();
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
          const isRecyclableCheckboxVisible = await this.pageObjects.checkoutPage.isRecyclableCheckboxVisible();
          await expect(
            isRecyclableCheckboxVisible,
            'Gift checkbox has not the correct status',
          ).to.equal(test.args.isRecyclablePackage);
        });

      it('should sign out from FO', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `sighOutFOAfterCheck${test.args.testIdentifier}`,
          baseContext,
        );
        await this.pageObjects.checkoutPage.goToHomePage();
        await this.pageObjects.homePage.logout();
        const isCustomerConnected = await this.pageObjects.homePage.isCustomerConnected();
        await expect(isCustomerConnected, 'Customer should be disconnected').to.be.false;
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goBackToBoAfterCheck${test.args.testIdentifier}`,
          baseContext,
        );
        page = await this.pageObjects.checkoutPage.closePage(browser, 1);
        this.pageObjects = await init();
        const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
      });
    });
  });
});
