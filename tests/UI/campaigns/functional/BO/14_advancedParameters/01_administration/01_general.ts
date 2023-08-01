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
    await expect(pageTitle).to.contains(administrationPage.pageTitle);
  });

  it('should disable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCookiesIPAddress', baseContext);

    const successMessage = await administrationPage.setCookiesIPAddress(page, false);
    await expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressEnabled', baseContext);

    const isEnabled = await administrationPage.isCheckCookiesAddressEnabled(page);
    await expect(isEnabled).to.be.false;
  });

  it('should enable \'Cookie\'s IP address\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCookiesIPAddress', baseContext);

    const successMessage = await administrationPage.setCookiesIPAddress(page, true);
    await expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should check that the \'Cookie\'s IP address\' is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatCookieIpAddressEnabled', baseContext);

    const isEnabled = await administrationPage.isCheckCookiesAddressEnabled(page);
    await expect(isEnabled).to.be.true;
  });

  it('should update \'Lifetime of front office cookies\'', async function(){
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfFOCookies', baseContext);

    const successMessage = await administrationPage.setLifetimeFOCookies(page, 500);
    await expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should update \' Lifetime of back office cookies\'', async function(){
    await testContext.addContextItem(this, 'testIdentifier', 'updateLifetimeOfFOCookies', baseContext);

    const successMessage = await administrationPage.setLifetimeBOCookies(page, 500);
    await expect(successMessage).to.eq(administrationPage.successfulUpdateMessage);
  });

  it('should update \'Cookie SameSite\'', async )
});
