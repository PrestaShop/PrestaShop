require('module-alias/register');

// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const catalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const addCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');

// Import data
const PriceRuleFaker = require('@data/faker/catalogPriceRule');
const {Products} = require('@data/demo/products');

const baseContext = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDCatalogPriceRule';

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
    fromQuantity: 4,
    reduction: 15,
  },
);
const productPrice = Products.demo_1.finalPrice;
const defaultDiscount = 'Save 20%';
const priceAfterNewDiscount = 8.68;
const discountAmountForNewDiscount = 'Save €20.00';
const priceAfterUpdatedDiscount = 13.68;
const discountAmountForUpdatedDiscount = 'Save €15.00';

/*
Create new catalog price rules
Check the rule in FO
Update catalog price rules
Check the updated rule in FO
Delete catalog price rules
*/
describe('BO - Catalog - Discounts : CRUD catalog price rules', async () => {
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

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, newCatalogPriceRuleData);
      await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
    });
  });

  // 2 - Check catalog price rule in FO
  describe('Check catalog price rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await addCatalogPriceRulePage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');
      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_1', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount_1', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await productPage.getQuantityDiscountValue(page);
      await expect(quantityDiscountValue).to.equal(newCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await productPage.getDiscountValue(page);
      await expect(unitDiscountValue).to.equal(`€${newCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await productPage.getDiscountPercentage(page);
      await expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await productPage.setQuantity(page, newCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await productPage.getDiscountAmount(page);
      await expect(columnValue).to.equal(discountAmountForNewDiscount);

      // Check final price
      finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(priceAfterNewDiscount);
    });
  });

  // 3 - Update catalog price rule
  describe('Update catalog price rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate', baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should update the created catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToEditCatalogPriceRulePage(page, newCatalogPriceRuleData);
      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCatalogPriceRulePage.editPageTitle);

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, editCatalogPriceRuleData);
      await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulUpdateMessage);
    });
  });

  // 4 - Check updated catalog price rule in FO
  describe('Check updated catalog price rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View my shop and init pages
      page = await addCatalogPriceRulePage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');
      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_2', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCatalogPriceRule', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await productPage.getQuantityDiscountValue(page);
      await expect(quantityDiscountValue).to.equal(editCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await productPage.getDiscountValue(page);
      await expect(unitDiscountValue).to.equal(`€${editCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await productPage.getDiscountPercentage(page);
      await expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await productPage.setQuantity(page, editCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await productPage.getDiscountAmount(page);
      await expect(columnValue).to.equal(discountAmountForUpdatedDiscount);

      // Check final price
      finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(priceAfterUpdatedDiscount);
    });
  });

  // 5 - Delete catalog price rule
  describe('Delete catalog price rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToDelete', baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, editCatalogPriceRuleData.name);
      await expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
