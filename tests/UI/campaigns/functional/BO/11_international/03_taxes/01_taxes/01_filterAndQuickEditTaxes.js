require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');

// Import data
const {DefaultFrTax} = require('@data/demo/tax');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_taxes_taxes_filterAndQuickEditTaxes';

let browserContext;
let page;
let numberOfTaxes = 0;

/*
Filter taxes by : id, name, rate, status
Quick edit taxes
 */
describe('BO - International - Taxes : Filter And Quick Edit', async () => {
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
    await expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  it('should reset all filters and get Number of Taxes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await taxesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTaxes).to.be.above(0);
  });

  // 1 : Filter Taxes
  describe('Filter Taxes', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_tax', filterValue: DefaultFrTax.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: DefaultFrTax.name,
        },
      },
      {
        args:
          {
            testIdentifier: 'filterRate',
            filterType: 'input',
            filterBy: 'rate',
            filterValue: DefaultFrTax.rate.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: DefaultFrTax.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        // Filter and check number of element
        await taxesPage.filterTaxes(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfTaxesAfterFilter = await taxesPage.getNumberOfElementInGrid(page);
        await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);

        // Check value in table
        for (let i = 1; i <= numberOfTaxesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const taxStatus = await taxesPage.getStatus(page, i);
            await expect(taxStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await taxesPage.getTextColumnFromTableTaxes(page, i, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
      });
    });
  });

  // 2 : Edit taxes in list
  describe('Quick Edit Taxes', async () => {
    it('should filter by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

      await taxesPage.filterTaxes(page, 'input', 'name', DefaultFrTax.name);

      const numberOfTaxesAfterFilter = await taxesPage.getNumberOfElementInGrid(page);
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);

      const textColumn = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      await expect(textColumn).to.contains(DefaultFrTax.name);
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
          await expect(resultMessage).to.contains(taxesPage.successfulUpdateStatusMessage);
        }

        const taxStatus = await taxesPage.getStatus(page, 1);
        await expect(taxStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });
  });
});
