// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import translationsPage from '@pages/BO/international/translations';
import {homePage} from '@pages/FO/home';

// Import data
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_translations_modifyTranslation';

describe('BO - International - Translation : Edit', async () => {
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

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.translationsLink,
    );

    const pageTitle = await translationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should choose the translation to modify', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation', baseContext);

    await translationsPage.modifyTranslation(page, 'Front office Translations', 'classic', Languages.french.name);
    const pageTitle = await translationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should search \'Popular Products\' expression and modify the french translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'translateExpression', baseContext);

    await translationsPage.searchTranslation(page, 'Popular Products');
    const textResult = await translationsPage.translateExpression(page, 'translate');
    expect(textResult).to.equal(translationsPage.validationMessage);
  });

  it('should go to FO page and change the language to French', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    page = await translationsPage.viewMyShop(page);
    await homePage.changeLanguage(page, 'fr');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should check the translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTranslation', baseContext);

    const title = await homePage.getBlockTitle(page);
    expect(title).to.contain('translate');
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await homePage.closePage(browserContext, page, 0);

    const pageTitle = await translationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should reset the french translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchAndResetTranslation', baseContext);

    const textResult = await translationsPage.resetTranslation(page);
    expect(textResult).to.equal(translationsPage.validationResetMessage);
  });

  it('should go to FO page and change the language to French', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoAfterReset', baseContext);

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'fr');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should check the translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTranslationAfterReset', baseContext);

    const title = await homePage.getBlockTitle(page);
    expect(title).to.equal('Produits populaires');
  });
});
