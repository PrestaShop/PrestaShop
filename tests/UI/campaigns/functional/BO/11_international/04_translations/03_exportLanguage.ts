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
import Modules from '@data/demo/modules';

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
          language: Languages.english.name,
          types: ['Back office'],
        },
    },
    {
      args:
        {
          language: Languages.french.name,
          types: ['Front office', 'Other'],
        },
    },
    {
      args:
        {
          language: Languages.english.name,
          module: Modules.psFacetedSearch.name,
        },
    },
  ];

  tests.forEach((test, index) => {
    if (index !== 2) {
      it(`should export language '${test.args.language}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `exportLanguage${index}`, baseContext);

        const filePath = await translationsPage.exportPrestashopTranslations(page, test.args.language, test.args.types);

        const doesFileExist = await files.doesFileExist(filePath);
        expect(doesFileExist, `File '${filePath}' was not downloaded`).to.eq(true);
      });

      it('should uncheck options in PrestaShop translations section', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `uncheckOptions${index}`, baseContext);

        await translationsPage.uncheckSelectedOptions(page, test.args.types);
      });
    } else {
      it(`should export language '${test.args.language}' with installed module '${Modules.psFacetedSearch}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `exportLanguage${index}`, baseContext);

        const filePath = await translationsPage.exportInstalledModuleTranslations(page, test.args.language, test.args.module!);

        const doesFileExist = await files.doesFileExist(filePath);
        expect(doesFileExist, `File '${filePath}' was not downloaded`).to.eq(true);
      });
    }
  });
});
