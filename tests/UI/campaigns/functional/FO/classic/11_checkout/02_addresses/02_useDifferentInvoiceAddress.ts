// Import utils
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

import {
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_addresses_useDifferentInvoiceAddress';

/*
Go to FO
Add product to cart
Go to checkout page
Choose to order as guest
Add guest information
Add delivery address
Click on Use another address for invoice
Fill a second form address
Finish the order
*/
describe('FO - Checkout - Addresses: Use different invoice address', async () => {
  // Create faker data
  const guestData: FakerCustomer = new FakerCustomer({password: ''});
  const deliveryAddress: FakerAddress = new FakerAddress({country: 'France'});
  const invoiceAddress: FakerAddress = new FakerAddress({country: 'France'});

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

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.equal(true);
  });

  it('should go to fourth product page', async function () {
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

  it('should fill customer information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

    const isStepCompleted = await checkoutPage.setGuestPersonalInformation(page, guestData);
    expect(isStepCompleted).to.equal(true);
  });

  it('should fill different delivery and invoice addresses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerAddresses', baseContext);

    const isStepCompleted = await checkoutPage.setAddress(page, deliveryAddress, invoiceAddress);
    expect(isStepCompleted).to.equal(true);
  });

  it('should complete the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'completeTheOrder', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

    // Payment step - Choose payment step
    await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

    // Check the confirmation message
    expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });
});
