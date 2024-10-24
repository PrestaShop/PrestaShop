// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boTranslationsPage,
  type BrowserContext,
  dataLanguages,
  dataModules,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_translations_exportLanguage';

describe('BO - International - Translation : Export languages', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.translationsLink,
    );

    const pageTitle = await boTranslationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
  });

  const tests = [
    {
      args:
        {
          language: dataLanguages.english.name,
          types: ['Back office'],
        },
    },
    {
      args:
        {
          language: dataLanguages.french.name,
          types: ['Front office', 'Other'],
        },
    },
    {
      args:
        {
          language: dataLanguages.english.name,
          module: dataModules.psFacetedSearch.name,
        },
    },
  ];

  tests.forEach((test, index) => {
    if (index !== 2) {
      it(`should export language '${test.args.language}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `exportLanguage${index}`, baseContext);

        const filePath = await boTranslationsPage.exportPrestashopTranslations(page, test.args.language, test.args.types);

        const doesFileExist = await utilsFile.doesFileExist(filePath);
        expect(doesFileExist, `File '${filePath}' was not downloaded`).to.eq(true);
      });

      it('should uncheck options in PrestaShop translations section', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `uncheckOptions${index}`, baseContext);

        await boTranslationsPage.uncheckSelectedOptions(page, test.args.types);
      });
    } else {
      it(
        `should export language '${test.args.language}' with installed module '${dataModules.psFacetedSearch}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `exportLanguage${index}`, baseContext);

          const filePath = await boTranslationsPage.exportInstalledModuleTranslations(
            page,
            test.args.language,
            test.args.module!,
          );

          const doesFileExist = await utilsFile.doesFileExist(filePath);
          expect(doesFileExist, `File '${filePath}' was not downloaded`).to.eq(true);
        });
    }
  });
});
