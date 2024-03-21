// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';

import {
  // Import data
  dataTaxes,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_taxes_taxes_filterAndQuickEditTaxes';

/*
Filter taxes by : id, name, rate, status
Quick edit taxes
 */
describe('BO - International - Taxes : Filter And Quick Edit', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxes: number = 0;

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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );

    const pageTitle = await taxesPage.getPageTitle(page);
    expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  it('should reset all filters and get Number of Taxes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await taxesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTaxes).to.be.above(0);
  });

  // 1 : Filter Taxes
  describe('Filter Taxes', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_tax', filterValue: dataTaxes.DefaultFrTax.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: dataTaxes.DefaultFrTax.name,
        },
      },
      {
        args:
          {
            testIdentifier: 'filterRate',
            filterType: 'input',
            filterBy: 'rate',
            filterValue: dataTaxes.DefaultFrTax.rate,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataTaxes.DefaultFrTax.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        // Filter and check number of element
        await taxesPage.filterTaxes(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfTaxesAfterFilter = await taxesPage.getNumberOfElementInGrid(page);
        expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);

        // Check value in table
        for (let i = 1; i <= numberOfTaxesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const taxStatus = await taxesPage.getStatus(page, i);
            expect(taxStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await taxesPage.getTextColumnFromTableTaxes(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
        expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
      });
    });
  });

  // 2 : Edit taxes in list
  describe('Quick Edit Taxes', async () => {
    it('should filter by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

      await taxesPage.filterTaxes(page, 'input', 'name', dataTaxes.DefaultFrTax.name);

      const numberOfTaxesAfterFilter = await taxesPage.getNumberOfElementInGrid(page);
      expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);

      const textColumn = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textColumn).to.contains(dataTaxes.DefaultFrTax.name);
    });

    [
      {args: {action: 'disable', column: 'active', enabledValue: false}},
      {args: {action: 'enable', column: 'active', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first tax`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Tax`, baseContext);

        const isActionPerformed = await taxesPage.setStatus(
          page,
          1,
          test.args.enabledValue,
        );

        if (isActionPerformed) {
          const resultMessage = await taxesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(taxesPage.successfulUpdateStatusMessage);
        }

        const taxStatus = await taxesPage.getStatus(page, 1);
        expect(taxStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });
  });
});
