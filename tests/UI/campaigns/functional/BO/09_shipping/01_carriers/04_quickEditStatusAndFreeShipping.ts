// Import utils
import testContext from '@utils/testContext';

import {
  boCarriersPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shipping_carriers_quickEditStatusAndFreeShipping';

describe('BO - Shipping - Carriers : Quick edit status and free shipping', async () => {
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

  describe('Go to \'Shipping > Carriers\' page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });
  });

  // 2 - Quick edit carriers
  describe('Quick edit carrier and check it on FO', async () => {
    it('should disable the first carrier and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableFirstCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setStatus(page, 1, false);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.getStatus(page, 1);
      expect(carrierStatus).to.equal(false);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boCarriersPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should add the first product to the cart and checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    // Personal information step - Login
    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isStepLoginComplete = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isStepLoginComplete, 'Step Personal information is not complete').to.equal(true);
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the carriers list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersList', baseContext);

      const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.myCarrier.name]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should enable the third carrier and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThirdCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setStatus(page, 3, true);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.getStatus(page, 3);
      expect(carrierStatus).to.equal(true);
    });

    it('should go back to FO > Checkout page and refresh the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 1);

      await foClassicCheckoutPage.reloadPage(page);

      const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.myCheapCarrier.name, dataCarriers.myCarrier.name]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should enable \'Free shipping\' for the second carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableFreeShippingSecondCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setFreeShippingStatus(page, 2, true);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.isFreeShipping(page, 2);
      expect(carrierStatus).to.equal(true);
    });

    it('should go back to FO > Checkout page and check the data of the first carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO2', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 1);

      await foClassicCheckoutPage.reloadPage(page);

      const carrierData = await foClassicCheckoutPage.getCarrierData(page, 3);
      await Promise.all([
        expect(carrierData.name).to.equal(dataCarriers.myCheapCarrier.name),
        expect(carrierData.transitName).to.equal(dataCarriers.myCheapCarrier.transitName),
        expect(carrierData.priceText).to.equal(`€${dataCarriers.myCheapCarrier.priceTTC.toFixed(2)} tax incl.`),
      ]);
    });

    it('should check the data of the second carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDataOfSecondCarrier', baseContext);

      const carrierData = await foClassicCheckoutPage.getCarrierData(page, 2);
      await Promise.all([
        expect(carrierData.name).to.equal(dataCarriers.myCarrier.name),
        expect(carrierData.transitName).to.equal(dataCarriers.myCarrier.transitName),
        expect(carrierData.priceText).to.equal('Free'),
      ]);
    });
  });

  // Post-condition : Go back to default configuration
  describe('POST-TEST: Go back to the default configurations', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should disable \'Free shipping\' for the second carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableFreeShippingSecondCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setFreeShippingStatus(page, 2, false);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.isFreeShipping(page, 2);
      expect(carrierStatus).to.equal(false);
    });

    it('should disable the third carrier and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableThirdCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setStatus(page, 3, false);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.getStatus(page, 3);
      expect(carrierStatus).to.equal(false);
    });

    it('should enable the first carrier and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableFirstCarrier', baseContext);

      const isActionPerformed = await boCarriersPage.setStatus(page, 1, true);

      if (isActionPerformed) {
        const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
      }

      const carrierStatus = await boCarriersPage.getStatus(page, 1);
      expect(carrierStatus).to.equal(true);
    });
  });
});
