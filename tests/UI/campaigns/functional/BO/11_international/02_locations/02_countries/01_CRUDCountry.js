require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');

const addCountryPage = require('@pages/BO/international/languages/add');
const foHomePage = require('@pages/FO/home');

// Import data
const CountryFaker = require('@data/faker/country');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_CRUDCountry';

let browserContext;
let page;

const createCountryData = new CountryFaker({isoCode: 'de'});
const editCountryData = new CountryFaker({isoCode: 'nl', status: false});
let numberOfCountries = 0;

describe('CRUD country', async () => {
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

  it(`should go to 'International>Locations' page`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.locationsLink,
    );

    await zonesPage.closeSfToolBar(page);

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to countries page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);
    const pageTitle = await countriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCountries).to.be.above(0);
  });

  /*describe('Create Language', async () => {
    it('should go to add new language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewLanguages', baseContext);

      await languagesPage.goToAddNewLanguage(page);
      const pageTitle = await addLanguagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addLanguagePage.pageTitle);
    });

    it('should create new language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewLanguages', baseContext);

      const textResult = await addLanguagePage.createEditLanguage(page, createLanguageData);
      await expect(textResult).to.to.contains(languagesPage.successfulCreationMessage);

      const numberOfLanguagesAfterCreation = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1);
    });

    it(`should go to FO and check that '${createLanguageData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedLanguageFO', baseContext);

      // View my shop and init pages
      page = await languagesPage.viewMyShop(page);

      const isLanguageInFO = await foHomePage.languageExists(page, createLanguageData.isoCode);
      await expect(isLanguageInFO, `${createLanguageData.name} was not found as a language in FO`).to.be.true;

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);
    });
  });

  describe('Update Language', async () => {
    it(`should filter language by name '${createLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', createLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(createLanguageData.name);
    });

    it('should go to edit language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditLanguagePage', baseContext);

      await languagesPage.goToEditLanguage(page, 1);
      const pageTitle = await addLanguagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addLanguagePage.pageEditTitle);
    });

    it('should edit language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editLanguage', baseContext);

      const textResult = await addLanguagePage.createEditLanguage(page, editLanguageData);
      await expect(textResult).to.to.contains(languagesPage.successfulUpdateMessage);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 1);
    });

    it(`should go to FO and check that '${editLanguageData.name}' do not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedLanguageFO', baseContext);

      // View my shop and init pages
      page = await languagesPage.viewMyShop(page);

      // Check languages if FO
      const isLanguageInFO = await foHomePage.languageExists(page, editLanguageData.isoCode);
      await expect(isLanguageInFO, `${editLanguageData.name} was found as a language in FO`).to.be.false;

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);
    });
  });

  describe('Delete Language', async () => {
    it(`should filter language by name '${editLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', editLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(editLanguageData.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await languagesPage.deleteLanguage(page, 1);
      await expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages);
    });
  });*/
});
