// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boLoginPage,
  type BrowserContext,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_login_checkLinks';

describe('BO - Login : Check links', async () => {
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

  it('should open the BO authentication page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openAuthenticationPage', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should click on the shop name on the top left corner', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnShopName', baseContext);

    await boLoginPage.clickOnBackToShopNameLink(page);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.equal(true);
  });

  it('should go back to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToLoginPagePage', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should click on "[© PrestaShop™ 2007-2024 - All rights reserved"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnAllRightsReservedLink', baseContext);

    page = await boLoginPage.clickOnAllRightsReservedLink(page);

    const url = await boLoginPage.getCurrentURL(page);
    expect(url).to.equal('https://www.prestashop-project.org/');
  });

  it('should close the Prestashop project page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closePrestashopPage', baseContext);

    page = await boLoginPage.closePage(browserContext, page, 1);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should click on "X icon"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnTwitterLink', baseContext);

    page = await boLoginPage.clickOnTwitterLink(page);

    const url = await boLoginPage.getCurrentURL(page);
    expect(url).to.contains('https://x.com').and.to.contains('PrestaShop');
  });

  it('should close the twitter page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeTwitterPage', baseContext);

    page = await boLoginPage.closePage(browserContext, page, 1);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should click on "Facebook icon"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnFacebookLink', baseContext);

    page = await boLoginPage.clickOnFacebookLink(page);

    const url = await boLoginPage.getCurrentURL(page);
    expect(url).to.contains('https://www.facebook.com/prestashop');
  });

  it('should close the facebook page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeFacebookPage', baseContext);

    page = await boLoginPage.closePage(browserContext, page, 1);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should click on "Github icon"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnGithubLink', baseContext);

    page = await boLoginPage.clickOnGithubLink(page);

    const url = await boLoginPage.getCurrentURL(page);
    expect(url).to.equal('https://github.com/PrestaShop/PrestaShop');
  });
});
