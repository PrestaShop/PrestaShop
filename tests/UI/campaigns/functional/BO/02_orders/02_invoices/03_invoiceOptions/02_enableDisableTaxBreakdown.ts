// Import utils
import testContext from '@utils/testContext';

// Import commonSteps
import {bulkDeleteProductsTest} from '@commonTests/BO/catalog/product';

// Import pages
// Import BO pages
import addProductPage from '@pages/BO/catalog/products/add';
import taxesPage from '@pages/BO/international/taxes';
import addTaxRulesPage from '@pages/BO/international/taxes/taxRules/add';
import taxRulesPage from '@pages/BO/international/taxes/taxRules';
import invoicesPage from '@pages/BO/orders/invoices';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  boProductsPage,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerProduct,
  FakerTaxRule,
  FakerTaxRulesGroup,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicProductPage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_enableDisableTaxBreakdown';

/*
Enable tax breakdown
Create new tax rule group
Create 2 new tax rules
Create new product with the new tax rule
Create new order in FO with the created product
Generate the invoice and check the tax breakdown
Disable tax breakdown
Generate the invoice and check that there is no tax breakdown
Delete the created tax rule with bulk action
Post-condition: Delete Product with bulk action
 */
describe('BO - Orders - Invoices : Enable/Disable tax breakdown', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let firstInvoiceFileName: string | null;
  let secondInvoiceFileName: string | null;

  const taxRuleGroupToCreate: FakerTaxRulesGroup = new FakerTaxRulesGroup();
  const firstTaxRuleToCreate: FakerTaxRule = new FakerTaxRule({
    country: 'France',
    behaviour: 'Combine',
    name: 'TVA FR 20%',
  });
  const secondTaxRuleToCreate: FakerTaxRule = new FakerTaxRule({
    country: 'France',
    behaviour: 'Combine',
    name: 'TVA FR 10%',
  });
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    taxRule: taxRuleGroupToCreate.name,
  });

  // before and after functions
  before(async function () {
    // Create new tab
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

  describe('Enable tax breakdown then check it in the invoice', async () => {
    describe('Enable tax breakdown', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableTaxBreakDown', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.invoicesLink,
        );
        await invoicesPage.closeSfToolBar(page);

        const pageTitle = await invoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should enable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableTaxBreakDown', baseContext);

        await invoicesPage.enableTaxBreakdown(page, true);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Create 2 new tax rules', async () => {
      it('should go to \'International > Taxes\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.internationalParentLink,
          invoicesPage.taxesLink,
        );

        const pageTitle = await taxesPage.getPageTitle(page);
        expect(pageTitle).to.contains(taxesPage.pageTitle);
      });

      it('should go to \'Tax Rules\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

        await taxesPage.goToTaxRulesPage(page);

        const pageTitle = await taxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(taxRulesPage.pageTitle);
      });

      it('should go to \'Add new tax rules group\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePage', baseContext);

        await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
      });

      it('should create new tax rule group', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleGroupToCreate);
        expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
      });

      it('should create new tax rule n°1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createFirstTaxRule', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRules(page, firstTaxRuleToCreate);
        expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
      });

      it('should go to \'Add new tax rule\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickToCreateSecondTaxRule', baseContext);

        await addTaxRulesPage.clickOnAddNewTaxRule(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTaxRulesPage.pageTitleEdit);
      });

      it('should create new tax rule n°2', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSecondTaxRule', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRules(page, secondTaxRuleToCreate);
        expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
      });
    });

    describe('Create new product with the new tax rule', async () => {
      it('should go to \'Products > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageToCreateProduct', baseContext);

        await addTaxRulesPage.goToSubMenu(
          page,
          addTaxRulesPage.catalogParentLink,
          addTaxRulesPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.eq(true);
      });

      it('should choose \'Standard product\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

        await boProductsPage.selectProductType(page, productData.type);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should create standard product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

        await addProductPage.closeSfToolBar(page);

        const createProductMessage = await addProductPage.setProduct(page, productData);
        expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
      });
    });

    describe('Create new order in FO with the created product', async () => {
      it('should preview product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

        // Click on preview button
        page = await addProductPage.previewProduct(page);
        await foClassicProductPage.changeLanguage(page, 'en');

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the created product to the cart
        await foClassicProductPage.addProductToTheCart(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should proceed to checkout and sign in by default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });

    describe('Generate the invoice and check the tax breakdown', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageTaxBreakdown', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageTaxBreakdown', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeOrderStatusTaxBreakdown', baseContext);

        const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
        expect(result).to.equal(dataOrderStatuses.paymentAccepted.name);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceTaxBreakdown', baseContext);

        // Download invoice
        firstInvoiceFileName = await boOrdersViewBlockTabListPage.downloadInvoice(page);
        expect(firstInvoiceFileName).to.not.eq(null);

        // Check that file exist
        const exist = await utilsFile.doesFileExist(firstInvoiceFileName);
        expect(exist).to.eq(true);
      });

      it('should check the tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTaxBreakdownInFile', baseContext);

        // Check the existence of the first tax
        let exist = await utilsFile.isTextInPDF(firstInvoiceFileName, '10.000 %');
        expect(exist).to.eq(true);

        // Check the existence of the second tax
        exist = await utilsFile.isTextInPDF(firstInvoiceFileName, '20.000 %');
        expect(exist).to.eq(true);
      });
    });
  });

  describe('Disable tax breakdown then check the invoice file', async () => {
    describe('Disable tax breakdown', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableTaxBreakdown', baseContext);

        await boOrdersViewBlockTabListPage.goToSubMenu(
          page,
          boOrdersViewBlockTabListPage.ordersParentLink,
          boOrdersViewBlockTabListPage.invoicesLink,
        );

        const pageTitle = await invoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should disable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableTaxBreakdown', baseContext);

        await invoicesPage.enableTaxBreakdown(page, false);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Generate the invoice and check that there is no tax breakdown', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageNoTaxBreakdown', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageNoTaxBreakdown', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceNoTaxBreakdown', baseContext);

        // Download invoice and check existence
        secondInvoiceFileName = await boOrdersViewBlockTabListPage.downloadInvoice(page);
        expect(secondInvoiceFileName).to.not.eq(null);

        const exist = await utilsFile.doesFileExist(secondInvoiceFileName);
        expect(exist).to.eq(true);
      });

      it('should check that there is no tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNoTaxBreakdownInFile', baseContext);

        // Check that there is only one tax line 30.000 %
        let exist = await utilsFile.isTextInPDF(secondInvoiceFileName, '10.000 %');
        expect(exist).to.eq(false);

        exist = await utilsFile.isTextInPDF(secondInvoiceFileName, '20.000 %');
        expect(exist).to.eq(false);

        exist = await utilsFile.isTextInPDF(secondInvoiceFileName, '30.000 %');
        expect(exist).to.eq(true);
      });
    });
  });

  // Delete tax rules created with bulk actions
  describe('Delete tax rules with Bulk Actions', async () => {
    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage1', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.internationalParentLink,
        invoicesPage.taxesLink,
      );

      const pageTitle = await taxesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage1', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxRulesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await taxRulesPage.filterTable(
        page,
        'input',
        'name',
        taxRuleGroupToCreate.name,
      );

      const numberOfLinesAfterFilter = await taxRulesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await taxRulesPage.getTextColumnFromTable(
          page,
          i,
          'name',
        );
        expect(textColumn).to.contains(taxRuleGroupToCreate.name);
      }
    });

    it('should delete tax rules with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

      const deleteTextResult = await taxRulesPage.bulkDeleteTaxRules(page);
      expect(deleteTextResult).to.be.contains(taxRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.be.above(0);
    });
  });

  // Post-condition: Delete the created products
  bulkDeleteProductsTest(productData.name, `${baseContext}_postTest`);
});
