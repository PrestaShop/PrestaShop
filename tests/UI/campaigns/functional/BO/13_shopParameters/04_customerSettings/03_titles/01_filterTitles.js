require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingPage = require('@pages/BO/shopParameters/customerSettings');
const titlesPage = require('@pages/BO/shopParameters/customerSettings/titles');

// Import data
const {Titles} = require('@data/demo/titles');

const baseContext = 'functional_BO_shopParameters_customerSettings_titles_filterTitles';

// Browser and tab
let browserContext;
let page;

let numberOfTitles = 0;

describe('BO _ Shop Parameters - Customer Settings : Filter titles by id, name and gender', async () => {
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

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );

    await customerSettingPage.closeSfToolBar(page);

    const pageTitle = await customerSettingPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customerSettingPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await customerSettingPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(titlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await titlesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTitles).to.be.above(0);
  });

  describe('Filter titles', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_gender', filterValue: Titles.Mrs.id,
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'b!name', filterValue: Titles.Mrs.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterGender', filterType: 'select', filterBy: 'a!type', filterValue: Titles.Mrs.gender,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await titlesPage.filterTitles(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfTitlesAfterFilter = await titlesPage.getNumberOfElementInGrid(page);
        await expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

        const textColumn = await titlesPage.getTextColumn(page, 1, test.args.filterBy);
        await expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfTitlesAfterReset).to.equal(numberOfTitles);
      });
    });
  });
});
