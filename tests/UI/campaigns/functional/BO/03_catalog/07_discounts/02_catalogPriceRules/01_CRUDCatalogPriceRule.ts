// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import catalogPriceRulesPage from '@pages/BO/catalog/discounts/catalogPriceRules';
import addCatalogPriceRulePage from '@pages/BO/catalog/discounts/catalogPriceRules/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';

// Import data
import Products from '@data/demo/products';
import CatalogPriceRuleData from '@data/faker/catalogPriceRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDCatalogPriceRule';

/*
Create new catalog price rules
Check the rule in FO
Update catalog price rules
Check the updated rule in FO
Delete catalog price rules
*/
describe('BO - Catalog - Discounts : CRUD catalog price rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCatalogPriceRuleData: CatalogPriceRuleData = new CatalogPriceRuleData({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 20,
  });
  const editCatalogPriceRuleData: CatalogPriceRuleData = new CatalogPriceRuleData({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 4,
    reduction: 15,
  });
  const productPrice: number = Products.demo_1.finalPrice;
  const defaultDiscount: string = 'Save 20%';
  const priceAfterNewDiscount: number = 8.68;
  const discountAmountForNewDiscount: string = 'Save €20.00';
  const priceAfterUpdatedDiscount: number = 13.68;
  const discountAmountForUpdatedDiscount: string = 'Save €15.00';

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
      expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to \'Catalog Price Rules\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

      await cartRulesPage.goToCatalogPriceRulesTab(page);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should create new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, newCatalogPriceRuleData);
      expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
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
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_1', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount_1', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await productPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(newCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await productPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal(`€${newCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await productPage.getDiscountPercentage(page);
      expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await productPage.setQuantity(page, newCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await productPage.getDiscountAmount(page);
      expect(columnValue).to.equal(discountAmountForNewDiscount);

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
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should update the created catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToEditCatalogPriceRulePage(page, newCatalogPriceRuleData.name);

      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCatalogPriceRulePage.editPageTitle);

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, editCatalogPriceRuleData);
      expect(validationMessage).to.contains(catalogPriceRulesPage.successfulUpdateMessage);
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
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_2', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCatalogPriceRule', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await productPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(editCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await productPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal(`€${editCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await productPage.getDiscountPercentage(page);
      expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await productPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await productPage.setQuantity(page, editCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await productPage.getDiscountAmount(page);
      expect(columnValue).to.equal(discountAmountForUpdatedDiscount);

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
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, editCatalogPriceRuleData.name);
      expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
