require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const translationsPage = require('@pages/BO/international/translations');
const homePage = require('@pages/FO/home');

const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_translations_modifyTranslation';

let browserContext;
let page;

describe('BO - International - Translation : Edit', async () => {
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
    await expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should choose the translation to modify', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation', baseContext);

    await translationsPage.modifyTranslation(page, 'Front office Translations', 'classic', Languages.french.name);
    const pageTitle = await translationsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should search \'Popular Products\' expression and modify the french translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'translateExpression', baseContext);

    await translationsPage.searchTranslation(page, 'Popular Products');
    const textResult = await translationsPage.translateExpression(page, 'translate');
    await expect(textResult).to.equal(translationsPage.validationMessage);
  });

  it('should go to FO page and change the language to French', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    page = await translationsPage.viewMyShop(page);
    await homePage.changeLanguage(page, 'fr');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should check the translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTranslation', baseContext);

    const title = await homePage.getPopularProductTitle(page);
    await expect(title).to.contain('translate');
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await homePage.closePage(browserContext, page, 0);

    const pageTitle = await translationsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it('should reset the french translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchAndResetTranslation', baseContext);

    const textResult = await translationsPage.resetTranslation(page);
    await expect(textResult).to.equal(translationsPage.validationResetMessage);
  });

  it('should go to FO page and change the language to French', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoAfterReset', baseContext);

    await homePage.goToFo(page);
    await homePage.changeLanguage(page, 'fr');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should check the translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTranslationAfterReset', baseContext);

    const title = await homePage.getPopularProductTitle(page);
    await expect(title).to.equal('Produits populaires');
  });
});
