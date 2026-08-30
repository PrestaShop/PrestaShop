import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLanguagesPage,
  boLoginPage,
  boLocalizationPage,
  boSeoUrlsPage,
  boTranslationsPage,
  type BrowserContext,
  dataLanguages,
  foHummingbirdHomePage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_robotsFileGeneration_generateRobotsTxtFile';

describe('BO - Shop Parameters - Traffic & SEO : Generate robots.txt file', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );
    await boSeoUrlsPage.closeSfToolBar(page);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should generate robots.txt file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'generateRobotsFile', baseContext);

    const result = await boSeoUrlsPage.generateRobotsTextFile(page);
    expect(result).to.contains(boSeoUrlsPage.successfulUpdateMessage);
  });

  it('should check that robots.txt file is accessible in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkRobotsFileFO', baseContext);

    page = await boSeoUrlsPage.viewMyShop(page);
    await page.goto(`${global.FO.URL}robots.txt`);

    await utilsFile.downloadFile(`${global.FO.URL}robots.txt`, 'robots.txt');

    const found = await utilsFile.doesFileExist('robots.txt');
    expect(found).to.eq(true);

    const hasText = await utilsFile.isTextInFile('robots.txt', `/${dataLanguages.deutsch.isoCode}/`);
    expect(hasText).to.eq(false);

    page = await foHummingbirdHomePage.closePage(browserContext, page, 0);
  });

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.translationsLink,
    );
    await boTranslationsPage.closeSfToolBar(page);

    const pageTitle = await boTranslationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
  });

  it(`should add '${dataLanguages.deutsch.name}' language`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addDeutschLanguage', baseContext);

    const textResult = await boTranslationsPage.addUpdateLanguage(page, dataLanguages.deutsch.name);
    expect(textResult).to.equal(boTranslationsPage.successAlertMessage);
  });

  it('should go back to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );
    await boSeoUrlsPage.closeSfToolBar(page);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should generate robots.txt file again', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'generateRobotsFileAfterLanguage', baseContext);

    const result = await boSeoUrlsPage.generateRobotsTextFile(page);
    expect(result).to.contains(boSeoUrlsPage.successfulUpdateMessage);
  });

  it(`should check that robots.txt file contains '/*${dataLanguages.deutsch.isoCode}/' directories`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeutschInRobotsFile', baseContext);

    page = await boSeoUrlsPage.viewMyShop(page);
    await utilsFile.downloadFile(`${global.FO.URL}robots.txt`, 'robots.txt');

    const found = await utilsFile.doesFileExist('robots.txt');
    expect(found).to.eq(true);

    const hasText = await utilsFile.isTextInFile('robots.txt', `/${dataLanguages.deutsch.isoCode}/`);
    expect(hasText).to.eq(true);

    page = await foHummingbirdHomePage.closePage(browserContext, page, 0);
  });

  // Post-condition: delete German language
  describe('POST-TEST: Delete German language', async () => {
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

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await boLocalizationPage.goToSubTabLanguages(page);

      const pageTitle = await boLanguagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
    });

    it('should reset all filters and get number of languages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfLanguages = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguages).to.be.above(0);
    });

    it(`should filter by language name '${dataLanguages.deutsch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeutsch', baseContext);

      await boLanguagesPage.filterTable(page, 'input', 'name', dataLanguages.deutsch.name);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(dataLanguages.deutsch.name);
    });

    it(`should delete '${dataLanguages.deutsch.name}' language`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDeutschLanguage', baseContext);

      const textResult = await boLanguagesPage.deleteLanguage(page, 1);
      expect(textResult).to.contains(boLanguagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages - 1);
    });
  });
});
