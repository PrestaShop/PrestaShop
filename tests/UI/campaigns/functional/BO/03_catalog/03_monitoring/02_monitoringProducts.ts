// Import utils
import testContext from '@utils/testContext';

// Import pages
import addProductPage from '@pages/BO/catalog/products/add';
import combinationsTab from '@pages/BO/catalog/products/add/combinationsTab';
import monitoringPage from '@pages/BO/catalog/monitoring';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_monitoring_monitoringProducts';

/*
Create new product
Check existence of new product in monitoring page
Delete product and check deletion in products page
 */
describe('BO - Catalog - Monitoring : Create different products and delete them from monitoring page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let numberOfProductsIngrid: number = 0;

  const productWithoutImage: FakerProduct = new FakerProduct({type: 'standard'});
  const disabledProduct: FakerProduct = new FakerProduct({type: 'standard', status: false});
  const productWithoutCombinationsWithoutQuantity: FakerProduct = new FakerProduct({type: 'standard', quantity: 0});
  const productWithCombinationsWithoutQuantity: FakerProduct = new FakerProduct({type: 'combinations', quantity: 0});
  const productWithoutPrice: FakerProduct = new FakerProduct({type: 'standard', price: 0});
  const productWithoutDescription: FakerProduct = new FakerProduct({type: 'standard', description: '', summary: ''});

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

  [
    {
      testIdentifier: 'productWithoutImage',
      productType: 'without image',
      productToCreate: productWithoutImage,
      gridName: 'product_without_image',
    },
    {
      testIdentifier: 'disabledProduct',
      productType: 'disabled',
      productToCreate: disabledProduct,
      gridName: 'disabled_product',
    },
    {
      testIdentifier: 'productWithoutCombinationsWithoutQuantity',
      productType: 'without combinations and without available quantities',
      productToCreate: productWithoutCombinationsWithoutQuantity,
      gridName: 'no_qty_product_without_combination',
    },
    {
      testIdentifier: 'productWithCombinationsWithQuantity',
      productType: 'with combinations and without available quantities',
      productToCreate: productWithCombinationsWithoutQuantity,
      gridName: 'no_qty_product_with_combination',
    },
    {
      testIdentifier: 'productWithoutPrice',
      productType: 'without price',
      productToCreate: productWithoutPrice,
      gridName: 'product_without_price',
    },
    {
      testIdentifier: 'productWithoutDescription',
      productType: 'without description',
      productToCreate: productWithoutDescription,
      gridName: 'product_without_description',
    },
  ].forEach((test: {testIdentifier: string, productType: string, productToCreate: FakerProduct, gridName: string}) => {
    describe(`Create product ${test.productType} in BO`, async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_goToProductsPage`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should reset filter and get number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_getNumberOfProduct`, baseContext);

        numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_clickNewProductBtn`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);
      });

      it('should choose the type of product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_chooseTypeOfProduct`, baseContext);

        await boProductsPage.selectProductType(page, test.productToCreate.type);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_goToNewProductPage`, baseContext);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}_createNewProduct`, baseContext);

        await addProductPage.closeSfToolBar(page);

        const createProductMessage = await addProductPage.setProduct(page, test.productToCreate);
        expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
      });

      if (test.productToCreate.type === 'combinations') {
        it('should create combinations and check generate combinations button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

          const generateCombinationsButton = await combinationsTab.setProductAttributes(
            page,
            test.productToCreate.attributes,
          );
          expect(generateCombinationsButton).to.equal('Generate 4 combinations');
        });

        it('should click on generate combinations button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

          const successMessage = await combinationsTab.generateCombinations(page);
          expect(successMessage).to.equal('Successfully generated 4 combinations.');
        });

        it('should check that combinations generation modal is closed', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed', baseContext);

          const isModalClosed = await combinationsTab.generateCombinationModalIsClosed(page);
          expect(isModalClosed).to.be.equal(true);
        });
      }
    });

    describe('Check created product in monitoring page', async () => {
      it('should go to \'Catalog > Monitoring\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_goToMonitoringPage`,
          baseContext,
        );

        await addProductPage.goToSubMenu(
          page,
          addProductPage.catalogParentLink,
          addProductPage.monitoringLink,
        );

        const pageTitle = await monitoringPage.getPageTitle(page);
        expect(pageTitle).to.contains(monitoringPage.pageTitle);

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(
          page,
          test.gridName,
        );
        expect(numberOfProductsIngrid).to.be.at.least(1);
      });

      it(`should filter products ${test.productType} grid and check existence of new product`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_checkProduct`,
          baseContext,
        );

        await monitoringPage.filterTable(
          page,
          test.gridName,
          'input',
          'name',
          test.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
          test.gridName,
          1,
          'name',
        );
        expect(textColumn).to.contains(test.productToCreate.name);
      });

      it(`should reset filter in products ${test.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_resetInMonitoringPage`,
          baseContext,
        );

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(page, test.gridName);
        expect(numberOfProductsIngrid).to.be.at.least(1);
      });
    });

    describe('Delete product in monitoring page', async () => {
      it(`should filter products ${test.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_filterToDelete`,
          baseContext,
        );

        await monitoringPage.filterTable(
          page,
          test.gridName,
          'input',
          'name',
          test.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
          test.gridName,
          1,
          'name',
        );
        expect(textColumn).to.contains(test.productToCreate.name);
      });

      it('should delete product', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_deleteProduct`,
          baseContext,
        );

        const textResult = await monitoringPage.deleteProductInGrid(page, test.gridName, 1);
        expect(textResult).to.equal(boProductsPage.successfulDeleteMessage);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should reset filter check number of products', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.testIdentifier}_resetInProductsPage`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts);
      });
    });
  });
});
