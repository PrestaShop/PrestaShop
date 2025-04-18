import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boCarriersPage,
  boCarriersCreatePage,
  boShippingPreferencesPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_shipping';

describe('BO - Shipping', async () => {
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

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShippingCarriersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );
    await boCarriersPage.closeSfToolBar(page);

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shipping > Carriers > New Carrier\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

    await boCarriersPage.goToAddNewCarrierPage(page);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shipping > Carriers > Edit Carrier\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToShippingCarriersPage', baseContext);

    await boCarriersCreatePage.goToPreviousPage(page);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shipping > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShippingPreferencesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.shippingPreferencesLink,
    );

    const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
