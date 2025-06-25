// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {resetModule} from '@commonTests/BO/modules/moduleManager';

import {
  boDashboardPage,
  boLoginPage,
  boPaymentMethodsPage,
  type BrowserContext,
  dataModules,
  dataPaymentMethods,
  modPsCheckPaymentBoMain,
  modPsWirepaymentBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_payment_paymentMethods_configureModuleLink';

describe('BO - Payments - Payment methods: Configure module link', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  describe('Configure module link', async () => {
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

    it('should go to \'Payment > Payment Methods\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentMethodsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.paymentParentLink,
        boDashboardPage.paymentMethodsLink,
      );
      await boPaymentMethodsPage.closeSfToolBar(page);

      const pageTitle = await boPaymentMethodsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentMethodsPage.pageTitle);

      const numActivePayments = await boPaymentMethodsPage.getCountActivePayments(page);
      expect(numActivePayments).to.be.greaterThanOrEqual(Object.keys(dataPaymentMethods).length);
    });

    it(`should click on the Configure button for "${dataPaymentMethods.wirePayment.displayName}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickConfigureButtonWirePayment', baseContext);

      const hasConfigureButton = await boPaymentMethodsPage.hasConfigureButton(page, dataModules.psWirePayment);
      expect(hasConfigureButton).to.equal(true);

      await boPaymentMethodsPage.clickConfigureButton(page, dataModules.psWirePayment);

      const pageTitle = await modPsWirepaymentBoMain.getPageSubTitle(page);
      expect(pageTitle).to.contains(modPsWirepaymentBoMain.pageTitle);
    });

    it(`should fill required fields for "${dataPaymentMethods.wirePayment.displayName}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillRequiredFieldsWirePayment', baseContext);

      await modPsWirepaymentBoMain.setAccountOwner(page, 'Account Owner');
      await modPsWirepaymentBoMain.setAccountDetails(page, 'Account Details');
      await modPsWirepaymentBoMain.setBankAddress(page, 'Bank Address');

      const result = await modPsWirepaymentBoMain.saveFormContactDetails(page);
      expect(result).to.contains(modPsWirepaymentBoMain.successfulUpdateMessage);
    });

    it('should return to \'Payment > Payment Methods\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToPaymentMethodsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.paymentParentLink,
        boDashboardPage.paymentMethodsLink,
      );
      await boPaymentMethodsPage.closeSfToolBar(page);

      const pageTitle = await boPaymentMethodsPage.getPageTitle(page);
      expect(pageTitle).to.equal(boPaymentMethodsPage.pageTitle);

      const numActivePayments = await boPaymentMethodsPage.getCountActivePayments(page);
      expect(numActivePayments).to.be.greaterThanOrEqual(Object.keys(dataPaymentMethods).length);
    });

    it(`should click on the Configure button for "${dataPaymentMethods.checkPayment.displayName}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickConfigureButtonCheckPayment', baseContext);

      const hasConfigureButton = await boPaymentMethodsPage.hasConfigureButton(page, dataModules.psCheckPayment);
      expect(hasConfigureButton).to.equal(true);

      await boPaymentMethodsPage.clickConfigureButton(page, dataModules.psCheckPayment);

      const pageTitle = await modPsCheckPaymentBoMain.getPageSubTitle(page);
      expect(pageTitle).to.equal(modPsCheckPaymentBoMain.pageTitle);
    });

    it(`should fill required fields for "${dataPaymentMethods.checkPayment.displayName}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillRequiredFieldsCheckPayment', baseContext);

      await modPsCheckPaymentBoMain.setPayee(page, 'Payee');
      await modPsCheckPaymentBoMain.setAddress(page, 'Address');

      const result = await modPsCheckPaymentBoMain.saveFormContactDetails(page);
      expect(result).to.contains(modPsCheckPaymentBoMain.successfulUpdateMessage);
    });

    it('should return to \'Payment > Payment Methods\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnFinalPaymentMethodsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.paymentParentLink,
        boDashboardPage.paymentMethodsLink,
      );
      await boPaymentMethodsPage.closeSfToolBar(page);

      const pageTitle = await boPaymentMethodsPage.getPageTitle(page);
      expect(pageTitle).to.equal(boPaymentMethodsPage.pageTitle);

      const numActivePayments = await boPaymentMethodsPage.getCountActivePayments(page);
      expect(numActivePayments).to.be.greaterThanOrEqual(Object.keys(dataPaymentMethods).length);

      const hasConfigureButton = await boPaymentMethodsPage.hasConfigureButton(page, dataModules.psCashOnDelivery);
      expect(hasConfigureButton).to.equal(false);
    });
  });

  resetModule(dataModules.psWirePayment, `${baseContext}_postTest_0`);

  resetModule(dataModules.psCheckPayment, `${baseContext}_postTest_1`);
});
