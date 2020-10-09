require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const catalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const addCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_catalogPriceRules_filterAndBulkActions';

// Import expect from chai
const {expect} = require('chai');

// Import data
const PriceRuleFaker = require('@data/faker/catalogPriceRule');

const firstPriceRule = new PriceRuleFaker(
  {
    name: 'toDelete1',
  },
);

const secondPriceRule = new PriceRuleFaker(
  {
    name: 'toDelete2',
  },
);

// Browser and tab
let browserContext;
let page;

let numberOfCatalogPriceRules = 0;

/*
Create 2 catalog price rules
Filter catalog price rules by id, priority, code, quantity, status
Quick edit first cart rule in list
Enable, disable and delete cart rules by bulk actions
 */
describe('Filter, quick edit and bulk actions catalog price rules', async () => {
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

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

    await cartRulesPage.goToCatalogPriceRulesTab(page);

    numberOfCatalogPriceRules = await catalogPriceRulesPage.resetAndGetNumberOfLines(page);

    const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
  });

  // 1 - Create 2 catalog price rules
  describe('Create 2 catalog price rules', async () => {
    [firstPriceRule, secondPriceRule]
      .forEach((catalogPriceRuleToCreate, index) => {
        it('should go to new catalog price rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewCatalogPriceRule${index}`, baseContext);

          await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

          const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);
        });

        it('should create new catalog price rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCatalogPriceRule${index}`, baseContext);

          const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, catalogPriceRuleToCreate);
          await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);

          const numberOfCatalogPriceRulesAfterCreation = await catalogPriceRulesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCatalogPriceRulesAfterCreation).to.be.at.most(numberOfCatalogPriceRules + index + 1);
        });
      });
  });

  // 2 - Filter catalog price rules table
  describe('Filter catalog price rules', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterID', filterType: 'input', filterBy: 'id_specific_price_rule', filterValue: 1,
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'a!name', filterValue: firstPriceRule.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterShop', filterType: 'input', filterBy: 's!name', filterValue: global.INSTALL.SHOPNAME,
        },
      },
      {
        args: {
          testIdentifier: 'filterCurrency',
          filterType: 'input',
          filterBy: 'cul!name',
          filterValue: secondPriceRule.currency,
        },
      },
      {
        args: {
          testIdentifier: 'filterCountry',
          filterType: 'input',
          filterBy: 'cl!name',
          filterValue: secondPriceRule.country,
        },
      },
      {
        args: {
          testIdentifier: 'filterGroup', filterType: 'input', filterBy: 'gl!name', filterValue: firstPriceRule.group,
        },
      },
      {
        args: {
          testIdentifier: 'filterFromQuantity',
          filterType: 'input',
          filterBy: 'from_quantity',
          filterValue: firstPriceRule.fromQuantity,
        },
      },
      {
        args: {
          testIdentifier: 'filterReductionType',
          filterType: 'select',
          filterBy: 'a!reduction_type',
          filterValue: secondPriceRule.reductionType,
        },
      },
      {
        args: {
          testIdentifier: 'filterReduction',
          filterType: 'input',
          filterBy: 'reduction',
          filterValue: firstPriceRule.reduction,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await catalogPriceRulesPage.filterPriceRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfPriceRulesAfterFilter = await catalogPriceRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfPriceRulesAfterFilter).to.be.at.most(numberOfCatalogPriceRules + 2);

        for (let row = 1; row <= numberOfPriceRulesAfterFilter; row++) {
          const textColumn = await catalogPriceRulesPage.getTextColumn(
            page,
            row,
            test.args.filterBy,
          );

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfPriceRulesAfterReset = await catalogPriceRulesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfPriceRulesAfterReset).to.equal(numberOfCatalogPriceRules + 2);
      });
    });
  });

  // 3 - Delete catalog price rules with bulk actions
  describe('Bulk delete catalog price rules', async () => {
    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeletePriceRules', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.bulkDeletePriceRules(page);
      await expect(deleteTextResult).to.be.contains(catalogPriceRulesPage.successfulMultiDeleteMessage);
    });
  });
});
