require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TranslationsPage = require('@pages/BO/international/translations');
const {Languages} = require('@data/demo/languages');
const files = require('@utils/files');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_translations_exportLanguage';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    translationsPage: new TranslationsPage(page),
  };
};

describe('Export languages in translations page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to translations page
  loginCommon.loginBO();

  it('should go to translations page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.translationsLink,
    );
    const pageTitle = await this.pageObjects.translationsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.translationsPage.pageTitle);
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', language: Languages.english, theme: 'classic', filename: 'classic.en-US.zip',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', language: Languages.french, theme: 'classic', filename: 'classic.fr-FR.zip',
        },
    },
  ];

  tests.forEach((test) => {
    it(`Export language '${test.args.language.name}' for theme '${test.args.theme}'`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `exportLanguage${test.args.language.name}Theme${test.args.theme}`,
        baseContext,
      );
      await this.pageObjects.translationsPage.exportLanguage(test.args.language.name, test.args.theme);
      const doesFileExist = await files.doesFileExist(test.args.filename);
      await expect(doesFileExist, `File ${test.args.filename} was not downloaded`).to.be.true;
      await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${test.args.filename}`);
    });
  });
});
