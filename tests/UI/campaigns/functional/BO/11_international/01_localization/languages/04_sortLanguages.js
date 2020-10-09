require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');

// Import pages
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_sortLanguages';


let browserContext;
let page;
let numberOfLanguages = 0;

describe('Sort Languages', async () => {
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

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.localizationLink,
    );

    await localizationPage.closeSfToolBar(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    await expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await localizationPage.goToSubTabLanguages(page);
    const pageTitle = await languagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
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

      // Get non sorted elements
      let nonSortedTable = await languagesPage.getAllRowsColumnContent(page, test.args.sortBy);
      await languagesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      // Get sorted elements
      let sortedTable = await languagesPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }

      // Sort non sorted array
      const expectedResult = await languagesPage.sortArray(nonSortedTable, test.args.isFloat);

      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
