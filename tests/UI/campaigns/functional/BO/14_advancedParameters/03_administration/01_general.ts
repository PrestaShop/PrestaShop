// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boAdministrationPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_administration_general';

describe('BO - Advanced Parameters - Administration : Check general options', async () => {
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

  it('should go to \'Advanced Parameters > Administration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.administrationLink,
    );

    const pageTitle = await boAdministrationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
  });

  it('should disable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCookiesIPAddress', baseContext);

    await boAdministrationPage.setCookiesIPAddress(page, false);

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressDisabled', baseContext);

    const isEnabled = await boAdministrationPage.isCheckCookiesAddressEnabled(page);
    expect(isEnabled).to.eq(false);
  });

  it('should enable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCookiesIPAddress', baseContext);

    await boAdministrationPage.setCookiesIPAddress(page, true);

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressEnabled', baseContext);

    const isEnabled = await boAdministrationPage.isCheckCookiesAddressEnabled(page);
    expect(isEnabled).to.eq(true);
  });

  it('should update \'Lifetime of front office cookies\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfFOCookies', baseContext);

    await boAdministrationPage.setLifetimeFOCookies(page, 500);

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });

  it('should update \' Lifetime of back office cookies\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfBOCookies', baseContext);

    await boAdministrationPage.setLifetimeBOCookies(page, 500);

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });

  it('should update \'Cookie SameSite\' to \'Strict\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite1', baseContext);

    await boAdministrationPage.setCookieSameSite(page, 'Strict');

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });

  it('should update \'Cookie SameSite\' to \'None\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite2', baseContext);

    await boAdministrationPage.setCookieSameSite(page, 'None');

    const message = await boAdministrationPage.saveGeneralForm(page);

    if (global.INSTALL.ENABLE_SSL) {
      expect(message).to.eq(boAdministrationPage.successfulUpdateMessage);
    } else {
      expect(message).to.eq(boAdministrationPage.dangerAlertCookieSameSite);
    }
  });

  it('should update \'Cookie SameSite\' to \'default\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite3', baseContext);

    await boAdministrationPage.setCookieSameSite(page, 'Lax');

    const successMessage = await boAdministrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
  });
});
