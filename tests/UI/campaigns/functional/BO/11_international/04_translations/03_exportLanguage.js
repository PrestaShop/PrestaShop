require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const translationsPage = require('@pages/BO/international/translations');

const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_translations_exportLanguage';

let browserContext;
let page;

describe('Export languages in translations page', async () => {
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

  it('should go to translations page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.translationsLink,
    );

    const pageTitle = await translationsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  const tests = [
    {
      args:
        {
          language: Languages.english, theme: 'classic',
        },
    },
    {
      args:
        {
          language: Languages.french, theme: 'classic',
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

      const filePath = await translationsPage.exportThemeTranslations(page, test.args.language.name, test.args.theme);

      const doesFileExist = await files.doesFileExist(filePath);
      await expect(doesFileExist, `File '${filePath}' was not downloaded`).to.be.true;
    });
  });
});
