import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  // Import BO pages
  boCartRulesPage,
  boCatalogPriceRulesCreatePage,
  boCatalogPriceRulesPage,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  boCurrenciesPage,
  // Import FO pages
  foHummingbirdCartPage,
  foHummingbirdSearchResultsPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdProductPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyAddressesPage,
  foHummingbirdMyAddressesCreatePage,
  // Import data
  dataProducts,
  FakerCustomer,
  FakerCatalogPriceRule,
  FakerAddress,
  dataCurrencies,
  // Import type
  type BrowserContext,
  type ImportContent,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDCountry';

describe('BO - Catalog - Discounts : CRUD country', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customerData: FakerCustomer = new FakerCustomer({email: 'test@test.com', password: '123456789'});
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'United Arab Emirates',
  });
  const editAddressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    state: 'Florida',
    country: 'United States',
  });

  const contentToImport: ImportContent = {
    importStates: true,
    importTaxes: true,
    importCurrencies: true,
    importLanguages: true,
    importUnits: true,
    updatePriceDisplayForGroups: false,
  };

  const catalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'test',
    currency: 'All currencies',
    country: 'France',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 10.00,
  });
  const editCatalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'test',
    currency: 'All currencies',
    country: 'United Arab Emirates',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 10.00,
  });
  const editCatalogPriceRuleData2: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'test',
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 10.00,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest`);

  describe('Import localization pack of United states', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPackUS', baseContext);

      const textResult = await boLocalizationPage.importLocalizationPack(page, 'United States', contentToImport);
      expect(textResult).to.equal(boLocalizationPage.importLocalizationPackSuccessfulMessage);
    });
  });

  describe('Import localization pack of United Arab Emirates', async () => {
    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPackUE', baseContext);

      const textResult = await boLocalizationPage.importLocalizationPack(page, 'United Arab Emirates', contentToImport);
      expect(textResult).to.equal(boLocalizationPage.importLocalizationPackSuccessfulMessage);
    });
  });

  describe('Create catalog price rule in Bo and check it in FO', async () => {
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

    it('should go to add catalog price rules page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goAddCatalogPriceRulesPage', baseContext);

      await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);
    });

    it('should create new catalog price rule with country : All', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await boCatalogPriceRulesCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount', baseContext);

      // Check discount percentage
      let columnValue = await foHummingbirdProductPage.getDiscountAmount(page);
      expect(columnValue).to.equal(`(Save €${catalogPriceRuleData.reduction.toFixed(2)})`);

      // Check final price
      let finalPrice = await foHummingbirdProductPage.getProductInformation(page);
      expect(finalPrice.price.toFixed(2)).to.equal(
        (
          dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
        ).toFixed(2),
      );

      // Set quantity of the product
      await foHummingbirdProductPage.setQuantity(page, catalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await foHummingbirdProductPage.getDiscountAmount(page);
      expect(columnValue).to.equal(`(Save €${catalogPriceRuleData.reduction.toFixed(2)})`);

      // Check final price
      finalPrice = await foHummingbirdProductPage.getProductInformation(page);
      expect(finalPrice.price.toFixed(2)).to.equal(
        (
          dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
        ).toFixed(2),
      );
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check the discount in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart', baseContext);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Edit catalog price rules and check it in FO - Country : United Arab Emirates', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should edit the catalog price rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCatalogPriceRules', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, editCatalogPriceRuleData);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boCatalogPriceRulesCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check that no reduction is applied', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoReduction', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].price
          ).toFixed(2),
        ),
      ]);
    });
  });

  describe('Add new customer Address - Country : United Arab Emirates', async () => {
    it('should login with the created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginCustomer', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);
      await foHummingbirdLoginPage.customerLogin(page, customerData);

      const connected = await foHummingbirdHomePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to \'Add first address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddFirstAddressPage', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.addressPageTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, addressData);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Check that the discount is applied in the cart page', async () => {
    it('should go to the cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCart', baseContext);
      await foHummingbirdMyAddressesCreatePage.goToCartPage(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41241
    it.skip('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart_2', baseContext);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Edit catalog price rules and check it in FO - Country : All', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_2', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should edit the catalog price rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCatalogPriceRules_2', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, editCatalogPriceRuleData.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, editCatalogPriceRuleData2);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_2', baseContext);

      page = await boCatalogPriceRulesCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41241
    it.skip('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart_3', baseContext);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Edit customer Address - Country : United States', async () => {
    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage_2', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDeleteAddress', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      await foHummingbirdMyAddressesPage.goToEditAddressPage(page, 1);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, editAddressData);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.updateAddressSuccessfulMessage);
    });

    it('should go to the cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCart_2', baseContext);
      await foHummingbirdMyAddressesCreatePage.goToCartPage(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41241
    it.skip('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart_3', baseContext);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].price - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Delete created catalog price rules', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_3', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, editCatalogPriceRuleData2.name);
      expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
    });
  });

  describe('Delete currency added by importing localization pack', async () => {
    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage_2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should go to currencies page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await boLocalizationPage.goToSubTabCurrencies(page);

      const pageTitle = await boCurrenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
    });

    [dataCurrencies.aed.isoCode, dataCurrencies.usd.isoCode].forEach((arg: string, index: number) => {
      it(`should filter by iso code of currency '${arg}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterCurrencies_${index}`, baseContext);

        await boCurrenciesPage.filterTable(page, 'input', 'iso_code', arg);

        const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
        expect(textColumn).to.contains(arg);
      });

      it('should delete currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCurrency_${index}`, baseContext);

        const result = await boCurrenciesPage.deleteCurrency(page, 1);
        expect(result).to.be.equal(boCurrenciesPage.successfulDeleteMessage);
      });
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencies', baseContext);

      const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.at.least(1);
    });
  });

  // Post-condition: Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
