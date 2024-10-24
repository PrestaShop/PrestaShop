// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  const guestData: FakerCustomer = new FakerCustomer({password: ''});
  const secondGuestData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

    // Go to FO and change language
    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicHomePage.goToProductPage(page, 1);
    await foClassicProductPage.addProductToTheCart(page, 1);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout validate the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.equal(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, guestData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
  });

  it('should click on edit Personal information and edit the guest information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerIdentity', baseContext);

    await foClassicCheckoutPage.clickOnEditPersonalInformationStep(page);

    const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, secondGuestData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

    const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressData);
    expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
  });

  it('should validate the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

    // Payment step - Choose payment step
    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

    // Check the confirmation message
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });
});
