// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import titlesPage from '@pages/BO/shopParameters/customerSettings/titles';

// Import data
import Titles from '@data/demo/titles';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_filterTitles';

describe('BO _ Shop Parameters - Customer Settings : Filter titles by id, name and gender', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

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
    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await customerSettingsPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(titlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await titlesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTitles).to.be.above(0);
  });

  describe('Filter titles', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_gender', filterValue: Titles.Mrs.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: Titles.Mrs.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterGender', filterType: 'select', filterBy: 'type', filterValue: Titles.Mrs.gender,
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
        expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

        const textColumn = await titlesPage.getTextColumn(page, 1, test.args.filterBy);
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
        expect(numberOfTitlesAfterReset).to.equal(numberOfTitles);
      });
    });
  });
});
