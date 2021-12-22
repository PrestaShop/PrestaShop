require('module-alias/register');

const {expect} = require('chai');

// Import test context
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_shopParameters_orderSettings_termsOfService';

let browserContext;
let page;

/*
Enable/Disable terms of service
Go to FO payment step and check terms of service checkbox and page title
 */
describe('BO - Shop Parameters - Order Settings : Enable/Disable terms of service', async () => {
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
    {args: {action: 'disable', enable: false, pageName: ''}},
    {
      args: {
        action: 'enable', enable: true, pageName: 'Delivery', title: 'Shipments and returns',
      },
    },
    {
      args: {
        action: 'enable', enable: true, pageName: 'Legal Notice', title: 'Legal',
      },
    },
    {
      args: {
        action: 'enable', enable: true, pageName: 'Terms and conditions of use', title: 'Terms and conditions of use',
      },
    },
    {
      args: {
        action: 'enable', enable: true, pageName: 'About us', title: 'About us',
      },
    },
    {
      args: {
        action: 'enable', enable: true, pageName: 'Secure payment', title: 'Secure payment',
      },
    },
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} terms of service`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}TermsOfService${index}`,
        baseContext,
      );

      const result = await orderSettingsPage.setTermsOfService(page, test.args.enable, test.args.pageName);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });

    it('should check terms of service checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkTermsOfService${index}`, baseContext);

      // Click on view my shop
      page = await orderSettingsPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);
        await checkoutPage.customerLogin(page, DefaultCustomer);
      }

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Check terms of service checkbox existence
      const isVisible = await checkoutPage.isConditionToApproveCheckboxVisible(page);
      await expect(isVisible).to.be.equal(test.args.enable);
    });

    if (test.args.enable) {
      it('should check the terms of service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkTermsOfServicePage${index}`, baseContext);

        const pageName = await checkoutPage.getTermsOfServicePageTitle(page);
        await expect(pageName).to.contains(test.args.title);
      });
    }

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAndBackToBO${index}`, baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });
  });
});
