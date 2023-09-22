// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import translationsPage from '@pages/BO/international/translations';

// Import data
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_translations_exportLanguage';

describe('BO - International - Translation : Export languages', async () => {
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

  const tests = [
    {
      args:
        {
          language: Languages.english,
          types: ['Front office'],
        },
    },
    {
      args:
        {
          language: Languages.french,
          types: ['Front office'],
        },
    },
  ];

  tests.forEach((test) => {
    it(`Export language '${test.args.language.name}'`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `exportLanguage${test.args.language.name}Theme`,
        baseContext,
      );

      const filePath = await translationsPage.exportPrestashopTranslations(
        page,
        test.args.language.name,
        test.args.types,
      );
      const doesFileExist = await files.doesFileExist(filePath);
      expect(doesFileExist, `File '${filePath}' was not downloaded`).to.eq(true);
    });
  });
});
