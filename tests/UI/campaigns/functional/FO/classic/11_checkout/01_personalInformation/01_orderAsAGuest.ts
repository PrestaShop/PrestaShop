// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import productPage from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import checkoutPage from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_personalInformation_orderAsAGuest';

/*
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Fill guest personal information
- Complete the order
 */

describe('FO - Checkout - Personal information : Order as a guest', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const guestData: CustomerData = new CustomerData({password: ''});
  const secondGuestData: CustomerData = new CustomerData({password: ''});
  const addressData: AddressData = new AddressData({country: 'France'});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should open FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

    // Go to FO and change language
    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await homePage.goToProductPage(page, 1);
    await productPage.addProductToTheCart(page, 1);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should proceed to checkout validate the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

    await cartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.equal(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, guestData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
  });

  it('should click on edit Personal information and edit the guest information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerIdentity', baseContext);

    await checkoutPage.clickOnEditPersonalInformationStep(page);

    const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, secondGuestData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

    const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
    expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
  });

  it('should validate the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

    // Payment step - Choose payment step
    await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

    // Check the confirmation message
    expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });
});
