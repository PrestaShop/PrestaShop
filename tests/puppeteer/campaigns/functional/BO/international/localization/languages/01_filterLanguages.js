require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalizationPage = require('@pages/BO/international/localization');
const LanguagesPage = require('@pages/BO/international/languages');
// Importing data
const {Languages} = require('@data/demo/languages');

let browser;
let page;
let numberOfLanguages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    languagesPage: new LanguagesPage(page),
  };
};

describe('Filter Languages', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to contact page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await this.pageObjects.localizationPage.goToSubTabLanguages();
    const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    numberOfLanguages = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
    await expect(numberOfLanguages).to.be.above(0);
  });

  // 1 : Filter languages with all inputs and selects in grid table
  describe('Filter languages', async () => {
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_lang', filterValue: Languages.english.id}},
      {args: {filterType: 'input', filterBy: 'name', filterValue: Languages.english.name}},
      {args: {filterType: 'input', filterBy: 'iso_code', filterValue: Languages.english.isoCode}},
      {args: {filterType: 'input', filterBy: 'language_code', filterValue: Languages.english.languageCode}},
      {args: {filterType: 'input', filterBy: 'date_format_lite', filterValue: Languages.english.dateFormat}},
      {args: {filterType: 'input', filterBy: 'date_format_full', filterValue: Languages.english.fullDateFormat}},
      {args: {filterType: 'select', filterBy: 'active', filterValue: Languages.english.enabled}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.languagesPage.filterTable(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
        await expect(numberOfLanguagesAfterFilter).to.be.at.most(numberOfLanguages);
        await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);
        for (let i = 1; i <= numberOfLanguagesAfterFilter; i++) {
          const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(
            i,
            test.args.filterBy,
          );
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
        await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
      });
    });
  });
});
