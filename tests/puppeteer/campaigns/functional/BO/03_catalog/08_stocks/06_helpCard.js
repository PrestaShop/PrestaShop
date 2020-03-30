require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const StocksPage = require('@pages/BO/catalog/stocks');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_stocks_helpCard';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    stocksPage: new StocksPage(page),
  };
};
// Check help card language in stocks page
describe('Help card in stocks page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to stocks page
  loginCommon.loginBO();

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.stocksLink,
    );
    const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);
    const isHelpSidebarVisible = await this.pageObjects.stocksPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;
    const documentURL = await this.pageObjects.stocksPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);
    const isHelpSidebarClosed = await this.pageObjects.stocksPage.closeHelpSideBar();
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
