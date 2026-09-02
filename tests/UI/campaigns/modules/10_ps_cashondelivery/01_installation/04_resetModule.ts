import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_cashondelivery_installation_resetModule';

describe('Cash on delivery (COD) module - Reset module', async () => {
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

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.psCashOnDelivery.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psCashOnDelivery, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psCashOnDelivery);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psCashOnDelivery, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psCashOnDelivery, 'reset');
    expect(successMessage).to.eq(boModuleManagerPage.resetModuleSuccessMessage(dataModules.psCashOnDelivery.tag));
  });

  it('should go to Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    page = await boModuleManagerPage.viewMyShop(page);
    await foHummingbirdHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHummingbirdHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

    await foHummingbirdHomePage.goToLoginPage(page);

    const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(foHummingbirdLoginPage.pageTitle);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

    await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).to.eq(true);
  });

  it('should add the first product to the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foHummingbirdLoginPage.goToHomePage(page);

    // Add first product to cart by quick view
    await foHummingbirdHomePage.quickViewProduct(page, 1);
    await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
    await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
    expect(pageTitle).to.eq(foHummingbirdCartPage.pageTitle);
  });

  it('should proceed to checkout and check Step Address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

    await foHummingbirdCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);

    const isStepPersonalInformationComplete = await foHummingbirdCheckoutPage.isStepCompleted(
      page,
      foHummingbirdCheckoutPage.personalInformationStepForm,
    );
    expect(isStepPersonalInformationComplete).to.eq(true);
  });

  it('should validate Step Address and go to Delivery Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

    const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete).to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it(`should check the '${dataModules.psCashOnDelivery.name}' payment module`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentModule', baseContext);

    // Payment step - Choose payment step
    const isVisible = await foHummingbirdCheckoutPage.isPaymentMethodExist(page, dataModules.psCashOnDelivery.tag);
    expect(isVisible).to.eq(true);
  });
});
