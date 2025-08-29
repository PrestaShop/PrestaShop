import testContext from '@utils/testContext';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabPricingPage,
  boShopParametersPage,
  type BrowserContext,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_general_general_roundMode';

/*
Create product
Select round mode
Check the selected round mode when last digit of product price (= 5 , >5 , <5)
Delete product
 */
describe('BO - Shop Parameters - General : Round mode', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({
    name: 'Product round mode',
    type: 'standard',
    taxRule: 'No tax',
    price: 17.115,
    quantity: 10,
  });

  // Pre-condition : Create product
  createProductTest(productData, `${baseContext}_preTest`);

  // before and after functions
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

  let tests = [
    {args: {roundMode: 'Round up away from zero, when it is half way there (recommended)', price: '€17.12'}},
    {args: {roundMode: 'Round down towards zero, when it is half way there', price: '€17.11'}},
    {args: {roundMode: 'Round towards the next even value', price: '€17.12'}},
    {args: {roundMode: 'Round towards the next odd value', price: '€17.11'}},
    {args: {roundMode: 'Round up to the nearest value', price: '€17.12'}},
    {args: {roundMode: 'Round down to the nearest value', price: '€17.11'}},
  ];
  describe('Test round mode when the last digit of the price = 5', async () => {
    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.roundMode}'`, async () => {
        it('should go to \'Shop parameters > General\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.shopParametersGeneralLink,
          );
          await boShopParametersPage.closeSfToolBar(page);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode${index}`, baseContext);

          const result = await boShopParametersPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          // View shop
          page = await boShopParametersPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct${index}`, baseContext);

          await foClassicHomePage.searchProduct(page, productData.name);

          const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

          await foClassicSearchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice${index}`, baseContext);

          const productPrice = await foClassicProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });
      });
    });
  });

  tests = [
    {args: {roundMode: 'Round up away from zero, when it is half way there (recommended)', price: '€17.11'}},
    {args: {roundMode: 'Round down towards zero, when it is half way there', price: '€17.11'}},
    {args: {roundMode: 'Round towards the next even value', price: '€17.11'}},
    {args: {roundMode: 'Round towards the next odd value', price: '€17.11'}},
    {args: {roundMode: 'Round up to the nearest value', price: '€17.12'}},
    {args: {roundMode: 'Round down to the nearest value', price: '€17.11'}},
  ];
  describe('Test round mode when the last digit of the price < 5', async () => {
    describe('Update product price', async () => {
      // Data to edit the product price
      const pricingData: FakerProduct = new FakerProduct({
        price: 17.114,
      });

      it('should go to products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should go to the created product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should edit the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice1', baseContext);

        await boProductsCreateTabPricingPage.setProductPricing(page, pricingData);

        const updateProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });
    });

    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.roundMode}'`, async () => {
        it('should go to \'Shop parameters > General\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage2${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.shopParametersGeneralLink,
          );
          await boShopParametersPage.closeSfToolBar(page);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode2${index}`, baseContext);

          const result = await boShopParametersPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop2${index}`, baseContext);

          // View shop
          page = await boShopParametersPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct2${index}`, baseContext);

          await foClassicHomePage.searchProduct(page, productData.name);

          const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage2${index}`, baseContext);

          await foClassicSearchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice2${index}`, baseContext);

          const productPrice = await foClassicProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo2${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });
      });
    });
  });

  tests = [
    {args: {roundMode: 'Round up away from zero, when it is half way there (recommended)', price: '€17.12'}},
    {args: {roundMode: 'Round down towards zero, when it is half way there', price: '€17.12'}},
    {args: {roundMode: 'Round towards the next even value', price: '€17.12'}},
    {args: {roundMode: 'Round towards the next odd value', price: '€17.12'}},
    {args: {roundMode: 'Round up to the nearest value', price: '€17.12'}},
    {args: {roundMode: 'Round down to the nearest value', price: '€17.11'}},
  ];
  describe('Test round mode when the last digit of the price > 5', async () => {
    describe('Update product price', async () => {
      // Data to edit the product price
      const pricingData: FakerProduct = new FakerProduct({
        price: 17.116,
      });

      it('should go to products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should go to the created product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should edit the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice2', baseContext);

        await boProductsCreateTabPricingPage.setProductPricing(page, pricingData);

        const updateProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });
    });

    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.roundMode}'`, async () => {
        it('should go to \'Shop parameters > General\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage3${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.shopParametersGeneralLink,
          );
          await boShopParametersPage.closeSfToolBar(page);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode3${index}`, baseContext);

          const result = await boShopParametersPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop3${index}`, baseContext);

          // View shop
          page = await boShopParametersPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct3${index}`, baseContext);

          await foClassicHomePage.searchProduct(page, productData.name);

          const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage3${index}`, baseContext);

          await foClassicSearchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice3${index}`, baseContext);

          const productPrice = await foClassicProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo3${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boShopParametersPage.getPageTitle(page);
          expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
        });
      });
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productData, `${baseContext}_postTest`);
});
