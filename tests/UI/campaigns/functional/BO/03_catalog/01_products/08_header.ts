import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boProductsCreateTabDetailsPage,
  boProductsCreateTabPricingPage,
  boProductsCreateTabStocksPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  type ProductHeaderSummary,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_header';

describe('BO - Catalog - Products : Header', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let productHeaderSummaryInitial: ProductHeaderSummary;

  const newProductData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(productCoverImage);
    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(productCoverImage);
    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.productsLink,
    );

    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should click on \'New product\' button and check new product modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

    const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
    expect(isModalVisible).to.eq(true);
  });

  it('should check the standard product description', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

    const productTypeDescription = await boProductsPage.getProductDescription(page);
    expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
  });

  it('should choose \'Standard product\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

    await boProductsPage.selectProductType(page, newProductData.type);

    const pageTitle = await boProductsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
  });

  it('should go to new product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

    await boProductsPage.clickOnAddNewProduct(page);

    const pageTitle = await boProductsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
  });

  it('should create standard product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

    await boProductsCreatePage.closeSfToolBar(page);

    const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
    expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should click on \'Go to catalog\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

    await boProductsCreatePage.goToCatalogPage(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should reset filter and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

    numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it(`should filter list by '${newProductData.name}' and check result`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterListByName', baseContext);

    await boProductsPage.filterProducts(page, 'product_name', newProductData.name, 'input');

    const numberOfProductsAfterFilter: number = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.eq(newProductData.name);
  });

  it('should go to the first product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await boProductsPage.goToProductPage(page, 1);

    const pageTitle = await boProductsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);

    productHeaderSummaryInitial = await boProductsCreatePage.getProductHeaderSummary(page);
  });

  it('should edit the product name (in English)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProductNameEn', baseContext);

    await boProductsCreatePage.setProductName(page, productNameEn, 'en');

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should edit the product name (in French)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProductNameFr', baseContext);

    await boProductsCreatePage.setProductName(page, productNameFr, 'fr');

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should go to the Pricing tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPricingTab', baseContext);

    await boProductsCreatePage.goToTab(page, 'pricing');

    const isTabActive = await boProductsCreatePage.isTabActive(page, 'pricing');
    expect(isTabActive).to.eq(true);
  });

  it('should edit the retail price', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

    await boProductsCreateTabPricingPage.setRetailPrice(page, false, productRetailPriceTaxIncluded);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);

    const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
    expect(productHeaderSummary.priceTaxExc).to.equals(`€${productRetailPriceTaxExcluded.toFixed(2)} tax excl.`);
    expect(productHeaderSummary.priceTaxIncl).to.equals(
      `€${productRetailPriceTaxIncluded.toFixed(2)} tax incl. (tax rule: ${newProductData.tax}%)`,
    );
  });

  it('should go to the Stocks tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksTab', baseContext);

    await boProductsCreatePage.goToTab(page, 'stock');

    const isTabActive = await boProductsCreatePage.isTabActive(page, 'stock');
    expect(isTabActive).to.eq(true);
  });

  it('should add items to stock', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addItemsToStock', baseContext);

    await boProductsCreateTabStocksPage.setQuantityDelta(page, 10);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);

    const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
    expect(productHeaderSummary.quantity).to.equal(`${productQuantity + 10} in stock`);
  });

  it('should subtract items to stock', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'subtractItemsToStock', baseContext);

    await boProductsCreateTabStocksPage.setQuantityDelta(page, -75);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);

    const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
    expect(productHeaderSummary.quantity).to.equal(`${productQuantity + 10 - 75} in stock`);
  });

  it('should go to the Description tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDescriptionTab', baseContext);

    await boProductsCreatePage.goToTab(page, 'description');

    const isTabActive = await boProductsCreatePage.isTabActive(page, 'description');
    expect(isTabActive).to.eq(true);
  });

  it('should add image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

    await boProductsCreateTabDescriptionPage.addProductImages(page, [productCoverImage]);

    const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
    expect(numOfImages).to.eq(2);
  });

  it('should set image information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

    const message = await boProductsCreateTabDescriptionPage.setProductImageInformation(
      page,
      2,
      true,
      'Caption EN',
      'Caption FR',
    );
    expect(message).to.be.eq(boProductsCreateTabDescriptionPage.settingUpdatedMessage);
  });

  it('should check image has changed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkImageHasChanged', baseContext);

    await boProductsCreateTabDescriptionPage.reloadPage(page);
    const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);

    expect(productHeaderSummary.imageUrl).to.be.not.eq(productHeaderSummaryInitial.imageUrl);
  });

  it('should change product type', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeProductType', baseContext);

    const message = await boProductsCreatePage.changeProductType(page, 'virtual');
    expect(message).to.be.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should check product type has changed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTypeHasChanged', baseContext);

    const productType = await boProductsCreatePage.getProductType(page);
    expect(productType).to.be.eq('virtual');

    // FYI : stock is the ID for the "Virtual product" tab
    const isTabVisible = await boProductsCreatePage.isTabVisible(page, 'stock');
    expect(isTabVisible).to.eq(true);
  });

  it('should go to the Details tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDetailsTab', baseContext);

    await boProductsCreatePage.goToTab(page, 'details');

    const isTabActive = await boProductsCreatePage.isTabActive(page, 'details');
    expect(isTabActive).to.eq(true);
  });

  it('should set references', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setReferences', baseContext);

    await boProductsCreateTabDetailsPage.setMPN(page, productMPN);
    await boProductsCreateTabDetailsPage.setUPC(page, productUPC);
    await boProductsCreateTabDetailsPage.setEAN13(page, productEAN13);
    await boProductsCreateTabDetailsPage.setISBN(page, productISBN);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should check references', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkReferences', baseContext);

    const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);

    expect(productHeaderSummary.mpn).to.be.eq(productMPN);
    expect(productHeaderSummary.upc).to.be.eq(productUPC);
    expect(productHeaderSummary.ean_13).to.be.eq(productEAN13);
    expect(productHeaderSummary.isbn).to.be.eq(productISBN);
  });

  it('should set offline', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setOffline', baseContext);

    await boProductsCreatePage.setProductStatus(page, false);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  deleteProductTest(newProductData, `${baseContext}_postTest_0`);
});
