import testContext from '@utils/testContext';
import {expect} from 'chai';
import {createCurrencyTest, deleteCurrencyTest} from '@commonTests/BO/international/currency';

import {
  boCartRulesPage,
  boCatalogPriceRulesCreatePage,
  boCatalogPriceRulesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCountries,
  dataCurrencies,
  dataCustomers,
  dataGroups,
  dataProducts,
  FakerCatalogPriceRule,
  foClassicCartPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDCatalogPriceRule';

/*
 * Create new catalog price rules
 * Check the rule in FO
 * Update catalog price rules
 * Check the updated rule in FO
 * Delete catalog price rules
 */
describe('BO - Catalog - Discounts : CRUD catalog price rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const catalogPriceRuleData0: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 10,
  });
  const catalogPriceRuleData1: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    fromQuantity: 5,
    reduction: 20,
  });
  const catalogPriceRuleData2: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: dataGroups.customer.name,
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    fromQuantity: 5,
    reduction: 20,
  });
  const catalogPriceRuleData3: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'Ethiopia',
    group: dataGroups.customer.name,
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    fromQuantity: 5,
    reduction: 20,
  });
  const catalogPriceRuleData4: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: dataCountries.france.name,
    group: dataGroups.customer.name,
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    fromQuantity: 5,
    reduction: 20,
  });
  const catalogPriceRuleData5: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: dataCurrencies.mad.name,
    country: dataCountries.france.name,
    group: dataGroups.customer.name,
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    fromQuantity: 5,
    reduction: 20,
  });

  // Pre-condition: Create currency
  createCurrencyTest(dataCurrencies.mad, `${baseContext}_preTest_2`);

  describe('CRUD catalog price rules', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    // 1 - Amount : Create catalog price rule
    describe('Amount : Create catalog price rule', async () => {
      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.discountsLink,
        );

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should go to \'Catalog Price Rules\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

        await boCartRulesPage.goToCatalogPriceRulesTab(page);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should create new catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

        await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData0);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
      });
    });

    // 2 - Amount : Check catalog price rule in FO
    describe('Amount : Check catalog price rule in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

        // View my shop and init pages
        page = await boCatalogPriceRulesCreatePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_1', baseContext);

        await foClassicHomePage.goToProductPage(page, 6);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(dataProducts.demo_11.name);
      });

      it('should check the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount_1', baseContext);

        // Check discount percentage
        let columnValue = await foClassicProductPage.getDiscountAmount(page);
        expect(columnValue).to.equal(`Save €${catalogPriceRuleData0.reduction.toFixed(2)}`);

        // Check final price
        let finalPrice = await foClassicProductPage.getProductInformation(page);
        expect(finalPrice.price.toString()).to.equal(
          (
            dataProducts.demo_11.finalPrice - catalogPriceRuleData0.reduction
          ).toFixed(2),
        );

        // Set quantity of the product
        await foClassicProductPage.setQuantity(page, catalogPriceRuleData0.fromQuantity);

        // Check discount value
        columnValue = await foClassicProductPage.getDiscountAmount(page);
        expect(columnValue).to.equal(`Save €${catalogPriceRuleData0.reduction.toFixed(2)}`);

        // Check final price
        finalPrice = await foClassicProductPage.getProductInformation(page);
        expect(finalPrice.price.toString()).to.equal(
          (
            dataProducts.demo_11.finalPrice - catalogPriceRuleData0.reduction
          ).toFixed(2),
        );
      });

      it('should add to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_1', baseContext);

        await foClassicProductPage.addProductToTheCart(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price.toString()).to.equal(
            (
              dataProducts.demo_11.finalPrice - catalogPriceRuleData0.reduction
            ).toFixed(2),
          ),
          expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData0.reduction.toFixed(2)}`),
        ]);
      });
    });

    // 3 - Percentage : Update catalog price rule
    describe('Percentage : Update catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate_0', baseContext);

        page = await foClassicProductPage.changePage(browserContext, 0);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should update the created catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule_0', baseContext);

        await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData1);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
      });
    });

    // 4 - Percentage : Check updated catalog price rule in FO
    describe('Percentage : Check updated catalog price rule in FO', async () => {
      it('should return to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

        page = await boCatalogPriceRulesPage.changePage(browserContext, 1);
        await foClassicCartPage.reloadPage(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_2', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.discountAmount).to.equal(''),
        ]);
      });

      it('should change the quantity to 5', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityTo5', baseContext);

        const quantity = await foClassicCartPage.setProductQuantity(page, 1, 5);
        expect(quantity).to.eq(5);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price.toString()).to.equal(
            (
              dataProducts.demo_11.finalPrice - ((dataProducts.demo_11.finalPrice / 100) * catalogPriceRuleData1.reduction)
            ).toFixed(2),
          ),
          expect(productDetail.discountPercentage).to.equal(`-${catalogPriceRuleData1.reduction.toFixed(0)}%`),
        ]);
      });
    });

    // 5 - Customer Group : Update catalog price rule
    describe('Customer Group : Update catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate_1', baseContext);

        page = await foClassicProductPage.changePage(browserContext, 0);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should update the created catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule_1', baseContext);

        await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData2);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
      });
    });

    // 6 - Customer Group : Check updated catalog price rule in FO
    describe('Customer Group : Check updated catalog price rule in FO', async () => {
      it('should return to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_3', baseContext);

        page = await boCatalogPriceRulesPage.changePage(browserContext, 1);
        await foClassicCartPage.reloadPage(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_3', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.discountAmount).to.equal(''),
        ]);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

        await foClassicCartPage.goToLoginPage(page);

        const pageTitle = await foClassicLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'customerLogin', baseContext);

        await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);

        const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected).to.eq(true);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_4', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price.toString()).to.equal(
            (
              dataProducts.demo_11.finalPrice - ((dataProducts.demo_11.finalPrice / 100) * catalogPriceRuleData1.reduction)
            ).toFixed(2),
          ),
          expect(productDetail.discountPercentage).to.equal(`-${catalogPriceRuleData1.reduction.toFixed(0)}%`),
        ]);
      });
    });

    // 7 - Country (NO) : Update catalog price rule
    describe('Country (NO) : Update catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate_2', baseContext);

        page = await foClassicProductPage.changePage(browserContext, 0);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should update the created catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule_2', baseContext);

        await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData3);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
      });
    });

    // 8 - Country (NO) : Check updated catalog price rule in FO
    describe('Country (NO) : Check updated catalog price rule in FO', async () => {
      it('should return to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_4', baseContext);

        page = await boCatalogPriceRulesPage.changePage(browserContext, 1);
        await foClassicCartPage.reloadPage(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_5', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.discountAmount).to.equal(''),
        ]);
      });
    });

    // 9 - Country (YES) : Update catalog price rule
    describe('Country (YES) : Update catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate_3', baseContext);

        page = await foClassicProductPage.changePage(browserContext, 0);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should update the created catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule_3', baseContext);

        await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData4);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
      });
    });

    // 10 - Country (YES) : Check updated catalog price rule in FO
    describe('Country (YES) : Check updated catalog price rule in FO', async () => {
      it('should return to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_5', baseContext);

        page = await boCatalogPriceRulesPage.changePage(browserContext, 1);
        await foClassicCartPage.reloadPage(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_6', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price.toString()).to.equal(
            (
              dataProducts.demo_11.finalPrice - ((dataProducts.demo_11.finalPrice / 100) * catalogPriceRuleData1.reduction)
            ).toFixed(2),
          ),
          expect(productDetail.discountPercentage).to.equal(`-${catalogPriceRuleData1.reduction.toFixed(0)}%`),
        ]);
      });
    });

    // 11 - Currency : Update catalog price rule
    describe('Currency : Update catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate_4', baseContext);

        page = await foClassicProductPage.changePage(browserContext, 0);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should update the created catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule_4', baseContext);

        await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData5);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
      });
    });

    // 12 - Currency : Check updated catalog price rule in FO
    describe('Currency : Check updated catalog price rule in FO', async () => {
      it('should return to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_6', baseContext);

        page = await boCatalogPriceRulesPage.changePage(browserContext, 1);
        await foClassicCartPage.reloadPage(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should check to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCart_7', baseContext);

        const productDetail = await foClassicCartPage.getProductDetail(page, 1);
        await Promise.all([
          expect(productDetail.regularPrice).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.price).to.equal(dataProducts.demo_11.finalPrice),
          expect(productDetail.discountAmount).to.equal(''),
        ]);
      });
    });

    // 13 - Delete catalog price rule
    describe('Delete catalog price rule', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToDelete', baseContext);

        page = await foClassicProductPage.closePage(browserContext, page, 1);

        const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
      });

      it('should delete catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

        const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, catalogPriceRuleData1.name);
        expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
      });
    });
  });

  // Post-condition: Delete currency
  deleteCurrencyTest(dataCurrencies.mad, `${baseContext}_postTest_0`);
});
