require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const catalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const addCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');

// Import data
const PriceRuleFaker = require('@data/faker/catalogPriceRule');

const baseContext = 'functional_BO_shopParameters_productSettings_displayDiscountedPrice';

let browserContext;
let page;

const priceRuleData = new PriceRuleFaker(
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
// Unit discount in Volume discounts table(Product page FO)
const unitDiscountToCheck = '€20.00';
// Unit price in Volume discounts table(Product page FO)
const unitPriceToCheck = '€8.68';

describe('BO - Shop Parameters - Product Settings : Enable/Disable display discounted price', async () => {
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
    const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
  });

  it('should create new catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

    await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);
    const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
    await expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);

    const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, priceRuleData);
    await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await addCatalogPriceRulePage.goToSubMenu(
      page,
      addCatalogPriceRulePage.shopParametersParentLink,
      addCatalogPriceRulePage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {
      args: {
        action: 'disable', enable: false, textColumnToCheck: 'Unit discount', valueToCheck: unitDiscountToCheck,
      },
    },
    {
      args: {
        action: 'enable', enable: true, textColumnToCheck: 'Unit price', valueToCheck: unitPriceToCheck,
      },
    },
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} display discounted price`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DisplayDiscountedPrice`,
        baseContext,
      );

      const result = await productSettingsPage.setDisplayDiscountedPriceStatus(
        page,
        test.args.enable,
      );

      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop and go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page was not opened').to.be.true;

      await homePage.goToProductPage(page, 1);
    });

    it('should check the existence of the unit value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkUnitValue${index}`, baseContext);

      const columnTitle = await productPage.getDiscountColumnTitle(page);
      await expect(columnTitle).to.equal(test.args.textColumnToCheck);

      const columnValue = await productPage.getDiscountValue(page);
      await expect(columnValue).to.equal(test.args.valueToCheck);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPageToDeletePriceRule', baseContext);

    await productSettingsPage.goToSubMenu(
      page,
      productSettingsPage.catalogParentLink,
      productSettingsPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRuleTabToDeletePriceRule', baseContext);

    await cartRulesPage.goToCatalogPriceRulesTab(page);
    const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
  });

  it('should delete catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

    const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, priceRuleData.name);
    await expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
  });
});
