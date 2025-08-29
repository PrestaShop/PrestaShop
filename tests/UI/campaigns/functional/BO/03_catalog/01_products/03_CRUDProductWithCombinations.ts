import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductsCreateTabPricingPage,
  type BrowserContext,
  FakerProduct,
  foClassicProductPage,
  type Page,
  type ProductAttributes,
  type ProductCombinationBulk,
  type ProductCombinationOptions,
  utilsCore,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_CRUDProductWithCombinations';

describe('BO - Catalog - Products : CRUD product with combinations', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const todayDate: string = utilsDate.getDateFormat('yyyy-mm-dd');

  // Data to create product with combinations
  const newProductData: FakerProduct = new FakerProduct({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    attributes: [
      {
        name: 'size',
        values: ['S', 'M', 'L', 'XL'],
      },
      {
        name: 'color',
        values: ['Gray', 'Taupe', 'Beige', 'White', 'Red', 'Black', 'Orange', 'Green', 'Yellow', 'Brown'],
      },
    ],
    status: false,
  });

  // Data after delete the first attribute
  const editProductAttributesData: ProductAttributes[] = [
    {
      name: 'size',
      values: ['S', 'M', 'L', 'XL'],
    },
    {
      name: 'color',
      values: ['Taupe', 'Beige', 'White', 'Red', 'Black', 'Orange', 'Green', 'Yellow', 'Brown'],
    },
  ];

  // Data to edit the first combination
  const firstCombinationData: ProductCombinationOptions = {
    reference: 'abcd',
    impactOnPriceTExc: 25,
    quantity: 100,
  };

  // Data to edit the second combination
  const secondCombinationData: ProductCombinationOptions = {
    reference: 'efghigk',
    minimalQuantity: 2,
    impactOnPriceTExc: 20,
    quantity: 150,
  };

  // Data to edit the stock of combinations by bulk actions
  const editStockData: ProductCombinationBulk = {
    stocks: {
      quantityToEnable: true,
      quantity: 20,
      minimalQuantityToEnable: true,
      minimalQuantity: 3,
      stockLocationToEnable: true,
      stockLocation: 'location 1',
    },
    retailPrice: {
      costPriceToEnable: true,
      costPrice: 10,
      impactOnPriceTIncToEnable: true,
      impactOnPriceTInc: 20,
      impactOnWeightToEnable: true,
      impactOnWeight: 0.1,
    },
    specificReferences: {
      referenceToEnable: true,
      reference: 'comb_ref_bulk',
    },
  };

  // Data to edit the product price
  const pricingData: FakerProduct = new FakerProduct({
    price: 18,
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
  });

  // Data to edit the product with combinations
  const editProductData: FakerProduct = new FakerProduct({
    type: 'combinations',
    taxRule: 'No tax',
    tax: 0,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    attributes: [
      {
        name: 'color',
        values: ['Pink', 'Camel', 'Off White'],
      },
      {
        name: 'size',
        values: ['L', 'XL'],
      },
    ],
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create product', async () => {
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

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Create combinations', async () => {
    it('should go to \'Combinations\' tab and click on \'Attributes & Features\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributes&FeaturesLink', baseContext);

      page = await boProductsCreateTabCombinationsPage.clickOnAttributesAndFeaturesLink(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should close \'Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await boAttributesPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
        page,
        newProductData.attributes,
      );
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(40));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(40));
    });

    it('combinations generation modal should be closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed1', baseContext);

      const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
      expect(isModalClosed).to.eq(true);
    });
  });

  describe('Edit combinations', async () => {
    it('should edit the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFirstCombination', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.editCombination(page, firstCombinationData);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should click on edit icon for the second combination and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditSecondCombination', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 2);
      expect(isVisible).to.eq(true);
    });

    it('should edit the combination from the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSecondCombination', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.editCombinationFromModal(page, secondCombinationData);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should check the recent stock movement in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result = await boProductsCreateTabCombinationsPage.getRecentStockMovements(page);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${global.BO.FIRSTNAME} ${global.BO.LASTNAME}`),
        expect(result.quantity).to.equal(secondCombinationData.quantity),
      ]);
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
      expect(isModalVisible).to.eq(false);
    });
  });

  describe('Sort combinations table', async () => {
    it('should change the items number to 100 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo100', baseContext);

      const paginationNumber = await boProductsCreateTabCombinationsPage.selectPaginationLimit(page, 100);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });

    const tests = [
      {
        args: {
          testIdentifier: 'sortByIdAsc',
          sortBy: 'combination_id',
          column: 3,
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByImpactOnPriceTExcAsc',
          sortBy: 'impact_on_price_te',
          column: 6,
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByImpactOnPriceTExcDesc',
          sortBy: 'impact_on_price_te',
          column: 6,
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByImpactOnPriceTIncAsc',
          sortBy: 'impact_on_price_ti',
          column: 7,
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByImpactOnPriceTIncDesc',
          sortBy: 'impact_on_price_ti',
          column: 7,
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByFinalPriceTaxExcAsc',
          sortBy: 'final_price_te',
          column: 9,
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByFinalPriceTaxExcDesc',
          sortBy: 'final_price_te',
          column: 9,
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByQuantityAsc',
          sortBy: 'delta_quantity_quantity',
          column: 10,
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByQuantityDesc',
          sortBy: 'delta_quantity_quantity',
          column: 10,
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdDesc',
          sortBy: 'combination_id',
          column: 3,
          sortDirection: 'asc',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boProductsCreateTabCombinationsPage.getAllRowsColumnContent(page, 40, test.args.sortBy);

        await boProductsCreateTabCombinationsPage.sortTable(page, test.args.sortBy, test.args.column, test.args.sortDirection);

        const sortedTable = await boProductsCreateTabCombinationsPage.getAllRowsColumnContent(page, 40, test.args.sortBy);

        const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
        const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

        const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTableFloat).to.deep.equal(expectedResult);
        } else {
          expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await boProductsCreateTabCombinationsPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boProductsCreateTabCombinationsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 4)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boProductsCreateTabCombinationsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boProductsCreateTabCombinationsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Delete combination', async () => {
    it('should try to delete the first combination then cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstCombinationCancel', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.clickOnDeleteIcon(page, 'cancel');
      expect(isModalVisible).to.eq(false);
    });

    it('should delete the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstCombination', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.clickOnDeleteIcon(page, 'delete');
      expect(successMessage).to.equal(boProductsCreatePage.successfulDeleteMessage);
    });
  });

  describe('Filter combinations table', async () => {
    it('should filter by size', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySize', baseContext);

      // Filter by the first color in the list GREY
      await boProductsCreateTabCombinationsPage.filterCombinationsBySize(page, 1);

      for (let i = 1; i <= 3; i++) {
        const name = await boProductsCreateTabCombinationsPage.getTextColumn(page, 'name', i);
        expect(name).to.contains('Size - S');
      }
    });

    it('should check the filter by size button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterBySizeButton', baseContext);

      const buttonName = await boProductsCreateTabCombinationsPage.getFilterBySizeButtonName(page);
      expect(buttonName).to.equal('Size (1)');
    });

    it('should clear filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearFilter', baseContext);

      const numberOfCombinations = await boProductsCreateTabCombinationsPage.clearFilter(page);
      expect(numberOfCombinations).to.equal(39);
    });
  });

  describe('Bulk actions', async () => {
    it('should select all combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAllCombinations', baseContext);

      const isBulkActionsButtonVisible = await boProductsCreateTabCombinationsPage.selectAllCombinations(page);
      expect(isBulkActionsButtonVisible).to.eq(true);
    });

    it('should click on edit combinations by bulk actions and check the modal title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditByBulkActions', baseContext);

      const modalTitle = await boProductsCreateTabCombinationsPage.clickOnEditCombinationsByBulkActions(page);
      expect(modalTitle).to.equal(boProductsCreateTabCombinationsPage.editCombinationsModalTitle(39));
    });

    it('should edit Stocks, Retail price and Specific references', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editStock', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.editCombinationsByBulkActions(page, editStockData);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.editCombinationsModalMessage(39));
    });
  });

  describe('Edit product and check the header details', async () => {
    it('should go to Pricing tab and edit retail price, enable the product then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      await boProductsCreateTabPricingPage.setProductPricing(page, pricingData);

      await boProductsCreatePage.setProductStatus(page, true);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName = await boProductsCreatePage.getSaveButtonName(page);
      expect(saveButtonName).to.equal(boProductsCreatePage.saveAndPublishButtonName);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue = utilsCore.percentage(pricingData.priceTaxExcluded, 20);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(pricingData.priceTaxExcluded.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(pricingData.priceTaxExcluded + taxValue).toFixed(2)} tax incl. (tax rule: 20%)`),
        expect(productHeaderSummary.quantity).to.equal('930 in stock'),
        expect(productHeaderSummary.reference).to.contains(newProductData.reference),
      ]);
    });
  });

  describe('Check product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue = utilsCore.percentage(pricingData.priceTaxExcluded + secondCombinationData.impactOnPriceTExc, 20);

      const result = await foClassicProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(newProductData.name),
        expect(result.price).to.equal(pricingData.priceTaxExcluded + secondCombinationData.impactOnPriceTExc + taxValue),
        expect(result.summary).to.equal(newProductData.summary),
        expect(result.description).to.equal(newProductData.description),
      ]);

      const productAttributes = await foClassicProductPage.getProductAttributes(page);
      await Promise.all([
        expect(productAttributes[0].name).to.equal(newProductData.attributes[0].name),
        expect(productAttributes[0].value).to.equal(newProductData.attributes[0].values.join(' ')),
        expect(productAttributes[1].name).to.equal(newProductData.attributes[1].name),
        expect(productAttributes[1].value).to.equal(editProductAttributesData[1].values.join(' ')),
      ]);
    });
  });

  describe('Edit product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, editProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCombinations', baseContext);

      const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
        page,
        editProductData.attributes,
      );
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(6));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations2', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(6));
    });

    it('combinations generation modal should be closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed2', baseContext);

      const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
      expect(isModalClosed).to.eq(true);
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct2', baseContext);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Check product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const result = await foClassicProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(editProductData.name),
        expect(result.price).to.equal(editProductData.price + secondCombinationData.impactOnPriceTExc),
        expect(result.description).to.equal(editProductData.description),
      ]);

      const productAttributes = await foClassicProductPage.getProductAttributes(page);
      await Promise.all([
        expect(productAttributes[0].name).to.equal(editProductAttributesData[0].name),
        expect(productAttributes[0].value).to.equal(editProductAttributesData[0].values.join(' ')),
        expect(productAttributes[1].name).to.equal(editProductAttributesData[1].name),
        expect(productAttributes[1].value).to.equal(editProductAttributesData[1].values.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
