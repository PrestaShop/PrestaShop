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

const baseContext = 'functional_BO_international_translations_exportLanguage';

let browserContext;
let page;

describe('BO - International - Translation : Export languages', async () => {
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

  const tests = [
    {
      args:
        {
          language: Languages.english, types: ['Front office'],
        },
    },
    {
      args:
        {
          language: Languages.french, types: ['Front office'],
        },
    },
  ];

  tests.forEach((test) => {
    it(`Export language '${test.args.language.name}'`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `exportLanguage${test.args.language.name}Theme${test.args.theme}`,
        baseContext,
      );

      const filePath = await translationsPage.exportPrestashopTranslations(
        page,
        test.args.language.name,
        test.args.types,
      );

      const doesFileExist = await files.doesFileExist(filePath);
      await expect(doesFileExist, `File '${filePath}' was not downloaded`).to.be.true;
    });
  });
});
