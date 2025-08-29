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

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_termsOfService';

/*
Enable/Disable terms of service
Go to FO payment step and check terms of service checkbox and page title
 */
describe('BO - Shop Parameters - Order Settings : Enable/Disable terms of service', async () => {
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

  const tests = [
    {
      args: {
        action: 'disable',
        enable: false,
        pageName: '',
      },
    },
    {
      args: {
        action: 'enable',
        enable: true,
        pageName: dataCMSPages.delivery.title,
        title: 'Shipments and returns',
      },
    },
    {
      args: {
        action: 'enable',
        enable: true,
        pageName: dataCMSPages.legalNotice.title,
        title: 'Legal',
      },
    },
    {
      args: {
        action: 'enable',
        enable: true,
        pageName: dataCMSPages.termsAndCondition.title,
        title: dataCMSPages.termsAndCondition.title,
      },
    },
    {
      args: {
        action: 'enable',
        enable: true,
        pageName: dataCMSPages.aboutUs.title,
        title: dataCMSPages.aboutUs.title,
      },
    },
    {
      args: {
        action: 'enable',
        enable: true,
        pageName: dataCMSPages.securePayment.title,
        title: dataCMSPages.securePayment.title,
      },
    },
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} terms of service`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}TermsOfService${index}`,
        baseContext,
      );

      const result = await boOrderSettingsPage.setTermsOfService(page, test.args.enable, test.args.pageName);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      // Click on view my shop
      page = await boOrderSettingsPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(index + 1);
    });

    it('should proceed to checkout and go to deliveryStep', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `proceedToCheckout${index}`, baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      }

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check terms of service checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkTermsOfService${index}`, baseContext);

      // Check terms of service checkbox existence
      const isVisible = await foClassicCheckoutPage.isConditionToApproveCheckboxVisible(page);
      expect(isVisible).to.be.equal(test.args.enable);
    });

    if (test.args.enable) {
      it('should check the terms of service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkTermsOfServicePage${index}`, baseContext);

        const pageName = await foClassicCheckoutPage.getTermsOfServicePageTitle(page);
        expect(pageName).to.contains(test.args.title);
      });
    }

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAndBackToBO${index}`, baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });
  });

  it('should reset terms of service', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetTermsOfService$', baseContext);

    const result = await boOrderSettingsPage.setTermsOfService(page, true, dataCMSPages.termsAndCondition.title);
    expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
  });
});
