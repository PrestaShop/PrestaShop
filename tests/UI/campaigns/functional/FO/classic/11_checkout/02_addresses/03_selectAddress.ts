// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.equal(true);
  });

  it('should go to the fourth product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 4);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_5.name);
  });

  it('should add product to cart and go to cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicProductPage.addProductToTheCart(page, 1);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should validate shopping cart and go to checkout page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.equal(true);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

    await foClassicCheckoutPage.clickOnSignIn(page);

    const isStepCompleted = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
    expect(isStepCompleted).to.equal(true);
  });

  it('should choose the second address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondAddress', baseContext);

    await foClassicCheckoutPage.selectDeliveryAddress(page, 2);

    const isStepCompleted = await foClassicCheckoutPage.clickOnContinueButtonFromAddressStep(page);
    expect(isStepCompleted).to.eq(true);
  });

  it('should continue to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'continueToPaymentStep', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
  });

  it('should check that no payment method is available', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoPaymentMethodAvailable', baseContext);

    const alertMessage = await foClassicCheckoutPage.getNoPaymentAvailableMessage(page);
    expect(alertMessage).to.equal('Unfortunately, there is no payment method available.');
  });
});
