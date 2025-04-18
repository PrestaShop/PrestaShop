import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boPaymentMethodsPage,
  boPaymentPreferencesPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_payment';

describe('BO - Payment', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
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

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Payment > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentPreferencesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.paymentParentLink,
      boDashboardPage.preferencesLink,
    );

    const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
