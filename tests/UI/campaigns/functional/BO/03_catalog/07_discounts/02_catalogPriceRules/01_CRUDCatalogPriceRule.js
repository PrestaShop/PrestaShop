require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const catalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const addCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');

// Import data
const PriceRuleFaker = require('@data/faker/catalogPriceRule');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCatalogPriceRules';

let browserContext;
let page;

const newCatalogPriceRuleData = new PriceRuleFaker(
  {
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 20,
  },
);


const editCatalogPriceRuleData = new PriceRuleFaker(
  {
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 15,
  },
);

describe('CRUD catalog price rules', async () => {
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

  // 1 - Create catalog price rule
  describe('Create catalog price rule', async () => {
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
      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should create new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);
      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);

      const validationMessage = await addCatalogPriceRulePage.createEditCatalogPriceRule(page, newCatalogPriceRuleData);
      await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
    });
  });

  // 2 - Check catalog price rule in FO
  describe('Check catalog price rule in FO', async () => {
    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCatalogPriceRule', baseContext);

      page = await addCatalogPriceRulePage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');
      // Go to first product page
      await homePage.goToProductPage(page, 1);

      // Check discount value

      let columnValue = await productPage.getDiscountAmount(page);
      await expect(columnValue).to.not.equal('Save €20.00');

      // Set quantity of the product
      await productPage.setQuantity(page, 3);

      // Check discount value
      columnValue = await productPage.getDiscountAmount(page);
      await expect(columnValue).to.equal('Save €20.00');
    });
  });

  // 3 - Update catalog price rule
  /*describe('Update catalog price rule', async () => {
    it('should update the created catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);
      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);

      const validationMessage = await addCatalogPriceRulePage.createEditCatalogPriceRule(page, newCatalogPriceRuleData);
      await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
    });
  });*/

  // 5 - Delete catalog price rule
  /*it('should delete catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

    const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, newCatalogPriceRuleData.name);
    await expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
  });*/
});
