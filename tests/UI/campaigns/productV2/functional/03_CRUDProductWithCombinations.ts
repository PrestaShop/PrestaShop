import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';
import date from '@utils/date';
import basicHelper from '@utils/basicHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {enableNewProductPageTest, disableNewProductPageTest} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import combinationsTab from '@pages/BO/catalog/productsV2/add/combinationsTab';
import attributesPage from '@pages/BO/catalog/attributes';
import pricingTab from '@pages/BO/catalog/productsV2/add/pricingTab';
import foProductPage from '@pages/FO/product';

// Import data
import ProductFaker from '@data/faker/product';
import {DefaultEmployee} from '@data/demo/employees';

const baseContext: string = 'productV2_functional_CRUDProductWithCombinations';

describe('BO - Catalog - Products : CRUD product with combinations', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const todayDate: string = date.getDateFormat('yyyy-mm-dd');

  // Data to create product with combinations
  const newProductData: ProductFaker = new ProductFaker({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    attributes: {
      size: ['S', 'M', 'L', 'XL'],
      color: ['Grey', 'Taupe', 'Beige', 'White', 'Red', 'Black', 'Orange', 'Green', 'Yellow', 'Brown'],
    },
    status: false,
  });

  // Data after delete the first attribute
  const editProductAttributesData: object = {
    attributes: {
      size: ['S', 'M', 'L', 'XL'],
      color: ['Taupe', 'Beige', 'White', 'Red', 'Black', 'Orange', 'Green', 'Yellow', 'Brown'],
    },
  };

  // Data to edit the first combination
  const firstCombinationData: object = {
    reference: 'abcd',
    impactOnPriceTExc: 25,
    quantity: 100,
  };

  // Data to edit the second combination
  const secondCombinationData: object = {
    reference: 'efghigk',
    minimalQuantity: 2,
    impactOnPriceTExc: 20,
    quantity: 150,
  };

  // Data to edit the stock of combinations by bulk actions
  const editStockData: object = {
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
  const pricingData: object = {
    price: 15,
    taxRule: 'FR Taux standard (20%)',
    priceTaxIncl: 18,
  };

  // Data to edit the product with combinations
  const editProductData: ProductFaker = new ProductFaker({
    type: 'combinations',
    taxRule: 'No tax',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    attributes: {
      color: ['Pink', 'Camel', 'Off White'],
      size: ['L', 'XL'],
    },
  });

  // Pre-condition: Enable new product page
  enableNewProductPageTest(`${baseContext}_enableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage(newProductData.coverImage);
    await files.generateImage(newProductData.thumbImage);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(newProductData.coverImage);
    await files.deleteFile(newProductData.thumbImage);
  });

  describe('Create product', async () => {
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

      const pageTitle: string = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible: boolean = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription: string = await productsPage.getProductDescription(page);
      await expect(productTypeDescription).to.contains(productsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage: string = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Create combinations', async () => {
    it('should go to \'Combinations\' tab and click on \'Attributes & Features\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributes&FeaturesLink', baseContext);

      page = await combinationsTab.clickOnAttributesAndFeaturesLink(page);

      const pageTitle: string = await attributesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should close \'Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await attributesPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton: string = await combinationsTab.setProductAttributes(
        page,
        newProductData.attributes,
      );
      await expect(generateCombinationsButton).to.equal(combinationsTab.generateCombinationsMessage(40));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage: string = await combinationsTab.generateCombinations(page);
      await expect(successMessage).to.equal(combinationsTab.successfulGenerateCombinationsMessage(40));
    });

    it('should close generate combinations modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeGenerateCombinationsModal', baseContext);

      const isModalVisible: boolean = await combinationsTab.closeGenerateCombinationModal(page);
      await expect(isModalVisible).to.be.true;
    });
  });

  describe('Edit combinations', async () => {
    it('should edit the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFirstCombination', baseContext);

      const successMessage: string = await combinationsTab.editCombination(page, firstCombinationData);
      await expect(successMessage).to.equal(combinationsTab.successfulUpdateMessage);
    });

    it('should click on edit icon for the second combination and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditSecondCombination', baseContext);

      const isVisible: boolean = await combinationsTab.clickOnEditIcon(page, 2);
      await expect(isVisible).to.be.true;
    });

    it('should edit the combination from the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSecondCombination', baseContext);

      const successMessage: string = await combinationsTab.editCombinationFromModal(page, secondCombinationData);
      await expect(successMessage).to.equal(combinationsTab.successfulUpdateMessage);
    });

    it('should check the recent stock movement in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result: object = await combinationsTab.getRecentStockMovements(page);
      await Promise.all([
        await expect(result.dateTime).to.contains(todayDate),
        await expect(result.employee).to.equal(`${DefaultEmployee.firstName} ${DefaultEmployee.lastName}`),
        await expect(result.quantity).to.equal(secondCombinationData.quantity),
      ]);
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalVisible: boolean = await combinationsTab.closeEditCombinationModal(page);
      await expect(isModalVisible).to.be.false;
    });
  });

  describe('Sort combinations table', async () => {
    it('should change the items number to 100 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo100', baseContext);

      const paginationNumber: string = await combinationsTab.selectPaginationLimit(page, 100);
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

        const nonSortedTable: string[] = await combinationsTab.getAllRowsColumnContent(page, 40, test.args.sortBy);

        await combinationsTab.sortTable(page, test.args.sortBy, test.args.column, test.args.sortDirection);

        const sortedTable: string[] = await combinationsTab.getAllRowsColumnContent(page, 40, test.args.sortBy);

        const nonSortedTableFloat: number[] = await nonSortedTable.map((text: string): number => parseFloat(text));
        const sortedTableFloat: number[] = await sortedTable.map((text: string): number => parseFloat(text));

        const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTableFloat).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber: string = await combinationsTab.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber: string = await combinationsTab.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 4)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber: string = await combinationsTab.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber: string = await combinationsTab.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Delete combination', async () => {
    it('should try to delete the first combination then cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstCombinationCancel', baseContext);

      const isModalVisible: boolean = await combinationsTab.clickOnDeleteIcon(page, 'cancel');
      await expect(isModalVisible).to.be.false;
    });

    it('should delete the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstCombination', baseContext);

      const successMessage: string = await combinationsTab.clickOnDeleteIcon(page, 'delete');
      await expect(successMessage).to.equal(createProductsPage.successfulDeleteMessage);
    });
  });

  describe('Filter combinations table', async () => {
    it('should filter by size', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySize', baseContext);

      // Filter by the first color in the list GREY
      await combinationsTab.filterCombinationsBySize(page, 1);

      for (let i = 1; i <= 3; i++) {
        const name: string = await combinationsTab.getTextColumn(page, 'name', i);
        await expect(name).to.contains('Size - S');
      }
    });

    it('should check the filter by size button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterBySizeButton', baseContext);

      const buttonName: string = await combinationsTab.getFilterBySizeButtonName(page);
      await expect(buttonName).to.equal('Size (1)');
    });

    it('should clear filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearFilter', baseContext);

      const numberOfCombinations: number = await combinationsTab.clearFilter(page);
      await expect(numberOfCombinations).to.equal(39);
    });
  });

  describe('Bulk actions', async () => {
    it('should select all combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAllCombinations', baseContext);

      const isBulkActionsButtonVisible: boolean = await combinationsTab.selectAllCombinations(page);
      await expect(isBulkActionsButtonVisible).to.be.true;
    });

    it('should click on edit combinations by bulk actions and check the modal title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditByBulkActions', baseContext);

      const modalTitle: string = await combinationsTab.clickOnEditCombinationsByBulkActions(page);
      await expect(modalTitle).to.equal(combinationsTab.editCombinationsModalTitle(39));
    });

    it('should edit Stocks, Retail price and Specific references', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editStock', baseContext);

      const successMessage: string = await combinationsTab.editCombinationsByBulkActions(page, editStockData);
      await expect(successMessage).to.equal(combinationsTab.editCombinationsModalMessage(39));
    });
  });

  describe('Edit product and check the header details', async () => {
    it('should go to Pricing tab and edit retail price, enable the product then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      await pricingTab.setProductPricing(page, pricingData);

      await createProductsPage.setProductStatus(page, true);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName: string = await createProductsPage.getSaveButtonName(page);
      await expect(saveButtonName).to.equal(createProductsPage.saveAndPublishButtonName);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue: number = await basicHelper.percentage(pricingData.price, 20);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(pricingData.price.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(pricingData.price + taxValue).toFixed(2)} tax incl. (tax rule: 20%)`),
        expect(productHeaderSummary.quantity).to.equal('930 in stock'),
        expect(productHeaderSummary.reference).to.contains(newProductData.reference),
      ]);
    });
  });

  describe('Check product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue: number = await basicHelper.percentage(pricingData.price + secondCombinationData.impactOnPriceTExc, 20);

      let result: object = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(newProductData.name),
        await expect(result.price).to.equal(pricingData.price + secondCombinationData.impactOnPriceTExc + taxValue),
        await expect(result.shortDescription).to.equal(newProductData.summary),
        await expect(result.description).to.equal(newProductData.description),
      ]);

      result = await foProductPage.getProductAttributes(page);
      await Promise.all([
        await expect(result.size).to.equal(editProductAttributesData.attributes.size.join(' ')),
        await expect(result.color).to.equal(editProductAttributesData.attributes.color.join(' ')),
      ]);
    });
  });

  describe('Edit product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage: string = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCombinations', baseContext);

      const generateCombinationsButton: string = await combinationsTab.setProductAttributes(
        page,
        editProductData.attributes,
      );
      await expect(generateCombinationsButton).to.equal(combinationsTab.generateCombinationsMessage(6));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations2', baseContext);

      const successMessage: string = await combinationsTab.generateCombinations(page);
      await expect(successMessage).to.equal(combinationsTab.successfulGenerateCombinationsMessage(6));
    });

    it('should close generate combinations modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeGenerateCombinationsModal2', baseContext);

      const isModalVisible: boolean = await combinationsTab.closeGenerateCombinationModal(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct2', baseContext);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Check product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      let result: object = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price).to.equal(editProductData.price + secondCombinationData.impactOnPriceTExc),
        await expect(result.description).to.equal(editProductData.description),
      ]);

      result = await foProductPage.getProductAttributes(page);
      await Promise.all([
        await expect(result.size).to.equal(editProductAttributesData.attributes.size.join(' ')),
        await expect(result.color).to.equal(editProductAttributesData.attributes.color.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage: string = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);
});
