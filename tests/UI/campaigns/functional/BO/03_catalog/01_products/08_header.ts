// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
import detailsTab from '@pages/BO/catalog/products/add/detailsTab';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';

// Import data
import ProductData from '@data/faker/product';
import {ProductHeaderSummary} from '@data/types/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

const baseContext: string = 'functional_BO_catalog_products_header';

describe('BO - Catalog - Products : Header', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let productHeaderSummaryInitial: ProductHeaderSummary;

  const newProductData: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'cover.jpg',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    retailPrice: 12,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const productNameEn: string = faker.commerce.productName();
  const productNameFr: string = faker.commerce.productName();
  const productRetailPriceTaxIncluded: number = 140;
  const productRetailPriceTaxExcluded: number = (140 / (100 + newProductData.tax)) * 100;
  const productQuantity = newProductData.quantity;
  const productCoverImage: string = 'productCoverImage.png';
  const productMPN: string = 'HSC0424PP';
  const productUPC: string = '987654321098';
  const productEAN13: string = '9782409038600';
  const productISBN: string = '978-2-409-03860-0';

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage(productCoverImage);
    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(productCoverImage);
    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await productsPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should click on \'New product\' button and check new product modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

    const isModalVisible = await productsPage.clickOnNewProductButton(page);
    expect(isModalVisible).to.eq(true);
  });

  it('should check the standard product description', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

    const productTypeDescription = await productsPage.getProductDescription(page);
    expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
  });

  it('should choose \'Standard product\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

    await productsPage.selectProductType(page, newProductData.type);

    const pageTitle = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);
  });

  it('should go to new product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

    await productsPage.clickOnAddNewProduct(page);

    const pageTitle = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);
  });

  it('should create standard product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

    await createProductsPage.closeSfToolBar(page);

    const createProductMessage = await createProductsPage.setProduct(page, newProductData);
    expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
  });

  it('should click on \'Go to catalog\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

    await createProductsPage.goToCatalogPage(page);

    const pageTitle = await productsPage.getPageTitle(page);
    expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset filter and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it(`should filter list by '${newProductData.name}' and check result`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterListByName', baseContext);

    await productsPage.filterProducts(page, 'product_name', newProductData.name, 'input');

    const numberOfProductsAfterFilter: number = await productsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.eq(newProductData.name);
  });

  it('should go to the first product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await productsPage.goToProductPage(page, 1);

    const pageTitle = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);

    productHeaderSummaryInitial = await createProductsPage.getProductHeaderSummary(page);
  });

  it('should edit the product name (in English)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProductNameEn', baseContext);

    await createProductsPage.setProductName(page, productNameEn, 'en');

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });

  it('should edit the product name (in French)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProductNameFr', baseContext);

    await createProductsPage.setProductName(page, productNameFr, 'fr');

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });

  it('should go to the Pricing tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPricingTab', baseContext);

    await createProductsPage.goToTab(page, 'pricing');

    const isTabActive = await createProductsPage.isTabActive(page, 'pricing');
    expect(isTabActive).to.eq(true);
  });

  it('should edit the retail price', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

    await pricingTab.setRetailPrice(page, false, productRetailPriceTaxIncluded);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);

    const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
    expect(productHeaderSummary.priceTaxExc).to.equals(`€${productRetailPriceTaxExcluded.toFixed(2)} tax excl.`);
    expect(productHeaderSummary.priceTaxIncl).to.equals(
      `€${productRetailPriceTaxIncluded.toFixed(2)} tax incl. (tax rule: ${newProductData.tax}%)`,
    );
  });

  it('should go to the Stocks tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksTab', baseContext);

    await createProductsPage.goToTab(page, 'stock');

    const isTabActive = await createProductsPage.isTabActive(page, 'stock');
    expect(isTabActive).to.eq(true);
  });

  it('should add items to stock', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addItemsToStock', baseContext);

    await stocksTab.setQuantityDelta(page, 10);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);

    const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
    expect(productHeaderSummary.quantity).to.equal(`${productQuantity + 10} in stock`);
  });

  it('should subtract items to stock', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'subtractItemsToStock', baseContext);

    await stocksTab.setQuantityDelta(page, -75);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);

    const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
    expect(productHeaderSummary.quantity).to.equal(`${productQuantity + 10 - 75} in stock`);
  });

  it('should go to the Description tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDescriptionTab', baseContext);

    await createProductsPage.goToTab(page, 'description');

    const isTabActive = await createProductsPage.isTabActive(page, 'description');
    expect(isTabActive).to.eq(true);
  });

  it('should add image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

    await descriptionTab.addProductImages(page, [productCoverImage]);

    const numOfImages = await descriptionTab.getNumberOfImages(page);
    expect(numOfImages).to.eq(2);
  });

  it('should set image information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

    const message = await descriptionTab.setProductImageInformation(page, 2, true, 'Caption EN', 'Caption FR');
    expect(message).to.be.eq(descriptionTab.settingUpdatedMessage);
  });

  it('should check image has changed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkImageHasChanged', baseContext);

    await descriptionTab.reloadPage(page);
    const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);

    expect(productHeaderSummary.imageUrl).to.be.not.eq(productHeaderSummaryInitial.imageUrl);
  });

  it('should change product type', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeProductType', baseContext);

    const message = await createProductsPage.changeProductType(page, 'virtual');
    expect(message).to.be.eq(createProductsPage.successfulUpdateMessage);
  });

  it('should check product type has changed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTypeHasChanged', baseContext);

    const productType = await createProductsPage.getProductType(page);
    expect(productType).to.be.eq('virtual');

    // FYI : stock is the ID for the "Virtual product" tab
    const isTabVisible = await createProductsPage.isTabVisible(page, 'stock');
    expect(isTabVisible).to.eq(true);
  });

  it('should go to the Details tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDetailsTab', baseContext);

    await createProductsPage.goToTab(page, 'details');

    const isTabActive = await createProductsPage.isTabActive(page, 'details');
    expect(isTabActive).to.eq(true);
  });

  it('should set references', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setReferences', baseContext);

    await detailsTab.setMPN(page, productMPN);
    await detailsTab.setUPC(page, productUPC);
    await detailsTab.setEAN13(page, productEAN13);
    await detailsTab.setISBN(page, productISBN);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });

  it('should check references', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkReferences', baseContext);

    const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);

    expect(productHeaderSummary.mpn).to.be.eq(productMPN);
    expect(productHeaderSummary.upc).to.be.eq(productUPC);
    expect(productHeaderSummary.ean_13).to.be.eq(productEAN13);
    expect(productHeaderSummary.isbn).to.be.eq(productISBN);
  });

  it('should set offline', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setOffline', baseContext);

    await createProductsPage.setProductStatus(page, false);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });

  deleteProductTest(newProductData, `${baseContext}_postTest_0`);
});
