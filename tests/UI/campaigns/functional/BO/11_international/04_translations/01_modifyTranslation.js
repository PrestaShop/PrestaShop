require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TranslationsPage = require('@pages/BO/international/translations');
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_translations_editTranslation';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    translationsPage: new TranslationsPage(page),
  };
};

describe('Edit translation', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to translations page
  loginCommon.loginBO();

  it('should go to translations page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.translationsLink,
    );

    const pageTitle = await this.pageObjects.translationsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.translationsPage.pageTitle);
  });

  it('should modify translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation', baseContext);

    await this.pageObjects.translationsPage.modifyTranslation('Themes translations', 'classic', 'Français (French)');
    const pageTitle = await this.pageObjects.translationsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.translationsPage.pageTitle);
  });

  it('should search \'Popular Products\' expression and modify the french translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'translateExpression', baseContext);

    await this.pageObjects.translationsPage.searchTranslation('Popular Products');
    const textResult = await this.pageObjects.translationsPage.translateExpression('translate');
    await expect(textResult).to.equal(this.pageObjects.translationsPage.validationMessage);
  });

  it('should go to FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await this.pageObjects.homePage.goToFo();
    await this.pageObjects.homePage.changeLanguage('fr');

    const isHomePage = await this.pageObjects.homePage.isHomePage();
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should check the translation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTranslation', baseContext);

    const title = await this.pageObjects.homePage.getPopularProductTitle();
    await expect(title).to.equal('translate');
  });
});
