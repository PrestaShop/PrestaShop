// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';

import {
  dataCustomers,
  dataProducts,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_addresses_selectAddress';

/*
Scenario:
- Go to FO
- Add product to cart
- Go to checkout page
- Login as a customer
- Select the second address
- Check that no payment method is available
*/
describe('FO - Checkout - Addresses: Select address', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.equal(true);
  });

  it('should go to the fourth product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await homePage.goToProductPage(page, 4);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_5.name);
  });

  it('should add product to cart and go to cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await productPage.addProductToTheCart(page, 1);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should validate shopping cart and go to checkout page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

    // Proceed to checkout the shopping cart
    await cartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.equal(true);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

    await checkoutPage.clickOnSignIn(page);

    const isStepCompleted = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
    expect(isStepCompleted).to.equal(true);
  });

  it('should choose the second address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondAddress', baseContext);

    await checkoutPage.selectDeliveryAddress(page, 2);

    const isStepCompleted = await checkoutPage.clickOnContinueButtonFromAddressStep(page);
    expect(isStepCompleted).to.eq(true);
  });

  it('should continue to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'continueToPaymentStep', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
  });

  it('should check that no payment method is available', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoPaymentMethodAvailable', baseContext);

    const alertMessage = await checkoutPage.getNoPaymentAvailableMessage(page);
    expect(alertMessage).to.equal('Unfortunately, there is no payment method available.');
  });
});
