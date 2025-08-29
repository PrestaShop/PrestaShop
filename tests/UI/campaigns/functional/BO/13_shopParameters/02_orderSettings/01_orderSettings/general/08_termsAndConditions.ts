// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  dataCMSPages,
  dataCustomers,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_termsAndConditions';

describe('BO - Shop Parameters - Order Settings : Terms and conditions', async () => {
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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.orderSettingsLink,
    );
    await boOrderSettingsPage.closeSfToolBar(page);

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
  });

  it('should check terms and conditions page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTermsAndConditionsPage', baseContext);

    const result = await boOrderSettingsPage.getTermsAndConditionsPage(page);
    expect(result).to.equals(dataCMSPages.termsAndCondition.title);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    // Click on view my shop
    page = await boOrderSettingsPage.viewMyShop(page);
    // Change FO language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    // Go to the first product page
    await foClassicHomePage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should proceed to checkout and go to deliveryStep', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    // Checkout the order
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Personal information step - Login
    await foClassicCheckoutPage.clickOnSignIn(page);
    await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);

    // Address step - Go to delivery step
    const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete).to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete).to.eq(true);
  });

  it('should check the terms of service page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTermsOfServicePage', baseContext);

    const isVisible = await foClassicCheckoutPage.isConditionToApproveCheckboxVisible(page);
    expect(isVisible).to.be.equal(true);

    const pageName = await foClassicCheckoutPage.getTermsOfServicePageTitle(page);
    expect(pageName).to.contains(dataCMSPages.termsAndCondition.title);
  });

  it('should return to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToBO', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
  });

  it('should change terms and conditions page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setTermsAndConditionsPage', baseContext);

    const result = await boOrderSettingsPage.setTermsOfService(page, true, dataCMSPages.legalNotice.title);
    expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
  });

  it('should check in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFO', baseContext);

    page = await boOrderSettingsPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const isVisible = await foClassicCheckoutPage.isConditionToApproveCheckboxVisible(page);
    expect(isVisible).to.be.equal(true);

    const pageName = await foClassicCheckoutPage.getTermsOfServicePageTitle(page);
    expect(pageName).to.contains('Legal');
  });

  it('should reset terms and conditions page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetTermsAndConditionsPage', baseContext);

    page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

    const result = await boOrderSettingsPage.setTermsOfService(page, true, dataCMSPages.termsAndCondition.title);
    expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
  });
});
