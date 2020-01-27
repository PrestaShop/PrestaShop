require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmailThemesPage = require('@pages/BO/design/emailThemes');
const PreviewEmailThemesPage = require('@pages/BO/design/emailThemes/preview');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    emailThemesPage: new EmailThemesPage(page),
    previewEmailThemesPage: new PreviewEmailThemesPage(page),
  };
};

describe('Preview Email themes classic and modern', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to taxes page
  loginCommon.loginBO();
  it('should go to design > email themes page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.emailThemeLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.emailThemesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.emailThemesPage.pageTitle);
  });

  describe('Preview email themes', async () => {
    const tests = [
      {args: {emailThemeName: 'classic', numberOfLayouts: 50}},
      {args: {emailThemeName: 'modern', numberOfLayouts: 50}},
    ];
    tests.forEach((test) => {
      it(`should preview email theme ${test.args.emailThemeName} and check number of layouts`, async function () {
        await this.pageObjects.emailThemesPage.previewEmailTheme(test.args.emailThemeName);
        const pageTitle = await this.pageObjects.emailThemesPage.getPageTitle();
        await expect(pageTitle).to.contains(
          `${this.pageObjects.previewEmailThemesPage.pageTitle} ${test.args.emailThemeName}`,
        );

        const numberOfLayouts = await this.pageObjects.previewEmailThemesPage.getNumberOfLayoutInGrid();
        await expect(numberOfLayouts).to.equal(test.args.numberOfLayouts);
      });

      it('should go back to email themes page', async function () {
        await this.pageObjects.previewEmailThemesPage.goBackToEmailThemesPage();
        const pageTitle = await this.pageObjects.emailThemesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.emailThemesPage.pageTitle);
      });
    });
  });
});
