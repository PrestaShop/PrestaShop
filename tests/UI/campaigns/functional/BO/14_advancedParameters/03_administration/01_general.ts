// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import administrationPage from '@pages/BO/advancedParameters/administration';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_administration_general';

describe('BO - Advanced Parameters - Administration : Check general options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Advanced Parameters > Administration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.administrationLink,
    );

    const pageTitle = await administrationPage.getPageTitle(page);
    expect(pageTitle).to.contains(administrationPage.pageTitle);
  });

  it('should disable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCookiesIPAddress', baseContext);

    await administrationPage.setCookiesIPAddress(page, false);

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressDisabled', baseContext);

    const isEnabled = await administrationPage.isCheckCookiesAddressEnabled(page);
    await expect(isEnabled).to.be.false;
  });

  it('should enable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCookiesIPAddress', baseContext);

    await administrationPage.setCookiesIPAddress(page, true);

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressEnabled', baseContext);

    const isEnabled = await administrationPage.isCheckCookiesAddressEnabled(page);
    await expect(isEnabled).to.be.true;
  });

  it('should update \'Lifetime of front office cookies\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfFOCookies', baseContext);

    await administrationPage.setLifetimeFOCookies(page, 500);

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should update \' Lifetime of back office cookies\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfBOCookies', baseContext);

    await administrationPage.setLifetimeBOCookies(page, 500);

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should update \'Cookie SameSite\' to \'Strict\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite1', baseContext);

    await administrationPage.setCookieSameSite(page, 'Strict');

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should update \'Cookie SameSite\' to \'None\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite2', baseContext);

    await administrationPage.setCookieSameSite(page, 'None');

    const message = await administrationPage.saveGeneralForm(page);

    if (global.INSTALL.ENABLE_SSL) {
      expect(message).to.eq(administrationPage.successfulUpdateMessage);
    } else {
      expect(message).to.eq(administrationPage.dangerAlertCookieSameSite);
    }
  });

  it('should update \'Cookie SameSite\' to \'default\' value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCookieSameSite3', baseContext);

    await administrationPage.setCookieSameSite(page, 'Lax');

    const successMessage = await administrationPage.saveGeneralForm(page);
    expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });
});
