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
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_sortLanguages';

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

describe('Sort Languages', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to localization page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);
    await this.pageObjects.localizationPage.goToSubTabLanguages();
    const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfLanguages = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
    await expect(numberOfLanguages).to.be.above(0);
  });

  const tests = [
    {
      args: {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_lang', sortDirection: 'desc', isFloat: true,
      },
    },
    {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByIsoCodeAsc', sortBy: 'iso_code', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByIsoCodeDesc', sortBy: 'iso_code', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByLanguageCodeAsc', sortBy: 'language_code', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByLanguageCodeDesc', sortBy: 'language_code', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByDateFormatLiteAsc', sortBy: 'date_format_lite', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByDateFormatLiteDesc', sortBy: 'date_format_lite', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByDateFormatFullAsc', sortBy: 'date_format_full', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByDateFormatFullDesc', sortBy: 'date_format_full', sortDirection: 'desc'}},
    {
      args: {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_lang', sortDirection: 'asc', isFloat: true,
      },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
      let nonSortedTable = await this.pageObjects.languagesPage.getAllRowsColumnContent(test.args.sortBy);
      await this.pageObjects.languagesPage.sortTable(test.args.sortBy, test.args.sortDirection);
      let sortedTable = await this.pageObjects.languagesPage.getAllRowsColumnContent(test.args.sortBy);
      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }
      const expectedResult = await this.pageObjects.languagesPage.sortArray(nonSortedTable, test.args.isFloat);
      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
