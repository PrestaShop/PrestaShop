// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import {foProductPage} from '@pages/FO/classic/product';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const productData: ProductData = new ProductData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
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

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.shopParametersGeneralLink,
          );
          await generalPage.closeSfToolBar(page);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode${index}`, baseContext);

          const result = await generalPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(generalPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          // View shop
          page = await generalPage.viewMyShop(page);
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct${index}`, baseContext);

          await homePage.searchProduct(page, productData.name);

          const pageTitle = await searchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(searchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

          await searchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice${index}`, baseContext);

          const productPrice = await foProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
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
      const pricingData: ProductData = new ProductData({
        price: 17.114,
      });

      it('should go to products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should go to the created product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should edit the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice1', baseContext);

        await pricingTab.setProductPricing(page, pricingData);

        const updateProductMessage = await createProductsPage.saveProduct(page);
        expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });
    });

    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.roundMode}'`, async () => {
        it('should go to \'Shop parameters > General\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage2${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.shopParametersGeneralLink,
          );
          await generalPage.closeSfToolBar(page);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode2${index}`, baseContext);

          const result = await generalPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(generalPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop2${index}`, baseContext);

          // View shop
          page = await generalPage.viewMyShop(page);
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct2${index}`, baseContext);

          await homePage.searchProduct(page, productData.name);

          const pageTitle = await searchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(searchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage2${index}`, baseContext);

          await searchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice2${index}`, baseContext);

          const productPrice = await foProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo2${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
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
      const pricingData: ProductData = new ProductData({
        price: 17.116,
      });

      it('should go to products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should go to the created product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should edit the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice2', baseContext);

        await pricingTab.setProductPricing(page, pricingData);

        const updateProductMessage = await createProductsPage.saveProduct(page);
        expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });
    });

    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.roundMode}'`, async () => {
        it('should go to \'Shop parameters > General\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage3${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.shopParametersGeneralLink,
          );
          await generalPage.closeSfToolBar(page);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
        });

        it(`should select the round mode '${test.args.roundMode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `selectRoundMode3${index}`, baseContext);

          const result = await generalPage.selectRoundMode(page, test.args.roundMode);
          expect(result).to.contains(generalPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop3${index}`, baseContext);

          // View shop
          page = await generalPage.viewMyShop(page);
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should search for the created product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProduct3${index}`, baseContext);

          await homePage.searchProduct(page, productData.name);

          const pageTitle = await searchResultsPage.getPageTitle(page);
          expect(pageTitle).to.equal(searchResultsPage.pageTitle);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage3${index}`, baseContext);

          await searchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foProductPage.getPageTitle(page);
          expect(pageTitle).to.contains(productData.name);
        });

        it('should check the product price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductPrice3${index}`, baseContext);

          const productPrice = await foProductPage.getProductPrice(page);
          expect(productPrice).to.equal(test.args.price);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo3${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await generalPage.getPageTitle(page);
          expect(pageTitle).to.contains(generalPage.pageTitle);
        });
      });
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productData, `${baseContext}_postTest`);
});
