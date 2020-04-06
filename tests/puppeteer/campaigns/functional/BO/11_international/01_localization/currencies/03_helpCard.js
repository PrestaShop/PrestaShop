require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalisationPage = require('@pages/BO/international/localization');
const CurrenciesPage = require('@pages/BO/international/currencies');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_currencies_helpCard';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalisationPage(page),
    currenciesPage: new CurrenciesPage(page),
  };
};

// Check that help card is in english in currencies page
describe('Currencies help card', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to currencies page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalisationPage', baseContext);
    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to currencies page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);
    await this.pageObjects.localizationPage.goToSubTabCurrencies();
    const pageTitle = await this.pageObjects.currenciesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.currenciesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);
    const isHelpSidebarVisible = await this.pageObjects.currenciesPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;
    const documentURL = await this.pageObjects.currenciesPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);
    const isHelpSidebarVisible = await this.pageObjects.currenciesPage.closeHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;
  });
});
