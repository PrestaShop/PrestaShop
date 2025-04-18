// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  dataLanguages,
  type ImportContent,
  type Page,
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it('should import localization pack', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

    const textResult = await boLocalizationPage.importLocalizationPack(page, 'Chile', contentToImport);
    expect(textResult).to.equal(boLocalizationPage.importLocalizationPackSuccessfulMessage);
  });

  it('should set "Spanish" as \'Default language\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setSpanishAsDefaultLanguage', baseContext);

    const textResult = await boLocalizationPage.setDefaultLanguage(page, dataLanguages.spanish.name, true);
    expect(textResult).to.equal(boLocalizationPage.successfulSettingsUpdateMessage);
  });

  it('should set "English" as \'Default language\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEnglishAsDefaultLanguage', baseContext);

    const textResult = await boLocalizationPage.setDefaultLanguage(page, dataLanguages.english.name, true);
    expect(textResult).to.equal(boLocalizationPage.successfulSettingsUpdateMessage);
  });
});
