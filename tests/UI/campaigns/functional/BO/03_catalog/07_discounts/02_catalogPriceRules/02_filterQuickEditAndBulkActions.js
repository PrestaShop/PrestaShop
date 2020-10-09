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

const baseContext = 'functional_BO_catalog_discounts_catalogPriceRules_filterQuickEditAndBulkActions';

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
});
