import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductsCreateTabPricingPage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  foClassicCartPage,
  foClassicProductPage,
  type Page,
  type ProductAttribute,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_quantityDiscountsBasedOn';

/*
Choose quantity discounts based on 'Products'
Create product with combinations and add a specific price(discount 50% for the first combination)
Add the combinations to the cart and check the price ATI
Choose quantity discounts based on 'Combinations'
Check the cart price ATI
 */
describe('BO - Shop Parameters - Product Settings : Choose quantity discount based on', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const productWithCombinations: FakerProduct = new FakerProduct({
    type: 'combinations',
    price: 20,
    tax: 0,
    taxRule: 'No tax',
    attributes: [
      {
        name: 'color',
        values: ['White', 'Black'],
      },
      {
        name: 'size',
        values: ['S'],
      },
    ],
    quantity: 10,
    specificPrice: {
      attributes: 2,
      discount: 50,
      startingAt: 2,
      reductionType: '%',
    },
  });
  const firstAttributeToChoose: ProductAttribute[] = [
    {
      name: 'color',
      value: 'White',
    },
    {
      name: 'size',
      value: 'S',
    },
  ];
  const secondAttributeToChoose: ProductAttribute[] = [
    {
      name: 'color',
      value: 'Black',
    },
    {
      name: 'size',
      value: 'S',
    },
  ];
  const firstCartTotalATI: number = 30;
  const secondCartTotalATI: number = 40;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Choose quantity discount based on', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.productSettingsLink,
      );
      await boProductSettingsPage.closeSfToolBar(page);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should choose quantity discounts based on \'Products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseQuantityDiscountsBasedOnProducts', baseContext);

      const result = await boProductSettingsPage.chooseQuantityDiscountsBasedOn(page, 'Products');
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boProductSettingsPage.goToSubMenu(
        page,
        boProductSettingsPage.catalogParentLink,
        boProductSettingsPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterProducts', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);

      await boProductsPage.selectProductType(page, productWithCombinations.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product with combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productWithCombinations);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should create combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombination', baseContext);

      const createProductMessage = await boProductsCreateTabCombinationsPage.setProductAttributes(
        page,
        productWithCombinations.attributes,
      );
      expect(createProductMessage).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(2));

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(2));
    });

    it('should edit the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantity', baseContext);

      await boProductsCreateTabCombinationsPage.editCombinationRowQuantity(page, 1, 5);
      await boProductsCreateTabCombinationsPage.editCombinationRowQuantity(page, 2, 5);

      const successMessage = await boProductsCreateTabCombinationsPage.saveCombinationsForm(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should add specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

      const createProductMessage = await boProductsCreateTabPricingPage.setSpecificPrice(
        page,
        productWithCombinations.specificPrice,
      );
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulCreationMessage);
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product and check price ATI in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProductAndCheckPriceATI', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.addProductToTheCart(page, 1, firstAttributeToChoose, false);
      await foClassicProductPage.addProductToTheCart(page, 1, secondAttributeToChoose, true);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(firstCartTotalATI);

      page = await foClassicCartPage.closePage(browserContext, page, 0);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage2', baseContext);

      await boProductsCreatePage.goToSubMenu(
        page,
        boProductsCreatePage.shopParametersParentLink,
        boProductsCreatePage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should choose quantity discounts based on \'Combinations\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseQuantityDiscountsBasedOnCombinations', baseContext);

      const result = await boProductSettingsPage.chooseQuantityDiscountsBasedOn(page, 'Combinations');
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop and check ATI price in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ViewMyShopAndCheckPriceATI', baseContext);

      page = await boProductSettingsPage.viewMyShop(page);
      await foClassicProductPage.goToCartPage(page);

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(secondCartTotalATI);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePageAndBackToBO', baseContext);

      page = await foClassicCartPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await boProductSettingsPage.goToSubMenu(
        page,
        boProductSettingsPage.catalogParentLink,
        boProductSettingsPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should filter list by the created product and delete product from dropDown menu', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      await boProductsPage.filterProducts(page, 'reference', productWithCombinations.reference, 'input');

      const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);

      const numberOfProductsAfterDelete = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterDelete).to.equal(numberOfProducts);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
