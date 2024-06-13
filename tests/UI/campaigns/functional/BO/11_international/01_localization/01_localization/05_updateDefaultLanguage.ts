// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import localizationPage from '@pages/BO/international/localization';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataLanguages,
  type ImportContent,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_localization_updateDefaultLanguage';

describe('BO - International - Localization : Update default language', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contentToImport: ImportContent = {
    importStates: false,
    importTaxes: true,
    importCurrencies: true,
    importLanguages: true,
    importUnits: false,
    updatePriceDisplayForGroups: false,
  };

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should import localization pack', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

    const textResult = await localizationPage.importLocalizationPack(page, 'Chile', contentToImport);
    expect(textResult).to.equal(localizationPage.importLocalizationPackSuccessfulMessage);
  });

  it('should set "Spanish" as \'Default language\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setSpanishAsDefaultLanguage', baseContext);

    const textResult = await localizationPage.setDefaultLanguage(page, dataLanguages.spanish.name, true);
    expect(textResult).to.equal(localizationPage.successfulSettingsUpdateMessage);
  });

  it('should set "English" as \'Default language\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEnglishAsDefaultLanguage', baseContext);

    const textResult = await localizationPage.setDefaultLanguage(page, dataLanguages.english.name, true);
    expect(textResult).to.equal(localizationPage.successfulSettingsUpdateMessage);
  });
});
