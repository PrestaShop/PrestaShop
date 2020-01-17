require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    foBasePage: new FOBasePage(page),
  };
};

describe('Enable/Disable friendly URL', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.trafficAndSeoLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
  });

  it('should disable friendly URL', async function () {
    const result = await this.pageObjects.seoAndUrlsPage.enableDisableFriendlyURL(false);
    await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const url = await this.pageObjects.seoAndUrlsPage.getCurrentURL();
    await expect(url).to.contains('index.php');
    page = await this.pageObjects.seoAndUrlsPage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should enable friendly URL', async function () {
    const result = await this.pageObjects.seoAndUrlsPage.enableDisableFriendlyURL(true);
    await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.foBasePage.changeLanguage('en');
    const url = await this.pageObjects.seoAndUrlsPage.getCurrentURL();
    await expect(url).to.contains('/en/');
    page = await this.pageObjects.seoAndUrlsPage.closePage(browser, 1);
    this.pageObjects = await init();
  });
});
